<?php
/**
 * Print sample ES mega-menu service URLs (no bash parentheses in wp eval).
 *
 * @package GerotechScripts
 */

$want = array(
	'Auto Doors',
	'Specialty Machine',
	'Part Programming',
	'HMI Design',
	'Electrical – Controls Solutions',
);

$panel = gerotech_es_panel();
foreach ( $panel['services'] as $group ) {
	foreach ( $group['links'] as $link ) {
		if ( in_array( $link['label'], $want, true ) ) {
			echo $link['label'] . ' => ' . $link['url'] . "\n";
		}
	}
}
