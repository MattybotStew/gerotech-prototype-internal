<?php
/**
 * Apply the Tristien Bridges Basecamp copy corrections that live in stored ACF
 * (client review, 2026-10).
 *
 * Theme defaults alone do not change Local/Dev: a stored row always wins, so
 * each of these edits updates the stored value in place.
 *
 *   1. Homepage hero slide 2 ("In-Stock & Ready"): eyebrow + peek eyebrow
 *      "New Arrivals" -> "Floor Inventory".
 *   2. Homepage stat 2 "Machines Placed": 14,000 -> 14,000+ (suffix "+").
 *   3. Testimonials: "Kingbury Professional Services" ->
 *      "Kingsbury Professional Services" (shared options row, every page).
 *
 * Idempotent: re-running reports "already correct" and writes nothing.
 * Image replacements from the same thread are NOT handled here (assets not
 * supplied yet).
 *
 * Usage (Local):
 *   wp eval-file scripts/apply-tristien-2026-10.php
 * Usage (Dev): rsync to wp-content/, `wp eval-file ...`, then delete it.
 *
 * @package GerotechChild
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

/* ── 1. Homepage hero slide 2 eyebrow ────────────────────────── */

$home_id = (int) get_option( 'page_on_front' );
if ( ! $home_id ) {
	echo "No static front page set.\n";
	return;
}

$slides = get_field( 'field_home_hero_slides', $home_id );
if ( is_array( $slides ) ) {
	$changed = false;
	foreach ( $slides as $i => $row ) {
		$eyebrow  = isset( $row['eyebrow'] ) ? (string) $row['eyebrow'] : '';
		$peek     = isset( $row['peek_eyebrow'] ) ? (string) $row['peek_eyebrow'] : '';
		if ( 'New Arrivals' === $eyebrow || 'New Arrivals' === $peek ) {
			$slides[ $i ]['eyebrow']      = 'Floor Inventory';
			$slides[ $i ]['peek_eyebrow'] = 'Floor Inventory';
			$changed = true;
		}
	}
	if ( $changed ) {
		update_field( 'field_home_hero_slides', $slides, $home_id );
		echo "  ~ Hero slide 2 eyebrow -> Floor Inventory\n";
	} else {
		echo "  = Hero eyebrow: already correct\n";
	}
}

/* ── 2. Homepage stat 2 — 14,000+ ────────────────────────────── */

$stats = get_field( 'field_home_stats', $home_id );
if ( is_array( $stats ) ) {
	$changed = false;
	foreach ( $stats as $i => $row ) {
		$value = isset( $row['value'] ) ? (string) $row['value'] : '';
		if ( '14,000' === $value || 'Machines Placed' === ( isset( $row['label'] ) ? $row['label'] : '' ) ) {
			if ( '14,000+' !== $value || '+' !== ( isset( $row['suffix'] ) ? (string) $row['suffix'] : '' ) ) {
				$stats[ $i ]['value']  = '14,000+';
				$stats[ $i ]['suffix'] = '+';
				$changed = true;
			}
		}
	}
	if ( $changed ) {
		update_field( 'field_home_stats', $stats, $home_id );
		echo "  ~ Stat 'Machines Placed' -> 14,000+\n";
	} else {
		echo "  = Stat 'Machines Placed': already 14,000+\n";
	}
}

/* ── 3. Testimonials — Kingsbury spelling ────────────────────── */

$testimonials = get_field( 'field_testimonials', 'option' );
if ( is_array( $testimonials ) ) {
	$changed = false;
	foreach ( $testimonials as $i => $row ) {
		$name = isset( $row['name'] ) ? (string) $row['name'] : '';
		if ( false !== strpos( $name, 'Kingbury' ) ) {
			$testimonials[ $i ]['name'] = str_replace( 'Kingbury', 'Kingsbury', $name );
			$changed = true;
		}
	}
	if ( $changed ) {
		update_field( 'field_testimonials', $testimonials, 'option' );
		echo "  ~ Testimonial name -> Kingsbury\n";
	} else {
		echo "  = Testimonial name: already correct\n";
	}
}

/* ── 4. Engineered Solutions footer CTA ──────────────────────
 * Match the other engineering pages: subhead present, phone card removed.
 * Targeted (not the full ES content migration) so client edits elsewhere on
 * the ES page survive. */
$es = get_page_by_path( 'engineered-solutions' );
if ( $es ) {
	$es_sub  = (string) get_field( 'field_es_cta_subhead', $es->ID );
	$want    = "Let's Talk Through It. Prefer Email?";
	if ( "" === trim( $es_sub ) ) {
		update_field( 'field_es_cta_subhead', $want, $es->ID );
		echo "  ~ ES CTA subhead set\n";
	} else {
		echo "  = ES CTA subhead: present\n";
	}

	$es_call = (string) get_field( 'field_es_cta_call_number', $es->ID );
	if ( '' !== trim( $es_call ) ) {
		update_field( 'field_es_cta_call_number', '', $es->ID );
		echo "  ~ ES CTA call number removed\n";
	} else {
		echo "  = ES CTA call number: already blank\n";
	}
}

$store = acf_get_store( 'values' );
if ( $store ) {
	$store->reset();
}
wp_cache_flush();
echo "Done.\n";