<?php
/**
 * One-shot: rebuild the Engineered Solutions mega-panel "All Services" lists
 * so they mirror the on-page service cards — same items, same order, same
 * #card anchors. Covers adds/removes/reorders, unlike update-es-service-anchors.php
 * which only rewrites URLs on labels it already knows.
 *
 * The single source of truth is gerotech_es_defaults()['services'] (this script
 * never invents a label or URL). It replaces the stored `nav_es_services`
 * options repeater with those rows.
 *
 * Traps handled (see .clinerules):
 * - Options-page repeaters store under the field NAME (`options_nav_es_services_…`).
 * - `url` is a Link field and needs an array, not a bare string.
 * - ACF memoises the repeater for the whole request — flush before reading back.
 *
 * Idempotent. Environment-agnostic: run the same file on Local and Dev.
 *
 * Usage (Local) — bundled PHP + goodshepherd's wp-cli.phar:
 *   "$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php" \
 *     -d "mysqli.default_socket=$HOME/Library/Application Support/Local/run/Rg1VtCBT9/mysql/mysqld.sock" \
 *     "$HOME/Local Sites/goodshepherd/_tools/wp-cli.phar" \
 *     --path="$HOME/Local Sites/gerotech/app/public" --url=https://gerotech.local \
 *     eval-file scripts/update-es-nav-order.php
 *
 * Usage (Dev): rsync over ssh to /nas/content/live/gerotechdev/_gerotech-scripts/,
 * then `wp eval-file` that absolute path.
 *
 * @package GerotechScripts
 */

if ( ! function_exists( 'gerotech_es_defaults' ) ) {
	echo "gerotech_es_defaults() not available — run inside the Gerotech child theme.\n";
	return;
}

$defaults = gerotech_es_defaults();
$key      = 'field_nav_es_services'; // field key (selector)
$name     = 'nav_es_services';        // field name (storage path)

/* Build the desired rows, keyed by ACF sub-field NAME. */
$desired = array();
foreach ( $defaults['services'] as $group ) {
	$links = array();
	foreach ( $group['links'] as $link ) {
		$row = array(
			'label' => $link['label'],
			'url'   => array(
				'url'    => $link['url'],
				'title'  => '',
				'target' => '',
			),
		);
		if ( ! empty( $link['mobile_label'] ) ) {
			$row['mobile_label'] = $link['mobile_label'];
		}
		$links[] = $row;
	}
	$desired[] = array(
		'heading_lead' => isset( $group['lead'] ) ? $group['lead'] : '',
		'heading_main' => isset( $group['main'] ) ? $group['main'] : '',
		'links'        => $links,
	);
}

/* Print the current stored order (before). */
$before = get_field( $key, 'option' );
echo "BEFORE:\n";
if ( is_array( $before ) ) {
	foreach ( $before as $g ) {
		$h = trim( ( isset( $g['heading_lead'] ) ? $g['heading_lead'] : '' ) . ' ' . ( isset( $g['heading_main'] ) ? $g['heading_main'] : '' ) );
		echo "  [$h]\n";
		foreach ( (array) ( isset( $g['links'] ) ? $g['links'] : array() ) as $l ) {
			$u = is_array( $l['url'] ) ? ( isset( $l['url']['url'] ) ? $l['url']['url'] : '' ) : (string) $l['url'];
			echo "    - " . ( isset( $l['label'] ) ? $l['label'] : '' ) . "  " . $u . "\n";
		}
	}
} else {
	echo "  (none stored)\n";
}

/* Replace the whole repeater. ACF trims rows beyond the new count. */
update_field( $key, $desired, 'option' );

/* Flush before reading back — ACF memoises repeaters per request. */
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

$after = get_field( $key, 'option' );
echo "\nAFTER:\n";
$total = 0;
if ( is_array( $after ) ) {
	foreach ( $after as $g ) {
		$h = trim( ( isset( $g['heading_lead'] ) ? $g['heading_lead'] : '' ) . ' ' . ( isset( $g['heading_main'] ) ? $g['heading_main'] : '' ) );
		echo "  [$h]\n";
		foreach ( (array) ( isset( $g['links'] ) ? $g['links'] : array() ) as $l ) {
			$u = is_array( $l['url'] ) ? ( isset( $l['url']['url'] ) ? $l['url']['url'] : '' ) : (string) $l['url'];
			echo "    - " . ( isset( $l['label'] ) ? $l['label'] : '' ) . "  " . $u . "\n";
			$total++;
		}
	}
}

/* Verify order matches the defaults exactly. */
$want = array();
foreach ( $desired as $g ) {
	$gh = trim( ( $g['heading_lead'] ?? '' ) . ' ' . ( $g['heading_main'] ?? '' ) );
	foreach ( $g['links'] as $l ) {
		$want[] = $gh . '|' . $l['label'] . '|' . $l['url']['url'];
	}
}
$got = array();
foreach ( (array) $after as $g ) {
	$gh = trim( ( ( $g['heading_lead'] ?? '' ) ) . ' ' . ( $g['heading_main'] ?? '' ) );
	foreach ( (array) ( $g['links'] ?? array() ) as $l ) {
		$u   = is_array( $l['url'] ) ? ( $l['url']['url'] ?? '' ) : (string) $l['url'];
		$got[] = $gh . '|' . ( $l['label'] ?? '' ) . '|' . $u;
	}
}

echo "\nTotal links: {$total}\n";
if ( $want === $got ) {
	echo "OK: stored nav_es_services matches gerotech_es_defaults() exactly (order + anchors).\n";
} else {
	echo "MISMATCH:\n";
	echo '  want ' . count( $want ) . ", got " . count( $got ) . "\n";
	foreach ( array_diff( $want, $got ) as $d ) {
		echo "  missing/stale: $d\n";
	}
	foreach ( array_diff( $got, $want ) as $d ) {
		echo "  unexpected   : $d\n";
	}
}
