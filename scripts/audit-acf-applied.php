<?php
/**
 * Audit which ACF fields are APPLIED on this environment.
 *
 * "Applied" = the field holds a stored value, so an editor opening the page sees
 * real content instead of an empty control. A field that is defined in code but
 * never stored still renders correctly on the front end (the template default
 * covers it), which is exactly why this is easy to miss — the site looks right
 * while the editor looks broken.
 *
 * Run:  wp eval-file scripts/audit-acf-applied.php
 *       wp eval-file scripts/audit-acf-applied.php -- --verbose
 *
 * Exit code is 0 either way; this reports, it does not enforce.
 *
 * @package GerotechChild
 */

$verbose = in_array( '--verbose', (array) $args, true );

if ( ! function_exists( 'acf_get_field_groups' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

/**
 * Describe a value compactly for the report.
 *
 * @param mixed $v Value.
 * @return string
 */
function gerotech_audit_repr( $v ) {
	if ( is_array( $v ) ) {
		return 'array(' . count( $v ) . ')';
	}
	if ( is_bool( $v ) ) {
		return $v ? 'true' : 'false';
	}
	$s = trim( (string) $v );
	return '' === $s ? '(empty)' : mb_substr( $s, 0, 40 );
}

/**
 * Every page that has ACF groups attached, plus the options page.
 *
 * @return array Map of label => post id or 'option'.
 */
function gerotech_audit_targets() {
	$targets = array();
	$dupes   = array();

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	// Detect genuine collisions by FULL path. Using the bare slug here is wrong for
	// hierarchical pages: `service/rotary-repair` (#194) and `rotary-repair` (#1484)
	// are two DIFFERENT pages, both reachable. Comparing slugs made #194 look like an
	// unreachable duplicate of #1484, which would have had us trash a live URL.
	$by_path = array();
	foreach ( $pages as $page ) {
		$uri = get_page_uri( $page->ID );
		if ( '' === $uri ) {
			$uri = $page->post_name;
		}
		$by_path[ $uri ][] = (int) $page->ID;
	}

	foreach ( $pages as $page ) {
		$uri = get_page_uri( $page->ID );
		if ( '' === $uri ) {
			$uri = $page->post_name;
		}

		if ( count( $by_path[ $uri ] ) > 1 ) {
			// A real collision: two published pages claiming the same full path.
			$dupes[ $uri ] = $by_path[ $uri ];
			continue;
		}

		$targets[ $uri . ' (#' . $page->ID . ')' ] = $page->ID;
	}

	$targets['OPTIONS: Site Content'] = 'option';
	$targets['__dupes__']             = $dupes;

	return $targets;
}

/**
 * Top-level fields that apply to a given target.
 *
 * Only top-level fields are reported: repeater/flexible sub-fields live inside
 * their parent, so "is the parent stored?" is the meaningful question.
 *
 * @param mixed $post_id Post ID or 'option'.
 * @return array
 */
function gerotech_audit_fields( $post_id ) {
	$out = array();

	if ( 'option' === $post_id ) {
		// Site Content is four admin screens (Header / Menus / Footer / Shared
		// Content) since 2026-09-29; groups attach to a screen, values to 'option'.
		$groups = array();
		$screens = function_exists( 'gerotech_site_content_screens' )
			? array_keys( gerotech_site_content_screens() )
			: array( 'gerotech-site-content' );
		foreach ( $screens as $screen ) {
			foreach ( acf_get_field_groups( array( 'options_page' => $screen ) ) as $group ) {
				$groups[ $group['key'] ] = $group;
			}
		}
	} else {
		$groups = acf_get_field_groups( array( 'post_id' => $post_id ) );
	}

	foreach ( $groups as $group ) {
		// Only OUR groups. The parent theme ships its own DB-stored groups
		// (Home Options, Page Options, SEO fields, …) which our templates never
		// read and which gerotech_hide_legacy_field_groups() hides in the editor.
		// Reporting them would bury the real gaps. Ours are PHP-local.
		if ( empty( $group['local'] ) || 'php' !== $group['local'] ) {
			continue;
		}
		foreach ( (array) acf_get_fields( $group ) as $field ) {
			// Skip layout-only field types.
			if ( in_array( $field['type'], array( 'tab', 'message', 'accordion', 'clone' ), true ) ) {
				continue;
			}
			$out[] = $field;
		}
	}

	return $out;
}

echo "ACF applied-state audit\n";
echo str_repeat( '=', 60 ) . "\n\n";

$all_targets = gerotech_audit_targets();
$dupes       = isset( $all_targets['__dupes__'] ) ? $all_targets['__dupes__'] : array();
unset( $all_targets['__dupes__'] );

$total_fields  = 0;
$total_applied = 0;
$gaps          = array();

foreach ( $all_targets as $label => $post_id ) {
	$fields = gerotech_audit_fields( $post_id );
	if ( ! $fields ) {
		continue;
	}

	$missing = array();
	foreach ( $fields as $field ) {
		$total_fields++;
		$v = get_field( $field['name'], $post_id );
		$applied = ( null !== $v && '' !== $v && false !== $v && ! ( is_array( $v ) && empty( $v ) ) );
		if ( $applied ) {
			$total_applied++;
		} else {
			$missing[] = $field;
		}
	}

	if ( $missing ) {
		$gaps[ $label ] = $missing;
	}

	if ( $verbose ) {
		printf( "%-40s %3d fields, %3d applied, %3d blank\n", $label, count( $fields ), count( $fields ) - count( $missing ), count( $missing ) );
	}
}

if ( $dupes ) {
	echo "DUPLICATE PATHS — two published pages claiming the same full URL:\n";
	foreach ( $dupes as $uri => $ids ) {
		printf( "  %-34s #%s\n", $uri, implode( ', #', $ids ) );
	}
	echo "\n  These are excluded from the counts above. One of each pair is unreachable;\n";
	echo "  resolve before seeding, or you will edit the page nobody sees.\n\n";
}

if ( ! $gaps ) {
	echo "Every ACF field on every page holds a stored value. Nothing to do.\n";
} else {
	echo "Fields DEFINED IN CODE but NOT APPLIED (no stored value):\n\n";
	foreach ( $gaps as $label => $missing ) {
		printf( "  %s\n", $label );
		foreach ( $missing as $field ) {
			printf( "     - %-38s %s\n", $field['name'], $field['label'] );
		}
	}
}

printf( "\n%d/%d top-level fields applied across %d targets.\n", $total_applied, $total_fields, count( gerotech_audit_targets() ) );
echo "\nNOTE: a blank accent-colour select is CORRECT — blank means \"keep the design\n";
echo "colour\". Blank text/repeater fields are the ones worth applying.\n";
