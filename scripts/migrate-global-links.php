<?php
/**
 * One-shot: bring stored Site Content rows in line with the 2026-09-29 editor
 * simplification (see inc/acf-global-fields.php header comment).
 *
 *  1. The separate "Open in a new tab" toggles were removed. Where a stored
 *     row still has `new_tab = 1` and its Link value's `target` is not
 *     `_blank`, copy the flag into the target so nothing changes on the page
 *     once the toggle is gone. (External links open in a new tab automatically
 *     now, so this only matters for internal links an editor flagged.)
 *  2. `footer_socials` gained a `network` picker. Rows saved before it only
 *     have the typed glyph ("in", "ig", "▶") — derive the network from it.
 *
 * Idempotent: a second run reports "nothing to do". Environment-agnostic.
 *
 * Writes use the field NAME path (`options_nav_items_0_links_1_url`), not the
 * key — see the trap in .clinerules. Link fields store an array.
 *
 * Usage (Local):  wp eval-file scripts/migrate-global-links.php
 * Usage (Dev):    copy to /nas/content/live/gerotechdev/wp-content/ and
 *                 `wp eval-file wp-content/migrate-global-links.php`, then rm.
 *
 * @package GerotechScripts
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'gerotech_social_network_from_glyph' ) ) {
	echo "ACF or the Gerotech child theme is not loaded — aborting.\n";
	return;
}

$changed = 0;

/**
 * Ensure a stored Link value opens in a new tab.
 *
 * @param string $path  Name path of the link field, e.g. 'nav_items_0_url'.
 * @param mixed  $value Current stored value.
 * @param string $label Log label.
 */
$force_blank = function ( $path, $value, $label ) use ( &$changed ) {
	if ( ! is_array( $value ) || empty( $value['url'] ) ) {
		return;
	}
	if ( isset( $value['target'] ) && '_blank' === $value['target'] ) {
		return;
	}
	$value['target'] = '_blank';
	$value['title']  = isset( $value['title'] ) ? $value['title'] : '';
	update_field( $path, $value, 'option' );
	echo "  new tab  {$label}\n";
	$changed++;
};

echo "Folding new_tab flags into link targets…\n";

/* Main menu items + their drop-down links. */
foreach ( (array) get_field( 'nav_items', 'option' ) as $i => $row ) {
	if ( ! empty( $row['new_tab'] ) ) {
		$force_blank( "nav_items_{$i}_url", $row['url'] ?? null, "menu item {$i} ({$row['label']})" );
	}
	foreach ( (array) ( $row['links'] ?? array() ) as $j => $sub ) {
		if ( ! empty( $sub['new_tab'] ) ) {
			$force_blank( "nav_items_{$i}_links_{$j}_url", $sub['url'] ?? null, "menu item {$i} › link {$j} ({$sub['label']})" );
		}
	}
}

/* Machine groups › links. */
foreach ( (array) get_field( 'nav_machines_groups', 'option' ) as $i => $group ) {
	foreach ( (array) ( $group['links'] ?? array() ) as $j => $link ) {
		if ( ! empty( $link['new_tab'] ) ) {
			$force_blank( "nav_machines_groups_{$i}_links_{$j}_url", $link['url'] ?? null, "{$group['title']} › {$link['label']}" );
		}
	}
}

/* Machines panel bottom link. */
if ( get_field( 'nav_machines_footer_new_tab', 'option' ) ) {
	$force_blank( 'nav_machines_footer_url', get_field( 'nav_machines_footer_url', 'option' ), 'machines panel bottom link' );
}

/* Footer columns › links, legal links, socials. */
foreach ( (array) get_field( 'footer_columns', 'option' ) as $i => $column ) {
	foreach ( (array) ( $column['links'] ?? array() ) as $j => $link ) {
		if ( ! empty( $link['new_tab'] ) ) {
			$force_blank( "footer_columns_{$i}_links_{$j}_url", $link['url'] ?? null, "footer › {$column['title']} › {$link['label']}" );
		}
	}
}
foreach ( (array) get_field( 'footer_legal_links', 'option' ) as $i => $link ) {
	if ( ! empty( $link['new_tab'] ) ) {
		$force_blank( "footer_legal_links_{$i}_url", $link['url'] ?? null, "legal link {$i} ({$link['label']})" );
	}
}

echo "\nSetting the social network picker from legacy glyphs…\n";
foreach ( (array) get_field( 'footer_socials', 'option' ) as $i => $row ) {
	if ( ! empty( $row['new_tab'] ) ) {
		$force_blank( "footer_socials_{$i}_url", $row['url'] ?? null, "social {$i} ({$row['label']})" );
	}
	if ( empty( $row['network'] ) ) {
		$network = gerotech_social_network_from_glyph( $row['icon'] ?? '' );
		if ( '' === $network && ! empty( $row['label'] ) ) {
			$by_name = array_search( strtolower( trim( $row['label'] ) ), array_map( 'strtolower', gerotech_social_networks() ), true );
			$network = false !== $by_name ? $by_name : '';
		}
		if ( '' !== $network ) {
			update_field( "footer_socials_{$i}_network", $network, 'option' );
			echo "  network  social {$i}: '{$row['icon']}' → {$network}\n";
			$changed++;
		} else {
			echo "  ?        social {$i}: could not infer a network from '{$row['icon']}' / '{$row['label']}' — pick it in the editor\n";
		}
	}
}

echo "\n" . ( $changed ? "{$changed} value(s) updated." : 'Nothing to do — already migrated.' ) . "\n";

/* Verify by reading back the way the templates do. ACF memoises every value it
 * has read this request in its 'values' store, so reset that — wp_cache_flush()
 * alone leaves it and the read-back would show the pre-update rows. */
wp_cache_flush();
if ( function_exists( 'acf_get_store' ) ) {
	$store = acf_get_store( 'values' );
	if ( $store && method_exists( $store, 'reset' ) ) {
		$store->reset();
	}
}
$socials = (array) get_field( 'footer_socials', 'option' );
$with    = 0;
foreach ( $socials as $row ) {
	if ( ! empty( $row['network'] ) ) {
		$with++;
	}
}
echo 'Verify: ' . count( $socials ) . " social rows, {$with} with a network.\n";
