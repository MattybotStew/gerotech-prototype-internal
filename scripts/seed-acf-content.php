<?php
/**
 * Seed the content fields that were newly made ACF-editable (2026-09-22).
 *
 * WHY THIS EXISTS
 * ---------------
 * The templates carry these strings as PHP defaults, so the front end is correct
 * with NO database changes at all. But a blank ACF text field shows as empty in
 * the editor, which makes editors think the content is missing. So — matching how
 * the 2026-09-18 seeding worked — we materialise the current copy into the DB once.
 *
 * Safeness:
 *   - Idempotent. Writes only when nothing is stored yet, so re-running never
 *     clobbers an editor's changes.
 *   - Environment-agnostic. Pages are looked up by slug, never by ID.
 *   - Deliberately does NOT seed any *_hero_accent_color select. Those are blank
 *     by design ("blank = keep the design colour"), which is also how Local and
 *     Dev already store them. Seeding one would freeze the design colour.
 *
 * Run:  wp eval-file scripts/seed-acf-content.php
 *
 * @package GerotechChild
 */

if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

/**
 * Set a field only when it holds nothing yet.
 *
 * @param string $selector Field key or name.
 * @param mixed  $value    Value to store.
 * @param mixed  $post_id  Post ID or 'option'.
 * @param string $label    Human label for the log.
 * @return bool True when written.
 */
function gerotech_seed_once( $selector, $value, $post_id, $label ) {
	$current = get_field( $selector, $post_id );
	if ( null !== $current && '' !== $current && false !== $current && ! ( is_array( $current ) && empty( $current ) ) ) {
		echo "  skip   {$label} (already set)\n";
		return false;
	}
	update_field( $selector, $value, $post_id );
	echo "  set    {$label}\n";
	return true;
}

/**
 * Look up a page ID by slug, or 0.
 *
 * @param string $slug Page slug.
 * @return int
 */
function gerotech_seed_page( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? (int) $page->ID : 0;
}

/**
 * Build a value in ACF's `link` field storage shape.
 *
 * ACF stores a link as title/url/target whether the editor picked a page from
 * the picker or typed a URL — a picked page is stored as its permalink, so this
 * is exactly what the admin UI writes. (It also means a renamed page leaves a
 * stale URL behind, which is why the nav fields tell editors to re-pick.)
 *
 * @param string $title   Link text.
 * @param string $url     Destination.
 * @param bool   $new_tab Open in a new tab.
 * @return array
 */
function gerotech_seed_link_value( $title, $url, $new_tab = false ) {
	return array(
		'title'  => (string) $title,
		'url'    => (string) $url,
		'target' => $new_tab ? '_blank' : '',
	);
}

/**
 * Convert one normalised link row (inc/global-content.php shape) to ACF.
 *
 * Use this for a bare *link* field. A repeater whose sub-fields are a plain
 * `label` text plus a `url` link needs gerotech_seed_link_repeater_rows()
 * instead: passing a link value straight into that repeater leaves the label
 * blank, which is the exact "the editor control is empty" failure the audit
 * is meant to catch.
 *
 * @param array $row Row with label/url/new_tab.
 * @return array
 */
function gerotech_seed_link_row( $row ) {
	return gerotech_seed_link_value( $row['label'], $row['url'], ! empty( $row['new_tab'] ) );
}

/**
 * Convert normalised link rows to a repeater with `label` + `url` sub-fields.
 *
 * @param array $rows Normalised rows.
 * @return array
 */
function gerotech_seed_link_repeater_rows( $rows ) {
	$out = array();
	foreach ( (array) $rows as $row ) {
		if ( ! isset( $row['label'] ) || '' === trim( (string) $row['label'] ) ) {
			continue;
		}
		$out[] = array(
			'label'   => (string) $row['label'],
			'url'     => gerotech_seed_link_row( $row ),
			'new_tab' => ! empty( $row['new_tab'] ),
			// Only the Engineered Solutions service links have this sub-field;
			// ACF ignores an unknown key, so it is safe to always include it.
			'mobile_label' => isset( $row['mobile_label'] ) ? (string) $row['mobile_label'] : '',
		);
	}
	return $out;
}

echo "Seeding newly ACF-editable content…\n\n";

/* ── Homepage CTA call card ───────────────────────────────────── */
$home_id = (int) get_option( 'page_on_front' );
if ( $home_id ) {
	echo "Homepage (ID {$home_id}):\n";
	gerotech_seed_once( 'field_home_cta_call_label', 'Prefer to talk it through?', $home_id, 'CTA call label' );
	gerotech_seed_once( 'field_home_cta_call_number', '(734) 379-7788', $home_id, 'CTA call number' );
	gerotech_seed_once( 'field_home_cta_call_note', 'Talk to a person, not a form.', $home_id, 'CTA call note' );
	echo "\n";
}

/* ── Machine Custom Solutions hero ────────────────────────────── */
$mcs_id = gerotech_seed_page( 'modification-of-standard-machine-tools' );
if ( $mcs_id ) {
	echo "Machine Custom Solutions (ID {$mcs_id}):\n";
	gerotech_seed_once( 'field_mcs_hero_lead', 'Machine', $mcs_id, 'hero lead' );
	// The <em> is what colours "Solutions" orange (blank accent colour = Brand Orange).
	gerotech_seed_once( 'field_mcs_hero_main', 'Custom <em>Solutions</em>', $mcs_id, 'hero main (accent on Solutions)' );
	echo "\n";
}

/* ── Service page ─────────────────────────────────────────────── */
$service_id = gerotech_seed_page( 'service' );
if ( $service_id ) {
	echo "Service (ID {$service_id}):\n";

	foreach ( array(
		'field_service_tab_label_service' => array( 'Submit a Service Request', 'tab label — service request' ),
		'field_service_tab_label_general' => array( 'General Service Inquiry', 'tab label — general inquiry' ),
		'field_service_tab_label_parts'   => array( 'Parts Order', 'tab label — parts order' ),
		'field_service_tab_label_rotary'  => array( 'Rotary Repair', 'tab label — rotary repair' ),
		'field_service_tab_label_plan'    => array( 'Preventive Maintenance', 'tab label — preventive maintenance' ),
		'field_service_tab_label_support' => array( 'Application Support', 'tab label — application support' ),
	) as $key => $data ) {
		gerotech_seed_once( $key, $data[0], $service_id, $data[1] );
	}

	gerotech_seed_once( 'field_service_plan_inspect_title', 'Planned Maintenance Inspection Items', $service_id, 'checklist heading' );
	gerotech_seed_once(
		'field_service_plan_inspect_intro',
		'Typical inspection items are noted below. Planned maintenance plans are available for all Haas machines. Additional service, repairs, and parts for repairs are scheduled with the HFO and are subject to standard service rates. <b>The Planned Maintenance Service provides a list of necessary repairs and possible parts needed for future maintenance.</b>',
		$service_id,
		'checklist intro'
	);
	gerotech_seed_once( 'field_service_plan_inspect_footnote', '* if applicable', $service_id, 'checklist footnote' );
	gerotech_seed_once( 'field_service_plan_optional_title', 'Optional Special Services', $service_id, 'optional services heading' );
	gerotech_seed_once( 'field_service_plan_cta_title', 'Request a Planned Maintenance Plan', $service_id, 'request heading' );
	gerotech_seed_once( 'field_service_plan_cta_body', 'Please use the form below or call (248) 476-8787.', $service_id, 'request body' );
	gerotech_seed_once(
		'field_service_support_note',
		'Please use the form below to contact our applications engineering department.',
		$service_id,
		'application support note'
	);
	gerotech_seed_once( 'field_service_locations_title', 'Locations', $service_id, 'locations heading' );

	gerotech_seed_once(
		'field_service_plan_inspect_groups',
		array(
			array( 'heading' => 'Electrical System', 'column' => 'left', 'items' => "Check incoming voltage\nDC buss voltage\nLogic voltages\nCondition of wires and connections\nEnsure fans are working\nCheck regen resistors\nCheck vector drive\nCheck transformers\nCheck cabinet filter\nCheck motor connections and brushes*" ),
			array( 'heading' => 'Operator Panel', 'column' => 'left', 'items' => "Condition of keypad\nFunction of keys, buttons, remote handle jog*\nCondition of floppy drive*\nAdjust CRT if needed\nCheck door rollers, switches, rails\nOperation of chip auger/conveyor" ),
			array( 'heading' => 'Wipers/Seals/Windows/Bellows', 'column' => 'left', 'items' => 'Check if in good working condition' ),
			array( 'heading' => 'Geometry', 'column' => 'left', 'items' => "Check level\nCheck backlash in axis’ with ballscrews\nComplete an alignment report" ),
			array( 'heading' => 'Pneumatic System', 'column' => 'left', 'items' => "Check filters*\nCheck hoses and fittings\nCheck for leaks\nCheck pressure switch" ),
			array( 'heading' => 'Way Lube System', 'column' => 'right', 'items' => "Inspect filters\nInspect lines and fittings\nCheck proper pump operation" ),
			array( 'heading' => 'Spindle/Transmission', 'column' => 'right', 'items' => "Check transmission oil\nCondition of belts\nCondition of air lube lines\nCondition of spindle taper or chuck" ),
			array( 'heading' => 'Hydraulic Power Unit*', 'column' => 'right', 'items' => "Check oil level and condition of oil\nCheck for leak\nCheck max pressure\nCheck low pressure switch\nCheck gauges\nCheck that filter has been changed" ),
			array( 'heading' => 'Coolant', 'column' => 'right', 'items' => "Check condition of hoses\nCheck for leaks\nCoolant pump and filters\nP-cool operation" ),
			array( 'heading' => 'Counterbalance*', 'column' => 'right', 'items' => "Check hoses and fill if necessary\nCheck chains, rollers, and weight" ),
		),
		$service_id,
		'inspection checklist (10 groups)'
	);
	gerotech_seed_once(
		'field_service_plan_optional_groups',
		array(
			array( 'heading' => 'Through Spindle Coolant (mills) High Pressure Coolant (lathes)', 'column' => 'left', 'items' => 'Check pre-charge pressure, hoses, pressure at pump, seal housing, and filters.' ),
			array( 'heading' => 'Pallet Changer or Parts Loader', 'column' => 'left', 'items' => 'Check for wear on rollers, status of switches, alignment to machine, condition of bumpers, and remote operator panel.' ),
			array( 'heading' => 'Bar Feed (Haas brand only)', 'column' => 'left', 'items' => 'Check alignment, switches, and repeatability.' ),
			array( 'heading' => 'Vibration Analyzer Plot', 'column' => 'right', 'items' => 'Verifies machine vibration against established criteria. Isolates potential problems while still manageable.' ),
			array( 'heading' => 'Ball Bar Plot', 'column' => 'right', 'items' => 'Used as a diagnostic tool, the ball bar tests circularity and verifies the positioning accuracy and repeatability of your machine tool. Only available on mills at this time.' ),
			array( 'heading' => 'Probe Calibration', 'column' => 'right', 'items' => 'By recalibrating your Visual Quick Code Probing System, you ensure the integrity of your measuring system.' ),
		),
		$service_id,
		'optional special services (6 groups)'
	);
	gerotech_seed_once(
		'field_service_locations',
		array(
			array( 'anchor' => 'location_grand_rapids', 'name' => 'Grand Rapids, MI', 'address' => '2716 Courier Court NW, Grand Rapids, MI 49544', 'phone' => '616-735-1100', 'fax' => '616-735-0776' ),
			array( 'anchor' => 'location_flat_rock', 'name' => 'Flat Rock, MI', 'address' => '29220 Commerce Drive, Flat Rock, MI 48134', 'phone' => '734-379-7788', 'fax' => '734-379-2244' ),
		),
		$service_id,
		'office locations (2)'
	);
	echo "\n";
} else {
	echo "Service page not found — skipped.\n\n";
}

/* ── Careers column headers ───────────────────────────────────── */
$careers_id = gerotech_seed_page( 'careers' );
if ( $careers_id ) {
	echo "Careers (ID {$careers_id}):\n";
	gerotech_seed_once( 'field_careers_col_job', 'Job Title', $careers_id, 'column — job title' );
	gerotech_seed_once( 'field_careers_col_location', 'Location', $careers_id, 'column — location' );
	gerotech_seed_once( 'field_careers_col_department', 'Department', $careers_id, 'column — department' );
	gerotech_seed_once( 'field_careers_col_date', 'Post Date', $careers_id, 'column — post date' );
	echo "\n";
}

/* ── Rotary Repair ────────────────────────────────────────────── */
$rotary_id = gerotech_seed_page( 'rotary-repair' );
if ( $rotary_id ) {
	echo "Rotary Repair (ID {$rotary_id}):\n";
	gerotech_seed_once( 'field_rotary_hero_title', 'ROTARY REPAIR', $rotary_id, 'hero title' );
	gerotech_seed_once( 'field_rotary_hero_subtitle', 'Gerotech offers world class repair of your HAAS rotary table.', $rotary_id, 'hero subtitle' );
	gerotech_seed_once( 'field_rotary_hero_subtitle_mobile', 'We are here to help.', $rotary_id, 'hero subtitle (mobile)' );
	gerotech_seed_once(
		'field_rotary_intro',
		'Gerotech provides Haas certified rotary and indexer repair capabilities.  With more than 15 years of experience, our experts will provide comprehensive and quality service for all of your rotary maintenance needs.  Fill out the form below to contact the repair experts at Gerotech.',
		$rotary_id,
		'intro'
	);
	gerotech_seed_once( 'field_rotary_form_title', 'Request for Return Authorization', $rotary_id, 'form heading' );
	gerotech_seed_once( 'field_rotary_form_body', 'Please complete as much information as possible. You will be contacted with a repair authorization number that must be attached to the indexer/rotary unit before it is shipped for repair.', $rotary_id, 'form intro' );
	gerotech_seed_once( 'field_rotary_cta_text', 'Put our engineers to work on your project', $rotary_id, 'CTA text' );
	gerotech_seed_once( 'field_rotary_cta_label', 'Engage with us today', $rotary_id, 'CTA button label' );
	// The template default is computed, not a literal, so match it exactly.
	gerotech_seed_once( 'field_rotary_cta_url', gerotech_page_url( 'contact' ), $rotary_id, 'CTA button URL' );
	echo "\n";
}

/* ── Simple page titles ───────────────────────────────────────── */
$titles = array(
	'contact'  => array( 'field_contact_page_title', 'Contact', 'contact form heading', 'field_contact_form_title', 'Contact Form' ),
	'training' => array( 'field_training_page_title', 'Training', null, null ),
	'about'    => array( 'field_about_page_title', 'About', null, null ),
);
foreach ( $titles as $slug => $spec ) {
	$pid = gerotech_seed_page( $slug );
	if ( ! $pid ) {
		echo ucfirst( $slug ) . " page not found — skipped.\n";
		continue;
	}
	echo ucfirst( $slug ) . " (ID {$pid}):\n";
	gerotech_seed_once( $spec[0], $spec[1], $pid, 'page title' );
	if ( $spec[2] ) {
		gerotech_seed_once( $spec[3], $spec[4], $pid, $spec[2] );
	}
	echo "\n";
}

/* ── Global forms (options) ───────────────────────────────────── */
echo "Site Content — Forms (options):\n";
gerotech_seed_once( 'field_signup_email_label', 'Email address', 'option', 'signup email label' );
gerotech_seed_once( 'field_signup_email_placeholder', 'your@email.com', 'option', 'signup email placeholder' );
gerotech_seed_once( 'field_signup_submit_label', 'Sign Up', 'option', 'signup button label' );
echo "\n";

/* ── Global header / navigation / footer (options) ────────────────
 *
 * Seeded from the theme's own default arrays (inc/global-content.php) rather
 * than from a second copy of the copy, so the database and the code fallback
 * can never disagree about what the site says today.
 */
echo "Site Content — Header (options):\n";
$header_default = gerotech_header_defaults();

$banner_rows = array();
foreach ( $header_default['banner_items'] as $banner ) {
	$banner_rows[] = array(
		'label' => $banner['label'],
		'value' => $banner['value'],
		// Left empty on purpose: the tel: link is derived from the number.
		'url'   => '',
	);
}
gerotech_seed_once( 'field_header_banner_items', $banner_rows, 'option', 'alert banner items (3)' );
gerotech_seed_once( 'field_header_logo_alt', $header_default['logo_alt'], 'option', 'logo alt text' );
gerotech_seed_once( 'field_header_cta_label', $header_default['cta_label'], 'option', 'header button label' );
gerotech_seed_once( 'field_header_cta_url', gerotech_seed_link_value( $header_default['cta_label'], gerotech_page_url( 'contact' ) ), 'option', 'header button link' );
gerotech_seed_once( 'field_header_search_title', $header_default['search_title'], 'option', 'search modal heading' );
gerotech_seed_once( 'field_header_search_hint', $header_default['search_hint'], 'option', 'search modal hint' );
gerotech_seed_once( 'field_header_search_placeholder', $header_default['search_placeholder'], 'option', 'search input placeholder' );
gerotech_seed_once( 'field_header_search_links', gerotech_seed_link_repeater_rows( $header_default['search_links'] ), 'option', 'search quick links (' . count( $header_default['search_links'] ) . ')' );
echo "\n";

echo "Site Content — Navigation (options):\n";
$nav_defaults = gerotech_nav_defaults();
$nav_rows     = array();
foreach ( $nav_defaults as $item ) {
	$sub_rows = array();
	foreach ( $item['links'] as $link ) {
		$sub_rows[] = array(
			'label'   => $link['label'],
			'url'     => gerotech_seed_link_row( $link ),
			'new_tab' => ! empty( $link['new_tab'] ),
		);
	}

	$nav_rows[] = array(
		'label'       => $item['label'],
		'url'         => gerotech_seed_link_row( $item ),
		'new_tab'     => ! empty( $item['new_tab'] ),
		'style'       => $item['style'],
		'show_mobile' => ! empty( $item['show_mobile'] ),
		'links'       => $sub_rows,
	);
}
gerotech_seed_once( 'field_nav_items', $nav_rows, 'option', 'main menu (' . count( $nav_rows ) . ' items)' );

$machines_default = gerotech_machines_defaults();
$machine_rows     = array();
foreach ( $machines_default['groups'] as $group ) {
	$link_rows = array();
	foreach ( $group['links'] as $link ) {
		$link_rows[] = array(
			'label'   => $link['label'],
			'url'     => gerotech_seed_link_row( $link ),
			'new_tab' => ! empty( $link['new_tab'] ),
		);
	}

	$machine_rows[] = array(
		'column'       => (string) $group['column'],
		'title'        => $group['title'],
		'mobile_order' => (string) $group['mobile_order'],
		'links'        => $link_rows,
	);
}
gerotech_seed_once( 'field_nav_machines_groups', $machine_rows, 'option', 'machines panel — machine groups (' . count( $machine_rows ) . ')' );
gerotech_seed_once( 'field_nav_machines_help_title', $machines_default['help_title'], 'option', 'machines panel — help card title' );
gerotech_seed_once( 'field_nav_machines_help_label', $machines_default['help_label'], 'option', 'machines panel — help card button' );
gerotech_seed_once( 'field_nav_machines_help_url', gerotech_seed_link_value( $machines_default['help_label'], $machines_default['help_url'] ), 'option', 'machines panel — help card link' );
gerotech_seed_once( 'field_nav_machines_footer_label', $machines_default['footer_label'], 'option', 'machines panel — full catalog link' );
gerotech_seed_once( 'field_nav_machines_footer_mobile_label', $machines_default['footer_mobile_label'], 'option', 'machines panel — full catalog link, mobile wording' );
gerotech_seed_once( 'field_nav_machines_footer_url', gerotech_seed_link_value( $machines_default['footer_label'], $machines_default['footer_url'], $machines_default['footer_new_tab'] ), 'option', 'machines panel — full catalog URL' );
gerotech_seed_once( 'field_nav_machines_footer_new_tab', 1, 'option', 'machines panel — full catalog opens in new tab' );

$es_default = gerotech_es_defaults();
$cat_rows   = array();
foreach ( $es_default['categories'] as $category ) {
	$label = '' !== trim( $category['lead'] ) ? trim( $category['lead'] . ' ' . $category['main'] ) : $category['main'];
	$cat_rows[] = array(
		'heading_lead' => $category['lead'],
		'heading_main' => $category['main'],
		'url'          => gerotech_seed_link_value( $label, $category['url'] ),
		'description'  => $category['description'],
		'last'         => ! empty( $category['last'] ),
	);
}
gerotech_seed_once( 'field_nav_es_col1_title', $es_default['col1_title'], 'option', 'ES panel — first column heading' );
gerotech_seed_once( 'field_nav_es_categories', $cat_rows, 'option', 'ES panel — categories (' . count( $cat_rows ) . ')' );
gerotech_seed_once( 'field_nav_es_cta_label', $es_default['cta_label'], 'option', 'ES panel — column button label' );
gerotech_seed_once( 'field_nav_es_cta_mobile_label', $es_default['cta_mobile_label'], 'option', 'ES panel — column button label, mobile wording' );
gerotech_seed_once( 'field_nav_es_cta_url', gerotech_seed_link_value( $es_default['cta_label'], $es_default['cta_url'] ), 'option', 'ES panel — column button link' );
gerotech_seed_once( 'field_nav_es_col2_title', $es_default['col2_title'], 'option', 'ES panel — second column heading' );

$service_rows = array();
foreach ( $es_default['services'] as $service ) {
	$service_rows[] = array(
		'heading_lead' => $service['lead'],
		'heading_main' => $service['main'],
		'links'        => gerotech_seed_link_repeater_rows( $service['links'] ),
	);
}
gerotech_seed_once( 'field_nav_es_services', $service_rows, 'option', 'ES panel — service groups (' . count( $service_rows ) . ')' );
echo "\n";

echo "Site Content — Footer (options):\n";
$footer_default = gerotech_footer_defaults();
gerotech_seed_once( 'field_footer_logo_alt', $footer_default['logo_alt'], 'option', 'logo alt text' );
gerotech_seed_once( 'field_footer_tagline', $footer_default['tagline'], 'option', 'tagline' );
gerotech_seed_once( 'field_footer_address', $footer_default['address'], 'option', 'address' );
gerotech_seed_once( 'field_footer_phone', $footer_default['phone'], 'option', 'phone' );

$social_rows = array();
foreach ( $footer_default['socials'] as $social ) {
	$social_rows[] = array(
		'icon'    => $social['icon'],
		'label'   => $social['label'],
		'url'     => gerotech_seed_link_row( $social ),
		'new_tab' => ! empty( $social['new_tab'] ),
	);
}
gerotech_seed_once( 'field_footer_socials', $social_rows, 'option', 'social links (' . count( $social_rows ) . ')' );

$column_rows = array();
foreach ( $footer_default['columns'] as $column ) {
	$column_rows[] = array(
		'title' => $column['title'],
		'links' => gerotech_seed_link_repeater_rows( $column['links'] ),
	);
}
gerotech_seed_once( 'field_footer_columns', $column_rows, 'option', 'link columns (' . count( $column_rows ) . ')' );
gerotech_seed_once( 'field_footer_copyright_text', $footer_default['copyright'], 'option', 'copyright line' );
gerotech_seed_once( 'field_footer_legal_links', gerotech_seed_link_repeater_rows( $footer_default['legal_links'] ), 'option', 'legal links (' . count( $footer_default['legal_links'] ) . ')' );
echo "\n";

/* ── Verification ─────────────────────────────────────────────── */
echo "Verification:\n";
if ( $service_id ) {
	$groups = get_field( 'field_service_plan_inspect_groups', $service_id );
	echo '  service inspection groups: ' . ( is_array( $groups ) ? count( $groups ) : 0 ) . " (expect 10)\n";
	echo '  service tab label 1: ' . get_field( 'field_service_tab_label_service', $service_id ) . "\n";
}
echo '  signup submit label: ' . get_field( 'field_signup_submit_label', 'option' ) . "\n";
if ( $careers_id ) {
	echo '  careers column 1: ' . get_field( 'field_careers_col_job', $careers_id ) . "\n";
}

echo '  nav items: ' . count( (array) get_field( 'nav_items', 'option' ) ) . " (expect 5)\n";
echo '  machine groups: ' . count( (array) get_field( 'nav_machines_groups', 'option' ) ) . " (expect 8)\n";
echo '  ES categories: ' . count( (array) get_field( 'nav_es_categories', 'option' ) ) . " (expect 3)\n";
echo '  footer columns: ' . count( (array) get_field( 'footer_columns', 'option' ) ) . " (expect 3)\n";
echo "\nDone.\n";
