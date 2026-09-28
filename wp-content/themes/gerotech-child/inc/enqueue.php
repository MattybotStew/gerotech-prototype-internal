<?php
/**
 * Enqueue styles and scripts.
 *
 * Load order is fixed: tokens → components → layout → elevated.
 * Scripts are enqueued conditionally, matching the prototype's per-page usage
 * (see handoff/js-spec.md).
 *
 * Phase 1 note — the parent `gerotech` theme is a legacy theme (author Tim Bomers)
 * that:
 *   - hardcodes style.css/fonts.css in its own header.php (overridden by the child),
 *   - moves wp_enqueue_scripts + wp_print_head_scripts to wp_footer (`js_to_footer`),
 *     which would print our CSS in the footer → flash of unstyled content,
 *   - enqueues jQuery-dependent site-scripts/home_script/ts_script.
 * The child undoes the footer move and dequeues the parent scripts below.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Undo the parent's `js_to_footer` hack so styles/scripts load in <head>.
 *
 * `js_to_footer` is hooked to wp_enqueue_scripts at priority 10. We remove it at
 * priority 1 so it never runs, leaving core's head printing intact.
 */
function gerotech_child_undo_parent_footer_scripts() {
	remove_action( 'wp_enqueue_scripts', 'js_to_footer' );
}
add_action( 'wp_enqueue_scripts', 'gerotech_child_undo_parent_footer_scripts', 1 );

/**
 * Front-end styles + scripts.
 */
function gerotech_child_enqueue_assets() {
	// ── Fonts ────────────────────────────────────────────────
	// Navigo — Adobe Fonts kit (domain-locked; add staging + production domains).
	wp_enqueue_style( 'gerotech-navigo', 'https://use.typekit.net/lqh7ybe.css', array(), null );
	// Barlow Condensed — display face.
	wp_enqueue_style(
		'gerotech-barlow',
		'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&display=swap',
		array(),
		null
	);

	// ── Theme CSS (fixed order) ──────────────────────────────
	$css = array(
		'gerotech-tokens'         => 'assets/css/tokens.css',
		'gerotech-components'     => 'assets/css/components.css',
		'gerotech-layout'         => 'assets/css/layout.css',
		'gerotech-elevated'       => 'assets/css/elevated.css',
		'gerotech-gallery-module' => 'assets/css/gallery-module.css',
	);

	$deps = array( 'gerotech-navigo', 'gerotech-barlow' );
	foreach ( $css as $handle => $rel ) {
		wp_enqueue_style( $handle, GEROTECH_CHILD_URI . '/' . $rel, $deps, gerotech_asset_version( $rel ) );
		$deps = array( $handle );
	}

	// Child style.css (theme header only — no rules).
	wp_enqueue_style( 'gerotech-child', get_stylesheet_uri(), array( 'gerotech-elevated' ), GEROTECH_CHILD_VERSION );

	// ── Legacy (dev) body pages ──────────────────────────────
	// Support + Service bodies are ported from the dev site and styled by the
	// parent theme's stylesheet, scoped to `.legacy` so it cannot leak into the
	// new header/footer. Replica fonts come from the parent theme.
	$legacy_pages = array( 'support', 'service', 'training', 'rotary-repair', 'planned-maintenance', 'about', 'contact' );
	if ( is_page( $legacy_pages ) ) {
		wp_enqueue_style( 'gerotech-legacy-fonts', get_template_directory_uri() . '/fonts.css', array(), null );
		wp_enqueue_style(
			'gerotech-legacy',
			GEROTECH_CHILD_URI . '/assets/css/legacy.css',
			array( 'gerotech-child' ),
			gerotech_asset_version( 'assets/css/legacy.css' )
		);
	}

	// ── Dequeue parent theme scripts (not used by the child) ──
	foreach ( array( 'site-scripts', 'home_script', 'ts_script' ) as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}

	// ── Scripts ──────────────────────────────────────────────
	$js = GEROTECH_CHILD_URI . '/assets/js/';

	// Site-wide.
	wp_enqueue_script( 'gerotech-nav', $js . 'nav.js', array(), gerotech_asset_version( 'assets/js/nav.js' ), true );
	wp_enqueue_script( 'gerotech-animations', $js . 'animations.js', array(), gerotech_asset_version( 'assets/js/animations.js' ), true );

	// Homepage only.
	if ( is_front_page() ) {
		wp_enqueue_script( 'gerotech-slider', $js . 'slider.js', array(), gerotech_asset_version( 'assets/js/slider.js' ), true );
		wp_enqueue_script( 'gerotech-stat-counter', $js . 'stat-counter.js', array(), gerotech_asset_version( 'assets/js/stat-counter.js' ), true );
		wp_enqueue_script( 'gerotech-machine-tabs', $js . 'machine-tabs.js', array(), gerotech_asset_version( 'assets/js/machine-tabs.js' ), true );
	}

	// Card modals + gallery collections viewer — ES detail pages.
	// NOTE: these are the actual WP page slugs (dev slugs), not prototype slugs.
	// Shared with footer.php, which renders the #mcs-modal container.
	$modal_pages = gerotech_modal_pages();
	if ( is_page( $modal_pages ) ) {
		wp_enqueue_script( 'gerotech-modal', $js . 'modal.js', array(), gerotech_asset_version( 'assets/js/modal.js' ), true );
		wp_enqueue_script( 'gerotech-gallery-module', $js . 'gallery-module.js', array(), gerotech_asset_version( 'assets/js/gallery-module.js' ), true );
	}

	// Legacy tab switcher — service page.
	if ( is_page( 'service' ) ) {
		wp_enqueue_script( 'gerotech-legacy-tabs', $js . 'legacy-tabs.js', array(), gerotech_asset_version( 'assets/js/legacy-tabs.js' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'gerotech_child_enqueue_assets', 20 );
