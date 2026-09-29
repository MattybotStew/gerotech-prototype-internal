<?php
/**
 * ACF field groups — global header, navigation and footer (options page).
 *
 * These blocks render site-wide (every template calls get_header()/get_footer()).
 * Each field therefore follows the project rule that ships in helpers.php:
 * the front-end default lives in inc/global-content.php, NEVER in
 * `default_value` — ACF injects a default on read, so a plain save would
 * persist it and the editor would silently freeze the design copy.
 *
 * Registered in PHP (not the admin UI) so the structure is version-controlled
 * and reviewable in git alongside the markup it drives.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

$gerotech_global_options_page = array(
	array(
		array(
			'param'    => 'options_page',
			'operator' => '==',
			'value'    => 'gerotech-site-content',
		),
	),
);

/**
 * Header — alert banner, logo, CTA button, search modal.
 *
 * Replaces the hardcoded markup in header.php. Phone numbers in the banner are
 * typed as display text; the tel: href is built from `value` (see
 * gerotech_tel_link()), so an editor can never publish a broken link.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_site_header',
		'title'    => 'Site Content — Header',
		'location' => $gerotech_global_options_page,
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			/* ── Alert banner ─────────────────────────────────── */
			array(
				'key'          => 'field_header_banner_tab',
				'label'        => 'Alert Banner',
				'type'         => 'tab',
				'placement'    => 'top',
			),
			array(
				'key'           => 'field_header_banner_items',
				'label'         => 'Banner items',
				'name'          => 'header_banner_items',
				'type'          => 'repeater',
				'layout'        => 'block',
				'max'           => 4,
				'button_label'  => 'Add banner item',
				'instructions'  => 'The black bar across the top of every page. Type the phone number as it should read; the <code>tel:</code> link is generated from it. Leave a row\'s number blank to hide that item.',
				'sub_fields'    => array(
					array(
						'key'  => 'field_header_banner_label',
						'label' => 'Label',
						'name' => 'label',
						'type' => 'text',
					),
					array(
						'key'  => 'field_header_banner_value',
						'label' => 'Phone number',
						'name' => 'value',
						'type' => 'text',
					),
					array(
						'key'  => 'field_header_banner_url',
						'label' => 'Link (optional override)',
						'name' => 'url',
						'type' => 'link',
					),
				),
			),

			/* ── Logo + CTA ───────────────────────────────────── */
			array(
				'key'       => 'field_header_identity_tab',
				'label'     => 'Logo &amp; Button',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array(
				'key'           => 'field_header_logo',
				'label'         => 'Logo',
				'name'          => 'header_logo',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Leave empty to keep the Gerotech logo bundled with the theme.',
			),
			array(
				'key'   => 'field_header_logo_alt',
				'label' => 'Logo alt text',
				'name'  => 'header_logo_alt',
				'type'  => 'text',
			),
			array(
				'key'          => 'field_header_logo_url',
				'label'        => 'Logo links to',
				'name'         => 'header_logo_url',
				'type'         => 'link',
				'instructions' => 'Default: the homepage.',
			),
			array(
				'key'   => 'field_header_cta_label',
				'label' => 'Header button label',
				'name'  => 'header_cta_label',
				'type'  => 'text',
			),
			array(
				'key'          => 'field_header_cta_url',
				'label'        => 'Header button link',
				'name'         => 'header_cta_url',
				'type'         => 'link',
				'instructions' => 'Shown as the button on desktop and as the last item in the mobile menu.',
			),

			/* ── Search modal ─────────────────────────────────── */
			array(
				'key'       => 'field_header_search_tab',
				'label'     => 'Search',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array( 'key' => 'field_header_search_title', 'label' => 'Modal heading', 'name' => 'header_search_title', 'type' => 'text' ),
			array( 'key' => 'field_header_search_hint', 'label' => 'Modal hint text', 'name' => 'header_search_hint', 'type' => 'text' ),
			array( 'key' => 'field_header_search_placeholder', 'label' => 'Input placeholder', 'name' => 'header_search_placeholder', 'type' => 'text' ),
			array(
				'key'          => 'field_header_search_links',
				'label'        => 'Quick links',
				'name'         => 'header_search_links',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 10,
				'button_label' => 'Add quick link',
				'sub_fields'   => array(
					array( 'key' => 'field_header_search_link_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
					array( 'key' => 'field_header_search_link_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
				),
			),
		),
	)
);

/**
 * Main navigation.
 *
 * `nav_items` is the top-level menu. Three shapes are supported, matching the
 * design:
 *   - plain        — a single link (Training, About)
 *   - dropdown     — link + a flat list of sub-links (Support)
 *   - machines-mega / es-mega — the two mega panels, whose contents live in the
 *     two tabs below because each panel has its own layout.
 *
 * The mobile menu is generated from this same tree — one edit updates both.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_site_navigation',
		'title'    => 'Site Content — Navigation',
		'location' => $gerotech_global_options_page,
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			array(
				'key'          => 'field_nav_items_tab',
				'label'        => 'Main Menu',
				'type'         => 'tab',
				'placement'    => 'top',
			),
			array(
				'key'          => 'field_nav_items',
				'label'        => 'Top-level items',
				'name'         => 'nav_items',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 8,
				'button_label' => 'Add top-level item',
				'instructions'  => 'The main menu, in order. Pick any page in <em>Link</em>, or type an external URL. The mega-panel contents live on the next two tabs — set an item\'s <em>Style</em> to <code>machines-mega</code> or <code>es-mega</code> to show that panel, and <strong>only one item may use each mega style</strong>.',
				'sub_fields'    => array(
					array( 'key' => 'field_nav_item_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
					array( 'key' => 'field_nav_item_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
					array(
						'key'    => 'field_nav_item_new_tab',
						'label'  => 'Open in a new tab',
						'name'   => 'new_tab',
						'type'   => 'true_false',
					),
					array(
						'key'           => 'field_nav_item_style',
						'label'         => 'Style',
						'name'          => 'style',
						'type'          => 'select',
						'choices'       => array(
							'plain'        => 'Simple link',
							'dropdown'     => 'Dropdown list',
							'machines-mega' => 'Machines mega panel',
							'es-mega'      => 'Engineered Solutions mega panel',
						),
						'allow_null' => 0,
						'instructions' => 'Leave blank for a simple link.',
					),
					array(
						'key'          => 'field_nav_item_show_mobile',
						'label'        => 'Show in the mobile menu',
						'name'         => 'show_mobile',
						'type'         => 'true_false',
						'instructions' => 'Uncheck to hide this item on phones and tablets only.',
					),
					array(
						'key'          => 'field_nav_item_links',
						'label'        => 'Sub-links',
						'name'         => 'links',
						'type'         => 'repeater',
						'layout'       => 'block',
						'max'          => 20,
						'button_label' => 'Add sub-link',
						'instructions'  => 'Used when the style above is <em>Dropdown list</em>.',
						'sub_fields'   => array(
							array( 'key' => 'field_nav_item_link_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
							array( 'key' => 'field_nav_item_link_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
							array( 'key' => 'field_nav_item_link_new_tab', 'label' => 'Open in a new tab', 'name' => 'new_tab', 'type' => 'true_false' ),
						),
					),
				),
			),

			array(
				'key'       => 'field_nav_machines_tab',
				'label'     => 'Machines Mega Panel',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array(
				'key'          => 'field_nav_machines_groups',
				'label'        => 'Machine groups',
				'name'         => 'nav_machines_groups',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 24,
				'button_label' => 'Add machine group',
				'instructions'  => 'These fill the Haas catalog panel, which is a four-column grid. Put each group in the column it should appear in; rows are otherwise kept in the order you add them.',
				'sub_fields'    => array(
					array( 'key' => 'field_nav_machines_group_title', 'label' => 'Group title', 'name' => 'title', 'type' => 'text' ),
					array(
						'key'           => 'field_nav_machines_group_column',
						'label'         => 'Column',
						'name'          => 'column',
						'type'          => 'select',
						'choices'       => array(
							'1' => 'Column 1',
							'2' => 'Column 2',
							'3' => 'Column 3',
							'4' => 'Column 4',
						),
						'allow_null'    => 0,
						'instructions'  => 'Leave blank for column 1.',
					),
					array(
						'key'          => 'field_nav_machines_group_mobile_order',
						'label'        => 'Mobile order',
						'name'         => 'mobile_order',
						'type'         => 'number',
						'min'          => 1,
						'max'          => 99,
						'instructions' => 'Optional. The phone menu is a single short list, so it can be ordered differently from the desktop columns. Blank keeps the row order.',
					),
					array(
						'key'          => 'field_nav_machines_group_links',
						'label'        => 'Machine links',
						'name'         => 'links',
						'type'         => 'repeater',
						'layout'       => 'block',
						'max'          => 40,
						'button_label' => 'Add machine link',
						'sub_fields'   => array(
							array( 'key' => 'field_nav_machine_link_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
							array( 'key' => 'field_nav_machine_link_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
							array( 'key' => 'field_nav_machine_link_new_tab', 'label' => 'Open in a new tab', 'name' => 'new_tab', 'type' => 'true_false' ),
						),
					),
				),
			),
			array( 'key' => 'field_nav_machines_help_title', 'label' => 'Help card — title', 'name' => 'nav_machines_help_title', 'type' => 'text', 'instructions' => 'The dark "not sure which machine" card in the last column. Clear both this and the button label to hide it.' ),
			array( 'key' => 'field_nav_machines_help_label', 'label' => 'Help card — button label', 'name' => 'nav_machines_help_label', 'type' => 'text' ),
			array( 'key' => 'field_nav_machines_help_url', 'label' => 'Help card — button link', 'name' => 'nav_machines_help_url', 'type' => 'link' ),
			array( 'key' => 'field_nav_machines_footer_label', 'label' => 'Footer link — label', 'name' => 'nav_machines_footer_label', 'type' => 'text', 'instructions' => 'The full-width link under the panel, e.g. “Browse the full Haas catalog”. Clear to hide.' ),
			array( 'key' => 'field_nav_machines_footer_mobile_label', 'label' => 'Footer link — mobile label', 'name' => 'nav_machines_footer_mobile_label', 'type' => 'text', 'instructions' => 'Shorter wording for the phone menu, e.g. “Full Haas Catalog ↗”.' ),
			array( 'key' => 'field_nav_machines_footer_url', 'label' => 'Footer link — URL', 'name' => 'nav_machines_footer_url', 'type' => 'link' ),
			array( 'key' => 'field_nav_machines_footer_new_tab', 'label' => 'Footer link — open in a new tab', 'name' => 'nav_machines_footer_new_tab', 'type' => 'true_false' ),

			array(
				'key'       => 'field_nav_es_tab',
				'label'     => 'Engineered Solutions Mega Panel',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array( 'key' => 'field_nav_es_col1_title', 'label' => 'First column — heading', 'name' => 'nav_es_col1_title', 'type' => 'text', 'instructions' => 'Default: “By Category”.' ),
			array(
				'key'          => 'field_nav_es_categories',
				'label'        => 'First column — categories',
				'name'         => 'nav_es_categories',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 6,
				'button_label' => 'Add category',
				'sub_fields'    => array(
					array(
						'key'           => 'field_nav_es_category_lead',
						'label'         => 'Title — first part',
						'name'          => 'heading_lead',
						'type'          => 'text',
						'instructions'  => 'Plain text, e.g. “Machine”. Leave empty for a single-line title.',
					),
					array(
						'key'          => 'field_nav_es_category_main',
						'label'        => 'Title — second part',
						'name'         => 'heading_main',
						'type'         => 'text',
						'instructions' => 'Optional. Renders as the emphasised half of a two-tone title.',
					),
					array( 'key' => 'field_nav_es_category_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
					array( 'key' => 'field_nav_es_category_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 2 ),
					array( 'key' => 'field_nav_es_category_last', 'label' => 'Last item (removes the extra gap above the button)', 'name' => 'last', 'type' => 'true_false' ),
				),
			),
			array( 'key' => 'field_nav_es_cta_label', 'label' => 'First column — button label', 'name' => 'nav_es_cta_label', 'type' => 'text', 'instructions' => 'Clear to hide the button at the bottom of the first column.' ),
			array( 'key' => 'field_nav_es_cta_mobile_label', 'label' => 'First column — mobile button label', 'name' => 'nav_es_cta_mobile_label', 'type' => 'text', 'instructions' => 'Shorter wording for the phone menu, e.g. “Talk to an Engineer”. A “→” is added automatically.' ),
			array( 'key' => 'field_nav_es_cta_url', 'label' => 'First column — button link', 'name' => 'nav_es_cta_url', 'type' => 'link' ),
			array( 'key' => 'field_nav_es_col2_title', 'label' => 'Second column — heading', 'name' => 'nav_es_col2_title', 'type' => 'text', 'instructions' => 'Default: “All Services”.' ),
			array(
				'key'          => 'field_nav_es_services',
				'label'        => 'Second column — service groups',
				'name'         => 'nav_es_services',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 12,
				'button_label' => 'Add service group',
				'sub_fields'    => array(
					array( 'key' => 'field_nav_es_service_lead', 'label' => 'Group title — first part', 'name' => 'heading_lead', 'type' => 'text' ),
					array( 'key' => 'field_nav_es_service_main', 'label' => 'Group title — second part', 'name' => 'heading_main', 'type' => 'text' ),
					array(
						'key'          => 'field_nav_es_service_links',
						'label'        => 'Links',
						'name'         => 'links',
						'type'         => 'repeater',
						'layout'       => 'block',
						'max'          => 20,
						'button_label' => 'Add link',
						'sub_fields'   => array(
							array( 'key' => 'field_nav_es_service_link_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
							array( 'key' => 'field_nav_es_service_link_mobile_label', 'label' => 'Mobile label', 'name' => 'mobile_label', 'type' => 'text', 'instructions' => 'Optional shorter wording for the phone menu, e.g. “Column Risers”. Blank reuses the label above.' ),
							array( 'key' => 'field_nav_es_service_link_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
						),
					),
				),
			),
		),
	)
);

/**
 * Footer — brand block, link columns, bottom bar.
 *
 * The link columns are independent of the main menu on purpose: the footer
 * groups links differently (and points at the external Haas catalog), so the
 * two are edited separately.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_site_footer',
		'title'    => 'Site Content — Footer',
		'location' => $gerotech_global_options_page,
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			array(
				'key'       => 'field_footer_brand_tab',
				'label'     => 'Brand Block',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array(
				'key'           => 'field_footer_logo',
				'label'         => 'Logo',
				'name'          => 'footer_logo',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Leave empty to keep the white Gerotech logo bundled with the theme.',
			),
			array( 'key' => 'field_footer_logo_alt', 'label' => 'Logo alt text', 'name' => 'footer_logo_alt', 'type' => 'text', 'instructions' => 'Describes the logo for screen readers and search engines.' ),
			array( 'key' => 'field_footer_tagline', 'label' => 'Tagline', 'name' => 'footer_tagline', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_footer_address', 'label' => 'Address', 'name' => 'footer_address', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Line breaks are preserved.' ),
			array( 'key' => 'field_footer_phone', 'label' => 'Phone', 'name' => 'footer_phone', 'type' => 'text', 'instructions' => 'Shown under the address; the <code>tel:</code> link is generated from it.' ),
			array(
				'key'          => 'field_footer_socials',
				'label'        => 'Social links',
				'name'         => 'footer_socials',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 6,
				'button_label' => 'Add social link',
				'sub_fields'   => array(
					array(
						'key'           => 'field_footer_social_icon',
						'label'         => 'Icon glyph',
						'name'          => 'icon',
						'type'          => 'text',
						'instructions'  => 'Short character(s) shown in the circle, e.g. “in”, “ig”, “f”.',
					),
					array( 'key' => 'field_footer_social_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text', 'instructions' => 'Read by screen readers.' ),
					array( 'key' => 'field_footer_social_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
					array( 'key' => 'field_footer_social_new_tab', 'label' => 'Open in a new tab', 'name' => 'new_tab', 'type' => 'true_false' ),
				),
			),

			array(
				'key'       => 'field_footer_columns_tab',
				'label'     => 'Link Columns',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array(
				'key'          => 'field_footer_columns',
				'label'        => 'Columns',
				'name'         => 'footer_columns',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 3,
				'button_label' => 'Add column',
				'instructions'  => 'The design fits three columns beside the brand block; removing one leaves an empty gap in the layout.',
				'sub_fields'    => array(
					array( 'key' => 'field_footer_column_title', 'label' => 'Column title', 'name' => 'title', 'type' => 'text' ),
					array(
						'key'          => 'field_footer_column_links',
						'label'        => 'Links',
						'name'         => 'links',
						'type'         => 'repeater',
						'layout'       => 'block',
						'max'          => 20,
						'button_label' => 'Add link',
						'sub_fields'   => array(
							array( 'key' => 'field_footer_column_link_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
							array( 'key' => 'field_footer_column_link_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
							array( 'key' => 'field_footer_column_link_new_tab', 'label' => 'Open in a new tab', 'name' => 'new_tab', 'type' => 'true_false' ),
						),
					),
				),
			),

			array(
				'key'       => 'field_footer_bottom_tab',
				'label'     => 'Bottom Bar',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array(
				'key'          => 'field_footer_copyright_text',
				'label'        => 'Copyright line',
				'name'         => 'footer_copyright_text',
				'type'         => 'text',
				'instructions' => 'The year is added automatically, so write only the wording. Leave blank to keep the default wording.',
				'note'         => 'Named <code>footer_copyright_text</code>, not <code>footer_copyright</code>: the 2017 legacy group “Site Options” already stores a field under <code>footer_copyright</code>, and ACF resolves option values by name, so the older value would win.',
			),
			array(
				'key'          => 'field_footer_legal_links',
				'label'        => 'Legal links',
				'name'         => 'footer_legal_links',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 6,
				'button_label' => 'Add legal link',
				'sub_fields'   => array(
					array( 'key' => 'field_footer_legal_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
					array( 'key' => 'field_footer_legal_url', 'label' => 'Link', 'name' => 'url', 'type' => 'link' ),
					array( 'key' => 'field_footer_legal_new_tab', 'label' => 'Open in a new tab', 'name' => 'new_tab', 'type' => 'true_false' ),
				),
			),
		),
	)
);
