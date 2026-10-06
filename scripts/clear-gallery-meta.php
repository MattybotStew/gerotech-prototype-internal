<?php
/**
 * Clear the grey subtitle ("meta") on every row of a gallery collections
 * repeater. The front-end template only prints `meta` when it is non-empty, so
 * emptying the stored rows removes the grey text. Stored rows beat the template
 * default, so this DB pass is required after the default is emptied.
 *
 *   wp eval-file scripts/clear-gallery-meta.php "<page slug>" "<repeater>"
 *   wp eval-file scripts/clear-gallery-meta.php "modification-of-standard-machine-tools" "mcs_collections"
 *
 * Idempotent: a second run reports 0 cleared. Environment-agnostic.
 *
 * @package GerotechScripts
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

$args  = is_array( $args ) ? array_values( $args ) : array();
$slug  = isset( $args[0] ) ? trim( (string) $args[0] ) : '';
$field = isset( $args[1] ) ? trim( (string) $args[1] ) : '';

if ( '' === $slug || '' === $field ) {
	echo "Usage: wp eval-file scripts/clear-gallery-meta.php \"<page slug>\" \"<repeater>\"\n";
	return;
}

$page = get_page_by_path( $slug );
if ( ! $page ) {
	echo "Page '{$slug}' not found.\n";
	return;
}
$post_id = (int) $page->ID;

$field_key = $field;
if ( function_exists( 'acf_get_field' ) ) {
	$f = acf_get_field( $field );
	if ( $f && ! empty( $f['key'] ) ) {
		$field_key = $f['key'];
	}
}

$rows = get_field( $field, $post_id );
if ( ! is_array( $rows ) ) {
	echo "No rows in '{$field}' on '{$slug}'.\n";
	return;
}

$cleared = 0;
foreach ( $rows as $i => $row ) {
	if ( ! empty( $row['meta'] ) ) {
		printf( "  clear [%d] %-42s %s\n", $i, isset( $row['title'] ) ? $row['title'] : '', $row['meta'] );
		$rows[ $i ]['meta'] = '';
		$cleared++;
	}
}

if ( $cleared ) {
	update_field( $field_key, $rows, $post_id );
}

wp_cache_flush();
if ( function_exists( 'acf_flush_cache' ) ) {
	acf_flush_cache();
}
if ( function_exists( 'acf_get_store' ) ) {
	$store = acf_get_store( 'values' );
	if ( $store && method_exists( $store, 'reset' ) ) {
		$store->reset();
	}
}

$check = get_field( $field, $post_id );
$left  = 0;
foreach ( (array) $check as $row ) {
	if ( ! empty( $row['meta'] ) ) {
		$left++;
	}
}
echo "\nCleared {$cleared} row(s) in '{$field}' on '{$slug}'; {$left} still have meta.\n";
