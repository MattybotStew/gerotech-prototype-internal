<?php
/**
 * Set the two card images swapped on 2026-10 (Cursor):
 *   - Applications -> app_cards -> "Process Troubleshooting" -> app-troubleshooting.jpg
 *   - Machine Custom Solutions -> mcs_cards -> "Process Engineering" -> process-engineering.jpg
 *
 * Card images are stored as media-library attachments, so the theme default alone
 * does not change the front end. This imports the bundled asset (via a temp copy —
 * media_handle_sideload MOVES its source) and sets it on the matching row.
 *
 * Self-contained and space-safe (no CLI args, so "Process Troubleshooting" can't
 * be word-split). Idempotent.
 *
 * Run (Local): php -c "<ini>" run-local.php scripts/set-card-images-2026-10.php
 * Run (Dev):   wp eval-file - < scripts/set-card-images-2026-10.php
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run this through WordPress.\n" );
	exit( 1 );
}
if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

/**
 * Point a service-card image at a bundled theme asset.
 *
 * @param string $slug      Page path.
 * @param string $field     Repeater field name.
 * @param string $title     Card title (exact).
 * @param string $asset_rel Theme-relative asset path.
 * @return void
 */
function gx_set_card_image( $slug, $field, $title, $asset_rel ) {
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		echo "Page '{$slug}' not found.\n";
		return;
	}
	$post_id = (int) $page->ID;

	$asset = get_theme_file_path( $asset_rel );
	if ( ! file_exists( $asset ) ) {
		echo "Missing theme asset: {$asset_rel}\n";
		return;
	}

	$field_key = $field;
	if ( function_exists( 'acf_get_field' ) ) {
		$f = acf_get_field( $field );
		if ( $f && ! empty( $f['key'] ) ) {
			$field_key = $f['key'];
		}
	}

	$rows = get_field( $field, $post_id );
	if ( ! is_array( $rows ) ) {
		echo "No rows in '{$field}' on #{$post_id}.\n";
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
		echo "No '{$title}' row in '{$field}'.\n";
		return;
	}

	$current_id = isset( $rows[ $target ]['image'] ) ? $rows[ $target ]['image'] : 0;
	if ( is_array( $current_id ) && isset( $current_id['ID'] ) ) {
		$current_id = (int) $current_id['ID'];
	}
	$current_id = (int) $current_id;

	if ( $current_id ) {
		$attached = (string) get_post_meta( $current_id, '_wp_attached_file', true );
		if ( basename( $attached ) === basename( $asset_rel ) ) {
			$meta = wp_get_attachment_metadata( $current_id );
			printf(
				"  '%s' already uses #%d (%s)%s — nothing to do.\n",
				$title,
				$current_id,
				$attached,
				isset( $meta['width'] ) ? " {$meta['width']}x{$meta['height']}" : ''
			);
			return;
		}
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$tmp = wp_tempnam( basename( $asset_rel ) );
	if ( ! $tmp || ! copy( $asset, $tmp ) ) {
		echo "Could not stage a temp copy of {$asset_rel}.\n";
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

	printf( "  '%s' -> imported #%d (%s) and set.\n", $title, $new_id, basename( $asset_rel ) );
}

gx_set_card_image( 'unique-applications-for-standard-machines', 'app_cards', 'Process Troubleshooting', 'assets/images/app-troubleshooting.jpg' );
gx_set_card_image( 'modification-of-standard-machine-tools', 'mcs_cards', 'Process Engineering', 'assets/images/process-engineering.jpg' );
// Figma #297: the new Electrical – Controls Solutions card uses the Pre-Engineered
// image until the client supplies a dedicated one (sheet: "Need Image").
gx_set_card_image( 'automated-system', 'ai_cards', 'Electrical – Controls Solutions', 'assets/images/pre-engineered-card.jpg' );

echo "Done.\n";
