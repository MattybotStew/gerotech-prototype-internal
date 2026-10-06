<?php
/**
 * Move the homepage hero slide-2 line break (Figma node 7329:2900).
 *
 * Slide 2's heading should break after "Are":
 *   Our Showroom Machines Are / Ready To Ship
 *
 * The stored ACF row (home_hero_slides) wins over the template default, so this
 * updates it in place — other slide fields (image, peek, colours) are preserved.
 * Idempotent.
 *
 * Run (Local):
 *   php -c "<local php.ini>" /tmp/run-local.php scripts/update-home-hero-slide2-break.php
 * Run (Dev): `wp eval-file -` < this file
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run this through WordPress.\n" );
	exit( 1 );
}
if ( ! function_exists( 'update_field' ) ) {
	fwrite( STDERR, "ACF is not loaded.\n" );
	exit( 1 );
}

$home_id = (int) get_option( 'page_on_front' );
if ( ! $home_id ) {
	fwrite( STDERR, "No static front page set.\n" );
	exit( 1 );
}

$new = "Our Showroom Machines Are\n<em>Ready To Ship</em>";

$store = acf_get_store( 'values' );
if ( $store ) {
	$store->reset();
}

$slides = get_field( 'field_home_hero_slides', $home_id );
if ( ! is_array( $slides ) ) {
	echo "No hero slides found.\n";
	exit( 0 );
}

$changed = false;
foreach ( $slides as $i => $slide ) {
	if ( isset( $slide['headline'] ) && false !== strpos( $slide['headline'], 'Showroom Machines' ) ) {
		if ( trim( $slide['headline'] ) === $new ) {
			echo "Slide " . ( $i + 1 ) . " headline: unchanged\n";
		} else {
			$slides[ $i ]['headline'] = $new;
			$changed = true;
			echo "Slide " . ( $i + 1 ) . " headline: updated\n";
		}
	}
}

if ( $changed ) {
	update_field( 'field_home_hero_slides', $slides, $home_id );
	wp_cache_flush();
}

echo "Done.\n";
