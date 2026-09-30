<?php
/**
 * Point a homepage machine-lineup panel photo at a bundled theme asset.
 *
 *   wp eval-file scripts/set-lineup-panel-photo.php "<tab label>" "assets/images/<file>.jpg"
 *
 * @package GerotechChild
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

$args      = is_array( $args ) ? array_values( $args ) : array();
$tab_label = isset( $args[0] ) ? trim( (string) $args[0], " \t\n\r\0\x0B\"'" ) : '';
$asset_rel = isset( $args[1] ) ? trim( (string) $args[1], " \t\n\r\0\x0B\"'" ) : '';

if ( '' === $tab_label || '' === $asset_rel ) {
	echo "Usage: wp eval-file scripts/set-lineup-panel-photo.php \"<tab label>\" \"assets/images/<file>.jpg\"\n";
	return;
}

$post_id = (int) get_option( 'page_on_front' );
if ( ! $post_id ) {
	echo "No static front page set.\n";
	return;
}

$asset = get_theme_file_path( $asset_rel );
if ( ! file_exists( $asset ) ) {
	echo "Missing theme asset: {$asset_rel}\n";
	return;
}

$field_key = 'field_home_lineup_panels';
if ( function_exists( 'acf_get_field' ) ) {
	$f = acf_get_field( 'lineup_panels' );
	if ( $f && ! empty( $f['key'] ) ) {
		$field_key = $f['key'];
	}
}

$rows = get_field( 'lineup_panels', $post_id );
if ( ! is_array( $rows ) ) {
	echo "No lineup_panels rows on front page #{$post_id}.\n";
	return;
}

$target = null;
foreach ( $rows as $i => $row ) {
	if ( isset( $row['tab_label'] ) && $tab_label === $row['tab_label'] ) {
		$target = $i;
		break;
	}
}
if ( null === $target ) {
	echo "No panel with tab_label '{$tab_label}'.\n";
	return;
}

$current_id = isset( $rows[ $target ]['photo'] ) ? $rows[ $target ]['photo'] : 0;
if ( is_array( $current_id ) && isset( $current_id['ID'] ) ) {
	$current_id = (int) $current_id['ID'];
}
$current_id = (int) $current_id;

if ( $current_id ) {
	$attached = (string) get_post_meta( $current_id, '_wp_attached_file', true );
	if ( basename( $attached ) === basename( $asset_rel ) ) {
		printf( "  '%s' already uses attachment #%d (%s) — nothing to do.\n", $tab_label, $current_id, $attached );
		return;
	}
}

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
	$tab_label
);

if ( file_exists( $tmp ) ) {
	@unlink( $tmp );
}

if ( is_wp_error( $new_id ) ) {
	echo 'Import failed: ' . $new_id->get_error_message() . "\n";
	return;
}

$rows[ $target ]['photo'] = $new_id;
update_field( $field_key, $rows, $post_id );

$meta = wp_get_attachment_metadata( $new_id );
printf(
	"  '%s' panel photo → attachment #%d (%sx%s).\n",
	$tab_label,
	$new_id,
	isset( $meta['width'] ) ? $meta['width'] : '?',
	isset( $meta['height'] ) ? $meta['height'] : '?'
);
