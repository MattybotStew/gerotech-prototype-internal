<?php
/**
 * ACF field groups — global header, navigation and footer.
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
 * WRITTEN FOR THE CLIENT, NOT FOR US
 * ----------------------------------
 * Every label and instruction below is what a Gerotech editor reads. Rules:
 *
 *  - Say what the thing IS on the page ("the black phone bar"), not what the
 *    field is called in code.
 *  - Layout decisions the design fixed (menu style, which column a machine
 *    group sits in, phone-menu ordering) carry the wrapper class
 *    `gerotech-advanced`. inc/admin.php hides them unless the URL has
 *    `&advanced=1`. They stay in the form so a save still posts them — see
 *    gerotech_show_advanced_fields() for why that matters.
 *  - One control per idea. A link is the Link field only: its own "open in a
 *    new tab" checkbox is used, and any link that leaves the site opens in a
 *    new tab automatically (gerotech_link_row()). The separate `new_tab`
 *    toggles that used to sit next to every link were removed 2026-09-29;
 *    stored values are still honoured by the renderers for old rows.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

/**
 * Wrapper that marks a field as developer-only (hidden by default).
 *
 * @return array
 */
function gerotech_acf_advanced_wrapper() {
	return array( 'class' => 'gerotech-advanced' );
}

/**
 * A short note rendered at the top of a screen.
 *
 * @param string $key  Field key.
 * @param string $html Message HTML.
 * @return array
 */
function gerotech_acf_screen_note( $key, $html ) {
	return array(
		'key'     => $key,
		'label'   => '',
		'type'    => 'message',
		'message' => $html,
		'new_lines' => '',
		'esc_html'  => 0,
		'wrapper' => array( 'class' => 'gerotech-screen-note' ),
	);
}

/**
 * A "Label + Link" pair of sub-fields, used by every link list.
 *
 * @param string $key_prefix e.g. 'field_footer_column_link'.
 * @param string $label_hint Instruction under the label box.
 * @return array[]
 */
function gerotech_acf_link_pair( $key_prefix, $label_hint = '' ) {
	return array(
		array(
			'key'          => $key_prefix . '_label',
			'label'        => 'Text',
			'name'         => 'label',
			'type'         => 'text',
			'instructions' => $label_hint,
			'wrapper'      => array( 'width' => '40' ),
		),
		array(
			'key'          => $key_prefix . '_url',
			'label'        => 'Goes to',
			'name'         => 'url',
			'type'         => 'link',
			'instructions' => 'Pick a page or paste a web address. Links to other websites open in a new tab automatically.',
			'wrapper'      => array( 'width' => '60' ),
		),
	);
}

/* ═══════════════════════════════════════════════════════════════════════════
 * HEADER — alert banner, logo, button, search
 * ═══════════════════════════════════════════════════════════════════════════ */

acf_add_local_field_group(
	array(
		'key'      => 'group_site_header',
		'title'    => 'Header',
		'location' => gerotech_options_location( 'gerotech-site-header' ),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			gerotech_acf_screen_note(
				'field_header_note',
				'<strong>Phone bar</strong> — the black strip at the very top. <strong>Logo &amp; Button</strong> — the white header under it. <strong>Search</strong> — the box that opens from the magnifying glass.'
			),

			/* ── Alert banner ─────────────────────────────────── */
			array(
				'key'       => 'field_header_banner_tab',
				'label'     => 'Phone Bar',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array(
				'key'          => 'field_header_banner_items',
				'label'        => 'Phone numbers',
				'name'         => 'header_banner_items',
				'type'         => 'repeater',
				'layout'       => 'table',
				'max'          => 4,
				'button_label' => 'Add a phone number',
				'instructions' => 'Type each number the way it should read, e.g. <code>734-379-7788</code>. Tapping it on a phone dials it automatically.',
				'sub_fields'   => array(
					array(
						'key'   => 'field_header_banner_label',
						'label' => 'Label',
						'name'  => 'label',
						'type'  => 'text',
						'instructions' => 'e.g. Headquarters &amp; Sales:',
					),
					array(
						'key'   => 'field_header_banner_value',
						'label' => 'Phone number',
						'name'  => 'value',
						'type'  => 'text',
					),
					array(
						'key'     => 'field_header_banner_url',
						'label'   => 'Link somewhere else instead',
						'name'    => 'url',
						'type'    => 'link',
						'instructions' => 'Only if this item should open a page rather than dial.',
						'wrapper' => gerotech_acf_advanced_wrapper(),
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
				'instructions'  => 'Leave empty to keep the Gerotech logo that comes with the site.',
			),
			array(
				'key'   => 'field_header_logo_alt',
				'label' => 'Logo description',
				'name'  => 'header_logo_alt',
				'type'  => 'text',
				'instructions' => 'Read aloud by screen readers and used by search engines. Not shown on the page.',
			),
			array(
				'key'          => 'field_header_logo_url',
				'label'        => 'Logo links to',
				'name'         => 'header_logo_url',
				'type'         => 'link',
				'instructions' => 'Leave blank for the homepage.',
				'wrapper'      => gerotech_acf_advanced_wrapper(),
			),
			array(
				'key'   => 'field_header_cta_label',
				'label' => 'Orange button — text',
				'name'  => 'header_cta_label',
				'type'  => 'text',
				'instructions' => 'e.g. Let\'s Connect. Also the last item in the phone menu.',
				'wrapper' => array( 'width' => '40' ),
			),
			array(
				'key'          => 'field_header_cta_url',
				'label'        => 'Orange button — goes to',
				'name'         => 'header_cta_url',
				'type'         => 'link',
				'wrapper'      => array( 'width' => '60' ),
			),

			/* ── Search modal ─────────────────────────────────── */
			array(
				'key'       => 'field_header_search_tab',
				'label'     => 'Search',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array( 'key' => 'field_header_search_title', 'label' => 'Heading', 'name' => 'header_search_title', 'type' => 'text', 'instructions' => 'e.g. Search Gerotech' ),
			array( 'key' => 'field_header_search_hint', 'label' => 'Line under the heading', 'name' => 'header_search_hint', 'type' => 'text' ),
			array( 'key' => 'field_header_search_placeholder', 'label' => 'Grey text inside the search box', 'name' => 'header_search_placeholder', 'type' => 'text' ),
			array(
				'key'          => 'field_header_search_links',
				'label'        => 'Quick links',
				'name'         => 'header_search_links',
				'type'         => 'repeater',
				'layout'       => 'table',
				'max'          => 10,
				'button_label' => 'Add a quick link',
				'instructions' => 'The shortcuts shown under the search box.',
				'sub_fields'   => gerotech_acf_link_pair( 'field_header_search_link' ),
			),
		),
	)
);

/* ═══════════════════════════════════════════════════════════════════════════
 * MENUS — main menu + the two mega panels
 * ═══════════════════════════════════════════════════════════════════════════ */

acf_add_local_field_group(
	array(
		'key'      => 'group_site_navigation',
		'title'    => 'Menus',
		'location' => gerotech_options_location( 'gerotech-site-menus' ),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			gerotech_acf_screen_note(
				'field_nav_note',
				'<strong>Main Menu</strong> — the row of links across the header. <strong>Machines Panel</strong> and <strong>Engineered Solutions Panel</strong> — the large drop-downs that open from those two menu items. Drag rows to reorder. The phone menu updates by itself.'
			),

			/* ── Main menu ───────────────────────────────────── */
			array(
				'key'       => 'field_nav_items_tab',
				'label'     => 'Main Menu',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array(
				'key'          => 'field_nav_items',
				'label'        => 'Menu items',
				'name'         => 'nav_items',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 8,
				'button_label' => 'Add a menu item',
				'instructions' => 'Give an item <em>Drop-down links</em> and it becomes a drop-down. The two big panels (Machines, Engineered Solutions) are edited on the next two tabs — those two items stay as they are.',
				'sub_fields'   => array_merge(
					gerotech_acf_link_pair( 'field_nav_item' ),
					array(
						array(
							'key'          => 'field_nav_item_links',
							'label'        => 'Drop-down links',
							'name'         => 'links',
							'type'         => 'repeater',
							'layout'       => 'table',
							'max'          => 20,
							'button_label' => 'Add a drop-down link',
							'instructions' => 'Optional. Leave empty for a plain link.',
							'sub_fields'   => gerotech_acf_link_pair( 'field_nav_item_link' ),
							// The two mega-panel items get their contents from the
							// next tabs; an empty drop-down table under them only
							// invites edits that the panel would ignore.
							'conditional_logic' => array(
								array(
									array( 'field' => 'field_nav_item_style', 'operator' => '!=', 'value' => 'machines-mega' ),
									array( 'field' => 'field_nav_item_style', 'operator' => '!=', 'value' => 'es-mega' ),
								),
							),
						),
						array(
							'key'          => 'field_nav_item_style',
							'label'        => 'Style',
							'name'         => 'style',
							'type'         => 'select',
							'choices'      => array(
								'plain'         => 'Simple link',
								'dropdown'      => 'Drop-down list',
								'machines-mega' => 'Machines mega panel',
								'es-mega'       => 'Engineered Solutions mega panel',
							),
							'allow_null'   => 1,
							'placeholder'  => 'Automatic',
							'instructions' => 'Automatic = a drop-down when the item has drop-down links, otherwise a simple link. Only one item may use each mega panel.',
							'wrapper'      => gerotech_acf_advanced_wrapper(),
						),
						array(
							'key'          => 'field_nav_item_show_mobile',
							'label'        => 'Show in the phone menu',
							'name'         => 'show_mobile',
							'type'         => 'true_false',
							'ui'           => 1,
							'instructions' => 'Off hides this item on phones and tablets only.',
							'wrapper'      => gerotech_acf_advanced_wrapper(),
						),
					)
				),
			),

			/* ── Machines panel ──────────────────────────────── */
			array(
				'key'       => 'field_nav_machines_tab',
				'label'     => 'Machines Panel',
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
				'button_label' => 'Add a machine group',
				'instructions' => 'Each group is a heading (e.g. Vertical Mills) with the machine links under it. Machine links go to the Haas catalogue and open in a new tab.',
				'sub_fields'   => array(
					array( 'key' => 'field_nav_machines_group_title', 'label' => 'Group heading', 'name' => 'title', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
					array(
						'key'          => 'field_nav_machines_group_column',
						'label'        => 'Column',
						'name'         => 'column',
						'type'         => 'select',
						'choices'      => array(
							'1' => 'Column 1',
							'2' => 'Column 2',
							'3' => 'Column 3',
							'4' => 'Column 4',
						),
						'allow_null'   => 1,
						'placeholder'  => 'Column 1',
						'instructions' => 'The panel is a four-column grid; the design places groups unevenly.',
						'wrapper'      => array_merge( gerotech_acf_advanced_wrapper(), array( 'width' => '25' ) ),
					),
					array(
						'key'          => 'field_nav_machines_group_mobile_order',
						'label'        => 'Phone-menu order',
						'name'         => 'mobile_order',
						'type'         => 'number',
						'min'          => 1,
						'max'          => 99,
						'instructions' => 'Optional. Blank keeps the row order.',
						'wrapper'      => array_merge( gerotech_acf_advanced_wrapper(), array( 'width' => '25' ) ),
					),
					array(
						'key'          => 'field_nav_machines_group_links',
						'label'        => 'Machine links',
						'name'         => 'links',
						'type'         => 'repeater',
						'layout'       => 'table',
						'max'          => 40,
						'button_label' => 'Add a machine link',
						'sub_fields'   => gerotech_acf_link_pair( 'field_nav_machine_link' ),
					),
				),
			),
			array( 'key' => 'field_nav_machines_help_title', 'label' => 'Dark card — heading', 'name' => 'nav_machines_help_title', 'type' => 'text', 'instructions' => 'The dark “Not sure which machine…” card in the last column. Clear both boxes to hide the card.', 'wrapper' => array( 'width' => '50' ) ),
			array( 'key' => 'field_nav_machines_help_label', 'label' => 'Dark card — button text', 'name' => 'nav_machines_help_label', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
			array( 'key' => 'field_nav_machines_help_url', 'label' => 'Dark card — button goes to', 'name' => 'nav_machines_help_url', 'type' => 'link' ),
			array( 'key' => 'field_nav_machines_footer_label', 'label' => 'Bottom link — text', 'name' => 'nav_machines_footer_label', 'type' => 'text', 'instructions' => 'The full-width link under the panel, e.g. “Browse the full Haas catalog”. Clear to hide.', 'wrapper' => array( 'width' => '50' ) ),
			array( 'key' => 'field_nav_machines_footer_url', 'label' => 'Bottom link — goes to', 'name' => 'nav_machines_footer_url', 'type' => 'link', 'wrapper' => array( 'width' => '50' ) ),
			array( 'key' => 'field_nav_machines_footer_mobile_label', 'label' => 'Bottom link — phone wording', 'name' => 'nav_machines_footer_mobile_label', 'type' => 'text', 'instructions' => 'Shorter wording for the phone menu, e.g. “Full Haas Catalog ↗”. Blank reuses the text above.', 'wrapper' => gerotech_acf_advanced_wrapper() ),

			/* ── Engineered Solutions panel ──────────────────── */
			array(
				'key'       => 'field_nav_es_tab',
				'label'     => 'Engineered Solutions Panel',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array( 'key' => 'field_nav_es_col1_title', 'label' => 'Left column — heading', 'name' => 'nav_es_col1_title', 'type' => 'text', 'instructions' => 'e.g. By Category', 'wrapper' => array( 'width' => '50' ) ),
			array( 'key' => 'field_nav_es_col2_title', 'label' => 'Right column — heading', 'name' => 'nav_es_col2_title', 'type' => 'text', 'instructions' => 'e.g. All Services', 'wrapper' => array( 'width' => '50' ) ),
			array(
				'key'          => 'field_nav_es_categories',
				'label'        => 'Left column — the three categories',
				'name'         => 'nav_es_categories',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 6,
				'button_label' => 'Add a category',
				'sub_fields'   => array(
					array(
						'key'          => 'field_nav_es_category_lead',
						'label'        => 'Title — first word(s)',
						'name'         => 'heading_lead',
						'type'         => 'text',
						'instructions' => 'e.g. “Machine”. The title is shown in two tones; leave this empty for a one-tone title.',
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_nav_es_category_main',
						'label'        => 'Title — rest',
						'name'         => 'heading_main',
						'type'         => 'text',
						'instructions' => 'e.g. “Custom Solutions”.',
						'wrapper'      => array( 'width' => '50' ),
					),
					array( 'key' => 'field_nav_es_category_url', 'label' => 'Goes to', 'name' => 'url', 'type' => 'link' ),
					array( 'key' => 'field_nav_es_category_desc', 'label' => 'One-line description', 'name' => 'description', 'type' => 'textarea', 'rows' => 2 ),
					array(
						'key'     => 'field_nav_es_category_last',
						'label'   => 'Last item (removes the extra gap above the button)',
						'name'    => 'last',
						'type'    => 'true_false',
						'ui'      => 1,
						'wrapper' => gerotech_acf_advanced_wrapper(),
					),
				),
			),
			array( 'key' => 'field_nav_es_cta_label', 'label' => 'Left column — button text', 'name' => 'nav_es_cta_label', 'type' => 'text', 'instructions' => 'Clear to hide the button.', 'wrapper' => array( 'width' => '40' ) ),
			array( 'key' => 'field_nav_es_cta_url', 'label' => 'Left column — button goes to', 'name' => 'nav_es_cta_url', 'type' => 'link', 'wrapper' => array( 'width' => '60' ) ),
			array( 'key' => 'field_nav_es_cta_mobile_label', 'label' => 'Left column — button phone wording', 'name' => 'nav_es_cta_mobile_label', 'type' => 'text', 'instructions' => 'Shorter wording for the phone menu; a “→” is added automatically.', 'wrapper' => gerotech_acf_advanced_wrapper() ),
			array(
				'key'          => 'field_nav_es_services',
				'label'        => 'Right column — service lists',
				'name'         => 'nav_es_services',
				'type'         => 'repeater',
				'layout'       => 'block',
				'max'          => 12,
				'button_label' => 'Add a service list',
				'sub_fields'   => array(
					array( 'key' => 'field_nav_es_service_lead', 'label' => 'List heading — first word(s)', 'name' => 'heading_lead', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
					array( 'key' => 'field_nav_es_service_main', 'label' => 'List heading — rest', 'name' => 'heading_main', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
					array(
						'key'          => 'field_nav_es_service_links',
						'label'        => 'Links',
						'name'         => 'links',
						'type'         => 'repeater',
						'layout'       => 'table',
						'max'          => 20,
						'button_label' => 'Add a link',
						'sub_fields'   => array_merge(
							gerotech_acf_link_pair( 'field_nav_es_service_link' ),
							array(
								array(
									'key'          => 'field_nav_es_service_link_mobile_label',
									'label'        => 'Phone wording',
									'name'         => 'mobile_label',
									'type'         => 'text',
									'instructions' => 'Optional shorter text for the phone menu.',
									'wrapper'      => gerotech_acf_advanced_wrapper(),
								),
							)
						),
					),
				),
			),
		),
	)
);

/* ═══════════════════════════════════════════════════════════════════════════
 * FOOTER — brand block, link columns, bottom bar
 * ═══════════════════════════════════════════════════════════════════════════ */

acf_add_local_field_group(
	array(
		'key'      => 'group_site_footer',
		'title'    => 'Footer',
		'location' => gerotech_options_location( 'gerotech-site-footer' ),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			gerotech_acf_screen_note(
				'field_footer_note',
				'<strong>Company Block</strong> — the logo, tagline, address and social icons on the left. <strong>Link Columns</strong> — the three lists of links. <strong>Bottom Line</strong> — the copyright and legal links.'
			),

			array(
				'key'       => 'field_footer_brand_tab',
				'label'     => 'Company Block',
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
				'instructions'  => 'Leave empty to keep the white Gerotech logo that comes with the site.',
			),
			array( 'key' => 'field_footer_logo_alt', 'label' => 'Logo description', 'name' => 'footer_logo_alt', 'type' => 'text', 'instructions' => 'Read aloud by screen readers. Not shown on the page.', 'wrapper' => gerotech_acf_advanced_wrapper() ),
			array( 'key' => 'field_footer_tagline', 'label' => 'Tagline', 'name' => 'footer_tagline', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_footer_address', 'label' => 'Address', 'name' => 'footer_address', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Press Enter for a new line.', 'wrapper' => array( 'width' => '60' ) ),
			array( 'key' => 'field_footer_phone', 'label' => 'Phone number', 'name' => 'footer_phone', 'type' => 'text', 'instructions' => 'Shown under the address. Tapping it dials.', 'wrapper' => array( 'width' => '40' ) ),
			array(
				'key'          => 'field_footer_socials',
				'label'        => 'Social media',
				'name'         => 'footer_socials',
				'type'         => 'repeater',
				'layout'       => 'table',
				'max'          => 6,
				'button_label' => 'Add a social network',
				'instructions' => 'Pick the network and paste the address of your page there.',
				'sub_fields'   => array(
					array(
						'key'         => 'field_footer_social_network',
						'label'       => 'Network',
						'name'        => 'network',
						'type'        => 'select',
						'choices'     => array(
							'linkedin'  => 'LinkedIn',
							'instagram' => 'Instagram',
							'youtube'   => 'YouTube',
							'facebook'  => 'Facebook',
							'x'         => 'X (Twitter)',
							'tiktok'    => 'TikTok',
						),
						'allow_null'  => 1,
						'placeholder' => 'Choose…',
					),
					array( 'key' => 'field_footer_social_url', 'label' => 'Your page', 'name' => 'url', 'type' => 'link', 'instructions' => 'e.g. https://www.linkedin.com/company/gerotech' ),
					array( 'key' => 'field_footer_social_label', 'label' => 'Description', 'name' => 'label', 'type' => 'text', 'instructions' => 'Read aloud by screen readers. Blank uses the network name.', 'wrapper' => gerotech_acf_advanced_wrapper() ),
					array(
						'key'          => 'field_footer_social_icon',
						'label'        => 'Fallback letters',
						'name'         => 'icon',
						'type'         => 'text',
						'instructions' => 'Only used when no network is chosen, e.g. “in”.',
						'wrapper'      => gerotech_acf_advanced_wrapper(),
					),
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
				'button_label' => 'Add a column',
				'instructions' => 'The design has room for three columns beside the company block.',
				'sub_fields'   => array(
					array( 'key' => 'field_footer_column_title', 'label' => 'Column heading', 'name' => 'title', 'type' => 'text' ),
					array(
						'key'          => 'field_footer_column_links',
						'label'        => 'Links',
						'name'         => 'links',
						'type'         => 'repeater',
						'layout'       => 'table',
						'max'          => 20,
						'button_label' => 'Add a link',
						'sub_fields'   => gerotech_acf_link_pair( 'field_footer_column_link' ),
					),
				),
			),

			array(
				'key'       => 'field_footer_bottom_tab',
				'label'     => 'Bottom Line',
				'type'      => 'tab',
				'placement' => 'top',
			),
			array(
				'key'          => 'field_footer_copyright_text',
				'label'        => 'Copyright wording',
				'name'         => 'footer_copyright_text',
				'type'         => 'text',
				'instructions' => 'The “© 2026” part is added automatically — write only what follows it, e.g. “Gerotech, Inc. All rights reserved.”',
				'note'         => 'Named <code>footer_copyright_text</code>, not <code>footer_copyright</code>: the 2017 legacy group “Site Options” already stores a field under <code>footer_copyright</code>, and ACF resolves option values by name, so the older value would win.',
			),
			array(
				'key'          => 'field_footer_legal_links',
				'label'        => 'Legal links',
				'name'         => 'footer_legal_links',
				'type'         => 'repeater',
				'layout'       => 'table',
				'max'          => 6,
				'button_label' => 'Add a legal link',
				'instructions' => 'e.g. Privacy Policy, Terms of Use.',
				'sub_fields'   => gerotech_acf_link_pair( 'field_footer_legal' ),
			),
		),
	)
);
