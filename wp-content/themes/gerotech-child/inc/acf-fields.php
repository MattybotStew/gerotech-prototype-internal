<?php
/**
 * ACF field groups (registered in PHP — version-controlled, not admin-UI).
 *
 * Phase 4: homepage first. Editors write plain text; wrap the accent phrase in
 * <em>…</em> and use line breaks for the design's forced breaks (see
 * gerotech_accent() in helpers.php).
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
 * Custom ACF location rule: Post Slug (`post_name`).
 *
 * ACF has no built-in slug location rule, so field groups located by
 * `post_name == <slug>` never matched — they were registered but never appeared
 * in the editor (the templates rendered only because of their code defaults).
 * Register the rule so those groups show up.
 */
add_filter(
	'acf/location/rule_types',
	function ( $choices ) {
		$choices['Post']['post_name'] = 'Post Slug';
		return $choices;
	}
);
add_filter(
	'acf/location/rule_values/post_name',
	function ( $choices ) {
		$pages = get_posts(
			array(
				'post_type'   => 'page',
				'numberposts' => -1,
				'post_status' => 'publish',
			)
		);
		foreach ( $pages as $page ) {
			$choices[ $page->post_name ] = $page->post_name . ' (#' . $page->ID . ')';
		}
		return $choices;
	}
);
add_filter(
	'acf/location/rule_match/post_name',
	function ( $match, $rule, $options ) {
		$post_id = isset( $options['post_id'] ) ? $options['post_id'] : 0;
		$post    = $post_id ? get_post( $post_id ) : null;
		if ( ! $post ) {
			return $match;
		}
		$value = (string) $rule['value'];
		$name  = (string) $post->post_name;
		if ( '==' === $rule['operator'] ) {
			return $name === $value;
		}
		if ( '!=' === $rule['operator'] ) {
			return $name !== $value;
		}
		return $match;
	},
	10,
	3
);

/**
 * Homepage content.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_home_content',
		'title'    => 'Homepage — Content',
		'location' => array(
			array(
				array(
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'front_page',
				),
			),
		),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			/* ── Hero slides ───────────────────────────────────── */
			array(
				'key'          => 'field_home_hero_tab',
				'label'        => 'Hero slides',
				'type'         => 'tab',
				'placement'    => 'top',
			),
			array(
				'key'          => 'field_home_hero_slides',
				'label'        => 'Slides',
				'name'         => 'home_hero_slides',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add slide',
				'sub_fields'   => array(
					array( 'key' => 'field_home_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'eyebrow', 'type' => 'text' ),
					array(
						'key'   => 'field_home_hero_headline',
						'label' => 'Headline',
						'name'  => 'headline',
						'type'  => 'textarea',
						'rows'  => 3,
						'instructions' => 'Use line breaks for the design breaks. Wrap the accent phrase in &lt;em&gt;…&lt;/em&gt;.',
					),
					array(
						'key'           => 'field_home_hero_accent_color',
						'label'         => 'Accent colour',
						'name'          => 'accent_color',
						'type'          => 'select',
						'choices'       => array(
							'white'  => 'White (no highlight)',
							'haas'   => 'Haas Red',
							'orange' => 'Brand Orange',
						),
						// Deliberately NO default_value. ACF injects a default on read, and
						// saving the page would then persist it over a slide whose design
						// colour differs (slide 1 is Haas Red, the rest Brand Orange). Left
						// unset, the template resolves the colour itself — see front-page.php.
						'default_value' => '',
						'allow_null'    => 1,
						'placeholder'   => 'Design default (slide 1 red, rest orange)',
						'instructions'  => 'Colour of the <em>accent</em> word in this slide’s headline and peek card. “White” leaves the word uncoloured. Leave unset to keep the design colour; picking a colour overrides it for this slide.',
					),
					array( 'key' => 'field_home_hero_body', 'label' => 'Body', 'name' => 'body', 'type' => 'textarea', 'rows' => 2 ),
					array( 'key' => 'field_home_hero_cta_label', 'label' => 'Button label', 'name' => 'cta_label', 'type' => 'text' ),
					array( 'key' => 'field_home_hero_cta_url', 'label' => 'Button URL', 'name' => 'cta_url', 'type' => 'text' ),
					array(
						'key'           => 'field_home_hero_cta_color',
						'label'         => 'Button colour',
						'name'          => 'cta_color',
						'type'          => 'select',
						'choices'       => array(
							'orange' => 'Brand Orange',
							'haas'   => 'Haas Red',
							'white'  => 'White outline',
						),
						// No default_value for the same reason as Accent colour: slide 1 uses
						// Haas Red, so a persisted default would silently change it.
						'default_value' => '',
						'allow_null'    => 1,
						'placeholder'   => 'Design default (slide 1 red, rest orange)',
						'instructions'  => 'Colour of this slide’s call-to-action button. Independent of the headline accent colour. Leave unset to keep the design colour; picking a colour overrides it for this slide.',
					),
					array( 'key' => 'field_home_hero_image', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
					array( 'key' => 'field_home_hero_title_alt', 'label' => 'Image alt', 'name' => 'title_alt', 'type' => 'text' ),
					array(
						'key'     => 'field_home_hero_image_position',
						'label'   => 'Image crop bias',
						'name'    => 'image_position',
						'type'    => 'select',
						'choices' => array( 'default' => 'Default', 'right' => 'Right' ),
						'default_value' => 'default',
					),
					array( 'key' => 'field_home_hero_peek_eyebrow', 'label' => 'Peek eyebrow', 'name' => 'peek_eyebrow', 'type' => 'text' ),
					array( 'key' => 'field_home_hero_peek_accent', 'label' => 'Peek accent word', 'name' => 'peek_accent', 'type' => 'text', 'instructions' => 'A word already inside the peek eyebrow to tint brand red (e.g. “Haas”). Leave empty for none.' ),
					array( 'key' => 'field_home_hero_peek_title', 'label' => 'Peek title', 'name' => 'peek_title', 'type' => 'text' ),
				),
			),

			/* ── Stats ─────────────────────────────────────────── */
			array( 'key' => 'field_home_stats_tab', 'label' => 'Stats', 'type' => 'tab', 'placement' => 'top' ),
			array(
				'key'          => 'field_home_stats',
				'label'        => 'Stats',
				'name'         => 'home_stats',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add stat',
				'sub_fields'   => array(
					array( 'key' => 'field_home_stat_value', 'label' => 'Display value', 'name' => 'value', 'type' => 'text', 'instructions' => 'e.g. 39+ or 14,000' ),
					array( 'key' => 'field_home_stat_count', 'label' => 'Count-to', 'name' => 'count', 'type' => 'number', 'instructions' => 'Numeric target for the count-up (e.g. 39, 14000).' ),
					array( 'key' => 'field_home_stat_suffix', 'label' => 'Suffix', 'name' => 'suffix', 'type' => 'text', 'instructions' => 'e.g. +' ),
					array( 'key' => 'field_home_stat_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
				),
			),

			/* ── Haas relationship ─────────────────────────────── */
			array( 'key' => 'field_home_haas_tab', 'label' => 'Haas Relationship', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_home_haas_eyebrow', 'label' => 'Eyebrow', 'name' => 'haas_eyebrow', 'type' => 'text' ),
			array(
				'key'           => 'field_home_haas_eyebrow_color',
				'label'         => 'Eyebrow colour',
				'name'          => 'haas_eyebrow_color',
				'type'          => 'select',
				'choices'       => array(
					'white'  => 'White',
					'haas'   => 'Haas Red (default)',
					'orange' => 'Brand Orange',
				),
				'default_value' => 'haas',
				'instructions'  => 'Colours the eyebrow text and its short rule together.',
			),
			array(
				'key'   => 'field_home_haas_headline',
				'label' => 'Headline',
				'name'  => 'haas_headline',
				'type'  => 'textarea',
				'rows'  => 2,
				'instructions' => 'Line breaks + &lt;em&gt; accent supported.',
			),
			array(
				'key'           => 'field_home_haas_accent_color',
				'label'         => 'Headline accent colour',
				'name'          => 'haas_accent_color',
				'type'          => 'select',
				'choices'       => array(
					'white'  => 'White',
					'haas'   => 'Haas Red (default)',
					'orange' => 'Brand Orange',
				),
				'default_value' => 'haas',
				'instructions'  => 'Colour of the &lt;em&gt; accent words in the headline.',
			),
			array( 'key' => 'field_home_haas_lede', 'label' => 'Lede', 'name' => 'haas_lede', 'type' => 'textarea', 'rows' => 5 ),
			array( 'key' => 'field_home_haas_brand_logo', 'label' => 'Brand logo (F1 lockup)', 'name' => 'haas_brand_logo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Machine lineup ────────────────────────────────── */
			array( 'key' => 'field_home_lineup_tab', 'label' => 'Machine Lineup', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_home_lineup_eyebrow', 'label' => 'Eyebrow', 'name' => 'lineup_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_home_lineup_headline', 'label' => 'Headline', 'name' => 'lineup_headline', 'type' => 'textarea', 'rows' => 2 ),
			array(
				'key'          => 'field_home_lineup_panels',
				'label'        => 'Panels',
				'name'         => 'lineup_panels',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add panel',
				'sub_fields'   => array(
					array( 'key' => 'field_home_panel_tab_label', 'label' => 'Tab label', 'name' => 'tab_label', 'type' => 'text' ),
					array( 'key' => 'field_home_panel_badge', 'label' => 'Badge', 'name' => 'badge', 'type' => 'text' ),
					array( 'key' => 'field_home_panel_category', 'label' => 'Category (small label)', 'name' => 'category', 'type' => 'text' ),
					array( 'key' => 'field_home_panel_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_home_panel_description', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 2 ),
					array( 'key' => 'field_home_panel_tags_label', 'label' => 'Tags label', 'name' => 'tags_label', 'type' => 'text', 'instructions' => 'e.g. Featured series' ),
					array(
						'key'          => 'field_home_panel_tags',
						'label'        => 'Tags',
						'name'         => 'tags',
						'type'         => 'textarea',
						'rows'         => 4,
						'instructions' => 'One per line: Label | https://url  (URL optional)',
					),
					array( 'key' => 'field_home_panel_cta_label', 'label' => 'Button label', 'name' => 'cta_label', 'type' => 'text' ),
					array( 'key' => 'field_home_panel_cta_url', 'label' => 'Button URL', 'name' => 'cta_url', 'type' => 'text' ),
					array( 'key' => 'field_home_panel_cta2_label', 'label' => 'Second button label', 'name' => 'cta2_label', 'type' => 'text', 'instructions' => 'Optional (e.g. Winner\'s Circle).' ),
					array( 'key' => 'field_home_panel_cta2_url', 'label' => 'Second button URL', 'name' => 'cta2_url', 'type' => 'text' ),
					array(
						'key'     => 'field_home_panel_photo_style',
						'label'   => 'Photo style',
						'name'    => 'photo_style',
						'type'    => 'select',
						'choices' => array( 'default' => 'Photo', 'logo' => 'Logo on white' ),
						'default_value' => 'default',
					),
					array( 'key' => 'field_home_panel_photo', 'label' => 'Photo', 'name' => 'photo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
				),
			),

			/* ── CTA band ──────────────────────────────────────── */
			array( 'key' => 'field_home_cta_tab', 'label' => 'CTA Band', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_home_cta_eyebrow', 'label' => 'Eyebrow', 'name' => 'cta_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_home_cta_headline', 'label' => 'Headline', 'name' => 'cta_headline', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_home_cta_body', 'label' => 'Body', 'name' => 'cta_body', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_home_cta_button_label', 'label' => 'Button label', 'name' => 'cta_button_label', 'type' => 'text' ),
			array( 'key' => 'field_home_cta_button_url', 'label' => 'Button URL', 'name' => 'cta_button_url', 'type' => 'text' ),
			array( 'key' => 'field_home_cta_call_label', 'label' => 'Call card label', 'name' => 'cta_call_label', 'type' => 'text' ),
			array( 'key' => 'field_home_cta_call_number', 'label' => 'Call card number', 'name' => 'cta_call_number', 'type' => 'text' ),
			array( 'key' => 'field_home_cta_call_note', 'label' => 'Call card note', 'name' => 'cta_call_note', 'type' => 'text' ),
			array( 'key' => 'field_home_cta_image', 'label' => 'Background image', 'name' => 'cta_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Email signup ──────────────────────────────────── */
			array( 'key' => 'field_home_signup_tab', 'label' => 'Mailing List', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_home_signup_title', 'label' => 'Title', 'name' => 'signup_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_home_signup_sub', 'label' => 'Subtext', 'name' => 'signup_sub', 'type' => 'text' ),
		),
	)
);

/**
 * Engineered Solutions hub.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_es_content',
		'title'    => 'Engineered Solutions — Content',
		'location' => array(
			array(
				array( 'param' => 'post_name', 'operator' => '==', 'value' => 'engineered-solutions' ),
			),
		),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			/* ── Hero ─────────────────────────────────────────── */
			array( 'key' => 'field_es_hero_tab', 'label' => 'Hero', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_es_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'es_hero_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_es_hero_headline', 'label' => 'Headline', 'name' => 'es_hero_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => 'Wrap the accent phrase in &lt;em&gt;…&lt;/em&gt;.' ),
			array(
				'key'           => 'field_es_hero_accent_color',
				'label'         => 'Accent colour',
				'name'          => 'es_hero_accent_color',
				'type'          => 'select',
				'choices'       => array(
					'white'  => 'White (no highlight)',
					'haas'   => 'Haas Red',
					'orange' => 'Brand Orange',
				),
				// No default_value, same reasoning as the homepage hero: ACF injects a
				// default on read and a plain save would persist it. Blank keeps the design
				// colour via the template — see the page template.
				'default_value' => '',
				'allow_null'    => 1,
				'placeholder'   => 'Design default (Brand Orange)',
				'instructions'  => 'Colour of the <em>accent</em> word in the hero headline. Leave unset to keep the design colour; picking a colour overrides it for this page.',
			),
			array( 'key' => 'field_es_hero_body', 'label' => 'Body', 'name' => 'es_hero_body', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_es_hero_cta1_label', 'label' => 'Primary button label', 'name' => 'es_hero_cta1_label', 'type' => 'text' ),
			array( 'key' => 'field_es_hero_cta1_url', 'label' => 'Primary button URL', 'name' => 'es_hero_cta1_url', 'type' => 'text' ),
			array( 'key' => 'field_es_hero_cta2_label', 'label' => 'Secondary button label', 'name' => 'es_hero_cta2_label', 'type' => 'text' ),
			array( 'key' => 'field_es_hero_cta2_url', 'label' => 'Secondary button URL', 'name' => 'es_hero_cta2_url', 'type' => 'text' ),
			array(
				'key'           => 'field_es_hero_cta_color',
				'label'         => 'Primary button colour',
				'name'          => 'es_hero_cta_color',
				'type'          => 'select',
				'choices'       => array(
					'orange' => 'Brand Orange',
					'haas'   => 'Haas Red',
					'white'  => 'White outline',
				),
				// No default_value (see Accent colour). Blank keeps the design colour
				// (Brand Orange) via the template. The white-outline label stays the
				// secondary button's fixed style regardless of this choice.
				'default_value' => '',
				'allow_null'    => 1,
				'placeholder'   => 'Design default (Brand Orange)',
				'instructions'  => 'Colour of the primary hero button. Leave unset to keep the design colour; picking a colour overrides it for this page.',
			),
			array( 'key' => 'field_es_hero_image', 'label' => 'Background image', 'name' => 'es_hero_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Why Gerotech ─────────────────────────────────── */
			array( 'key' => 'field_es_why_tab', 'label' => 'Why Gerotech', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_es_why_headline', 'label' => 'Headline', 'name' => 'es_why_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_es_why_body', 'label' => 'Body', 'name' => 'es_why_body', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_es_why_cta_label', 'label' => 'Button label', 'name' => 'es_why_cta_label', 'type' => 'text' ),
			array( 'key' => 'field_es_why_cta_url', 'label' => 'Button URL', 'name' => 'es_why_cta_url', 'type' => 'text' ),
			array(
				'key'          => 'field_es_why_features',
				'label'        => 'Features',
				'name'         => 'es_why_features',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add feature',
				'sub_fields'   => array(
					array( 'key' => 'field_es_feature_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_es_feature_body', 'label' => 'Body', 'name' => 'body', 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_es_feature_link_label', 'label' => 'Link label', 'name' => 'link_label', 'type' => 'text' ),
					array( 'key' => 'field_es_feature_link_url', 'label' => 'Link URL', 'name' => 'link_url', 'type' => 'text' ),
				),
			),

			/* ── FANUC ASI ────────────────────────────────────── */
			array( 'key' => 'field_es_fanuc_tab', 'label' => 'FANUC ASI', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_es_fanuc_eyebrow', 'label' => 'Eyebrow', 'name' => 'es_fanuc_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_es_fanuc_headline', 'label' => 'Headline', 'name' => 'es_fanuc_headline', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_es_fanuc_body', 'label' => 'Body', 'name' => 'es_fanuc_body', 'type' => 'textarea', 'rows' => 4 ),
			array( 'key' => 'field_es_fanuc_benefits', 'label' => 'Benefits (one per line)', 'name' => 'es_fanuc_benefits', 'type' => 'textarea', 'rows' => 4 ),
			array( 'key' => 'field_es_fanuc_cta1_label', 'label' => 'Primary button label', 'name' => 'es_fanuc_cta1_label', 'type' => 'text' ),
			array( 'key' => 'field_es_fanuc_cta1_url', 'label' => 'Primary button URL', 'name' => 'es_fanuc_cta1_url', 'type' => 'text' ),
			array( 'key' => 'field_es_fanuc_cta2_label', 'label' => 'Secondary button label', 'name' => 'es_fanuc_cta2_label', 'type' => 'text' ),
			array( 'key' => 'field_es_fanuc_cta2_url', 'label' => 'Secondary button URL', 'name' => 'es_fanuc_cta2_url', 'type' => 'text' ),
			array( 'key' => 'field_es_fanuc_badge', 'label' => 'Badge image', 'name' => 'es_fanuc_badge', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Technology Partners ──────────────────────────── */
			array( 'key' => 'field_es_partners_tab', 'label' => 'Technology Partners', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_es_partners_eyebrow', 'label' => 'Eyebrow', 'name' => 'es_partners_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_es_partners_headline', 'label' => 'Headline', 'name' => 'es_partners_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_es_partners_body', 'label' => 'Body', 'name' => 'es_partners_body', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_es_partners_cta_label', 'label' => 'Button label', 'name' => 'es_partners_cta_label', 'type' => 'text' ),
			array( 'key' => 'field_es_partners_cta_url', 'label' => 'Button URL', 'name' => 'es_partners_cta_url', 'type' => 'text' ),
			array(
				'key'          => 'field_es_partners_logos',
				'label'        => 'Logos (overrides the wordmark list below)',
				'name'         => 'es_partners_logos',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add logo',
				'instructions' => 'Upload each partner logo from the WordPress Media Library and add an optional link. Reorder rows to control display order. Leave empty to use the default wordmark list below.',
				'sub_fields'   => array(
					array( 'key' => 'field_es_partners_logo_image', 'label' => 'Logo image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ),
					array( 'key' => 'field_es_partners_logo_brand', 'label' => 'Name / alt text', 'name' => 'brand', 'type' => 'text', 'instructions' => 'Used as the image alt text and shown only if no image is uploaded.' ),
					array( 'key' => 'field_es_partners_logo_url', 'label' => 'Link (optional)', 'name' => 'url', 'type' => 'text' ),
				),
			),
			array( 'key' => 'field_es_partners_wordmarks', 'label' => 'Default wordmarks (one per line)', 'name' => 'es_partners_wordmarks', 'type' => 'textarea', 'rows' => 9, 'instructions' => 'Used only when the Logos repeater above is empty.' ),

			/* ── Capability band ──────────────────────────────── */
			array( 'key' => 'field_es_cap_tab', 'label' => 'Capability Band', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_es_cap_eyebrow', 'label' => 'Eyebrow', 'name' => 'es_cap_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_es_cap_headline', 'label' => 'Headline', 'name' => 'es_cap_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => 'Line breaks become &lt;br&gt;.' ),
			array( 'key' => 'field_es_cap_body', 'label' => 'Body', 'name' => 'es_cap_body', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_es_cap_cta1_label', 'label' => 'Primary button label', 'name' => 'es_cap_cta1_label', 'type' => 'text' ),
			array( 'key' => 'field_es_cap_cta1_url', 'label' => 'Primary button URL', 'name' => 'es_cap_cta1_url', 'type' => 'text' ),
			array( 'key' => 'field_es_cap_cta2_label', 'label' => 'Secondary button label', 'name' => 'es_cap_cta2_label', 'type' => 'text' ),
			array( 'key' => 'field_es_cap_cta2_url', 'label' => 'Secondary button URL', 'name' => 'es_cap_cta2_url', 'type' => 'text' ),
			array(
				'key'          => 'field_es_cap_cards',
				'label'        => 'Cards',
				'name'         => 'es_cap_cards',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add card',
				'sub_fields'   => array(
					array( 'key' => 'field_es_cap_card_text', 'label' => 'Text', 'name' => 'text', 'type' => 'textarea', 'rows' => 2 ),
				),
			),

			/* ── FAQ ──────────────────────────────────────────── */
			array( 'key' => 'field_es_faq_tab', 'label' => 'Common Questions', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_es_faq_headline', 'label' => 'Headline', 'name' => 'es_faq_headline', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array(
				'key'          => 'field_es_faq_items',
				'label'        => 'Questions',
				'name'         => 'es_faq_items',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add question',
				'sub_fields'   => array(
					array( 'key' => 'field_es_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text' ),
					array( 'key' => 'field_es_faq_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'textarea', 'rows' => 3 ),
				),
			),

			/* ── News ─────────────────────────────────────────── */
			array( 'key' => 'field_es_news_tab', 'label' => 'News', 'type' => 'tab', 'placement' => 'top' ),
			array(
				'key'           => 'field_es_show_news',
				'label'         => 'Show the Latest Projects & News section',
				'name'          => 'es_show_news',
				'type'          => 'true_false',
				'ui'            => 1,
				'ui_on_text'    => 'Show',
				'ui_off_text'   => 'Hidden',
				'default_value' => 0,
				'instructions'  => 'Off by default at the client\'s request (Sep 2026) — they cannot commit to supporting this section yet. Nothing has been deleted: all of the content below is still here. Set to "Show" to bring the section back on this page.',
			),
			array( 'key' => 'field_es_news_eyebrow', 'label' => 'Eyebrow', 'name' => 'es_news_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_es_news_headline', 'label' => 'Headline', 'name' => 'es_news_headline', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_es_news_lead_tag', 'label' => 'Lead story tag', 'name' => 'es_news_lead_tag', 'type' => 'text' ),
			array( 'key' => 'field_es_news_lead_date', 'label' => 'Lead story date', 'name' => 'es_news_lead_date', 'type' => 'text' ),
			array( 'key' => 'field_es_news_lead_title', 'label' => 'Lead story title', 'name' => 'es_news_lead_title', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_es_news_lead_excerpt', 'label' => 'Lead story excerpt', 'name' => 'es_news_lead_excerpt', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_es_news_lead_image', 'label' => 'Lead story image', 'name' => 'es_news_lead_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
			array(
				'key'          => 'field_es_news_lead_stats',
				'label'        => 'Lead story stats',
				'name'         => 'es_news_lead_stats',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add stat',
				'sub_fields'   => array(
					array( 'key' => 'field_es_news_stat_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text' ),
					array( 'key' => 'field_es_news_stat_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
				),
			),
			array(
				'key'          => 'field_es_news_items',
				'label'        => 'Secondary stories',
				'name'         => 'es_news_items',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add story',
				'sub_fields'   => array(
					array( 'key' => 'field_es_news_item_tag', 'label' => 'Tag', 'name' => 'tag', 'type' => 'text' ),
					array( 'key' => 'field_es_news_item_date', 'label' => 'Date', 'name' => 'date', 'type' => 'text' ),
					array( 'key' => 'field_es_news_item_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_es_news_item_excerpt', 'label' => 'Excerpt', 'name' => 'excerpt', 'type' => 'textarea', 'rows' => 2 ),
					array( 'key' => 'field_es_news_item_image', 'label' => 'Thumbnail', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ),
				),
			),

			/* ── CTA band ─────────────────────────────────────── */
			array( 'key' => 'field_es_cta_tab', 'label' => 'CTA Band', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_es_cta_eyebrow', 'label' => 'Eyebrow', 'name' => 'es_cta_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_es_cta_headline', 'label' => 'Headline', 'name' => 'es_cta_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_es_cta_body', 'label' => 'Body', 'name' => 'es_cta_body', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_es_cta_button_label', 'label' => 'Button label', 'name' => 'es_cta_button_label', 'type' => 'text' ),
			array( 'key' => 'field_es_cta_button_url', 'label' => 'Button URL', 'name' => 'es_cta_button_url', 'type' => 'text' ),
			array( 'key' => 'field_es_cta_image', 'label' => 'Background image', 'name' => 'es_cta_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
			array( 'key' => 'field_es_cta_call_label', 'label' => 'Call card label', 'name' => 'es_cta_call_label', 'type' => 'text' ),
			array( 'key' => 'field_es_cta_call_number', 'label' => 'Call card number', 'name' => 'es_cta_call_number', 'type' => 'text' ),
			array( 'key' => 'field_es_cta_call_note', 'label' => 'Call card note', 'name' => 'es_cta_call_note', 'type' => 'text' ),

			/* ── Email signup ─────────────────────────────────── */
			array( 'key' => 'field_es_signup_tab', 'label' => 'Mailing List', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_es_signup_title', 'label' => 'Title', 'name' => 'es_signup_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_es_signup_sub', 'label' => 'Subtext', 'name' => 'es_signup_sub', 'type' => 'text' ),
		),
	)
);

/**
 * Machine Custom Solutions.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_mcs_content',
		'title'    => 'Machine Custom Solutions — Content',
		'location' => array(
			array(
				array( 'param' => 'post_name', 'operator' => '==', 'value' => 'modification-of-standard-machine-tools' ),
			),
		),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			/* ── Hero ─────────────────────────────────────────── */
			array( 'key' => 'field_mcs_hero_tab', 'label' => 'Hero', 'type' => 'tab', 'placement' => 'top' ),
			// NOTE: a 'Eyebrow' text field used to sit here (field_mcs_hero_eyebrow). It was
			// registered but no template ever rendered it, so filling it in silently did
			// nothing. Removed 2026-09-22. If the design ever gains a hero eyebrow for this
			// page, copy the ES hero eyebrow markup + field rather than re-adding a dead field.
			array( 'key' => 'field_mcs_hero_lead', 'label' => 'Headline lead (gray)', 'name' => 'mcs_hero_lead', 'type' => 'text', 'instructions' => 'e.g. Machine' ),
			array( 'key' => 'field_mcs_hero_main', 'label' => 'Headline main (primary)', 'name' => 'mcs_hero_main', 'type' => 'text', 'instructions' => 'e.g. Custom Solutions. Wrap words in &lt;em&gt;…&lt;/em&gt; to colour them with the accent colour below.' ),
			array( 'key' => 'field_mcs_hero_breadcrumb', 'label' => 'Breadcrumb — current page', 'name' => 'mcs_hero_breadcrumb', 'type' => 'text', 'instructions' => 'The last crumb in the hero breadcrumb. Default: “Machine Custom Solutions”.' ),
			array(
				'key'           => 'field_mcs_hero_accent_color',
				'label'         => 'Accent colour',
				'name'          => 'mcs_hero_accent_color',
				'type'          => 'select',
				'choices'       => array(
					'white'  => 'White (no highlight)',
					'haas'   => 'Haas Red',
					'orange' => 'Brand Orange',
				),
				// No default_value, same reasoning as the homepage hero: ACF injects a
				// default on read and a plain save would persist it. Blank keeps the design
				// colour via the template — see the page template.
				'default_value' => '',
				'allow_null'    => 1,
				'placeholder'   => 'Design default (Brand Orange)',
				'instructions'  => 'Colour of any &lt;em&gt; accent words in the headline lead and main above. Leave unset to keep the design colour; picking a colour overrides it for this page.',
			),
			array( 'key' => 'field_mcs_hero_image', 'label' => 'Background image', 'name' => 'mcs_hero_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Services grid ────────────────────────────────── */
			array( 'key' => 'field_mcs_grid_tab', 'label' => 'Services Grid', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_mcs_grid_eyebrow', 'label' => 'Eyebrow', 'name' => 'mcs_grid_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_mcs_grid_lead', 'label' => 'Title lead (gray)', 'name' => 'mcs_grid_lead', 'type' => 'text' ),
			array( 'key' => 'field_mcs_grid_main', 'label' => 'Title main (primary)', 'name' => 'mcs_grid_main', 'type' => 'text' ),
			array(
				'key'          => 'field_mcs_cards',
				'label'        => 'Service cards',
				'name'         => 'mcs_cards',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add card',
				'sub_fields'   => array(
					array( 'key' => 'field_mcs_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_mcs_card_image', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
					array(
						'key'           => 'field_mcs_card_video',
						'label'         => 'Video (optional)',
						'name'          => 'video',
						'type'          => 'file',
						'return_format' => 'array',
						'library'       => 'all',
						'mime_types'    => 'mp4,webm,mov',
						'instructions'  => 'Upload a short, silent clip to turn this card into a moving thumbnail. It plays muted, looping and inline, and the Image above is used as its poster frame. Leave empty to show the still image only. Keep clips small — they download with the page.',
					),
					array( 'key' => 'field_mcs_card_detail', 'label' => 'Detail (modal)', 'name' => 'detail', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'instructions' => 'Rich content shown in the card modal. A “Talk to an Engineer” button is added automatically.' ),
				),
			),

			/* ── Gallery ──────────────────────────────────────── */
			array( 'key' => 'field_mcs_gallery_tab', 'label' => 'Installed Gallery', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_mcs_gallery_eyebrow', 'label' => 'Eyebrow', 'name' => 'mcs_gallery_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_mcs_gallery_title', 'label' => 'Title', 'name' => 'mcs_gallery_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_mcs_gallery_body', 'label' => 'Body', 'name' => 'mcs_gallery_body', 'type' => 'textarea', 'rows' => 2 ),
			array(
				'key'          => 'field_mcs_collections',
				'label'        => 'Collections',
				'name'         => 'mcs_collections',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add collection',
				'sub_fields'   => array(
					array( 'key' => 'field_mcs_coll_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_mcs_coll_meta', 'label' => 'Meta', 'name' => 'meta', 'type' => 'text' ),
					array( 'key' => 'field_mcs_coll_media', 'label' => 'Media (one per line)', 'name' => 'media', 'type' => 'textarea', 'rows' => 6, 'instructions' => 'One per line: type | src | poster | alt | caption. type = image or video; poster only for video.' ),
				),
			),

			/* ── CTA band ─────────────────────────────────────── */
			array( 'key' => 'field_mcs_cta_tab', 'label' => 'CTA Band', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_mcs_cta_eyebrow', 'label' => 'Eyebrow', 'name' => 'mcs_cta_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_mcs_cta_headline', 'label' => 'Headline', 'name' => 'mcs_cta_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_mcs_cta_subhead', 'label' => 'Subhead', 'name' => 'mcs_cta_subhead', 'type' => 'text' ),
			array( 'key' => 'field_mcs_cta_body', 'label' => 'Body', 'name' => 'mcs_cta_body', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_mcs_cta_button_label', 'label' => 'Button label', 'name' => 'mcs_cta_button_label', 'type' => 'text' ),
			array( 'key' => 'field_mcs_cta_button_url', 'label' => 'Button URL', 'name' => 'mcs_cta_button_url', 'type' => 'text' ),
			array( 'key' => 'field_mcs_cta_image', 'label' => 'Background image', 'name' => 'mcs_cta_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
			array( 'key' => 'field_mcs_cta_call_label', 'label' => 'Call card label', 'name' => 'mcs_cta_call_label', 'type' => 'text' ),
			array( 'key' => 'field_mcs_cta_call_number', 'label' => 'Call card number', 'name' => 'mcs_cta_call_number', 'type' => 'text' ),
			array( 'key' => 'field_mcs_cta_call_note', 'label' => 'Call card note', 'name' => 'mcs_cta_call_note', 'type' => 'text' ),

			/* ── Email signup ─────────────────────────────────── */
			array( 'key' => 'field_mcs_signup_tab', 'label' => 'Mailing List', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_mcs_signup_title', 'label' => 'Title', 'name' => 'mcs_signup_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_mcs_signup_sub', 'label' => 'Subtext', 'name' => 'mcs_signup_sub', 'type' => 'text' ),
		),
	)
);

/**
 * Applications.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_application_content',
		'title'    => 'Applications — Content',
		'location' => array(
			array(
				array( 'param' => 'post_name', 'operator' => '==', 'value' => 'unique-applications-for-standard-machines' ),
			),
		),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			/* ── Hero ─────────────────────────────────────────── */
			array( 'key' => 'field_app_hero_tab', 'label' => 'Hero', 'type' => 'tab', 'placement' => 'top' ),
			// NOTE: a 'Eyebrow' text field used to sit here (field_app_hero_eyebrow). It was
			// registered but no template ever rendered it, so filling it in silently did
			// nothing. Removed 2026-09-22. If the design ever gains a hero eyebrow for this
			// page, copy the ES hero eyebrow markup + field rather than re-adding a dead field.
			array( 'key' => 'field_app_hero_headline', 'label' => 'Headline', 'name' => 'app_hero_headline', 'type' => 'textarea', 'rows' => 1, 'instructions' => 'Wrap the accent phrase in &lt;em&gt;…&lt;/em&gt;.' ),
			array( 'key' => 'field_app_hero_breadcrumb', 'label' => 'Breadcrumb — current page', 'name' => 'app_hero_breadcrumb', 'type' => 'text', 'instructions' => 'The last crumb in the hero breadcrumb. Default: “Applications”.' ),
			array(
				'key'           => 'field_app_hero_accent_color',
				'label'         => 'Accent colour',
				'name'          => 'app_hero_accent_color',
				'type'          => 'select',
				'choices'       => array(
					'white'  => 'White (no highlight)',
					'haas'   => 'Haas Red',
					'orange' => 'Brand Orange',
				),
				'default_value' => '',
				'allow_null'    => 1,
				'placeholder'   => 'Design default (Brand Orange)',
				'instructions'  => 'Colour of the <em>accent</em> word in the hero headline. Leave unset to keep the design colour; picking a colour overrides it for this page.',
			),
			array( 'key' => 'field_app_hero_image', 'label' => 'Background image', 'name' => 'app_hero_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Services grid ────────────────────────────────── */
			array( 'key' => 'field_app_grid_tab', 'label' => 'Services Grid', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_app_grid_eyebrow', 'label' => 'Eyebrow', 'name' => 'app_grid_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_app_grid_title', 'label' => 'Title', 'name' => 'app_grid_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array(
				'key'          => 'field_app_cards',
				'label'        => 'Service cards',
				'name'         => 'app_cards',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add card',
				'sub_fields'   => array(
					array( 'key' => 'field_app_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_app_card_image', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
					array( 'key' => 'field_app_card_detail', 'label' => 'Detail (modal)', 'name' => 'detail', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0 ),
				),
			),

			/* ── Gallery ──────────────────────────────────────── */
			array( 'key' => 'field_app_gallery_tab', 'label' => 'Product Gallery', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_app_gallery_title', 'label' => 'Title', 'name' => 'app_gallery_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array(
				'key'          => 'field_app_collections',
				'label'        => 'Collections',
				'name'         => 'app_collections',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add collection',
				'sub_fields'   => array(
					array( 'key' => 'field_app_coll_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_app_coll_meta', 'label' => 'Meta', 'name' => 'meta', 'type' => 'text' ),
					array( 'key' => 'field_app_coll_media', 'label' => 'Media (one per line)', 'name' => 'media', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'One per line: type | src | poster | alt | caption. type = image or video; poster only for video.' ),
				),
			),

			/* ── CTA band ─────────────────────────────────────── */
			array( 'key' => 'field_app_cta_tab', 'label' => 'CTA Band', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_app_cta_eyebrow', 'label' => 'Eyebrow', 'name' => 'app_cta_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_app_cta_headline', 'label' => 'Headline', 'name' => 'app_cta_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_app_cta_subhead', 'label' => 'Subhead', 'name' => 'app_cta_subhead', 'type' => 'text' ),
			array( 'key' => 'field_app_cta_body', 'label' => 'Body', 'name' => 'app_cta_body', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_app_cta_button_label', 'label' => 'Button label', 'name' => 'app_cta_button_label', 'type' => 'text' ),
			array( 'key' => 'field_app_cta_button_url', 'label' => 'Button URL', 'name' => 'app_cta_button_url', 'type' => 'text' ),
			array( 'key' => 'field_app_cta_image', 'label' => 'Background image', 'name' => 'app_cta_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
			array( 'key' => 'field_app_cta_call_label', 'label' => 'Call card label', 'name' => 'app_cta_call_label', 'type' => 'text' ),
			array( 'key' => 'field_app_cta_call_number', 'label' => 'Call card number', 'name' => 'app_cta_call_number', 'type' => 'text' ),
			array( 'key' => 'field_app_cta_call_note', 'label' => 'Call card note', 'name' => 'app_cta_call_note', 'type' => 'text' ),

			/* ── Email signup ─────────────────────────────────── */
			array( 'key' => 'field_app_signup_tab', 'label' => 'Mailing List', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_app_signup_title', 'label' => 'Title', 'name' => 'app_signup_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_app_signup_sub', 'label' => 'Subtext', 'name' => 'app_signup_sub', 'type' => 'text' ),
		),
	)
);

/**
 * Automation & Controls.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_automation_content',
		'title'    => 'Automation & Controls — Content',
		'location' => array(
			array(
				array( 'param' => 'post_name', 'operator' => '==', 'value' => 'automated-system' ),
			),
		),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			/* ── Hero ─────────────────────────────────────────── */
			array( 'key' => 'field_ai_hero_tab', 'label' => 'Hero', 'type' => 'tab', 'placement' => 'top' ),
			// NOTE: a 'Eyebrow' text field used to sit here (field_ai_hero_eyebrow). It was
			// registered but no template ever rendered it, so filling it in silently did
			// nothing. Removed 2026-09-22. If the design ever gains a hero eyebrow for this
			// page, copy the ES hero eyebrow markup + field rather than re-adding a dead field.
			array( 'key' => 'field_ai_hero_lead', 'label' => 'Headline lead (gray)', 'name' => 'ai_hero_lead', 'type' => 'text' ),
			array( 'key' => 'field_ai_hero_main', 'label' => 'Headline main (primary)', 'name' => 'ai_hero_main', 'type' => 'text' ),
			array( 'key' => 'field_ai_hero_breadcrumb', 'label' => 'Breadcrumb — current page', 'name' => 'ai_hero_breadcrumb', 'type' => 'text', 'instructions' => 'The last crumb in the hero breadcrumb. Default: “Automation and Controls Solutions”.' ),
			array(
				'key'           => 'field_ai_hero_accent_color',
				'label'         => 'Accent colour',
				'name'          => 'ai_hero_accent_color',
				'type'          => 'select',
				'choices'       => array(
					'white'  => 'White (no highlight)',
					'haas'   => 'Haas Red',
					'orange' => 'Brand Orange',
				),
				'default_value' => '',
				'allow_null'    => 1,
				'placeholder'   => 'Design default (Brand Orange)',
				'instructions'  => 'Colour of the <em>accent</em> word in the hero headline. Leave unset to keep the design colour; picking a colour overrides it for this page.',
			),
			array( 'key' => 'field_ai_hero_body', 'label' => 'Body', 'name' => 'ai_hero_body', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_ai_hero_image', 'label' => 'Background image', 'name' => 'ai_hero_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Services grid ────────────────────────────────── */
			array( 'key' => 'field_ai_grid_tab', 'label' => 'Services Grid', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_ai_grid_eyebrow', 'label' => 'Eyebrow', 'name' => 'ai_grid_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_ai_grid_title', 'label' => 'Title', 'name' => 'ai_grid_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array(
				'key'          => 'field_ai_cards',
				'label'        => 'Service cards',
				'name'         => 'ai_cards',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add card',
				'sub_fields'   => array(
					array( 'key' => 'field_ai_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_ai_card_image', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
					array( 'key' => 'field_ai_card_detail', 'label' => 'Detail (modal)', 'name' => 'detail', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0 ),
				),
			),

			/* ── Gallery ──────────────────────────────────────── */
			array( 'key' => 'field_ai_gallery_tab', 'label' => 'Installed Gallery', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_ai_gallery_eyebrow', 'label' => 'Eyebrow', 'name' => 'ai_gallery_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_ai_gallery_title', 'label' => 'Title', 'name' => 'ai_gallery_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array(
				'key'          => 'field_ai_collections',
				'label'        => 'Collections',
				'name'         => 'ai_collections',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add collection',
				'sub_fields'   => array(
					array( 'key' => 'field_ai_coll_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_ai_coll_meta', 'label' => 'Meta', 'name' => 'meta', 'type' => 'text' ),
					array( 'key' => 'field_ai_coll_media', 'label' => 'Media (one per line)', 'name' => 'media', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'One per line: type | src | poster | alt | caption. type = image or video; poster only for video.' ),
				),
			),

			/* ── CTA band ─────────────────────────────────────── */
			array( 'key' => 'field_ai_cta_tab', 'label' => 'CTA Band', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_ai_cta_eyebrow', 'label' => 'Eyebrow', 'name' => 'ai_cta_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_ai_cta_headline', 'label' => 'Headline', 'name' => 'ai_cta_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_ai_cta_subhead', 'label' => 'Subhead', 'name' => 'ai_cta_subhead', 'type' => 'text' ),
			array( 'key' => 'field_ai_cta_body', 'label' => 'Body', 'name' => 'ai_cta_body', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_ai_cta_button_label', 'label' => 'Button label', 'name' => 'ai_cta_button_label', 'type' => 'text' ),
			array( 'key' => 'field_ai_cta_button_url', 'label' => 'Button URL', 'name' => 'ai_cta_button_url', 'type' => 'text' ),
			array( 'key' => 'field_ai_cta_image', 'label' => 'Background image', 'name' => 'ai_cta_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
			array( 'key' => 'field_ai_cta_call_label', 'label' => 'Call card label', 'name' => 'ai_cta_call_label', 'type' => 'text' ),
			array( 'key' => 'field_ai_cta_call_number', 'label' => 'Call card number', 'name' => 'ai_cta_call_number', 'type' => 'text' ),
			array( 'key' => 'field_ai_cta_call_note', 'label' => 'Call card note', 'name' => 'ai_cta_call_note', 'type' => 'text' ),

			/* ── Email signup ─────────────────────────────────── */
			array( 'key' => 'field_ai_signup_tab', 'label' => 'Mailing List', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_ai_signup_title', 'label' => 'Title', 'name' => 'ai_signup_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_ai_signup_sub', 'label' => 'Subtext', 'name' => 'ai_signup_sub', 'type' => 'text' ),
		),
	)
);

/**
 * Global site content (options pages).
 *
 * One "Site Content" menu with four short screens instead of a single page
 * that stacked every global group and a dozen tabs. Values are stored by field
 * name under the 'option' post id, so which screen a group sits on is purely
 * an editing concern — moving a group never moves data.
 *
 *   Site Content
 *     ├── Header          gerotech-site-header   (group_site_header)
 *     ├── Menus           gerotech-site-menus    (group_site_navigation)
 *     ├── Footer          gerotech-site-footer   (group_site_footer)
 *     └── Shared Content  gerotech-site-shared   (testimonials, forms)
 *
 * The parent slug `gerotech-site-content` is kept so old bookmarks and the
 * journal's URLs still land somewhere (ACF redirects to the first screen).
 * Requires ACF Pro (acf_add_options_page).
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(
		array(
			'page_title' => 'Site Content',
			'menu_title' => 'Site Content',
			'menu_slug'  => 'gerotech-site-content',
			'capability' => 'edit_posts',
			'redirect'   => true,
			'icon_url'   => 'dashicons-layout',
			'position'   => 3,
		)
	);

	foreach ( gerotech_site_content_screens() as $slug => $screen ) {
		acf_add_options_sub_page(
			array(
				'page_title'  => 'Site Content — ' . $screen['title'],
				'menu_title'  => $screen['title'],
				'menu_slug'   => $slug,
				'parent_slug' => 'gerotech-site-content',
				'capability'  => 'edit_posts',
			)
		);
	}
}

/**
 * Testimonials (global — shared across homepage + Engineered Solutions pages).
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_site_testimonials',
		'title'    => 'Testimonials',
		'location' => gerotech_options_location( 'gerotech-site-shared' ),
		'menu_order' => 10,
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(
			array( 'key' => 'field_testimonials_eyebrow', 'label' => 'Eyebrow', 'name' => 'testimonials_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_testimonials_title', 'label' => 'Title', 'name' => 'testimonials_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => 'Wrap the accent phrase in &lt;em&gt;…&lt;/em&gt;.' ),
			array(
				'key'          => 'field_testimonials',
				'label'        => 'Testimonials',
				'name'         => 'testimonials',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add testimonial',
				'sub_fields'   => array(
					array( 'key' => 'field_testimonial_quote', 'label' => 'Quote', 'name' => 'quote', 'type' => 'textarea', 'rows' => 4 ),
					array( 'key' => 'field_testimonial_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
					array( 'key' => 'field_testimonial_sub', 'label' => 'Subtitle', 'name' => 'sub', 'type' => 'text' ),
				),
			),
		),
	)
);

/**
 * Careers.
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_careers_content',
		'title'    => 'Careers — Content',
		'location' => array(
			array(
				array( 'param' => 'post_name', 'operator' => '==', 'value' => 'careers' ),
			),
		),
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(

			/* ── Hero ─────────────────────────────────────────── */
			array( 'key' => 'field_careers_hero_tab', 'label' => 'Hero', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_careers_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'careers_hero_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_careers_hero_headline', 'label' => 'Headline', 'name' => 'careers_hero_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => 'Wrap the accent phrase in &lt;em&gt;…&lt;/em&gt;.' ),
			array(
				'key'           => 'field_careers_hero_accent_color',
				'label'         => 'Accent colour',
				'name'          => 'careers_hero_accent_color',
				'type'          => 'select',
				'choices'       => array(
					'white'  => 'White (no highlight)',
					'haas'   => 'Haas Red',
					'orange' => 'Brand Orange',
				),
				'default_value' => '',
				'allow_null'    => 1,
				'placeholder'   => 'Design default (Brand Orange)',
				'instructions'  => 'Colour of the <em>accent</em> word in the hero headline. Leave unset to keep the design colour; picking a colour overrides it for this page.',
			),
			array( 'key' => 'field_careers_hero_body', 'label' => 'Body', 'name' => 'careers_hero_body', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_careers_hero_cta_label', 'label' => 'Button label', 'name' => 'careers_hero_cta_label', 'type' => 'text' ),
			array( 'key' => 'field_careers_hero_cta_url', 'label' => 'Button URL', 'name' => 'careers_hero_cta_url', 'type' => 'text' ),
			array(
				'key'           => 'field_careers_hero_cta_color',
				'label'         => 'Button colour',
				'name'          => 'careers_hero_cta_color',
				'type'          => 'select',
				'choices'       => array(
					'orange' => 'Brand Orange',
					'haas'   => 'Haas Red',
					'white'  => 'White outline',
				),
				'default_value' => '',
				'allow_null'    => 1,
				'placeholder'   => 'Design default (Brand Orange)',
				'instructions'  => 'Colour of the hero button. Leave unset to keep the design colour; picking a colour overrides it for this page.',
			),
			array( 'key' => 'field_careers_hero_image', 'label' => 'Background image', 'name' => 'careers_hero_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
			array(
				'key'          => 'field_careers_hero_stats',
				'label'        => 'Hero facts',
				'name'         => 'careers_hero_stats',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add fact',
				'sub_fields'   => array(
					array( 'key' => 'field_careers_stat_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text' ),
					array( 'key' => 'field_careers_stat_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
				),
			),

			/* ── Culture ──────────────────────────────────────── */
			array( 'key' => 'field_careers_culture_tab', 'label' => 'Culture', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_careers_culture_eyebrow', 'label' => 'Eyebrow', 'name' => 'careers_culture_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_careers_culture_headline', 'label' => 'Headline', 'name' => 'careers_culture_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_careers_culture_body', 'label' => 'Body', 'name' => 'careers_culture_body', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_careers_culture_body2', 'label' => 'Body (second paragraph)', 'name' => 'careers_culture_body2', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_careers_culture_cta_label', 'label' => 'Button label', 'name' => 'careers_culture_cta_label', 'type' => 'text' ),
			array( 'key' => 'field_careers_culture_cta_url', 'label' => 'Button URL', 'name' => 'careers_culture_cta_url', 'type' => 'text' ),
			array( 'key' => 'field_careers_culture_image', 'label' => 'Image', 'name' => 'careers_culture_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Open positions ───────────────────────────────── */
			array( 'key' => 'field_careers_positions_tab', 'label' => 'Open Positions', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_careers_positions_eyebrow', 'label' => 'Eyebrow', 'name' => 'careers_positions_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_careers_positions_title', 'label' => 'Title', 'name' => 'careers_positions_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			// Table column headers — these were hardcoded in page-careers.php.
			array( 'key' => 'field_careers_col_job', 'label' => 'Column — job title', 'name' => 'careers_col_job', 'type' => 'text', 'instructions' => 'Default: “Job Title”.' ),
			array( 'key' => 'field_careers_col_location', 'label' => 'Column — location', 'name' => 'careers_col_location', 'type' => 'text', 'instructions' => 'Default: “Location”.' ),
			array( 'key' => 'field_careers_col_department', 'label' => 'Column — department', 'name' => 'careers_col_department', 'type' => 'text', 'instructions' => 'Default: “Department”.' ),
			array( 'key' => 'field_careers_col_date', 'label' => 'Column — post date', 'name' => 'careers_col_date', 'type' => 'text', 'instructions' => 'Default: “Post Date”.' ),
			array(
				'key'          => 'field_careers_positions',
				'label'        => 'Positions',
				'name'         => 'careers_positions',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add position',
				'sub_fields'   => array(
					array( 'key' => 'field_careers_pos_title', 'label' => 'Job title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_careers_pos_url', 'label' => 'Application URL', 'name' => 'url', 'type' => 'text', 'instructions' => 'mailto: or ATS URL.' ),
					array( 'key' => 'field_careers_pos_location', 'label' => 'Location', 'name' => 'location', 'type' => 'text' ),
					array( 'key' => 'field_careers_pos_dept', 'label' => 'Department', 'name' => 'department', 'type' => 'text' ),
					array( 'key' => 'field_careers_pos_date', 'label' => 'Post date', 'name' => 'date', 'type' => 'text' ),
				),
			),

			/* ── Benefits ─────────────────────────────────────── */
			array( 'key' => 'field_careers_benefits_tab', 'label' => 'Benefits', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_careers_benefits_eyebrow', 'label' => 'Eyebrow', 'name' => 'careers_benefits_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_careers_benefits_title', 'label' => 'Title', 'name' => 'careers_benefits_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_careers_benefits_body', 'label' => 'Body', 'name' => 'careers_benefits_body', 'type' => 'textarea', 'rows' => 3 ),
			array(
				'key'          => 'field_careers_benefits',
				'label'        => 'Benefit cards',
				'name'         => 'careers_benefits',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add card',
				'sub_fields'   => array(
					array( 'key' => 'field_careers_benefit_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
					array( 'key' => 'field_careers_benefit_body', 'label' => 'Body', 'name' => 'body', 'type' => 'textarea', 'rows' => 2 ),
				),
			),
			array( 'key' => 'field_careers_benefits_note', 'label' => 'Note', 'name' => 'careers_benefits_note', 'type' => 'textarea', 'rows' => 2 ),

			/* ── CTA band ─────────────────────────────────────── */
			array( 'key' => 'field_careers_cta_tab', 'label' => 'CTA Band', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_careers_cta_eyebrow', 'label' => 'Eyebrow', 'name' => 'careers_cta_eyebrow', 'type' => 'text' ),
			array( 'key' => 'field_careers_cta_headline', 'label' => 'Headline', 'name' => 'careers_cta_headline', 'type' => 'textarea', 'rows' => 2, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_careers_cta_body', 'label' => 'Body', 'name' => 'careers_cta_body', 'type' => 'textarea', 'rows' => 2 ),
			array( 'key' => 'field_careers_cta_primary_label', 'label' => 'Primary button label', 'name' => 'careers_cta_primary_label', 'type' => 'text' ),
			array( 'key' => 'field_careers_cta_primary_url', 'label' => 'Primary button URL', 'name' => 'careers_cta_primary_url', 'type' => 'text' ),
			array( 'key' => 'field_careers_cta_secondary_label', 'label' => 'Secondary button label', 'name' => 'careers_cta_secondary_label', 'type' => 'text' ),
			array( 'key' => 'field_careers_cta_secondary_url', 'label' => 'Secondary button URL', 'name' => 'careers_cta_secondary_url', 'type' => 'text' ),
			array( 'key' => 'field_careers_cta_image', 'label' => 'Background image', 'name' => 'careers_cta_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),

			/* ── Mailing list ─────────────────────────────────── */
			array( 'key' => 'field_careers_signup_tab', 'label' => 'Mailing List', 'type' => 'tab', 'placement' => 'top' ),
			array( 'key' => 'field_careers_signup_title', 'label' => 'Title', 'name' => 'careers_signup_title', 'type' => 'textarea', 'rows' => 1, 'instructions' => '&lt;em&gt; accent supported.' ),
			array( 'key' => 'field_careers_signup_sub', 'label' => 'Subtext', 'name' => 'careers_signup_sub', 'type' => 'text' ),
		),
	)
);

/**
 * Site Content — Forms.
 *
 * The mailing-list signup form is repeated in six templates (homepage, ES, MCS,
 * Applications, Automation, Careers). Its label / placeholder / button text were
 * hardcoded in each one, so they are global fields on the same options page.
 * Defaults live in the templates, NOT in `default_value` — ACF injects a default
 * on read and a plain save would then persist it (see the hero accent colour note).
 */
acf_add_local_field_group(
	array(
		'key'      => 'group_site_forms',
		'title'    => 'Mailing-list Signup Form',
		'location' => gerotech_options_location( 'gerotech-site-shared' ),
		'menu_order' => 20,
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => array(
			array( 'key' => 'field_signup_email_label', 'label' => 'Signup — email field label', 'name' => 'signup_email_label', 'type' => 'text', 'instructions' => 'Visually hidden; read by screen readers. Default: “Email address”.' ),
			array( 'key' => 'field_signup_email_placeholder', 'label' => 'Signup — email placeholder', 'name' => 'signup_email_placeholder', 'type' => 'text', 'instructions' => 'Default: “your@email.com”.' ),
			array( 'key' => 'field_signup_submit_label', 'label' => 'Signup — button label', 'name' => 'signup_submit_label', 'type' => 'text', 'instructions' => 'Default: “Sign Up”.' ),
		),
	)
);

/**
 * Shared UI copy — service-card cue/button and breadcrumb labels.
 *
 * These strings were hardcoded in the rebuilt page templates. They read the same
 * everywhere the components appear, so they live once on Site Content →
 * Shared Content. Defaults live in the templates / gerotech_shared_ui_defaults(),
 * never in `default_value` (ACF injects a default on read; a plain save persists it).
 */
acf_add_local_field_group(
	array(
		'key'        => 'group_site_shared_ui',
		'title'      => 'Cards & Breadcrumbs',
		'location'   => gerotech_options_location( 'gerotech-site-shared' ),
		'menu_order' => 30,
		'position'   => 'normal',
		'style'      => 'default',
		'fields'     => array(
			array( 'key' => 'field_card_cue_label', 'label' => 'Service card — link cue', 'name' => 'card_cue_label', 'type' => 'text', 'instructions' => 'The small cue on every service card. The arrow is drawn by CSS. Default: “View Details”.' ),
			array( 'key' => 'field_card_cta_label', 'label' => 'Service card — button label', 'name' => 'card_cta_label', 'type' => 'text', 'instructions' => 'The button inside a service card’s pop-up. Default: “Talk to an Engineer”.' ),
			array( 'key' => 'field_card_cta_url', 'label' => 'Service card — button link', 'name' => 'card_cta_url', 'type' => 'text', 'instructions' => 'Where the card pop-up button points. Default: the quote email.' ),
			array( 'key' => 'field_breadcrumb_home_label', 'label' => 'Breadcrumb — first item', 'name' => 'breadcrumb_home_label', 'type' => 'text', 'instructions' => 'Default: “Home”.' ),
			array( 'key' => 'field_breadcrumb_engineered_label', 'label' => 'Breadcrumb — second item', 'name' => 'breadcrumb_engineered_label', 'type' => 'text', 'instructions' => 'Default: “Engineered Solutions”.' ),
		),
	)
);
