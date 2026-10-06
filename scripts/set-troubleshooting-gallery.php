<?php
/**
 * Point the Applications "Process Troubleshooting" gallery collection at the
 * in-cut machining photo. Stored app_collections rows win over the template
 * default, so the theme change alone leaves the cabinet photo live.
 *
 * Idempotent. Run via wp eval-file (Local bootstrap or Dev stdin).
 *
 * @package GerotechChild
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

$page = get_page_by_path( 'unique-applications-for-standard-machines' );
if ( ! $page ) {
	echo "Applications page not found.\n";
	return;
}

$post_id = (int) $page->ID;
$store   = function_exists( 'acf_get_store' ) ? acf_get_store( 'values' ) : null;
if ( $store ) {
	$store->reset();
}

$rows = get_field( 'app_collections', $post_id );
if ( ! is_array( $rows ) ) {
	echo "No app_collections rows.\n";
	return;
}

$uri  = defined( 'GEROTECH_CHILD_URI' ) ? GEROTECH_CHILD_URI : '';
$line = "image | {$uri}/assets/images/app-troubleshooting.jpg | | Coolant blasting a part while a tool cuts inside a CNC | Process Troubleshooting · in the cut";
$hit  = false;

foreach ( $rows as $i => $row ) {
	$title = isset( $row['title'] ) ? $row['title'] : '';
	if ( 'process troubleshooting' !== strtolower( trim( $title ) ) ) {
		continue;
	}
	$hit = true;
	$media = isset( $row['media'] ) ? (string) $row['media'] : '';
	if ( $media === $line ) {
		echo "Process Troubleshooting gallery already uses the in-cut photo.\n";
		return;
	}
	$rows[ $i ]['media'] = $line;
}

if ( ! $hit ) {
	echo "Process Troubleshooting collection not found.\n";
	return;
}

update_field( 'field_app_collections', $rows, $post_id );
echo "Process Troubleshooting gallery now uses app-troubleshooting.jpg.\n";
