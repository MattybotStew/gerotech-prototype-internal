<?php
/**
 * Read-only verification of the Tristien Basecamp round DB state.
 * Run: wp eval-file _gerotech-scripts/verify-tristien-2026-10.php
 */
if ( ! function_exists( 'get_field' ) ) {
	echo "ACF not loaded\n";
	return;
}
$home = (int) get_option( 'page_on_front' );

$s = get_field( 'home_hero_slides', $home );
echo "slide1 eyebrow=[" . $s[1]['eyebrow'] . "] peek=[" . $s[1]['peek_eyebrow'] . "]\n";

$st = get_field( 'home_stats', $home );
echo "stat1 value=[" . $st[1]['value'] . "] suffix=[" . $st[1]['suffix'] . "]\n";

$t = get_field( 'testimonials', 'option' );
foreach ( $t as $r ) {
	if ( false !== strpos( $r['name'], 'ingsbury' ) || false !== strpos( $r['name'], 'ingbury' ) ) {
		echo "testimonial=[" . $r['name'] . "]\n";
	}
}

$es = get_page_by_path( 'engineered-solutions' );
echo "es_sub=[" . get_field( 'es_cta_subhead', $es->ID ) . "] es_call=[" . get_field( 'es_cta_call_number', $es->ID ) . "]\n";

$panels = get_field( 'lineup_panels', $home );
foreach ( $panels as $p ) {
	if ( 'Haas Automation' === $p['tab_label'] ) {
		echo "lineup_tags=" . str_replace( "\n", " | ", $p['tags'] ) . "\n";
	}
}

$rows = get_field( 'field_nav_machines_groups', 'option' );
$n = 0;
$pal = 0;
foreach ( $rows as $g ) {
	foreach ( (array) $g['links'] as $l ) {
		$n++;
		if ( 'Pallet-Changing VMCs' === $l['label'] ) {
			$pal++;
		}
	}
}
echo "nav_links={$n} pallet={$pal}\n";
