<?php
/**
 * Gerotech Child — theme bootstrap.
 *
 * Front-end build of the Gerotech prototype as a WordPress child theme.
 * No page builder: section markup is ported verbatim from the static prototype
 * and client-editable content is wired to ACF fields.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GEROTECH_CHILD_VERSION', '1.0.0' );
define( 'GEROTECH_CHILD_DIR', get_stylesheet_directory() );
define( 'GEROTECH_CHILD_URI', get_stylesheet_directory_uri() );

require_once GEROTECH_CHILD_DIR . '/inc/helpers.php';
require_once GEROTECH_CHILD_DIR . '/inc/enqueue.php';
require_once GEROTECH_CHILD_DIR . '/inc/acf-fields.php';
require_once GEROTECH_CHILD_DIR . '/inc/acf-legacy-fields.php';
require_once GEROTECH_CHILD_DIR . '/inc/acf-global-fields.php';
require_once GEROTECH_CHILD_DIR . '/inc/global-content.php';
require_once GEROTECH_CHILD_DIR . '/inc/admin.php';

/**
 * Theme supports.
 */
function gerotech_child_setup() {
	load_child_theme_textdomain( 'gerotech-child', GEROTECH_CHILD_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// The header navigation and the footer's link columns are rendered from the
	// Site Content options page (inc/acf-global-fields.php + inc/global-content.php)
	// rather than from WP menus: the mega panels need descriptions, a CTA button
	// inside a column and fixed column counts, which the core menu UI cannot
	// express. These menu locations stay registered for a future simple menu.
	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'gerotech-child' ),
			'footer'  => __( 'Footer Navigation', 'gerotech-child' ),
		)
	);
}
add_action( 'after_setup_theme', 'gerotech_child_setup' );

/**
 * Excerpt / content helpers are intentionally absent — v1 templates are static.
 */

/**
 * Legacy / alias 301 redirects.
 *
 * The prototype ships `machine-modification.html` as a canonical redirect to
 * Machine Custom Solutions. Recreate it as a real 301 (the dev DB has no such
 * page, so it would otherwise 404).
 */
function gerotech_legacy_redirects() {
	if ( ! is_404() ) {
		return;
	}

	$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );

	$map = array(
		'machine-modification' => '/modification-of-standard-machine-tools/',
	);

	if ( isset( $map[ $path ] ) ) {
		wp_safe_redirect( home_url( $map[ $path ] ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'gerotech_legacy_redirects' );

/**
 * Hide the parent theme's legacy ACF groups on pages we've rebuilt.
 *
 * The parent theme's DB field groups (Home Options, Page Options, ES/About/
 * Training/Service Options, …) are tied to its old markup and are not read by
 * our templates. They still appear in the editor's “Meta Boxes” panel and
 * confuse editors. Remove them on the pages that now use our “— Content”
 * groups, keeping the parent groups intact everywhere else (CPTs, unconverted
 * pages).
 */
function gerotech_hide_legacy_field_groups( $groups ) {
	if ( ! is_admin() ) {
		return $groups;
	}

	$post_id = 0;
	if ( isset( $_GET['post'] ) ) {
		$post_id = (int) $_GET['post'];
	} elseif ( isset( $_POST['post_ID'] ) ) {
		$post_id = (int) $_POST['post_ID'];
	} elseif ( isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
		$post_id = (int) $GLOBALS['post']->ID;
	}
	if ( ! $post_id ) {
		return $groups;
	}

	$our_slugs = array(
		'home',
		'engineered-solutions',
		'modification-of-standard-machine-tools',
		'unique-applications-for-standard-machines',
		'automated-system',
		'careers',
		'training',
		'support',
		'service',
		'rotary-repair',
		'planned-maintenance',
		'about',
		'contact',
	);

	if ( ! in_array( get_post_field( 'post_name', $post_id ), $our_slugs, true ) ) {
		return $groups;
	}

	$legacy_keys = array(
		'group_59525c6a53d41', // Home Options.
		'group_594d6a67f345d', // Engineered Solutions Options.
		'group_594d512fa81a6', // About Options.
		'group_5952a41d18bbd', // Training Options.
		'group_5952be53d9277', // Service Options.
		'group_60e854e038370', // Rotary Repair.
		'group_60e85395352dd', // Planned Maintenance.
		'group_5953c09413a0f', // Contact Options.
		'group_599b1967dce3b', // Page Options.
	);

	foreach ( $groups as $i => $group ) {
		if ( isset( $group['key'] ) && in_array( $group['key'], $legacy_keys, true ) ) {
			unset( $groups[ $i ] );
		}
	}

	return array_values( $groups );
}
add_filter( 'acf/load_field_groups', 'gerotech_hide_legacy_field_groups' );
