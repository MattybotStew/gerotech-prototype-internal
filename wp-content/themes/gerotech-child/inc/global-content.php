<?php
/**
 * Global header, navigation and footer content.
 *
 * Everything the client is allowed to edit site-wide is read here and merged
 * over the markup that used to be hardcoded in header.php / footer.php.
 *
 * THE RULE THIS FILE ENFORCES
 * ----------------------------
 * A blank field must render exactly what the site rendered before this file
 * existed. Every default therefore lives HERE, in PHP — never in an ACF
 * `default_value`. ACF injects `default_value` on read, so a plain save would
 * persist it and the editor would freeze the design copy without ever
 * touching it.
 *
 * Repeaters are replaced wholesale when they hold rows (an editor who deletes
 * a link means it), and fall back to the default list when the repeater is
 * empty. Scalars fall back per-field, so a half-filled form never blanks a page.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Small helpers ──────────────────────────────────────────────────────── */

/**
 * Normalise an ACF `link` field (or a plain string) into a render-ready row.
 *
 * ACF link fields resolve pages from a stored post ID, so a page renamed or
 * moved keeps working. A deleted page still yields its last known URL rather
 * than an empty href — the editor sees "page not found" in the field itself.
 *
 * @param mixed  $value    ACF link array, URL string, or null.
 * @param string $fallback_label Label used when the field stores no title.
 * @param bool   $new_tab  Default for "open in a new tab".
 * @return array|null array( label, url, new_tab ), or null when there is no URL.
 */
function gerotech_link_row( $value, $fallback_label = '', $new_tab = false ) {
	$url   = '';
	$label = $fallback_label;

	if ( is_array( $value ) ) {
		if ( isset( $value['type'], $value['id'] ) && 'page' === $value['type'] && $value['id'] ) {
			$permalink = get_permalink( (int) $value['id'] );
			if ( $permalink ) {
				$url = $permalink;
			} elseif ( isset( $value['url'] ) ) {
				$url = $value['url'];
			}
		} elseif ( isset( $value['url'] ) ) {
			$url = $value['url'];
		}
		if ( '' === trim( (string) $label ) && isset( $value['title'] ) ) {
			$label = $value['title'];
		}
		if ( isset( $value['target'] ) && '_blank' === $value['target'] ) {
			$new_tab = true;
		}
	} elseif ( is_string( $value ) ) {
		$url = $value;
	}

	$url = trim( (string) $url );
	if ( '' === $url ) {
		return null;
	}

	// A bare relative path (e.g. "/about/") is resolved against the site root.
	if ( '/' === substr( $url, 0, 1 ) && '#' !== substr( $url, 0, 1 ) ) {
		$url = home_url( $url );
	} elseif ( '#' === $url ) {
		$url = '#';
	}

	// Anything that leaves the site opens in a new tab. This is the one rule
	// an editor would otherwise have to remember per link, so it is automatic;
	// the Link field's own "open in a new tab" still forces it for internal URLs.
	if ( ! $new_tab && gerotech_is_external_url( $url ) ) {
		$new_tab = true;
	}

	return array(
		'label'   => (string) $label,
		'url'     => $url,
		'new_tab' => (bool) $new_tab,
	);
}

/**
 * Whether a URL points at another website.
 *
 * `mailto:`, `tel:`, anchors and relative paths are not external. The public
 * domain is treated as this site on every environment, so a Dev/Local build
 * does not open its own catalogue links in a new tab differently from live.
 *
 * @param string $url Absolute or relative URL.
 * @return bool
 */
function gerotech_is_external_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url || '#' === $url[0] || '/' === $url[0] || preg_match( '#^(mailto|tel|sms):#i', $url ) ) {
		return false;
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! $host ) {
		return false;
	}
	$host = strtolower( preg_replace( '/^www\./', '', $host ) );

	$ours = array(
		strtolower( preg_replace( '/^www\./', '', (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) ) ),
		'gerotech.com',
	);

	/**
	 * Filter the hostnames treated as "this site".
	 *
	 * @param string[] $ours Lower-case hostnames without a leading www.
	 */
	$ours = (array) apply_filters( 'gerotech_internal_hosts', $ours );

	return ! in_array( $host, $ours, true );
}

/**
 * Build a tel: URL from a display phone number.
 *
 * Editors type "734-379-7788"; this normalises to +17343797788 so the footer
 * and alert banner can never ship a broken dial link.
 *
 * @param string $number Display phone number.
 * @return string tel: URL, or '' when no digits are present.
 */
function gerotech_tel_link( $number ) {
	$digits = preg_replace( '/\D+/', '', (string) $number );
	if ( '' === $digits ) {
		return '';
	}
	if ( 10 === strlen( $digits ) ) {
		$digits = '1' . $digits;
	}
	return 'tel:+' . $digits;
}

/**
 * target/rel attributes for a link row.
 *
 * @param array $row Link row from gerotech_link_row().
 * @return string Attribute string starting with a space, or ''.
 */
function gerotech_link_attrs( $row ) {
	if ( empty( $row['new_tab'] ) ) {
		return '';
	}
	return ' target="_blank" rel="noopener noreferrer"';
}

/**
 * Render a two-tone title ("Machine" + "Custom Solutions").
 *
 * Both halves inherit the surrounding colour/weight — the split exists so the
 * design can break the line without the title losing its alignment. A single
 * lead part is rendered as one string.
 *
 * @param string $lead First part.
 * @param string $main Optional second part.
 * @return string Safe HTML.
 */
function gerotech_split_title( $lead, $main = '' ) {
	$lead = trim( (string) $lead );
	$main = trim( (string) $main );

	if ( '' === $lead ) {
		return esc_html( $main );
	}

	$out = '<span class="mcs-name-split__lead">' . esc_html( $lead ) . '</span>';

	if ( '' !== $main ) {
		$out .= ' <span class="mcs-name-split__main">' . esc_html( $main ) . '</span>';
	}

	return $out;
}

/**
 * Read a global (options-page) field.
 *
 * Thin wrapper over gerotech_field() so this file is safe on a site where ACF
 * is inactive — every call site would otherwise fatal on an undefined function.
 *
 * @param string $key     Field name.
 * @param mixed  $default Value when the field is empty/unset.
 * @return mixed
 */
function gerotech_option( $key, $default = '' ) {
	return gerotech_field( $key, $default, 'option' );
}

/**
 * Keep a scalar ACF value when the editor filled it in, else use the default.
 *
 * @param mixed  $value   ACF value.
 * @param string $default Code default.
 * @return string
 */
function gerotech_pick( $value, $default ) {
	if ( null === $value || '' === $value || false === $value ) {
		return $default;
	}
	if ( is_array( $value ) && empty( $value ) ) {
		return $default;
	}
	return (string) $value;
}

/* ── Header defaults ────────────────────────────────────────────────────── */

/**
 * The alert banner, header CTA and search modal as they ship today.
 *
 * @return array
 */
function gerotech_header_defaults() {
	return array(
		'banner_items'    => array(
			array(
				'label' => 'Headquarters & Sales:',
				'value' => '734-379-7788',
				'url'   => '',
			),
			array(
				'label' => 'Service:',
				'value' => '248-476-8787',
				'url'   => '',
			),
			array(
				'label' => 'Grand Rapids:',
				'value' => '616-735-1100',
				'url'   => '',
			),
		),
		'logo'            => '',
		'logo_alt'        => 'Gerotech — Machines, Solutions, Support',
		'logo_url'        => '',
		'logo_width'      => 188,
		'logo_height'     => 30,
		'cta_label'       => 'Talk to an Engineer',
		'cta_url'         => '',
		'search_title'    => 'Search Gerotech',
		'search_hint'     => 'Prototype site search — browse by section:',
		'search_placeholder' => 'Search pages and topics…',
		'search_links'    => array(
			array( 'label' => 'Haas Machines ↗', 'url' => 'https://gerotech.com/machines', 'new_tab' => true ),
			array( 'label' => 'Engineered Solutions', 'url' => gerotech_page_url( 'engineered-solutions' ) ),
			array( 'label' => 'Machine Custom Solutions', 'url' => gerotech_page_url( 'machine-custom-solutions' ) ),
			array( 'label' => 'Automation & Controls', 'url' => gerotech_page_url( 'automation-integration' ) ),
			array( 'label' => 'Training', 'url' => gerotech_page_url( 'training' ) ),
			array( 'label' => 'Service & Support', 'url' => gerotech_page_url( 'support' ) ),
			array( 'label' => 'About Gerotech', 'url' => gerotech_page_url( 'about' ) ),
		),
	);
}

/**
 * Header content: ACF over the defaults above.
 *
 * @return array
 */
function gerotech_header_data() {
	$d = gerotech_header_defaults();

	$banner = array();
	$rows   = gerotech_option( 'header_banner_items' );
	if ( is_array( $rows ) && ! empty( $rows ) ) {
		foreach ( $rows as $row ) {
			$value = isset( $row['value'] ) ? trim( (string) $row['value'] ) : '';
			$link  = gerotech_link_row( isset( $row['url'] ) ? $row['url'] : '', $value, false );
			if ( '' === $value && ! $link ) {
				continue;
			}
			$banner[] = array(
				'label' => isset( $row['label'] ) ? (string) $row['label'] : '',
				'value' => $value,
				'url'   => $link ? $link['url'] : gerotech_tel_link( $value ),
			);
		}
	} else {
		foreach ( $d['banner_items'] as $row ) {
			$banner[] = array(
				'label' => $row['label'],
				'value' => $row['value'],
				'url'   => gerotech_tel_link( $row['value'] ),
			);
		}
	}

	$search_links = array();
	$rows         = gerotech_option( 'header_search_links' );
	if ( is_array( $rows ) && ! empty( $rows ) ) {
		foreach ( $rows as $row ) {
			$link = gerotech_link_row( isset( $row['url'] ) ? $row['url'] : '', isset( $row['label'] ) ? $row['label'] : '' );
			if ( $link && '' !== trim( $link['label'] ) ) {
				$search_links[] = $link;
			}
		}
	} else {
		foreach ( $d['search_links'] as $row ) {
			$search_links[] = array(
				'label'   => $row['label'],
				'url'     => $row['url'],
				'new_tab' => ! empty( $row['new_tab'] ),
			);
		}
	}

	$logo     = gerotech_field( 'header_logo', '', 'option' );
	$logo_url = gerotech_link_row( gerotech_option( 'header_logo_url' ) );
	$cta_url  = gerotech_link_row( gerotech_option( 'header_cta_url' ) );

	$data = array(
		'banner_items'      => $banner,
		'logo'              => $logo,
		'logo_alt'          => gerotech_pick( gerotech_option( 'header_logo_alt' ), $d['logo_alt'] ),
		'logo_url'          => $logo_url ? $logo_url['url'] : home_url( '/' ),
		'logo_width'        => $d['logo_width'],
		'logo_height'       => $d['logo_height'],
		'cta_label'         => gerotech_pick( gerotech_option( 'header_cta_label' ), $d['cta_label'] ),
		'cta_url'           => $cta_url ? $cta_url['url'] : gerotech_page_url( 'contact' ),
		'cta_new_tab'       => $cta_url ? $cta_url['new_tab'] : false,
		'search_title'      => gerotech_pick( gerotech_option( 'header_search_title' ), $d['search_title'] ),
		'search_hint'       => gerotech_pick( gerotech_option( 'header_search_hint' ), $d['search_hint'] ),
		'search_placeholder' => gerotech_pick( gerotech_option( 'header_search_placeholder' ), $d['search_placeholder'] ),
		'search_links'      => $search_links,
	);

	/**
	 * Filter the resolved header content.
	 *
	 * @param array $data Header content.
	 */
	return apply_filters( 'gerotech_header_data', $data );
}

/* ── Main navigation defaults ────────────────────────────────────────────── */

/**
 * The top-level menu as it ships today.
 *
 * @return array[]
 */
function gerotech_nav_defaults() {
	return array(
		array(
			'label'      => 'Machines',
			'url'        => 'https://gerotech.com/machines',
			'new_tab'    => true,
			'style'      => 'machines-mega',
			'show_mobile' => true,
			'links'      => array(),
		),
		array(
			'label'      => 'Engineered Solutions',
			'url'        => gerotech_page_url( 'engineered-solutions' ),
			'new_tab'    => false,
			'style'      => 'es-mega',
			'show_mobile' => true,
			'links'      => array(),
		),
		array(
			'label'      => 'Training',
			'url'        => gerotech_page_url( 'training' ),
			'new_tab'    => false,
			'style'      => 'plain',
			'show_mobile' => true,
			'links'      => array(),
		),
		array(
			'label'      => 'Support',
			'url'        => gerotech_page_url( 'support' ),
			'new_tab'    => false,
			'style'      => 'dropdown',
			'show_mobile' => true,
			'links'      => array(
				array( 'label' => 'Service Request Forms', 'url' => gerotech_page_url( 'service' ) ),
				array( 'label' => 'Rotary Repair', 'url' => gerotech_page_url( 'rotary-repair' ) ),
				array( 'label' => 'Planned Maintenance', 'url' => gerotech_page_url( 'planned-maintenance' ) ),
			),
		),
		array(
			'label'      => 'About',
			'url'        => gerotech_page_url( 'about' ),
			'new_tab'    => false,
			'style'      => 'plain',
			'show_mobile' => true,
			'links'      => array(),
		),
	);
}

/**
 * Top-level navigation items: ACF over gerotech_nav_defaults().
 *
 * @return array[]
 */
function gerotech_nav_items() {
	$defaults = gerotech_nav_defaults();
	$rows     = gerotech_option( 'nav_items' );

	if ( ! is_array( $rows ) || empty( $rows ) ) {
		$items = $defaults;
	} else {
		$items = array();

		foreach ( $rows as $row ) {
			$label = isset( $row['label'] ) ? trim( (string) $row['label'] ) : '';
			$link  = gerotech_link_row( isset( $row['url'] ) ? $row['url'] : '', $label, ! empty( $row['new_tab'] ) );

			if ( '' === $label && ! $link ) {
				continue;
			}

			$links = array();
			if ( isset( $row['links'] ) && is_array( $row['links'] ) ) {
				foreach ( $row['links'] as $sub ) {
					$sub_link = gerotech_link_row(
						isset( $sub['url'] ) ? $sub['url'] : '',
						isset( $sub['label'] ) ? $sub['label'] : '',
						! empty( $sub['new_tab'] )
					);
					if ( $sub_link && '' !== trim( $sub_link['label'] ) ) {
						$links[] = $sub_link;
					}
				}
			}

			// Style is a developer field and is blank ("Automatic") for anything
			// an editor adds: sub-links make it a drop-down, otherwise it is a
			// plain link. The two mega styles must be chosen explicitly.
			$style = isset( $row['style'] ) && in_array( $row['style'], array( 'plain', 'dropdown', 'machines-mega', 'es-mega' ), true )
				? $row['style']
				: ( empty( $links ) ? 'plain' : 'dropdown' );
			if ( 'dropdown' === $style && empty( $links ) ) {
				$style = 'plain';
			}

			$items[] = array(
				'label'       => '' !== $label ? $label : ( $link ? $link['label'] : '' ),
				'url'         => $link ? $link['url'] : '#',
				'new_tab'     => $link ? $link['new_tab'] : false,
				'style'       => $style,
				'show_mobile' => ! isset( $row['show_mobile'] ) || false !== $row['show_mobile'],
				'links'       => $links,
			);
		}
	}

	/**
	 * Filter the resolved top-level navigation items.
	 *
	 * @param array[] $items Normalised items.
	 */
	return apply_filters( 'gerotech_nav_items', $items );
}

/* ── Machines mega panel defaults ───────────────────────────────────────── */

/**
 * The Haas catalog panel as it ships today.
 *
 * Column numbers are explicit: the panel is a four-column grid and the design
 * places groups unevenly across the columns.
 *
 * @return array
 */
function gerotech_machines_defaults() {
	$catalog = 'https://gerotech.com/machines';

	$groups = array(
		// Column 1
		array(
			'column' => 1,
			'mobile_order' => 1,
			'title'  => 'Vertical Mills',
			'links'  => array(
				'VF Series' => 'https://www.haascnc.com/machines/vertical-mills/vf-series.html',
				'Universal Machines' => 'https://www.haascnc.com/machines/vertical-mills/universal-machine.html',
				'VR Series' => 'https://www.haascnc.com/machines/vertical-mills/vr-series.html',
				'VP-5 Prismatic' => 'https://www.haascnc.com/machines/vertical-mills/vp-5.html',
				'Pallet-Changing VMCs' => 'https://www.haascnc.com/machines/vertical-mills/vc-series.html',
				'Mini Mills' => 'https://www.haascnc.com/machines/vertical-mills/mini-mills.html',
				'Mold Machines' => 'https://www.haascnc.com/machines/vertical-mills/mold-machines.html',
				'High-Speed Drill Centers' => 'https://www.haascnc.com/machines/vertical-mills/high-speed-drill-centers.html',
				'Drill/Tap/Mill Series' => 'https://www.haascnc.com/machines/vertical-mills/drill-tap-mill.html',
				'Toolroom Mills' => 'https://www.haascnc.com/machines/vertical-mills/toolroom-mills.html',
				'Pocket Mill' => 'https://www.haascnc.com/machines/vertical-mills/pocket-mill.html',
				'Compact Mills' => 'https://www.haascnc.com/machines/vertical-mills/compact-mills.html',
				'Gantry Series' => 'https://www.haascnc.com/machines/vertical-mills/gantry.html',
				'SR Sheet Routers' => 'https://www.haascnc.com/machines/vertical-mills/sheet-routers.html',
				'Extra-Large VMC' => 'https://www.haascnc.com/machines/vertical-mills/extra-large-vmc.html',
				'Double-Column Mills' => 'https://www.haascnc.com/machines/vertical-mills/double-column.html',
			),
		),
		// Column 2
		array(
			'column' => 2,
			'mobile_order' => 2,
			'title'  => 'Lathes',
			'links'  => array(
				'ST Series' => 'https://www.haascnc.com/machines/lathes/st.html',
				'Dual-Spindle' => 'https://www.haascnc.com/machines/lathes/dual-spindle.html',
				'Box Way Series' => 'https://www.haascnc.com/machines/lathes/box-way-series.html',
				'Toolroom Lathes' => 'https://www.haascnc.com/machines/lathes/toolroom-lathe.html',
				'Chucker Lathe' => 'https://www.haascnc.com/machines/lathes/chucker-lathe.html',
				'Haas Bar Feeders' => 'https://www.haascnc.com/machines/lathes/bar-feeders.html',
			),
		),
		array(
			'column' => 2,
			'mobile_order' => 4,
			'title'  => 'Rotaries & Indexers',
			'links'  => array(
				'Rotary Tables' => 'https://www.haascnc.com/machines/rotaries-indexers/rotary-tables.html',
				'Indexers' => 'https://www.haascnc.com/machines/rotaries-indexers/indexers.html',
				'5-Axis Rotaries' => 'https://www.haascnc.com/machines/rotaries-indexers/5-axis-rotaries.html',
				'Extra-Large Rotaries' => 'https://www.haascnc.com/machines/rotaries-indexers/extra-large-rotaries.html',
			),
		),
		// Column 3
		array(
			'column' => 3,
			'mobile_order' => 3,
			'title'  => 'Horizontal Mills',
			'links'  => array(
				'50-Taper' => 'https://www.haascnc.com/machines/horizontal-mills/ec-series.html',
				'40-Taper' => 'https://www.haascnc.com/machines/horizontal-mills/40-taper.html',
			),
		),
		array(
			'column' => 3,
			'mobile_order' => 5,
			'title'  => 'Automation Systems',
			'links'  => array(
				'Mill Automation' => 'https://www.haascnc.com/machines/automation-systems/mill_automation.html',
				'Lathe Automation' => 'https://www.haascnc.com/machines/automation-systems/lathe_automation.html',
				'Automatic Parts Loaders' => 'https://www.haascnc.com/machines/automation-systems/haas_apls.html',
				'Automation Models' => 'https://www.haascnc.com/machines/automation-systems/automation-models.html',
			),
		),
		array(
			'column' => 3,
			'mobile_order' => 6,
			'title'  => 'Desktop Machines',
			'links'  => array(
				'Desktop Mill' => 'https://www.haascnc.com/machines/desktop-machines/desktop-mill.html',
				'Desktop Lathe' => 'https://www.haascnc.com/machines/desktop-machines/desktop-lathe.html',
				'Control Simulator, Standard' => 'https://www.haascnc.com/machines/desktop-machines/simulator-std.html',
				'Control Simulator, Premium' => 'https://www.haascnc.com/machines/desktop-machines/simulator-premium.html',
			),
		),
		// Column 4
		array(
			'column' => 4,
			'mobile_order' => 7,
			'title'  => 'Shop Equipment',
			'links'  => array(
				'Knee Mill' => 'https://www.haascnc.com/machines/shop-equipment/knee-mills.html',
				'Haas Manual Lathes' => 'https://www.haascnc.com/machines/shop-equipment/manual-lathes.html',
				'Haas Saws' => 'https://www.haascnc.com/machines/shop-equipment/saws.html',
			),
		),
		array(
			'column' => 4,
			'mobile_order' => 8,
			'title'  => 'Fabrication Machines',
			'links'  => array(
				'Laser Cutting Machines' => 'https://www.haascnc.com/machines/fab-machines/laser-cutting-machines.html',
				'CNC Press Brakes' => 'https://www.haascnc.com/machines/fab-machines/press-brakes.html',
			),
		),
	);

$groups = array_map(
		function ( $group ) {
			$links = array();
			foreach ( $group['links'] as $label => $url ) {
				// Model URLs come from the client's live catalogue at
				// gerotech.com/machines/; they open in a new tab because
				// they leave the site.
				$links[] = array(
					'label'   => $label,
					'url'     => $url,
					'new_tab' => true,
				);
			}
			return array(
				'column'       => (int) $group['column'],
				'title'        => $group['title'],
				'mobile_order' => isset( $group['mobile_order'] ) ? (int) $group['mobile_order'] : 0,
				'links'        => $links,
			);
		},
		$groups
	);

	return array(
		'columns'    => 4,
		'groups'     => $groups,
		'help_title' => 'Not sure which machine fits your job?',
		'help_label' => 'Talk to an Engineer',
		'help_url'   => gerotech_page_url( 'engineered-solutions' ),
		'footer_label'        => 'Browse the full Haas catalog',
		'footer_mobile_label' => 'Full Haas Catalog ↗',
		'footer_url'          => $catalog,
		'footer_new_tab'      => true,
	);
}

/**
 * Machines mega panel: ACF over the defaults, grouped by column.
 *
 * @return array array( groups_by_column, columns, help, footer )
 */
function gerotech_machines_panel() {
	$defaults = gerotech_machines_defaults();

	$rows   = gerotech_option( 'nav_machines_groups' );
	$groups = array();

	if ( is_array( $rows ) && ! empty( $rows ) ) {
		foreach ( $rows as $row ) {
			$title = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
			if ( '' === $title ) {
				continue;
			}

			$links = array();
			if ( isset( $row['links'] ) && is_array( $row['links'] ) ) {
				foreach ( $row['links'] as $sub ) {
					$sub_label = isset( $sub['label'] ) ? trim( (string) $sub['label'] ) : '';
					$sub_link  = gerotech_link_row(
						isset( $sub['url'] ) ? $sub['url'] : '',
						$sub_label,
						! empty( $sub['new_tab'] )
					);
					if ( ! $sub_link || '' === $sub_label ) {
						continue;
					}
					$links[] = $sub_link;
				}
			}

			$column = isset( $row['column'] ) && in_array( (int) $row['column'], array( 1, 2, 3, 4 ), true ) ? (int) $row['column'] : 1;

			$groups[] = array(
				'column'       => $column,
				'title'        => $title,
				'mobile_order' => isset( $row['mobile_order'] ) ? (int) $row['mobile_order'] : 0,
				'links'        => $links,
			);
		}
	} else {
		$groups = $defaults['groups'];
	}

	// Bucket into columns, keeping row order inside each column.
	$by_column = array( 1 => array(), 2 => array(), 3 => array(), 4 => array() );
	foreach ( $groups as $group ) {
		$by_column[ $group['column'] ][] = $group;
	}

	// Drop trailing empty columns so a short list does not stretch the panel.
	$used = 4;
	foreach ( array( 4, 3, 2, 1 ) as $col ) {
		if ( ! empty( $by_column[ $col ] ) ) {
			$used = $col;
			break;
		}
	}

	$help_title = gerotech_pick( gerotech_option( 'nav_machines_help_title' ), $defaults['help_title'] );
	$help_label = gerotech_pick( gerotech_option( 'nav_machines_help_label' ), $defaults['help_label'] );
	$help_url   = gerotech_link_row( gerotech_option( 'nav_machines_help_url' ) );
	$footer_row = gerotech_link_row( gerotech_option( 'nav_machines_footer_url' ) );

	$panel = array(
		'columns' => $used,
		'groups'  => $by_column,
		'help'    => array(
			'title' => $help_title,
			'label' => $help_label,
			'url'   => $help_url ? $help_url['url'] : $defaults['help_url'],
		),
		'footer'  => array(
			'label'        => gerotech_pick( gerotech_option( 'nav_machines_footer_label' ), $defaults['footer_label'] ),
			'mobile_label' => gerotech_pick( gerotech_option( 'nav_machines_footer_mobile_label' ), $defaults['footer_mobile_label'] ),
			'url'          => $footer_row ? $footer_row['url'] : $defaults['footer_url'],
			// The URL target wins, but the checkbox can force a new tab on an
			// internal link the editor left on "same window".
			'new_tab'      => $footer_row
				? ( $footer_row['new_tab'] || (bool) gerotech_option( 'nav_machines_footer_new_tab', false ) )
				: $defaults['footer_new_tab'],
		),
	);

	/**
	 * Filter the resolved Machines mega panel.
	 *
	 * @param array $panel Panel data.
	 */
	return apply_filters( 'gerotech_machines_panel', $panel );
}

/* ── Engineered Solutions mega panel defaults ───────────────────────────── */

/**
 * The Engineered Solutions panel as it ships today.
 *
 * @return array
 */
function gerotech_es_defaults() {
	$mcs = gerotech_page_url( 'machine-custom-solutions' );
	$app = gerotech_page_url( 'application' );
	$acs = gerotech_page_url( 'automation-integration' );

	return array(
		'col1_title' => 'By Category',
		'categories' => array(
			array(
				'lead'        => 'Machine',
				'main'        => 'Custom Solutions',
				'url'         => $mcs,
				'description' => 'Column risers, auto doors, hydraulics, sheet metal, custom workholding, and specialty builds',
				'last'        => false,
			),
			array(
				'lead'        => '',
				'main'        => 'Applications',
				'url'         => $app,
				'description' => 'Part programming, process troubleshooting, optimization, tooling, demos, and training',
				'last'        => false,
			),
			array(
				'lead'        => 'Automation',
				'main'        => 'Controls Solutions',
				'url'         => $acs,
				'description' => 'Electrical controls, HMI design, automation cells, EOAT, and pre-engineered packages',
				'last'        => true,
			),
		),
		'cta_label'        => 'Talk to a Sales Engineer',
		'cta_mobile_label' => 'Talk to an Engineer',
		'cta_url'          => gerotech_quote_mailto( 'Gerotech Quote Request' ),
		'col2_title' => 'All Services',
		'services'   => array(
			array(
				'lead'  => 'Machine',
				'main'  => 'Custom Solutions',
				'links' => array(
					// The phone menu drops the "Machine" prefix to keep the list scannable.
					// Hashes match gerotech_card_id( title ) on each page's .mcs-card.
					array( 'label' => 'Machine Column Risers', 'url' => $mcs . '#machine-column-risers', 'mobile_label' => 'Column Risers' ),
					array( 'label' => 'Auto Doors', 'url' => $mcs . '#auto-doors' ),
					array( 'label' => 'Hydraulic – Pneumatics', 'url' => $mcs . '#hydraulic-pneumatics' ),
					array( 'label' => 'Custom Workholding', 'url' => $mcs . '#custom-workholding' ),
					array( 'label' => 'Sheet Metal Modifications', 'url' => $mcs . '#sheet-metal-modifications' ),
					array( 'label' => 'Process Engineering', 'url' => $mcs . '#process-engineering' ),
					array( 'label' => 'Specialty Machine', 'url' => $mcs . '#specialty-machine' ),
				),
			),
			array(
				'lead'  => '',
				'main'  => 'Applications',
				'links' => array(
					array( 'label' => 'Part Programming', 'url' => $app . '#part-programming' ),
					array( 'label' => 'Process Optimization', 'url' => $app . '#process-optimization' ),
					array( 'label' => 'Tooling Recommendation', 'url' => $app . '#tooling-recommendation' ),
					array( 'label' => 'Training', 'url' => $app . '#training' ),
				),
			),
			array(
				'lead'  => 'Automation',
				'main'  => 'Controls Solutions',
				'links' => array(
					// Menu still says "Electrical…"; the live card is Layered Controls Solutions.
					array( 'label' => 'Electrical – Controls Solutions', 'url' => $acs . '#layered-controls-solutions' ),
					array( 'label' => 'HMI Design', 'url' => $acs . '#hmi-design' ),
					array( 'label' => 'Automation Cell Design', 'url' => $acs . '#automation-cell-design' ),
					array( 'label' => 'Pre-Engineered Solutions', 'url' => $acs . '#pre-engineered-solutions' ),
				),
			),
		),
	);
}

/**
 * Engineered Solutions mega panel: ACF over the defaults.
 *
 * @return array
 */
function gerotech_es_panel() {
	$d    = gerotech_es_defaults();
	$rows = gerotech_option( 'nav_es_categories' );

	if ( is_array( $rows ) && ! empty( $rows ) ) {
		$categories = array();
		foreach ( $rows as $row ) {
			$link = gerotech_link_row( isset( $row['url'] ) ? $row['url'] : '' );
			$lead = isset( $row['heading_lead'] ) ? (string) $row['heading_lead'] : '';
			$main = isset( $row['heading_main'] ) ? (string) $row['heading_main'] : '';

			if ( ! $link && '' === trim( $lead . $main ) ) {
				continue;
			}

			$categories[] = array(
				'lead'        => $lead,
				'main'        => $main,
				'url'         => $link ? $link['url'] : '#',
				'new_tab'     => $link ? $link['new_tab'] : false,
				'description' => isset( $row['description'] ) ? (string) $row['description'] : '',
				'last'        => ! empty( $row['last'] ),
			);
		}
	} else {
		$categories = $d['categories'];
	}

	$rows = gerotech_option( 'nav_es_services' );
	if ( is_array( $rows ) && ! empty( $rows ) ) {
		$services = array();
		foreach ( $rows as $row ) {
			$lead = isset( $row['heading_lead'] ) ? (string) $row['heading_lead'] : '';
			$main = isset( $row['heading_main'] ) ? (string) $row['heading_main'] : '';

			$links = array();
			if ( isset( $row['links'] ) && is_array( $row['links'] ) ) {
				foreach ( $row['links'] as $sub ) {
					$sub_label = isset( $sub['label'] ) ? trim( (string) $sub['label'] ) : '';
					$sub_link = gerotech_link_row( isset( $sub['url'] ) ? $sub['url'] : '', $sub_label );
					if ( ! $sub_link || '' === $sub_label ) {
						continue;
					}
					if ( isset( $sub['mobile_label'] ) && '' !== trim( (string) $sub['mobile_label'] ) ) {
						$sub_link['mobile_label'] = trim( (string) $sub['mobile_label'] );
					}
					$links[] = $sub_link;
				}
			}

			if ( '' === trim( $lead . $main ) && empty( $links ) ) {
				continue;
			}

			$services[] = array(
				'lead'  => $lead,
				'main'  => $main,
				'links' => $links,
			);
		}
	} else {
		$services = $d['services'];
	}

	$cta = gerotech_link_row( gerotech_option( 'nav_es_cta_url' ) );

	$panel = array(
		'col1_title' => gerotech_pick( gerotech_option( 'nav_es_col1_title' ), $d['col1_title'] ),
		'categories' => $categories,
		'cta'        => array(
			'label'        => gerotech_pick( gerotech_option( 'nav_es_cta_label' ), $d['cta_label'] ),
			'mobile_label' => gerotech_pick( gerotech_option( 'nav_es_cta_mobile_label' ), $d['cta_mobile_label'] ),
			'url'          => $cta ? $cta['url'] : $d['cta_url'],
		),
		'col2_title' => gerotech_pick( gerotech_option( 'nav_es_col2_title' ), $d['col2_title'] ),
		'services'   => $services,
	);

	/**
	 * Filter the resolved Engineered Solutions mega panel.
	 *
	 * @param array $panel Panel data.
	 */
	return apply_filters( 'gerotech_es_panel', $panel );
}

/* ── Footer defaults ────────────────────────────────────────────────────── */

/**
 * The footer as it ships today.
 *
 * @return array
 */
function gerotech_footer_defaults() {
	return array(
		'logo'      => '',
		'logo_alt'  => 'Gerotech — Machines, Solutions, Support',
		'logo_width'  => 176,
		'logo_height' => 28,
		'tagline'   => "Michigan's Premier CNC Machinery Distributor & Engineering Solutions Provider — serving manufacturers since 1987.",
		'address'   => "29220 Commerce Drive\nFlat Rock, MI 48134",
		'phone'     => '734-379-7788',
		// Placeholder destinations until the client confirms its social URLs;
		// the whole block is also hidden by .site-footer__socials in components.css.
		'socials'   => array(
			array( 'network' => 'linkedin', 'icon' => 'in', 'label' => 'LinkedIn', 'url' => '#', 'new_tab' => false ),
			array( 'network' => 'instagram', 'icon' => 'ig', 'label' => 'Instagram', 'url' => '#', 'new_tab' => false ),
			array( 'network' => 'youtube', 'icon' => '▶', 'label' => 'YouTube', 'url' => '#', 'new_tab' => false ),
		),
		'columns'   => array(
			array(
				'title' => 'Machines',
				'links' => array(
					array( 'label' => 'Machining Centers ↗', 'url' => 'https://gerotech.com/machines', 'new_tab' => true ),
					array( 'label' => 'Turning Centers ↗', 'url' => 'https://gerotech.com/machines', 'new_tab' => true ),
					array( 'label' => 'EDM ↗', 'url' => 'https://gerotech.com/machines', 'new_tab' => true ),
					array( 'label' => 'Automation', 'url' => gerotech_page_url( 'automation-integration' ), 'new_tab' => false ),
					array( 'label' => 'All Machines ↗', 'url' => 'https://gerotech.com/machines', 'new_tab' => true ),
				),
			),
			array(
				'title' => 'Solutions & Support',
				'links' => array(
					array( 'label' => 'Engineered Solutions', 'url' => gerotech_page_url( 'engineered-solutions' ), 'new_tab' => false ),
					array( 'label' => 'Service Request', 'url' => gerotech_page_url( 'support' ), 'new_tab' => false ),
					array( 'label' => 'Parts', 'url' => gerotech_page_url( 'support' ), 'new_tab' => false ),
					array( 'label' => 'Training', 'url' => gerotech_page_url( 'training' ), 'new_tab' => false ),
					array( 'label' => 'Documentation', 'url' => gerotech_page_url( 'support' ) . '#documentation', 'new_tab' => false ),
				),
			),
			array(
				'title' => 'Company',
				'links' => array(
					// TODO: wire to the client's real legal / news URLs.
					array( 'label' => 'About Gerotech', 'url' => gerotech_page_url( 'about' ), 'new_tab' => false ),
					array( 'label' => 'Our Team', 'url' => gerotech_page_url( 'about' ), 'new_tab' => false ),
					array( 'label' => 'Careers', 'url' => gerotech_page_url( 'careers' ), 'new_tab' => false ),
					array( 'label' => 'News', 'url' => gerotech_page_url( 'about' ), 'new_tab' => false ),
					array( 'label' => 'Contact', 'url' => gerotech_page_url( 'contact' ), 'new_tab' => false ),
				),
			),
		),
		'copyright' => 'Gerotech, Inc. All rights reserved.',
		'legal_links' => array(
			array( 'label' => 'Privacy Policy', 'url' => gerotech_page_url( 'about' ), 'new_tab' => false ),
			array( 'label' => 'Terms of Use', 'url' => gerotech_page_url( 'about' ), 'new_tab' => false ),
		),
	);
}

/**
 * Social networks the footer can show, keyed by the ACF `network` value.
 *
 * @return array slug => display name
 */
function gerotech_social_networks() {
	return array(
		'linkedin'  => 'LinkedIn',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'facebook'  => 'Facebook',
		'x'         => 'X',
		'tiktok'    => 'TikTok',
	);
}

/**
 * Map a legacy typed glyph ("in", "ig", "▶") to a network slug.
 *
 * @param string $glyph Stored `icon` text.
 * @return string Network slug or ''.
 */
function gerotech_social_network_from_glyph( $glyph ) {
	$map = array(
		'in' => 'linkedin',
		'li' => 'linkedin',
		'ig' => 'instagram',
		'▶'  => 'youtube',
		'yt' => 'youtube',
		'f'  => 'facebook',
		'fb' => 'facebook',
		'x'  => 'x',
		'tw' => 'x',
		'tt' => 'tiktok',
	);
	$glyph = strtolower( trim( (string) $glyph ) );
	return isset( $map[ $glyph ] ) ? $map[ $glyph ] : '';
}

/**
 * Inline SVG for a social network, sized to the 36px .social-icon circle.
 *
 * Simple Icons-style single-path glyphs, `fill: currentColor` so the existing
 * white / orange-on-hover colouring applies unchanged.
 *
 * @param string $network Network slug from gerotech_social_networks().
 * @return string Safe SVG markup, or '' for an unknown network.
 */
function gerotech_social_icon_svg( $network ) {
	$paths = array(
		'linkedin'  => 'M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45C23.2 24 24 23.23 24 22.27V1.73C24 .77 23.2 0 22.22 0z',
		'instagram' => 'M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63a5.9 5.9 0 0 0-2.13 1.38A5.9 5.9 0 0 0 .63 4.14C.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.79.72 1.46 1.38 2.13a5.9 5.9 0 0 0 2.13 1.38c.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.9 5.9 0 0 0 2.13-1.38 5.9 5.9 0 0 0 1.38-2.13c.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91a5.9 5.9 0 0 0-1.38-2.13A5.9 5.9 0 0 0 19.86.63C19.1.33 18.22.13 16.95.07 15.67.01 15.26 0 12 0zm0 5.84a6.16 6.16 0 1 0 0 12.32 6.16 6.16 0 0 0 0-12.32zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.85a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88z',
		'youtube'   => 'M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81zM9.55 15.57V8.43L15.82 12l-6.27 3.57z',
		'facebook'  => 'M24 12.07C24 5.41 18.63 0 12 0S0 5.41 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.7 4.53-4.7 1.31 0 2.69.24 2.69.24v2.97h-1.52c-1.49 0-1.96.93-1.96 1.89v2.26h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07z',
		'x'         => 'M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.21-6.82-5.97 6.82H1.67l7.73-8.84L1.25 2.25h6.83l4.71 6.23 5.45-6.23zm-1.16 17.52h1.83L7.08 4.13H5.12l11.96 15.64z',
		'tiktok'    => 'M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z',
	);
	if ( ! isset( $paths[ $network ] ) ) {
		return '';
	}
	return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="' . esc_attr( $paths[ $network ] ) . '"/></svg>';
}

/**
 * Normalise a list of simple link rows (label + url + new_tab).
 *
 * @param array  $rows    ACF repeater rows.
 * @param string $label_key Key holding the label.
 * @param string $url_key   Key holding the link field.
 * @return array[]
 */
function gerotech_normalise_link_rows( $rows, $label_key = 'label', $url_key = 'url' ) {
	$out = array();

	if ( ! is_array( $rows ) ) {
		return $out;
	}

	foreach ( $rows as $row ) {
		$label = isset( $row[ $label_key ] ) ? trim( (string) $row[ $label_key ] ) : '';
		$link  = gerotech_link_row(
			isset( $row[ $url_key ] ) ? $row[ $url_key ] : '',
			$label,
			! empty( $row['new_tab'] )
		);

		if ( ! $link || '' === $label ) {
			continue;
		}

		$link['label'] = $label;

		// Optional shorter wording for the phone menu, set per link.
		if ( isset( $row['mobile_label'] ) && '' !== trim( (string) $row['mobile_label'] ) ) {
			$link['mobile_label'] = trim( (string) $row['mobile_label'] );
		}

		$out[] = $link;
	}

	return $out;
}

/**
 * Footer content: ACF over the defaults above.
 *
 * @return array
 */
function gerotech_footer_data() {
	$d    = gerotech_footer_defaults();
	$rows = gerotech_option( 'footer_columns' );

	if ( is_array( $rows ) && ! empty( $rows ) ) {
		$columns = array();
		foreach ( $rows as $row ) {
			$title = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
			$links = isset( $row['links'] ) ? gerotech_normalise_link_rows( $row['links'] ) : array();

			if ( '' === $title && empty( $links ) ) {
				continue;
			}

			$columns[] = array(
				'title' => $title,
				'links' => $links,
			);
		}
	} else {
		$columns = $d['columns'];
	}

	$socials  = array();
	$networks = gerotech_social_networks();
	$rows     = gerotech_option( 'footer_socials' );
	if ( is_array( $rows ) && ! empty( $rows ) ) {
		foreach ( $rows as $row ) {
			$network = isset( $row['network'] ) && isset( $networks[ $row['network'] ] ) ? $row['network'] : '';
			$icon    = isset( $row['icon'] ) ? trim( (string) $row['icon'] ) : '';
			$label   = isset( $row['label'] ) ? trim( (string) $row['label'] ) : '';
			$link    = gerotech_link_row( isset( $row['url'] ) ? $row['url'] : '', $label, ! empty( $row['new_tab'] ) );

			// Rows saved before the Network picker existed only have the glyph.
			if ( '' === $network && '' !== $icon ) {
				$network = gerotech_social_network_from_glyph( $icon );
			}
			if ( '' === $label && '' !== $network ) {
				$label = $networks[ $network ];
			}

			if ( '' === $network && '' === $icon && ! $link ) {
				continue;
			}

			$socials[] = array(
				'network' => $network,
				'icon'    => $icon,
				'label'   => $label,
				'url'     => $link ? $link['url'] : '#',
				'new_tab' => $link ? $link['new_tab'] : false,
			);
		}
	} else {
		$socials = $d['socials'];
	}

	$legal = gerotech_option( 'footer_legal_links' );
	$legal = gerotech_normalise_link_rows( is_array( $legal ) && ! empty( $legal ) ? $legal : $d['legal_links'] );

	$phone = gerotech_pick( gerotech_option( 'footer_phone' ), $d['phone'] );

	$data = array(
		'logo'        => gerotech_field( 'footer_logo', '', 'option' ),
		'logo_alt'    => gerotech_pick( gerotech_option( 'footer_logo_alt' ), $d['logo_alt'] ),
		'logo_width'  => $d['logo_width'],
		'logo_height' => $d['logo_height'],
		'tagline'     => gerotech_pick( gerotech_option( 'footer_tagline' ), $d['tagline'] ),
		'address'     => gerotech_pick( gerotech_option( 'footer_address' ), $d['address'] ),
		'phone'       => $phone,
		'phone_url'   => gerotech_tel_link( $phone ),
		'socials'     => $socials,
		'columns'     => $columns,
		'copyright'   => gerotech_pick( gerotech_option( 'footer_copyright_text' ), $d['copyright'] ),
		'legal_links' => $legal,
	);

	/**
	 * Filter the resolved footer content.
	 *
	 * @param array $data Footer content.
	 */
	return apply_filters( 'gerotech_footer_data', $data );
}
