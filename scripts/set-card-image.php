<?php
/**
 * Point a service-card image at a bundled theme asset, on any page.
 *
 * WHY THIS IS NEEDED
 * ------------------
 * Card images on the rebuilt pages are stored as media-library ATTACHMENTS inside
 * a repeater (e.g. `ai_cards_<i>_image`, `app_cards_<i>_image`), while gallery
 * items use theme-relative paths. Replacing `assets/images/<file>` updates the
 * front-end default but leaves the CARD on whatever upload is stored — so a DB
 * step is always required. This is that step.
 *
 * Usage (positional; WP-CLI rejects custom --flags on eval-file):
 *
 *   wp eval-file scripts/set-card-image.php "<page slug>" "<repeater field>" "<card title>" "assets/images/<file>.jpg"
 *
 * e.g.
 *   wp eval-file scripts/set-card-image.php "automated-system" "ai_cards" "HMI Design" "assets/images/hmi-design.jpg"
 *   wp eval-file scripts/set-card-image.php "unique-applications-for-standard-machines" "app_cards" "Training" "assets/images/app-training.jpg"
 *
 * Idempotent: skips when the stored attachment already comes from that asset.
 *
 * NOTE: media_handle_sideload() MOVES the file it is given, so it is handed a
 * wp_tempnam() copy. Pointing it at the theme asset would delete the asset.
 *
 * @package GerotechChild
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

$args = is_array( $args ) ? array_values( $args ) : array();
$slug  = isset( $args[0] ) ? trim( (string) $args[0], " \t\n\r\0\x0B\"'" ) : '';
$field = isset( $args[1] ) ? trim( (string) $args[1], " \t\n\r\0\x0B\"'" ) : '';
$title = isset( $args[2] ) ? trim( (string) $args[2], " \t\n\r\0\x0B\"'" ) : '';
$asset_rel = isset( $args[3] ) ? trim( (string) $args[3], " \t\n\r\0\x0B\"'" ) : '';

if ( '' === $slug || '' === $field || '' === $title || '' === $asset_rel ) {
	echo "Usage: wp eval-file scripts/set-card-image.php \"<page slug>\" \"<repeater field>\" \"<card title>\" \"assets/images/<file>.jpg\"\n";
	return;
}

$page = get_page_by_path( $slug );
if ( ! $page ) {
	echo "Page '{$slug}' not found — aborting.\n";
	return;
}
$post_id = (int) $page->ID;

$asset = get_theme_file_path( $asset_rel );
if ( ! file_exists( $asset ) ) {
	echo "Missing theme asset: {$asset_rel}\n";
	return;
}

// Resolve the field key from the name so update_field() writes to the real field.
$field_key = $field;
if ( function_exists( 'acf_get_field' ) ) {
	$f = acf_get_field( $field );
	if ( $f && ! empty( $f['key'] ) ) {
		$field_key = $f['key'];
	}
}

$rows = get_field( $field, $post_id );
if ( ! is_array( $rows ) ) {
	echo "No rows in '{$field}' on page #{$post_id}.\n";
	return;
}

$target = null;
foreach ( $rows as $i => $row ) {
	if ( isset( $row['title'] ) && $title === $row['title'] ) {
		$target = $i;
		break;
	}
}
if ( null === $target ) {
	echo "No '{$title}' row found in '{$field}'.\n";
	return;
}

$current_id = isset( $rows[ $target ]['image'] ) ? $rows[ $target ]['image'] : 0;
if ( is_array( $current_id ) && isset( $current_id['ID'] ) ) {
	$current_id = (int) $current_id['ID'];
}
$current_id = (int) $current_id;

$stub = pathinfo( $asset_rel, PATHINFO_FILENAME );
if ( $current_id ) {
	$attached = (string) get_post_meta( $current_id, '_wp_attached_file', true );
	if ( basename( $attached ) === basename( $asset_rel ) ) {
		$meta = wp_get_attachment_metadata( $current_id );
		printf(
			"  '%s' already uses attachment #%d (%s)%s — nothing to do.\n",
			$title,
			$current_id,
			$attached,
			isset( $meta['width'] ) ? " {$meta['width']}x{$meta['height']}" : ''
		);
		return;
	}
}

printf( "  '%s' is row %d of '%s', currently attachment #%d.\n", $title, $target, $field, $current_id );

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$tmp = wp_tempnam( basename( $asset_rel ) );
if ( ! $tmp || ! copy( $asset, $tmp ) ) {
	echo "Could not stage a temp copy of the asset.\n";
	return;
}

$new_id = media_handle_sideload(
	array(
		'name'     => basename( $asset_rel ),
		'tmp_name' => $tmp,
	),
	$post_id,
	$title
);

if ( file_exists( $tmp ) ) {
	@unlink( $tmp );
}

if ( is_wp_error( $new_id ) ) {
	echo 'Import failed: ' . $new_id->get_error_message() . "\n";
	return;
}

$rows[ $target ]['image'] = $new_id;
update_field( $field_key, $rows, $post_id );

$meta = wp_get_attachment_metadata( $new_id );
printf(
	"  imported #%d (%sx%s) and set as the '%s' card image.\n",
	$new_id,
	isset( $meta['width'] ) ? $meta['width'] : '?',
	isset( $meta['height'] ) ? $meta['height'] : '?',
	$title
);
