<?php
/**
 * Keep the out-of-scope legacy pages on their LEGACY page template.
 *
 * WHY THIS EXISTS
 * ---------------
 * The child theme used to ship its own `page-service.php`, `page-contact.php`,
 * `rotary-repair.php`, `preventive-maintenance.php` and `page-support.php`.
 * Those files DID NOT render the page's Gravity Form — the parent's originals
 * do, via `do_shortcode('[gravityform id="N" …]')`. So the port silently
 * replaced four working forms with Contact Form 7 forms that deliver to a
 * `@…wpenginepowered.com` hostname, i.e. nowhere. The child templates have been
 * removed; this script makes sure the pages they used to hijack fall back to
 * the legacy template rather than the generic `page.php`.
 *
 * `page-solutions.php` is deliberately NOT in this list — the four ported ES
 * pages are handled by the child's `page-solutions.php` dispatcher.
 *
 * Idempotent: re-running changes nothing. Prints what it did, and what it left.
 *
 *   wp eval-file scripts/align-legacy-page-templates.php
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy slug => the `_wp_page_template` value the live site uses.
 *
 * Values taken from the legacy-faithful copy of the site, not invented.
 */
$gerotech_expected = array(
	// page-service.php in the parent renders Gravity Form 1.
	'service'                      => 'page-service.php',
	'submit-a-request'             => 'page-service.php',
	'parts-order'                  => 'page-service.php',
	'application-support'          => 'page-service.php',
	'preventive-maintenance-plan'  => 'page-service.php',
	// rotary-repair.php in the parent renders Gravity Form 2.
	'rotary-repair'                => 'rotary-repair.php',
	// preventive-maintenance.php in the parent renders Gravity Form 3.
	'planned-maintenance'          => 'preventive-maintenance.php',
	// page-contact.php in the parent renders Gravity Form 4.
	'contact'                      => 'page-contact.php',
	// page-service-home.php in the parent renders six CF7 service forms.
	'support'                      => 'page-service-home.php',
	// Not form-bearing, but out of port scope — keep the legacy rendering.
	'about'                        => 'page-about.php',
	'training'                     => 'page-training.php',
);

$gerotech_changed = 0;
$gerotech_ok      = 0;
$gerotech_missing = array();

foreach ( $gerotech_expected as $slug => $wanted ) {
	$page = get_page_by_path( $slug );

	/*
	 * This site is hierarchical and two different pages can share a slug
	 * (`rotary-repair` at / and under /service/). Prefer the top-level one,
	 * which is what get_page_by_path() already returns.
	 */
	if ( ! $page ) {
		$gerotech_missing[] = $slug;
		continue;
	}

	$current = (string) get_post_meta( $page->ID, '_wp_page_template', true );
	$current = ( 'default' === $current ) ? '' : $current;

	if ( $current === $wanted ) {
		$gerotech_ok++;
		printf( "  ok       %-32s #%-5d %s\n", $slug, $page->ID, $wanted );
		continue;
	}

	update_post_meta( $page->ID, '_wp_page_template', $wanted );
	$gerotech_changed++;
	printf(
		"  set      %-32s #%-5d %s  ->  %s\n",
		$slug,
		$page->ID,
		'' === $current ? '(none)' : $current,
		$wanted
	);
}

/*
 * Guard the whole point of the exercise: every page that should render a
 * Gravity Form must be on a template the parent actually ships.
 */
$gerotech_forms = array(
	'service'             => 'page-service.php',
	'rotary-repair'       => 'rotary-repair.php',
	'planned-maintenance' => 'preventive-maintenance.php',
	'contact'             => 'page-contact.php',
	'support'             => 'page-service-home.php',
);

echo "\nGravity Forms / legacy template verification:\n";
foreach ( $gerotech_forms as $slug => $template ) {
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		printf( "  MISSING PAGE %-24s\n", $slug );
		continue;
	}
	$current   = (string) get_post_meta( $page->ID, '_wp_page_template', true );
	$child     = get_stylesheet_directory() . '/' . $template;
	$parent    = get_template_directory() . '/' . $template;
	$resolves  = file_exists( $child ) ? 'child' : ( file_exists( $parent ) ? 'parent' : 'NOWHERE' );
	printf(
		"  %-22s tmpl=%-26s resolves=%-7s %s\n",
		$slug,
		$current,
		$resolves,
		'parent' === $resolves ? 'OK (legacy template, form intact)' : 'CHECK'
	);
}

printf(
	"\n%d set, %d already correct%s\n",
	$gerotech_changed,
	$gerotech_ok,
	$gerotech_missing ? ', missing: ' . implode( ', ', $gerotech_missing ) : ''
);
