<?php
/**
 * URL helpers.
 *
 * The prototype used flat `.html` filenames. WordPress uses permalinks, and the
 * final URL strategy (reuse existing top-level slugs vs. new URLs) is still an
 * open decision — see handoff/implementation-plan-wordpress-theme-acf.md §11.
 *
 * All internal links therefore go through gerotech_page_url(), so the mapping can
 * be changed in one place (or overridden with the `gerotech_page_url` filter)
 * without touching templates.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve a prototype page slug to its WordPress URL.
 *
 * @param string $slug Prototype filename without extension, e.g. 'engineered-solutions'.
 * @return string Escaped absolute URL.
 */
function gerotech_page_url( $slug ) {
	$slug = trim( (string) $slug, '/' );

	$map = array(
		'home'                      => '/',
		'engineered-solutions'      => '/engineered-solutions/',
		// Prototype slug → existing dev URL (mapping A, 2026-09-16).
		'machine-custom-solutions'  => '/modification-of-standard-machine-tools/',
		'application'               => '/unique-applications-for-standard-machines/',
		'automation-integration'    => '/automated-system/',
		'training'                  => '/training/',
		'support'                   => '/support/',
		'service'                   => '/service/',
		'rotary-repair'             => '/rotary-repair/',
		'planned-maintenance'       => '/planned-maintenance/',
		'about'                     => '/about/',
		'careers'                   => '/careers/',
		'contact'                   => '/contact/',
	);

	/**
	 * Filter the prototype-slug → URL map.
	 *
	 * @param array $map Slug => path map.
	 */
	$map = apply_filters( 'gerotech_page_url_map', $map );

	$path = isset( $map[ $slug ] ) ? $map[ $slug ] : '/' . $slug . '/';

	/**
	 * Filter the resolved URL for a single slug.
	 *
	 * @param string $url  Resolved absolute URL.
	 * @param string $slug Prototype slug.
	 */
	return apply_filters( 'gerotech_page_url', home_url( $path ), $slug );
}

/**
 * Echo gerotech_page_url().
 *
 * @param string $slug Prototype slug.
 */
function gerotech_page_link( $slug ) {
	echo esc_url( gerotech_page_url( $slug ) );
}

/**
 * Stable fragment id for an Engineered Solutions service card.
 *
 * Mega-menu "All Services" sublinks use page URL + this hash so they scroll
 * to the matching `.mcs-card` (e.g. Auto Doors → …/#auto-doors). Derives from
 * the card title via WordPress sanitize_title(), so ACF title edits keep the
 * id in sync without a separate slug field.
 *
 * @param string $title Card title (may include entities or en-dashes).
 * @return string Kebab-case id, or empty string if the title is blank.
 */
function gerotech_card_id( $title ) {
	$id = sanitize_title( wp_strip_all_tags( html_entity_decode( (string) $title, ENT_QUOTES, 'UTF-8' ) ) );
	return is_string( $id ) ? $id : '';
}

/**
 * Shared sales quote mailto used by header/footer/CTAs.
 *
 * @param string $subject Optional subject line.
 * @return string
 */
function gerotech_quote_mailto( $subject = 'Gerotech Quote Request' ) {
	return 'mailto:sales@gerotech.com?subject=' . rawurlencode( $subject );
}

/**
 * Engineering-space footer mailto (homepage, ES, MCS, Applications, Automation).
 *
 * @param string $subject Optional subject line.
 * @return string
 */
function gerotech_engineering_mailto( $subject = 'Gerotech Quote Request' ) {
	return 'mailto:Engineeringproposals@gerotech.com?subject=' . rawurlencode( $subject );
}

/**
 * Page slugs whose templates render card modals + gallery collections.
 *
 * Shared by inc/enqueue.php (which loads modal.js / gallery-module.js) and
 * footer.php (which renders the #mcs-modal container), so the container and its
 * script can never drift apart — a missing container silently kills every popup.
 *
 * @return string[] Actual WP page slugs (dev slugs), not prototype filenames.
 */
function gerotech_modal_pages() {
	return (array) apply_filters(
		'gerotech_modal_pages',
		array( 'modification-of-standard-machine-tools', 'automated-system', 'unique-applications-for-standard-machines' )
	);
}

/**
 * The "Site Content" admin screens, in menu order.
 *
 * Shared by the options-page registration (inc/acf-fields.php), the field
 * groups that attach to each screen, the admin polish (inc/admin.php) and the
 * audit script, so adding a screen is a one-line change here.
 *
 * @return array slug => array( title, description )
 */
function gerotech_site_content_screens() {
	return array(
		'gerotech-site-header' => array(
			'title'       => 'Header',
			'description' => 'The black phone bar, the logo, the orange button and the search box at the top of every page.',
		),
		'gerotech-site-menus'  => array(
			'title'       => 'Menus',
			'description' => 'The main menu, the Machines panel and the Engineered Solutions panel. The phone menu is built from the same lists.',
		),
		'gerotech-site-footer' => array(
			'title'       => 'Footer',
			'description' => 'Everything in the dark band at the bottom of every page.',
		),
		'gerotech-site-shared' => array(
			'title'       => 'Shared Content',
			'description' => 'Testimonials and the mailing-list form, which appear on several pages.',
		),
	);
}

/**
 * ACF location rule for one Site Content screen.
 *
 * @param string $slug Screen slug from gerotech_site_content_screens().
 * @return array
 */
function gerotech_options_location( $slug ) {
	return array(
		array(
			array(
				'param'    => 'options_page',
				'operator' => '==',
				'value'    => $slug,
			),
		),
	);
}

/**
 * Whether the developer-only layout controls should be visible in the editor.
 *
 * The Site Content screens hide fields such as menu "Style", machine-panel
 * "Column" and "Mobile order" behind this switch: they exist so the design can
 * be reproduced exactly, not because an editor should ever change them. They
 * are hidden with CSS, NOT removed from the form — a removed repeater sub-field
 * is skipped on save and its stored value stays at the OLD row index, so a
 * reorder would silently scramble the layout. Hidden-but-posted travels with
 * its row.
 *
 * Append `&advanced=1` to any Site Content URL to show them.
 *
 * @return bool
 */
function gerotech_show_advanced_fields() {
	$show = ! empty( $_GET['advanced'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	/**
	 * Filter whether developer-only layout fields render in the editor.
	 *
	 * @param bool $show
	 */
	return (bool) apply_filters( 'gerotech_show_advanced_fields', $show );
}

/**
 * Read a theme asset versioned by file mtime (cache-busting).
 *
 * @param string $rel Relative path under the theme, e.g. 'assets/css/tokens.css'.
 * @return string|null
 */
function gerotech_asset_version( $rel ) {
	$abs = GEROTECH_CHILD_DIR . '/' . ltrim( $rel, '/' );
	return file_exists( $abs ) ? (string) filemtime( $abs ) : null;
}

/**
 * Normalise a hero slide's "Accent colour" value to a semantic choice.
 *
 * Accepts the current values (`white` | `haas` | `orange`) and the raw CSS class
 * names written by the earlier `accent_class` field, so a database that has not
 * been migrated keeps rendering as designed. Anything unrecognised → white.
 *
 * @param string $choice Stored value, e.g. 'haas' or 'accent--haas'.
 * @return string One of 'white', 'haas', 'orange'.
 */
function gerotech_accent_choice( $choice ) {
	$choice = trim( (string) $choice );

	$legacy = array(
		'accent'        => 'orange',
		'accent--haas'  => 'haas',
		'accent--deep'  => 'orange',
		'accent--white' => 'white',
	);
	if ( isset( $legacy[ $choice ] ) ) {
		return $legacy[ $choice ];
	}

	return in_array( $choice, array( 'white', 'haas', 'orange' ), true ) ? $choice : 'white';
}

/**
 * Map a hero slide's "Accent colour" choice to headline accent classes.
 *
 * @param string $choice Stored choice, e.g. 'haas'.
 * @return string Space-separated CSS classes for gerotech_accent().
 */
function gerotech_accent_class( $choice ) {
	$map = array(
		'white'  => 'accent accent--white',
		'haas'   => 'accent accent--haas',
		'orange' => 'accent',
	);

	return $map[ gerotech_accent_choice( $choice ) ];
}

/**
 * Map a hero slide's "Button colour" choice to CTA classes.
 *
 * Choices are stored as semantic values (`orange` | `haas` | `white`) and are
 * independent of the headline accent colour. The base `btn` class is added by
 * the template.
 *
 * @param string $choice Stored choice, e.g. 'haas'.
 * @return string Space-separated CSS classes, without the base `btn`.
 */
function gerotech_btn_class( $choice ) {
	$map = array(
		'orange' => 'btn--primary',
		'haas'   => 'btn--primary btn--haas',
		'white'  => 'btn--outline-white',
	);

	$choice = trim( (string) $choice );

	return isset( $map[ $choice ] ) ? $map[ $choice ] : $map['orange'];
}

/**
 * Render a client-authored headline/lede with accent + line-break support.
 *
 * Editors write plain text; wrap the accent phrase in <em>…</em> and use line
 * breaks for the design's forced breaks. <em>/<i> become an accent span.
 *
 * @param string $text         Raw field value.
 * @param string $accent_class Accent classes: 'accent' (brand orange), 'accent--haas' (Haas red),
 *                             'accent--white' (no colour), or 'accent--deep' (light surfaces).
 *                             Pass gerotech_accent_class() output for hero slides.
 * @param bool   $breaks       Convert newlines to <br> (headlines) vs. paragraphs (body).
 * @return string Safe HTML.
 */
function gerotech_accent( $text, $accent_class = 'accent', $breaks = true ) {
	$text = (string) $text;
	if ( '' === trim( $text ) ) {
		return '';
	}

	$allowed = array(
		'em'     => array(),
		'i'      => array(),
		'strong' => array(),
		'b'      => array(),
		'br'     => array(),
		'span'   => array( 'class' => true ),
		'a'      => array( 'href' => true, 'target' => true, 'rel' => true ),
	);
	$text = wp_kses( $text, $allowed );

	$span = '<span class="' . esc_attr( $accent_class ) . '">$2</span>';
	$text = preg_replace( '/<(em|i)>(.*?)<\/\1>/is', $span, $text );

	// Only auto-break plain textareas (skip strings already carrying <br>).
	if ( $breaks && false === stripos( $text, '<br' ) ) {
		$text = nl2br( $text, false );
	}
	if ( ! $breaks ) {
		$text = wpautop( $text );
	}

	return $text;
}

/**
 * Read an ACF field with a default fallback (null-safe if ACF is inactive).
 *
 * @param string $key     Field name.
 * @param mixed  $default Value returned when the field is empty/unset.
 * @param mixed  $post_id Optional post ID (defaults to the current post).
 * @return mixed
 */
function gerotech_field( $key, $default = null, $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	// Optional capture hook (used by the one-time content seeder): records the
	// code default for every read, regardless of any stored value.
	if ( isset( $GLOBALS['gerotech_capture_defaults'] ) && is_array( $GLOBALS['gerotech_capture_defaults'] ) ) {
		$GLOBALS['gerotech_capture_defaults'][ $key ] = $default;
	}
	$v = get_field( $key, $post_id );
	if ( null === $v || '' === $v || false === $v || ( is_array( $v ) && empty( $v ) ) ) {
		return $default;
	}
	return $v;
}

/**
 * Resolve an ACF image field to an attachment ID (supports ID/array/URL returns).
 *
 * @param mixed  $value Field value.
 * @param string $fallback_rel Theme-relative fallback path (e.g. 'assets/images/x.jpg').
 * @return string Image URL
 */
function gerotech_image_url( $value, $fallback_rel = '' ) {
	if ( is_array( $value ) && ! empty( $value['url'] ) ) {
		return $value['url'];
	}
	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, 'full' );
		if ( $url ) {
			return $url;
		}
	}
	if ( is_string( $value ) && '' !== $value ) {
		if ( 0 === strpos( $value, 'http' ) || 0 === strpos( $value, '/' ) ) {
			return $value;
		}
		return GEROTECH_CHILD_URI . '/' . ltrim( $value, '/' );
	}
	return $fallback_rel ? GEROTECH_CHILD_URI . '/' . ltrim( $fallback_rel, '/' ) : '';
}

/**
 * Alt text for an image field, taken from the Media Library entry.
 *
 * Editors type alt text where they upload — in the Media Library — so there is
 * no alt field to keep in sync beside every image control. Returns that alt when
 * the field holds an uploaded attachment, and the passed design string when it
 * holds a theme-bundled path (which has no attachment and therefore no alt), so
 * swapping in a photo and filling in its alt is all that is ever needed.
 *
 * Pair with gerotech_image_url() and pass the same value.
 *
 * @param mixed  $value    ACF image value (ID/array/URL) or theme-relative path.
 * @param string $fallback Design alt text, used when the image carries none.
 * @return string Alt text — escape with esc_attr() at output.
 */
function gerotech_image_alt( $value, $fallback = '' ) {
	$id = 0;
	if ( is_array( $value ) && ! empty( $value['ID'] ) ) {
		$id = (int) $value['ID'];
	} elseif ( is_numeric( $value ) ) {
		$id = (int) $value;
	}

	if ( $id ) {
		// An alt typed in the Media Library wins; the attachment's own caption,
		// title and filename are deliberately NOT used — a filename is not a
		// description, and a wrong alt is worse than the design string.
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		if ( is_string( $alt ) && '' !== trim( $alt ) ) {
			return trim( $alt );
		}
	}

	return $fallback;
}

/**
 * Build a `srcset` for an ACF image field or a theme-relative asset.
 *
 * ACF values resolve through WordPress, so its registered sizes are offered. A
 * theme asset falls back to a hand-built responsive pair when the `@2x` sibling
 * exists (e.g. `assets/images/hero-slide-1.jpg` + `hero-slide-1@2x.jpg`).
 *
 * Pair with `sizes="100vw"` for full-bleed imagery.
 *
 * @param mixed  $value        ACF image value (ID/array/URL) or theme-relative path.
 * @param string $fallback_rel Theme-relative fallback path, as passed to gerotech_image_url().
 * @return string Raw srcset value — escape with esc_attr() at output. Empty when unavailable.
 */
function gerotech_image_srcset( $value, $fallback_rel = '' ) {
	$id = 0;
	if ( is_array( $value ) && ! empty( $value['ID'] ) ) {
		$id = (int) $value['ID'];
	} elseif ( is_numeric( $value ) ) {
		$id = (int) $value;
	}
	if ( $id ) {
		$set = wp_get_attachment_image_srcset( $id, 'full' );
		if ( $set ) {
			return $set;
		}
	}

	// Only theme-relative paths have a predictable `@2x` sibling.
	$rel = '';
	if ( is_string( $value ) && '' !== $value && 0 !== strpos( $value, 'http' ) && 0 !== strpos( $value, '/' ) ) {
		$rel = $value;
	} elseif ( '' !== $fallback_rel ) {
		$rel = $fallback_rel;
	}
	if ( '' === $rel ) {
		return '';
	}

	$x2 = preg_replace( '/\.(jpe?g|png)$/i', '@2x.$1', $rel );
	if ( ! $x2 || $x2 === $rel ) {
		return '';
	}

	$candidates = array();
	foreach ( array( $rel, $x2 ) as $candidate ) {
		$abs = GEROTECH_CHILD_DIR . '/' . ltrim( $candidate, '/' );
		if ( ! file_exists( $abs ) ) {
			return '';
		}
		$info = @getimagesize( $abs ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! $info ) {
			return '';
		}
		$candidates[] = GEROTECH_CHILD_URI . '/' . ltrim( $candidate, '/' ) . ' ' . (int) $info[0] . 'w';
	}

	return implode( ', ', $candidates );
}

/**
 * Parse a "Label | URL" per-line textarea into tag chips.
 *
 * @param string $text Raw field value.
 * @return array[] Each: array( 'label' => string, 'url' => string ).
 */
function gerotech_parse_tags( $text ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$out[] = array(
			'label' => $parts[0],
			'url'   => isset( $parts[1] ) ? $parts[1] : '',
		);
	}
	return $out;
}

/**
 * Parse a gallery-collection media list (one item per line) into items.
 *
 * Line format: type | src | poster | alt | caption
 *   - type:    'image' or 'video'
 *   - src:     image URL / video URL
 *   - poster:  poster image URL (video only; leave empty for images)
 *   - alt:     alt text / aria-label
 *   - caption: viewer caption
 *
 * @param string $text Raw field value.
 * @return array[] Each: type/src/poster/alt/caption.
 */
function gerotech_parse_media( $text ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$p = array_map( 'trim', explode( '|', $line, 5 ) );
		$out[] = array(
			'type'    => isset( $p[0] ) ? $p[0] : 'image',
			'src'     => isset( $p[1] ) ? $p[1] : '',
			'poster'  => isset( $p[2] ) ? $p[2] : '',
			'alt'     => isset( $p[3] ) ? $p[3] : '',
			'caption' => isset( $p[4] ) ? $p[4] : '',
		);
	}
	return $out;
}

/**
 * Split checklist groups into the legacy two-column markup.
 *
 * Used by the Service page's Planned Maintenance checklist, which ships as an ACF
 * repeater but renders into the legacy `.t_left` / `.t_right` float columns.
 *
 * @param array  $groups Repeater rows.
 * @param string $col    'left' or 'right'.
 * @return array Rows belonging to that column.
 */
function gerotech_service_column( $groups, $col ) {
	$out = array();
	foreach ( (array) $groups as $group ) {
		$group_col = isset( $group['column'] ) ? $group['column'] : 'left';
		if ( $col === $group_col ) {
			$out[] = $group;
		}
	}
	return $out;
}

/**
 * Render one checklist column: a bold heading followed by its lines.
 *
 * @param array $groups Rows for this column.
 */
function gerotech_service_render_groups( $groups ) {
	foreach ( (array) $groups as $group ) {
		$heading = isset( $group['heading'] ) ? $group['heading'] : '';
		$items   = isset( $group['items'] ) ? $group['items'] : '';
		echo '<p>';
		if ( '' !== trim( (string) $heading ) ) {
			echo '<b>' . esc_html( $heading ) . '</b><br />';
		}
		echo nl2br( esc_html( $items ) ) . "</p>\n";
	}
}
