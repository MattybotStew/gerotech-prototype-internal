<?php
/**
 * Remove the "Pallet Changers" model tag from the homepage Haas Automation
 * machine-lineup panel (client request, Tristien Bridges, 2026-10).
 *
 * The homepage lineup tags are stored in the `lineup_panels` ACF repeater, so
 * changing the template default alone has no effect on Local/Dev — the stored
 * row wins. This edits the row in place, preserving every other sub-field.
 *
 * Idempotent: re-running reports "already removed" and writes nothing.
 *
 * Usage (Local):
 *   wp eval-file scripts/remove-lineup-pallet-tag.php
 * Usage (Dev): rsync to wp-content/, `wp eval-file ...`, then delete it.
 *
 * @package GerotechChild
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

$post_id = (int) get_option( 'page_on_front' );
if ( ! $post_id ) {
	echo "No static front page set.\n";
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

$label = 'Pallet Changers';
$hit   = false;

foreach ( $rows as $i => $row ) {
	if ( ! isset( $row['tab_label'] ) || 'Haas Automation' !== $row['tab_label'] ) {
		continue;
	}
	if ( empty( $row['tags'] ) ) {
		continue;
	}

	$lines = preg_split( '/\r\n|\r|\n/', (string) $row['tags'] );
	$kept  = array();
	foreach ( $lines as $line ) {
		$trim = trim( $line );
		if ( '' === $trim ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $trim ) );
		if ( isset( $parts[0] ) && 0 === strcasecmp( $parts[0], $label ) ) {
			$hit = true;
			continue;
		}
		$kept[] = $line;
	}

	if ( $hit ) {
		$rows[ $i ]['tags'] = implode( "\n", $kept );
	}
	break;
}

if ( ! $hit ) {
	echo "  = '{$label}' already absent from the Haas Automation panel.\n";
	return;
}

update_field( $field_key, $rows, $post_id );
echo "  ~ Removed '{$label}' from the Haas Automation lineup panel.\n";
