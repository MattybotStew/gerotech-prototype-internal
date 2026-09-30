<?php
/**
 * Admin polish for the Site Content screens.
 *
 * Three small things that make the header / menus / footer editor safe for a
 * non-developer:
 *
 *   1. The parent theme's 2017 "Site Options" menu is hidden. Nothing in this
 *      theme reads it, and having two look-alike menus was the first thing an
 *      editor clicked on by mistake.
 *   2. Developer-only layout fields (menu "Style", machine-panel "Column",
 *      "Mobile order", …) are hidden with CSS unless the URL carries
 *      `&advanced=1`. See gerotech_show_advanced_fields() for why CSS and not
 *      removal.
 *   3. Every Site Content screen shows a one-line note explaining the two
 *      rules an editor needs: blank keeps the current wording, and a save is
 *      live on every page immediately.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Site Content screen the current admin request is on, or ''.
 *
 * @return string Screen slug or ''.
 */
function gerotech_current_site_content_screen() {
	if ( ! is_admin() ) {
		return '';
	}
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( '' === $page ) {
		return '';
	}
	$screens = gerotech_site_content_screens();
	return isset( $screens[ $page ] ) ? $page : '';
}

/**
 * Hide the parent theme's legacy "Site Options" page.
 *
 * `acf_add_options_page()` with no arguments (parent theme functions.php)
 * registers the slug `acf-options`. Its field group `group_59305cb95c705`
 * still stores values (e.g. the old `footer_copyright`), but no template in
 * this theme reads them — the rebuilt chrome reads the Site Content screens.
 */
function gerotech_hide_legacy_site_options_menu() {
	/**
	 * Filter whether the legacy "Site Options" menu is hidden.
	 *
	 * @param bool $hide Default true.
	 */
	if ( apply_filters( 'gerotech_hide_legacy_site_options', true ) ) {
		remove_menu_page( 'acf-options' );
	}
}
add_action( 'admin_menu', 'gerotech_hide_legacy_site_options_menu', 99 );

/**
 * Keep the old single-page URL working.
 *
 * ACF's `redirect => true` renames the parent menu slug to the first child's,
 * so `admin.php?page=gerotech-site-content` (the journal's and Dev's
 * bookmarked URL) would otherwise show "not allowed". Send it to Header.
 *
 * Hooked to `admin_menu`, not `admin_init`: wp-admin/menu.php runs the
 * access check (and dies) at its end, before admin_init ever fires.
 */
function gerotech_redirect_legacy_site_content_url() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'gerotech-site-content' !== $page ) {
		return;
	}
	$screens = array_keys( gerotech_site_content_screens() );
	wp_safe_redirect( admin_url( 'admin.php?page=' . $screens[0] ) );
	exit;
}
add_action( 'admin_menu', 'gerotech_redirect_legacy_site_content_url', 100 );

/**
 * Body class that reveals the developer-only fields.
 *
 * @param string $classes Space-separated classes.
 * @return string
 */
function gerotech_admin_body_class( $classes ) {
	if ( '' !== gerotech_current_site_content_screen() ) {
		$classes .= ' gerotech-site-content';
		if ( gerotech_show_advanced_fields() ) {
			$classes .= ' gerotech-advanced-on';
		}
	}
	return $classes;
}
add_filter( 'admin_body_class', 'gerotech_admin_body_class' );

/**
 * Field keys (at any depth) whose wrapper carries `gerotech-advanced`.
 *
 * Walks the PHP-registered Site Content groups so the list can never drift
 * from the definitions in inc/acf-global-fields.php.
 *
 * @return string[]
 */
function gerotech_advanced_field_keys() {
	static $keys = null;
	if ( null !== $keys ) {
		return $keys;
	}
	$keys = array();
	if ( ! function_exists( 'acf_get_fields' ) ) {
		return $keys;
	}

	$walk = function ( $fields ) use ( &$walk, &$keys ) {
		foreach ( (array) $fields as $field ) {
			$class = isset( $field['wrapper']['class'] ) ? (string) $field['wrapper']['class'] : '';
			if ( false !== strpos( $class, 'gerotech-advanced' ) && ! empty( $field['key'] ) ) {
				$keys[] = $field['key'];
			}
			if ( ! empty( $field['sub_fields'] ) ) {
				$walk( $field['sub_fields'] );
			}
		}
	};

	foreach ( array_keys( gerotech_site_content_screens() ) as $screen ) {
		foreach ( acf_get_field_groups( array( 'options_page' => $screen ) ) as $group ) {
			$walk( acf_get_fields( $group ) );
		}
	}

	return $keys;
}

/**
 * Inline CSS for the Site Content screens.
 *
 * `.gerotech-advanced` is the wrapper class every developer-only field carries
 * in inc/acf-global-fields.php. The fields stay in the form (and therefore in
 * the POST) — they are only not shown.
 */
function gerotech_admin_site_content_css() {
	if ( '' === gerotech_current_site_content_screen() ) {
		return;
	}

	// Table-layout repeaters render a <th> per sub-field without the wrapper
	// class, so hide those column headings by field key as well.
	$th_rules = array();
	foreach ( gerotech_advanced_field_keys() as $key ) {
		$th_rules[] = 'body.gerotech-site-content:not(.gerotech-advanced-on) th.acf-th[data-key="' . esc_attr( $key ) . '"]';
	}

	$css = '
		body.gerotech-site-content:not(.gerotech-advanced-on) .acf-field.gerotech-advanced { display: none !important; }
		' . ( $th_rules ? implode( ",\n", $th_rules ) . ' { display: none !important; }' : '' ) . '
		body.gerotech-site-content.gerotech-advanced-on .acf-field.gerotech-advanced > .acf-label label::after {
			content: " (layout)"; color: #b32d2e; font-weight: 400;
		}
		body.gerotech-site-content .acf-field-message.gerotech-screen-note .acf-label { display: none; }
		body.gerotech-site-content .acf-field-message.gerotech-screen-note {
			background: #f6f7f7; border-left: 4px solid #f38a2c; padding: 12px 16px;
		}
	';
	wp_register_style( 'gerotech-admin-site-content', false, array(), GEROTECH_CHILD_VERSION );
	wp_enqueue_style( 'gerotech-admin-site-content' );
	wp_add_inline_style( 'gerotech-admin-site-content', $css );
}
add_action( 'admin_enqueue_scripts', 'gerotech_admin_site_content_css' );

/**
 * A single note at the top of every Site Content screen.
 */
function gerotech_admin_site_content_notice() {
	$slug = gerotech_current_site_content_screen();
	if ( '' === $slug ) {
		return;
	}
	$screens = gerotech_site_content_screens();
	$desc    = $screens[ $slug ]['description'];
	?>
	<div class="notice notice-info">
		<p>
			<strong><?php echo esc_html( $desc ); ?></strong><br />
			<?php esc_html_e( 'Leave a field blank to keep the wording the site uses today. Changes appear on every page as soon as you click Update.', 'gerotech-child' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'gerotech_admin_site_content_notice' );
