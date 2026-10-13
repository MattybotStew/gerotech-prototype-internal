<?php
/**
 * Clear the Machines menu item's URL.
 *
 * The Machines item is a hover/focus trigger for the Haas catalogue panel, not a
 * link. Its URL lives in the stored `nav_items` options repeater on any site
 * where an editor has saved the menu, and a stored row beats the code default in
 * inc/global-content.php, so the template default alone is not enough.
 *
 * Blanking the URL makes gerotech_nav_items() normalise it to '#', and the
 * template then renders a <button> instead of an <a>.
 *
 * Idempotent. Only the row labelled "Machines" is touched; every other URL is
 * left exactly as the editor saved it.
 *
 *   wp eval-file scripts/clear-machines-nav-url.php
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rows = get_field( 'nav_items', 'option' );

if ( ! is_array( $rows ) || empty( $rows ) ) {
	echo "nav_items is not stored — the code default (now blank) already renders. Nothing to do.\n";
	exit;
}

$changed = 0;

foreach ( $rows as $i => $row ) {
	$label = isset( $row['label'] ) ? trim( (string) $row['label'] ) : '';

	if ( 'machines' !== strtolower( $label ) ) {
		continue;
	}

	$url = isset( $row['url'] ) ? $row['url'] : '';

	if ( is_array( $url ) ) {
		$current = isset( $url['url'] ) ? $url['url'] : '';
	} else {
		$current = (string) $url;
	}

	if ( '' === $current ) {
		printf( "  ok    row %d (Machines) already has no URL\n", $i );
		continue;
	}

	// ACF stores an empty Link field as an array of empty strings.
	$rows[ $i ]['url'] = array(
		'url'    => '',
		'title'  => '',
		'target' => '',
	);

	/*
	 * Options-page repeaters store under the field NAME, not the key. Writing to
	 * field_nav_items_0_url creates an unread options_field_* row that changes
	 * nothing while reporting success.
	 */
	update_field( 'field_nav_items', $rows, 'option' );

	$changed++;
	printf( "  set   row %d (Machines)  %s  ->  (blank)\n", $i, $current );
}

if ( $changed > 0 ) {
	$store = acf_get_store( 'values' );
	if ( $store ) {
		$store->reset();
	}
}

// Prove it, from a re-read.
$after = get_field( 'nav_items', 'option' );
foreach ( (array) $after as $i => $row ) {
	$label = isset( $row['label'] ) ? trim( (string) $row['label'] ) : '';
	if ( 'machines' !== strtolower( $label ) ) {
		continue;
	}
	$u = isset( $row['url'] ) ? $row['url'] : '';
	if ( is_array( $u ) ) {
		$u = isset( $u['url'] ) ? $u['url'] : '';
	}
	printf(
		"\nread-back: row %d label=%s url=%s\n",
		$i,
		$label,
		'' === (string) $u ? '(blank)' : $u
	);
}

printf( "\n%d row(s) changed.\n", $changed );
