<?php
/**
 * One-shot: point Engineered Solutions mega-panel service sublinks at
 * page URL + #card-anchor (scroll to the matching .mcs-card).
 *
 * The URL map lives in gerotech_es_defaults() — this script only pushes
 * those defaults into the stored ACF rows. Idempotent.
 *
 * Traps (same class as update-machine-links.php):
 * - Options-page repeaters store under the field NAME, not the key.
 * - `url` is a Link field and needs an array; a bare string is discarded.
 * - Same-request get_field() is memoised — flush before verifying.
 *
 * Usage (Local):
 *   php "$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php" \
 *     -d "mysql.default_socket=$HOME/Library/Application Support/Local/run/VjZ_PwL-d/mysql/mysqld.sock" \
 *     -d "mysqli.default_socket=$HOME/Library/Application Support/Local/run/VjZ_PwL-d/mysql/mysqld.sock" \
 *     "$HOME/Local Sites/goodshepherd/_tools/wp-cli.phar" \
 *     --path="$HOME/Local Sites/gerotech/app/public" --url=https://gerotech.local \
 *     eval-file scripts/update-es-service-anchors.php
 *
 * Usage (Dev): rsync over ssh (not scp — WPE often rejects scp "subsystem request
 * failed on channel 0") to e.g. /nas/content/live/gerotechdev/_gerotech-scripts/,
 * then `wp eval-file` that absolute path. Do not use inline `wp eval` with
 * parentheses over ssh (bash quoting breaks).
 *
 * @package GerotechScripts
 */

if ( ! function_exists( 'gerotech_es_defaults' ) ) {
	echo "gerotech_es_defaults() not available — run inside the Gerotech child theme.\n";
	return;
}

$defaults = gerotech_es_defaults();

/* label => url across every service group. */
$map = array();
foreach ( $defaults['services'] as $group ) {
	foreach ( $group['links'] as $link ) {
		$map[ $link['label'] ] = $link['url'];
	}
}

$repeater = 'field_nav_es_services'; // field key, for get_field()/audit
$name     = 'nav_es_services';        // field name, for the option storage path
$rows     = get_field( $repeater, 'option' );

if ( ! is_array( $rows ) || empty( $rows ) ) {
	echo "No stored $repeater rows — run scripts/seed-acf-content.php first.\n";
	return;
}

$updated = 0;
$kept    = 0;
$unknown = array();

foreach ( $rows as $i => $group ) {
	$heading = trim(
		( isset( $group['heading_lead'] ) ? (string) $group['heading_lead'] : '' ) . ' ' .
		( isset( $group['heading_main'] ) ? (string) $group['heading_main'] : '' )
	);

	if ( empty( $group['links'] ) || ! is_array( $group['links'] ) ) {
		continue;
	}

	foreach ( $group['links'] as $j => $link ) {
		$label = isset( $link['label'] ) ? trim( (string) $link['label'] ) : '';
		if ( '' === $label || ! isset( $map[ $label ] ) ) {
			if ( '' !== $label ) {
				$unknown[] = trim( $heading ) . ' / ' . $label;
			}
			continue;
		}

		$want_url = $map[ $label ];
		$cur_url  = '';
		if ( isset( $link['url'] ) ) {
			$cur_url = is_array( $link['url'] )
				? ( isset( $link['url']['url'] ) ? (string) $link['url']['url'] : '' )
				: (string) $link['url'];
		}

		if ( $cur_url === $want_url ) {
			$kept++;
			continue;
		}

		// ACF stores an options-page repeater under the field NAME path, not
		// the key. Passing the key writes an unread options_field_… row.
		$prefix = "{$name}_{$i}_links_{$j}_";
		update_field(
			$prefix . 'url',
			array(
				'url'    => $want_url,
				'title'  => '',
				'target' => '',
			),
			'option'
		);
		printf( "  set  %-36s %s\n", $label, $want_url );
		$updated++;
	}
}

echo "\nES service anchors: {$updated} updated, {$kept} already correct\n";

if ( ! empty( $unknown ) ) {
	echo "Labels with no default URL (left alone):\n";
	foreach ( $unknown as $u ) {
		echo "  - {$u}\n";
	}
}

wp_cache_flush();
if ( function_exists( 'acf_get_store' ) ) {
	$store = acf_get_store( 'values' );
	if ( $store && method_exists( $store, 'reset' ) ) {
		$store->reset();
	}
}
if ( function_exists( 'acf_flush_cache' ) ) {
	acf_flush_cache();
}

$check  = get_field( $repeater, 'option' );
$total  = 0;
$hashed = 0;
foreach ( (array) $check as $group ) {
	foreach ( (array) ( isset( $group['links'] ) ? $group['links'] : array() ) as $link ) {
		$total++;
		$u = is_array( $link['url'] ) ? ( isset( $link['url']['url'] ) ? $link['url']['url'] : '' ) : (string) $link['url'];
		if ( false !== strpos( $u, '#' ) ) {
			$hashed++;
		}
	}
}
echo "Verify: {$total} links, {$hashed} with #anchors\n";
