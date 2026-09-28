<?php
/**
 * Point the Applications gallery collections at bundled theme assets.
 *
 * The card images on this page were already local media-library attachments, but
 * the five gallery collections still referenced remote Unsplash URLs — the last
 * external runtime dependency on a live page. The same photos are now bundled in
 * the theme, so this rewrites the stored `media` lines to use them.
 *
 * Why a script rather than just changing the template default: the gallery rows
 * are STORED, and a stored value beats the code default. Editing the default
 * alone would change nothing on Local or Dev.
 *
 * Idempotent and environment-agnostic — resolves the page by slug and the asset
 * URL from GEROTECH_CHILD_URI, so it is safe to run on Local and Dev, repeatedly.
 *
 * Run:  wp eval-file scripts/localize-applications-gallery.php
 *
 * @package GerotechChild
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

$page = get_page_by_path( 'unique-applications-for-standard-machines' );
if ( ! $page ) {
	echo "Applications page not found — aborting.\n";
	return;
}
$post_id = (int) $page->ID;

/** Unsplash id => bundled theme file. */
$map = array(
	'photo-1713371398485-7bde1bde9def' => 'app-troubleshooting-cabinet.jpg',
	'photo-1713371398484-cc4e4f6a262a' => 'app-optimization.jpg',
	'photo-1666618090858-fbcee636bd3e' => 'app-tooling-cart.jpg',
	'photo-1727292485858-588c7652ad69' => 'app-demo-showroom.jpg',
	'photo-1647427060118-4911c9821b82' => 'app-training.jpg',
);

$rows = get_field( 'app_collections', $post_id );
if ( ! is_array( $rows ) ) {
	echo "No app_collections rows found.\n";
	return;
}

echo "Applications gallery (page #{$post_id}):\n";

$changed = 0;
foreach ( $rows as $i => $row ) {
	$media = isset( $row['media'] ) ? (string) $row['media'] : '';
	if ( '' === $media || false === strpos( $media, 'images.unsplash.com' ) ) {
		printf( "  row %d  %-26s already local — skip\n", $i, isset( $row['title'] ) ? $row['title'] : '' );
		continue;
	}

	$new = $media;
	foreach ( $map as $uid => $file ) {
		$new = preg_replace(
			'#https://images\.unsplash\.com/' . preg_quote( $uid, '#' ) . '\?[^|\s]*#',
			GEROTECH_CHILD_URI . '/assets/images/' . $file,
			$new
		);
	}

	if ( $new === $media ) {
		printf( "  row %d  %-26s NO MAPPING — left alone\n", $i, isset( $row['title'] ) ? $row['title'] : '' );
		continue;
	}

	$rows[ $i ]['media'] = $new;
	$changed++;
	printf( "  row %d  %-26s -> %s\n", $i, isset( $row['title'] ) ? $row['title'] : '', basename( explode( '|', $new )[1] ) );
}

if ( $changed ) {
	update_field( 'field_app_collections', $rows, $post_id );
	echo "\n  updated {$changed} row(s).\n";
} else {
	echo "\n  nothing to change.\n";
}

// Verify nothing remote is left in this field.
$after = get_field( 'app_collections', $post_id );
$remote = 0;
foreach ( (array) $after as $row ) {
	if ( isset( $row['media'] ) && false !== strpos( (string) $row['media'], 'images.unsplash.com' ) ) {
		$remote++;
	}
}
echo "  remaining remote rows in app_collections: {$remote}\n";
