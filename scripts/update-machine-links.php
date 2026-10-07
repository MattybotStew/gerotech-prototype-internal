<?php
/**
 * One-shot: point the Machines mega-panel sublinks at the client's live
 * catalogue and open them in a new tab.
 *
 * The 41 sublinks were seeded as "#" placeholders ("model links are
 * placeholders until the client supplies per-model URLs"). The client has
 * since confirmed the URLs should come from https://gerotech.com/machines/,
 * which resolves to the per-model haascnc.com pages, all target="_blank".
 *
 * The URL map lives in the theme default (gerotech_machines_defaults()), not
 * here, so there is still only one copy of it. This script only pushes those
 * defaults into the stored ACF rows — it never invents a URL, and it never
 * touches a label it does not recognise.
 *
 * Idempotent: re-running reports "already correct" and writes nothing.
 * Environment-agnostic: run the same file on Local and on Dev.
 *
 * Usage (Local):
 *   php "$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php" \
 *     -d "mysql.default_socket=$HOME/Library/Application Support/Local/run/VjZ_PwL-d/mysql/mysqld.sock" \
 *     -d "mysqli.default_socket=$HOME/Library/Application Support/Local/run/VjZ_PwL-d/mysql/mysqld.sock" \
 *     "$HOME/Local Sites/goodshepherd/_tools/wp-cli.phar" \
 *     --path="$HOME/Local Sites/gerotech/app/public" --url=https://gerotech.local \
 *     eval-file scripts/update-machine-links.php
 *
 * Usage (Dev): rsync this file to /nas/content/live/gerotechdev/wp-content/,
 * then `wp eval-file wp-content/update-machine-links.php` and delete it.
 *
 * @package GerotechScripts
 */

if ( ! function_exists( 'gerotech_machines_defaults' ) ) {
	echo "gerotech_machines_defaults() not available — run inside the Gerotech child theme.\n";
	return;
}

$defaults = gerotech_machines_defaults();

/* label => url, per group title. */
$map = array();
foreach ( $defaults['groups'] as $group ) {
	foreach ( $group['links'] as $link ) {
		$map[ $group['title'] ][ $link['label'] ] = $link['url'];
	}
}

$repeater = 'field_nav_machines_groups'; // field key, for get_field()/audit
$name     = 'nav_machines_groups';        // field name, for the option storage path
$rows     = get_field( $repeater, 'option' );

if ( ! is_array( $rows ) || empty( $rows ) ) {
	echo "No stored $repeater rows — run scripts/seed-acf-content.php first.\n";
	return;
}

/* ── Prune labels the client has removed from the catalogue ────────────────
 * The update loop below only ever rewrites labels it still knows about and
 * deliberately leaves unrecognised labels alone. A label dropped from
 * gerotech_machines_defaults() therefore has to be removed explicitly, or the
 * stored row would keep rendering it forever. This runs BEFORE the update loop
 * so the loop then writes url + new_tab back for every surviving link (the
 * full replace above drops new_tab, so it must be re-applied by the loop). */
$prune = array( 'Pallet-Changing VMCs' );
$pruned = 0;
foreach ( $rows as $i => $group ) {
	if ( empty( $group['links'] ) || ! is_array( $group['links'] ) ) {
		continue;
	}
	$kept_links = array();
	foreach ( $group['links'] as $link ) {
		$label = isset( $link['label'] ) ? trim( (string) $link['label'] ) : '';
		if ( in_array( $label, $prune, true ) ) {
			printf( "  rm   %-16s %s\n", isset( $group['title'] ) ? $group['title'] : '', $label );
			$pruned++;
			continue;
		}
		$kept_links[] = $link;
	}
	$rows[ $i ]['links'] = $kept_links;
}
if ( $pruned > 0 ) {
	// Full replace by field NAME (the stored options path), not key.
	update_field( $name, $rows, 'option' );
	echo "Pruned {$pruned} removed label(s) from the stored nav.\n";
	wp_cache_flush();
	if ( function_exists( 'acf_flush_cache' ) ) {
		acf_flush_cache();
	}
}

$updated = 0;
$kept    = 0;
$unknown = array();

foreach ( $rows as $i => $group ) {
	$title = isset( $group['title'] ) ? (string) $group['title'] : '';
	$group_map = isset( $map[ $title ] ) ? $map[ $title ] : array();

	if ( empty( $group_map ) ) {
		echo "  ! no default links for group \"$title\" — skipped\n";
		continue;
	}

	if ( empty( $group['links'] ) || ! is_array( $group['links'] ) ) {
		continue;
	}

	foreach ( $group['links'] as $j => $link ) {
		$label = isset( $link['label'] ) ? trim( (string) $link['label'] ) : '';
		if ( '' === $label || ! isset( $group_map[ $label ] ) ) {
			if ( '' !== $label ) {
				$unknown[] = $title . ' / ' . $label;
			}
			continue;
		}

		$want_url = $group_map[ $label ];
		$cur_url  = '';
		if ( isset( $link['url'] ) ) {
			$cur_url = is_array( $link['url'] )
				? ( isset( $link['url']['url'] ) ? (string) $link['url']['url'] : '' )
				: (string) $link['url'];
		}
		$cur_new_tab = ! empty( $link['new_tab'] );

		if ( $cur_url === $want_url && $cur_new_tab ) {
			$kept++;
			continue;
		}

		// ACF stores an options-page repeater under the field NAME path, not
		// the key: the stored option is options_nav_machines_groups_0_links_0_url.
		// Passing the key here writes options_field_nav_machines_groups_... which
		// nothing reads -- it looks like it worked and the site never changes.
		$prefix = "{$name}_{$i}_links_{$j}_";
		// `url` is a Link field: it stores an array, and a bare string is
		// silently discarded.
		update_field( $prefix . 'url', array( 'url' => $want_url, 'title' => '', 'target' => '' ), 'option' );
		update_field( $prefix . 'new_tab', 1, 'option' );
		printf( "  set  %-16s %-28s %s\n", $title, $label, $want_url );
		$updated++;
	}
}

echo "\nMachine sublinks: {$updated} updated, {$kept} already correct\n";

if ( ! empty( $unknown ) ) {
	echo "Labels with no default URL (left alone, needs a decision):\n";
	foreach ( $unknown as $u ) {
		echo "  - {$u}\n";
	}
}

// Verify by reading the rows back, the way the template does.
// The cache must go first: ACF memoises the repeater for the rest of the
// request, so a same-request read-back reports the pre-update values and looks
// like the write failed when it did not.
wp_cache_flush();
if ( function_exists( 'acf_flush_cache' ) ) {
	acf_flush_cache();
}
$check = get_field( $repeater, 'option' );
$total = $blank = $tab = 0;
foreach ( (array) $check as $group ) {
	foreach ( (array) $group['links'] as $link ) {
		$total++;
		$u = is_array( $link['url'] ) ? ( isset( $link['url']['url'] ) ? $link['url']['url'] : '' ) : (string) $link['url'];
		if ( '#' === trim( $u ) || '' === trim( $u ) ) {
			$blank++;
		}
		if ( ! empty( $link['new_tab'] ) ) {
			$tab++;
		}
	}
}
echo "Verify: {$total} links, {$blank} still blank, {$tab} flagged new_tab\n";
