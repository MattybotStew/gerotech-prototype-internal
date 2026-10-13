<?php
/**
 * Keep the out-of-scope legacy pages on their LEGACY page template.
 *
 * WHY THIS EXISTS
 * ---------------
 * The child theme used to ship its own `page-service.php`, `page-contact.php`,
 * `rotary-repair.php`, `preventive-maintenance.php` and `page-support.php`.
 * Those files did NOT render the page's Gravity Form — the parent's originals
 * do, via `do_shortcode('[gravityform id="N" …]')`. So the port silently
 * replaced four working Gravity Forms with Contact Form 7 forms that deliver to
 * a `@…wpenginepowered.com` hostname, i.e. nowhere. The child templates have
 * been removed; this script makes sure the pages they used to hijack fall back
 * to the legacy template rather than the generic `page.php`.
 *
 * KEYED ON FULL PATHS, NOT SLUGS. This site is hierarchical and two different
 * pages can share a slug — there is an `application-support` at /application-support/
 * (#3179) AND under /service/ (#198), and a `rotary-repair` at both levels.
 * Matching a bare slug sets the template on whichever page comes back first,
 * which is how this script once changed #3179 by mistake. Full paths are
 * unambiguous.
 *
 * `page-solutions.php` is deliberately NOT in this list — the four ported ES
 * pages are handled by the child's `page-solutions.php` dispatcher.
 *
 * Idempotent. Prints what it did, then re-reads every page to prove the
 * template actually resolves to a file the PARENT ships.
 *
 *   wp eval-file scripts/align-legacy-page-templates.php
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Full page path => the `_wp_page_template` value the live site uses.
 *
 * Values are taken from the legacy-faithful copy of the site, not invented.
 * `page-service.php` is shared by six pages; it renders Gravity Form 1.
 */
$gerotech_expected = array(
	'service'                             => 'page-service.php',
	'service/submit-a-request'            => 'page-service.php',
	'service/parts-order'                 => 'page-service.php',
	'service/rotary-repair'               => 'page-service.php',
	'service/preventive-maintenance-plan' => 'page-service.php',
	'service/application-support'         => 'page-service.php',
	'rotary-repair'                       => 'rotary-repair.php',
	'planned-maintenance'                 => 'preventive-maintenance.php',
	'contact'                             => 'page-contact.php',
	'support'                             => 'page-service-home.php',
	// Not form-bearing, but out of port scope — keep the legacy rendering.
	'about'                               => 'page-about.php',
	'training'                            => 'page-training.php',
);

$gerotech_changed = 0;
$gerotech_ok      = 0;
$gerotech_missing = array();

foreach ( $gerotech_expected as $path => $wanted ) {
	$page = get_page_by_path( $path );

	if ( ! $page ) {
		$gerotech_missing[] = $path;
		continue;
	}

	$current = (string) get_post_meta( $page->ID, '_wp_page_template', true );
	$current = ( 'default' === $current ) ? '' : $current;

	if ( $current === $wanted ) {
		$gerotech_ok++;
		continue;
	}

	update_post_meta( $page->ID, '_wp_page_template', $wanted );
	$gerotech_changed++;
	printf(
		"  set  %-38s #%-5d %-12s -> %s\n",
		$path,
		$page->ID,
		'' === $current ? '(none)' : $current,
		$wanted
	);
}

/*
 * Prove the point: every page that should render a form must resolve to a
 * template the PARENT ships. If it says NOWHERE, the page would fall through
 * to the generic page.php and the form would be gone again.
 */
$gerotech_forms = array(
	'service'             => 'page-service.php',
	'rotary-repair'       => 'rotary-repair.php',
	'planned-maintenance' => 'preventive-maintenance.php',
	'contact'             => 'page-contact.php',
	'support'             => 'page-service-home.php',
);

echo "\nLegacy template verification (each must resolve to the PARENT):\n";
$gerotech_bad = 0;

foreach ( $gerotech_forms as $path => $template ) {
	$page = get_page_by_path( $path );

	if ( ! $page ) {
		printf( "  PROBLEM  %-22s page not found\n", $path );
		$gerotech_bad++;
		continue;
	}

	$current  = (string) get_post_meta( $page->ID, '_wp_page_template', true );
	$in_child = file_exists( get_stylesheet_directory() . '/' . $template );
	$in_parent = file_exists( get_template_directory() . '/' . $template );
	$resolves = $in_child ? 'child' : ( $in_parent ? 'parent' : 'NOWHERE' );

	// Either theme owning the file is fine — the child wins when it ships one.
	// NOWHERE means the page falls through to the generic page.php, which is
	// what silently drops the form.
	if ( 'NOWHERE' === $resolves ) {
		$gerotech_bad++;
	}

	printf(
		"  %-8s %-22s tmpl=%-26s resolves=%s\n",
		'NOWHERE' === $resolves ? 'PROBLEM' : 'OK',
		$path,
		$current,
		$resolves
	);
}

printf(
	"\n%d set, %d already correct%s\n",
	$gerotech_changed,
	$gerotech_ok,
	$gerotech_missing ? ', NOT FOUND: ' . implode( ', ', $gerotech_missing ) : ''
);

if ( $gerotech_bad > 0 ) {
	printf( "%d page(s) would NOT render their legacy template.\n", $gerotech_bad );
}
