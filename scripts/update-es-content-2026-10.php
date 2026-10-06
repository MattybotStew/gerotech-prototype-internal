<?php
/**
 * Apply the client's final (2026-10) content doc to the 4 Engineered Solutions
 * pages in the WordPress database.
 *
 * Stored ACF rows win over template defaults, so this pushes the new copy into
 * the DB. Idempotent: it compares each value and only writes when different.
 *
 * Safety: card repeaters hold media-library attachments, so card rows are
 * updated *in place by title* (image/video sub-fields are preserved). Only the
 * text-only gallery collections are replaced wholesale.
 *
 * Run (Local):
 *   php -c "<local php.ini>" /tmp/run-local.php scripts/update-es-content-2026-10.php
 * Run (Dev): pipe stdin — `wp eval-file -` < this file
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run this through WordPress (wp eval-file / wp-load bootstrap).\n" );
	exit( 1 );
}

if ( ! function_exists( 'update_field' ) ) {
	fwrite( STDERR, "ACF is not loaded.\n" );
	exit( 1 );
}

$uri = defined( 'GEROTECH_CHILD_URI' ) ? GEROTECH_CHILD_URI : '';

/* ── Helpers ─────────────────────────────────────────────── */

function gx_page_id( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? (int) $page->ID : 0;
}

function gx_norm( $s ) {
	$s = is_string( $s ) ? $s : '';
	$s = str_replace( array( '–', '—', "\xe2\x80\x93", "\xe2\x80\x94" ), '-', $s );
	$s = html_entity_decode( $s, ENT_QUOTES, 'UTF-8' );
	return strtolower( trim( preg_replace( '/\s+/', ' ', $s ) ) );
}

function gx_set( $post_id, $key, $value, $label ) {
	$store  = acf_get_store( 'values' );
	if ( $store ) {
		$store->reset();
	}
	$current = get_field( $key, $post_id );
	if ( $current === $value ) {
		echo "  = {$label}: unchanged\n";
		return;
	}
	update_field( $key, $value, $post_id );
	echo "  ~ {$label}: updated\n";
}

/**
 * Update a text sub-field on a card row found by its title, preserving the
 * row's other sub-fields (image / video attachment data).
 */
function gx_set_card_detail( $post_id, $repeater_key, $title, $detail ) {
	$store = acf_get_store( 'values' );
	if ( $store ) {
		$store->reset();
	}
	$rows  = get_field( $repeater_key, $post_id );
	if ( ! is_array( $rows ) ) {
		echo "  ! {$title}: repeater {$repeater_key} empty\n";
		return;
	}
	$want = gx_norm( $title );
	$hit  = false;
	foreach ( $rows as $i => $row ) {
		if ( isset( $row['title'] ) && gx_norm( $row['title'] ) === $want ) {
			if ( isset( $row['detail'] ) && trim( $row['detail'] ) === trim( $detail ) ) {
				echo "  = {$title}: unchanged\n";
				return;
			}
			$rows[ $i ]['detail'] = $detail;
			$hit = true;
			break;
		}
	}
	if ( ! $hit ) {
		echo "  ! {$title}: row not found\n";
		return;
	}
	update_field( $repeater_key, $rows, $post_id );
	echo "  ~ {$title}: detail updated\n";
}

/* ── Resolve pages ───────────────────────────────────────── */

$ids = array(
	'es'  => gx_page_id( 'engineered-solutions' ),
	'mcs' => gx_page_id( 'modification-of-standard-machine-tools' ),
	'app' => gx_page_id( 'unique-applications-for-standard-machines' ),
	'ai'  => gx_page_id( 'automated-system' ),
);

foreach ( $ids as $k => $v ) {
	if ( ! $v ) {
		fwrite( STDERR, "Missing page for {$k}\n" );
		exit( 1 );
	}
}

$CTA_BODY = 'Tell us about your machine, part, process, and project goals, and include any drawings, photos, or specifications that may help. This will help our team come prepared to discuss your application.';
$CTA_SUB  = "Let's Talk Through It. Prefer Email?";

/* ── Engineered Solutions ────────────────────────────────── */

echo "Engineered Solutions (#{$ids['es']})\n";

gx_set( $ids['es'], 'field_es_hero_body',
	'From machine modification and customization to full automation cells, Gerotech engineers are solution driven to provide creative, robust, most efficient process for its customers — backed by decades of engineering experience.',
	'Hero body' );
gx_set( $ids['es'], 'field_es_hero_cta2_label', '', 'Hero CTA2 label (remove)' );
gx_set( $ids['es'], 'field_es_why_body',
	"Every manufacturing operation is unique. That's why our engineers start by understanding your process. We work alongside your team to solve manufacturing challenges and develop practical solutions built around your operation.",
	'Why body' );
gx_set( $ids['es'], 'field_es_partners_eyebrow', 'Our Technology Partners', 'Partners eyebrow' );
gx_set( $ids['es'], 'field_es_partners_cta_label', '', 'Partners CTA (remove)' );
gx_set( $ids['es'], 'field_es_cap_cta2_label', '', 'Capability CTA2 (remove)' );

// The content doc has every ES CTA read "Talk to an Engineer"; stored rows still
// carried "Get a Quote" / "Get A Quote" (the template defaults were already right).
gx_set( $ids['es'], 'field_es_hero_cta1_label', 'Talk to an Engineer', 'Hero CTA1 label' );
gx_set( $ids['es'], 'field_es_why_cta_label', 'Talk to an Engineer', 'Why CTA label' );
gx_set( $ids['es'], 'field_es_cap_cta1_label', 'Talk to an Engineer', 'Capability CTA1 label' );
gx_set( $ids['es'], 'field_es_cta_button_label', 'Talk to an Engineer', 'CTA band button label' );

// FAQ: reword #1, drop the downtime item (now 4 items).
gx_set( $ids['es'], 'field_es_faq_items', array(
	array(
		'field_es_faq_q' => 'Do you automate machines other than Haas?',
		'field_es_faq_a' => 'Yes. Our engineering team modifies, retrofits, and automates equipment from any OEM and any control platform — not just the machines we sell.',
	),
	array(
		'field_es_faq_q' => 'Can Gerotech handle design through installation in-house?',
		'field_es_faq_a' => 'We provide concept development, electrical and mechanical design, controls programming, panel build, on-site commissioning, and production support — all under one roof in Michigan.',
	),
	array(
		'field_es_faq_q' => 'What does FANUC Authorized System Integrator mean for my project?',
		'field_es_faq_a' => "It certifies that Gerotech meets FANUC's standards for robotic cell design, integration methodology, and ongoing support — with direct access to FANUC technical resources.",
	),
	array(
		'field_es_faq_q' => 'Do you offer training for operators and programmers?',
		'field_es_faq_a' => 'Yes. Complimentary Haas operator and programming courses run at Gerotech-supported locations, including Macomb Community College. Custom on-site training is also available.',
	),
), 'FAQ items' );

/* ── Machine Custom Solutions ────────────────────────────── */

echo "Machine Custom Solutions (#{$ids['mcs']})\n";

gx_set_card_detail( $ids['mcs'], 'field_mcs_cards', 'Machine Column Risers',
	'<p>A Mill column riser is a precision ground block installed between the machine base and the column along with custom sheet metal to accommodate the change in height. Column riser will increase the clearance height of the machine between the spindle and table. This can be beneficial when machining taller parts, adding 4th/5th rotary tables, that may limit access to part features or restrict tooling options. Additional advantage — avoid the need for larger machines that only require additional Z axis clearance.</p>' );
gx_set_card_detail( $ids['mcs'], 'field_mcs_cards', 'Auto Doors',
	'<p><strong>Horizontal Door:</strong> We offer a custom Servax door drive solution that provide enhanced safety and reliability. Fully integrated with your machine tool. This solution is ideal for single or double door machines. The intelligent self-monitoring features reliable, integrated safety functions. Position, speed and torque are constantly monitored. Automatically reacts to obstacles immediately changing directions. Light curtains and two-hand buttons are not required with this solution.</p><p><strong>Vertical Door:</strong> Vertical doors are a great option for machine tending robot cells. They allow for operator full access into the primary door without having to enter the robot cell. Door is fully integrated with machine and outputs provided to robot cell.</p>' );
gx_set_card_detail( $ids['mcs'], 'field_mcs_cards', 'Hydraulic – Pneumatics',
	'<p>Hydraulic solutions can be custom designed to accommodate your machine workholding. We can determine the power unit to ensure it achieves the PSI and flow rate required to provide the necessary clamping force, and the valves to meet the desired sequencing requirements.</p><p>Pneumatic circuit integration can be added for cylinders, actuators, and part blow-offs — just a few examples of enhancements.</p>' );
gx_set_card_detail( $ids['mcs'], 'field_mcs_cards', 'Custom Workholding',
	'<p>When you need more than off the shelf vises and chucks, our mechanical design team can provide a custom solution built around your parts to optimize your process. Whether it\'s a hydraulic fixture, trunnion fixture, tombstone fixture, or custom chuck for a complex part, we engineer it to your machine, your process, and your production goals.</p>' );
gx_set_card_detail( $ids['mcs'], 'field_mcs_cards', 'Process Engineering',
	'<p>Gerotech has a fully staffed engineering department that can take your drawings and models and deliver an engineered solution, from one machine to a completely automated machining line. One partner, one accountable team, from concept through production.</p>' );
gx_set_card_detail( $ids['mcs'], 'field_mcs_cards', 'Specialty Machine',
	'<p><strong>5 Axis Grinding:</strong> Gerotech has provided specialty 5-axis grinding machines for over 20 years to many customers in the Aerospace Industry.</p><p><strong>Spin Forming:</strong> Gerotech has converted our standard lathe into a special purpose spin forming machine to contour cylindrical parts.</p>' );

gx_set( $ids['mcs'], 'field_mcs_gallery_eyebrow', '', 'Gallery eyebrow (remove)' );
gx_set( $ids['mcs'], 'field_mcs_gallery_title', 'Machine Custom Solutions <em>Gallery</em>', 'Gallery title' );

// Gallery collections (text-only) — safe to replace wholesale.
gx_set( $ids['mcs'], 'field_mcs_collections', array(
	array(
		'title' => 'Machine Column Risers',
		'meta'  => '',
		'media' => "image | {$uri}/assets/images/mcs-gallery/column-riser.jpg | | Machine column riser between base and column | Column riser · increased Z-axis clearance",
	),
	array(
		'title' => 'Safety & Environmental Modifications',
		'meta'  => 'Fire protection · mist collection · air quality',
		'media' => "image | {$uri}/assets/images/mcs-gallery/fire-suppression.jpg | | Kidde machine-integrated fire suppression | Kidde system · machine-integrated\nimage | {$uri}/assets/images/mcs-gallery/mist-torit.jpg | | Donaldson Torit mist collector on a mill | Donaldson Torit mist collector\nimage | {$uri}/assets/images/mcs-gallery/mist-lina.jpg | | LINA3nine mist collector on a mill | LINA3nine mist collector",
	),
	array(
		'title' => 'Sheet Metal Modifications',
		'meta'  => 'Guards · enclosures · fabrication',
		'media' => "image | {$uri}/assets/images/mcs-gallery/sheet-metal-stainless.jpg | | Custom stainless sheet metal guards and covers | Stainless guards and covers\nimage | {$uri}/assets/images/mcs-gallery/sheet-metal-enclosure.jpg | | Painted sheet metal enclosure wrap on a machine column | Painted enclosure fabrication\nimage | {$uri}/assets/images/mcs-gallery/sheet-metal-machine.jpg | | Custom sheet metal enclosure wrapping a machining center | Full enclosure fabrication",
	),
	array(
		'title' => 'Auto Doors',
		'meta'  => 'Servak · vertical doors',
		'media' => "image | {$uri}/assets/images/mcs-gallery/auto-door-haas.jpg | | Servak auto door on a Haas mill | Servak auto door · Haas mill\nimage | {$uri}/assets/images/mcs-gallery/vertical-door-closed.jpg | | Vertical auto door closed on a mill | Vertical door · closed\nimage | {$uri}/assets/images/mcs-gallery/vertical-door-window.jpg | | Vertical auto door with window on a mill | Vertical door · windowed\nimage | {$uri}/assets/images/mcs-gallery/vertical-door-drive.jpg | | Vertical auto door drive assembly | Vertical door · drive\nimage | {$uri}/assets/images/mcs-gallery/auto-door-vf2yt.jpg | | Haas VF-2YT with both auto doors open and control pendant on the right | Haas VF-2YT · doors open\nimage | {$uri}/assets/images/mcs-gallery/auto-door-servax.jpg | | Servax Drives actuator on top of a Haas VF-2YT enclosure | Servax Drives actuator · VF-2YT\nimage | {$uri}/assets/images/mcs-gallery/auto-door-pendant.jpg | | Auto door control pendant with DOOR MANUAL / DOOR AUTO switch and status lights | Door control pendant\nvideo | {$uri}/assets/videos/auto-door-540.mp4 | {$uri}/assets/images/mcs-gallery/auto-door-haas.jpg | Auto door cycling video | Auto door cycling · bench test",
	),
	array(
		'title' => 'Hydraulic/Pneumatics',
		'meta'  => 'Rotary and workholding',
		'media' => "image | {$uri}/assets/images/mcs-gallery/hydraulic-rotary.jpg | | Hydraulic rotary and workholding integration | Rotary and workholding integration",
	),
	array(
		'title' => 'Custom Workholding',
		'meta'  => 'Tombstone fixtures · custom plates',
		'media' => "image | {$uri}/assets/images/mcs-gallery/workholding-p2.jpg | | Serrated tombstone fixture plate marked P2 | Tombstone plate · P2\nimage | {$uri}/assets/images/mcs-gallery/workholding-tombstone.jpg | | Dark tombstone fixture marked 259-300-15 | Tombstone fixture · 259-300-15\nimage | {$uri}/assets/images/mcs-gallery/workholding-gtd-11962.jpg | | Gerotech fixture GTD-11962 marked 1B | GTD-11962 · 1B\nimage | {$uri}/assets/images/mcs-gallery/workholding-gtd-11961.jpg | | Gerotech fixture GTD-11961 holding a part marked 1A | GTD-11961 · 1A",
	),
	array(
		'title' => 'Process Engineering',
		'meta'  => '',
		'media' => "image | {$uri}/assets/images/mcs-gallery/custom-fixtures.jpg | | Process engineering fixture on a machining center | Process Engineering · engineered solution",
	),
	array(
		'title' => 'Specialty Machine',
		'meta'  => '',
		'media' => "image | {$uri}/assets/images/mcs-gallery/specialty-machine.jpg | | Haas ST-45 lathe with bar feeder on the shop floor | Specialty machine · Haas ST-45",
	),
), 'Gallery collections' );

gx_set( $ids['mcs'], 'field_mcs_cta_subhead', $CTA_SUB, 'CTA subhead' );
gx_set( $ids['mcs'], 'field_mcs_cta_body', $CTA_BODY, 'CTA body' );
gx_set( $ids['mcs'], 'field_mcs_cta_button_label', 'Talk to an Engineer', 'CTA button label' );
gx_set( $ids['mcs'], 'field_mcs_cta_call_number', '', 'CTA call number (remove)' );

/* ── Applications ────────────────────────────────────────── */

echo "Applications (#{$ids['app']})\n";

gx_set_card_detail( $ids['app'], 'field_app_cards', 'Part Programming',
	'<p>We write programs that get the most out of your machine. From simple 2-axis turning and 3-axis milling work to complex multi-axis solutions, our application engineers handle the full range — G-code, conversational programming, and CAM-generated toolpaths.</p><p>Whether you need a one-off program or high-volume production, we will deliver a reliable toolpath that is right for your part.</p>' );
gx_set_card_detail( $ids['app'], 'field_app_cards', 'Process Troubleshooting',
	'<p>When you\'re struggling to resolve a tool path issue, our talented team of Application Engineers is here to assist.</p><p>If the root cause is not obvious from looking at the program, we can take your program and run it through our simulators or, when necessary, trial it on one of our showroom machines depending on the model fit.</p><p>Reach out at applications@gerotech.com with a brief description of your issue along with the necessary tooling and program information.</p>' );
gx_set_card_detail( $ids['app'], 'field_app_cards', 'Process Optimization',
	'<p>Every program is optimized for cycle time, tool life, and part quality. We handle custom probing and macro programming when the standard available routines don\'t meet your needs.</p>' );
gx_set_card_detail( $ids['app'], 'field_app_cards', 'Tooling Recommendation',
	'<p>Our Application Engineers utilize Mfg Engineering backgrounds to help select tooling matched to your material, machine, and process — selected for your job, not the catalog. From standard tooling to custom tooling, we can help you optimize process performance on your shop floor.</p>' );
gx_set_card_detail( $ids['app'], 'field_app_cards', 'Demo',
	'<p>We can run machine demos for any machine in the showroom.</p><p>If you have a specific part you would like to see demoed, with a shared approach for materials and tooling we can accommodate it provided the machine model is the right fit to our showroom equipment.</p>' );
gx_set_card_detail( $ids['app'], 'field_app_cards', 'Training',
	'<p>Gerotech offers, at no cost to our customers, instructor-led operator training for basic lathe/mill, VPS, Intro to G&amp;M code, and programming classes for both Mill and Lathe at our Grand Rapids location and our Macomb Community College partner.</p><p>If you need onsite training, our application engineers can tailor training to your needs for a fee. Contact your Account Manager to discuss.</p>' );

gx_set( $ids['app'], 'field_app_gallery_title', 'Applications Product <em>Gallery</em>', 'Gallery title' );

// Clear the Part Programming gray subtext, keep every other collection row.
$store = acf_get_store( 'values' );
if ( $store ) {
	$store->reset();
}
$app_cols = get_field( 'field_app_collections', $ids['app'] );
if ( is_array( $app_cols ) ) {
	$changed = false;
	foreach ( $app_cols as $i => $col ) {
		if ( isset( $col['title'] ) && 'part programming' === gx_norm( $col['title'] ) && ! empty( $col['meta'] ) ) {
			$app_cols[ $i ]['meta'] = '';
			$changed = true;
		}
	}
	if ( $changed ) {
		update_field( 'field_app_collections', $app_cols, $ids['app'] );
		echo "  ~ Gallery: Part Programming meta cleared\n";
	} else {
		echo "  = Gallery meta: unchanged\n";
	}
}

gx_set( $ids['app'], 'field_app_cta_eyebrow', 'Application', 'CTA eyebrow' );
gx_set( $ids['app'], 'field_app_cta_subhead', $CTA_SUB, 'CTA subhead' );
gx_set( $ids['app'], 'field_app_cta_body', $CTA_BODY, 'CTA body' );
gx_set( $ids['app'], 'field_app_cta_button_label', 'Talk to an Engineer', 'CTA button label' );
gx_set( $ids['app'], 'field_app_cta_call_number', '', 'CTA call number (remove)' );

/* ── Automation & Controls ───────────────────────────────── */

echo "Automation & Controls (#{$ids['ai']})\n";

gx_set( $ids['ai'], 'field_ai_hero_body', '', 'Hero body (remove)' );

// Insert Electrical – Controls Solutions as the first card, preserving images
// on the rows that already exist.
$store = acf_get_store( 'values' );
if ( $store ) {
	$store->reset();
}
$ai_cards = get_field( 'field_ai_cards', $ids['ai'] );
if ( is_array( $ai_cards ) ) {
	$has_electrical = false;
	$by_title      = array();
	foreach ( $ai_cards as $i => $row ) {
		if ( isset( $row['title'] ) && 'electrical' === substr( gx_norm( $row['title'] ), 0, 10 ) ) {
			$has_electrical = true;
		}
		$by_title[ gx_norm( isset( $row['title'] ) ? $row['title'] : '' ) ] = $i;
	}

	$electrical_detail = '<p>From concept development to long-term production support, we provide complete electrical and controls engineering services for industrial automation systems. Whether you\'re upgrading a single machine or implementing a fully integrated manufacturing cell, we deliver solutions that are designed for performance, reliability, and maintainability.</p><details open><summary>Concept &amp; System Design</summary><p>Every successful automation project begins with a solid foundation. We work with customers to understand their manufacturing objectives, evaluate technical requirements, and develop practical automation concepts that balance performance, reliability, and cost.</p><ul><li>System concept development</li><li>Automation feasibility studies</li><li>Control system architecture</li><li>Safety system design</li><li>Hardware selection</li><li>Electrical power distribution</li><li>Network architecture</li></ul></details><details><summary>Electrical Engineering</summary><p>Our electrical engineering services transform concepts into complete manufacturing-ready documentation packages.</p><ul><li>Electrical schematics</li><li>Control panel design</li><li>I/O layouts</li><li>Bill of materials (BOM)</li><li>Device selection</li><li>Network layouts</li><li>Documentation packages</li></ul></details><details><summary>Panel Build &amp; System Integration</summary><p>From component procurement to final assembly, we build reliable control systems designed for long-term operation in demanding industrial environments.</p><ul><li>Control panel assembly</li><li>Electrical wiring</li><li>Hardware integration</li><li>FAT testing</li></ul></details><details><summary>PLC &amp; HMI Development</summary><p>Our controls engineers develop robust PLC and HMI software using standardized design practices and proven software frameworks to deliver reliable, maintainable automation systems.</p><ul><li>PLC programming</li><li>HMI development</li><li>Motion control</li><li>Industrial networking</li><li>Robot integration</li><li>Vision integration</li><li>Data collection</li><li>Process control</li></ul></details><details><summary>Commissioning &amp; Production Support</summary><p>Successful projects don\'t end when the equipment ships. We provide on-site commissioning, production startup, and ongoing technical support to ensure your automation system performs as intended.</p><ul><li>System commissioning</li><li>Startup assistance</li><li>Production support</li><li>System debugging</li><li>Performance optimization</li><li>Operator training</li><li>Troubleshooting</li><li>Remote support</li></ul></details><details><summary>Control Platform Options</summary><p>We develop automation solutions using a variety of industrial control platforms to meet the technical requirements, standards, and budget of each application. Our experience spans multiple manufacturers, allowing us to recommend and implement the solution that best fits your project.</p><p><strong>Common control platforms include:</strong></p><ul><li>Allen-Bradley</li><li>Siemens</li><li>Automation Direct</li></ul></details>';

	if ( ! $has_electrical ) {
		array_unshift( $ai_cards, array(
			'title'  => 'Electrical – Controls Solutions',
			'image'  => 'assets/images/pre-engineered-card.jpg',
			'detail' => $electrical_detail,
		) );
		echo "  ~ Electrical – Controls Solutions: card added\n";
	} else {
		echo "  = Electrical – Controls Solutions: already present\n";
	}

	// Figma #297: image = the Pre-Engineered Solutions image, and the card sits
	// directly after HMI Design.
	foreach ( $ai_cards as $i => $row ) {
		if ( isset( $row['title'] ) && 0 === strpos( gx_norm( $row['title'] ), 'electrical' ) ) {
			$ai_cards[ $i ]['image'] = 'assets/images/pre-engineered-card.jpg';
		}
	}
	$card_order = array( 'hmi design', 'electrical', 'layered', 'automation cell', 'robot eoat', 'pre-engineered' );
	usort( $ai_cards, function ( $a, $b ) use ( $card_order ) {
		$rank = function ( $card ) use ( $card_order ) {
			$title = strtolower( html_entity_decode( isset( $card['title'] ) ? $card['title'] : '', ENT_QUOTES, 'UTF-8' ) );
			foreach ( $card_order as $i => $needle ) {
				if ( false !== strpos( $title, $needle ) ) {
					return $i;
				}
			}
			return PHP_INT_MAX;
		};
		return $rank( $a ) <=> $rank( $b );
	} );
	update_field( 'field_ai_cards', $ai_cards, $ids['ai'] );
	echo "  ~ Card order: HMI Design, Electrical, Layered, Cell, EOAT, Pre-Engineered\n";
} else {
	echo "  ! ai_cards empty\n";
}

gx_set_card_detail( $ids['ai'], 'field_ai_cards', 'Automation Cell Design',
	'<p>We design every automation cell in SolidWorks and validate it in RoboGuide. SolidWorks lets us model the robot, the machine, the workholding, and EOAT as one integrated assembly so interference, reach, and cycle time concerns are identified in the design phase. RoboGuide then simulates the full motion path confirming the robot path prior to build.</p>' );
gx_set_card_detail( $ids['ai'], 'field_ai_cards', 'Robot EOAT – Ancillary Material Handling',
	'<p>Our end-of-arm tools are engineered for your robot\'s payload, reach, and duty cycle. Whether it\'s multiple jaws on a Schunk gripper, Servo Onrobot gripper, vacuum, or magnetic, we design, build, and integrate the complete package around your process.</p>' );
gx_set_card_detail( $ids['ai'], 'field_ai_cards', 'HMI Design',
	'<details open><summary>Customizable Operator Screens</summary><p>Every application is different, and the operator interface should reflect the needs of the people using it. Our HMI is fully configurable, providing a centralized location for the information and functions required for efficient day-to-day operation.</p></details><details><summary>HMI – Ethernet Diagnostics</summary><p>When implemented on Allen-Bradley control platforms, our software library utilizes native EtherNet/IP diagnostic capabilities to provide operators and maintenance personnel with detailed device diagnostics directly from the HMI. Access to fault codes, device status, fault descriptions, and manufacturer diagnostic information helps reduce troubleshooting time while minimizing the need for a programming laptop.</p></details><details><summary>HMI – I/O Diagnostics</summary><p>When implemented on Allen-Bradley control platforms, our software library provides extensive I/O diagnostics directly on the HMI, giving operators and maintenance technicians clear visibility into machine status without the need for a programming laptop. Where supported, device-specific diagnostics include manufacturer fault information, descriptions, and recommended corrective actions, enabling faster troubleshooting and reduced downtime.</p></details><details><summary>HMI – Device Specific Diagnostics</summary><p>Where applicable, our library components include device-level diagnostics, providing immediate access to fault codes, fault descriptions, and manufacturer-recommended corrective actions.</p></details><details><summary>HMI – Device Centric Control / Feedback</summary><p>Our device-centric PLC and HMI design provides a consistent, intuitive, and flexible operator experience throughout the entire system. Every device utilizes a standardized interface that presents the information, diagnostics, and controls needed for efficient operation and maintenance.</p><p><strong>Typical device interface items:</strong></p><ul><li>Current operating status and operating mode</li><li>Manual operation and jog functions</li><li>Clear indication of manual operation inhibits and the conditions preventing device operation</li><li>Runtime statistics and performance information</li><li>Device configuration and setup parameters</li><li>Maintenance and service functions</li><li>Device health and communication status</li></ul></details><details><summary>HMI – Cell Automation – Overview</summary><p>The Cell Overview screen serves as the primary operational dashboard, presenting the most critical information required to monitor and operate the cell at a glance. Key production metrics, including part counts, cycle times, and active part status, are displayed alongside a high-level summary of the machine\'s safety system that corresponds directly with the detailed Safety Diagnostics screen. By consolidating essential production and safety information into a single interface, operators can quickly assess machine status, identify production bottlenecks, and respond to abnormal conditions without navigating through multiple screens.</p></details><details><summary>HMI – Cell Automation – Station</summary><p>Each station includes a dedicated detail screen that consolidates all relevant information into a single, easy-to-navigate interface. Operators and maintenance personnel can view and control the station operating mode, monitor active interlocks and permissives, access station-specific I/O diagnostics, review part tracking data, and interact with device-specific functions without navigating between areas of the HMI. By centralizing these tools in one location, troubleshooting is simplified, operator training is reduced, and critical machine information is always readily accessible.</p></details><details><summary>HMI – Cell Automation – Part Program</summary><p>The integrated Part Program system provides the flexibility to accommodate multiple product variants, manufacturing requirements, and configurable process options without requiring software modifications. Part Programs define the parameters and processing requirements for each product, allowing the automation system to automatically adjust machine behavior based on the selected part configuration. A guided Program Load screen simplifies changeovers by walking operators through the program selection and loading process. This streamlined workflow reduces setup time, minimizes the risk of operator error, and enables fast, repeatable product changeovers with minimal training.</p></details><details><summary>HMI – Cell Automation – Part Data View</summary><p>The cell-level Part Data screen provides a centralized view of the current status of each part as it progresses through the manufacturing process. Operators can quickly identify required and completed operations, review process-specific data, monitor part tracking information. By consolidating critical production data into a single interface, the system improves traceability, simplifies troubleshooting, and provides clear visibility into the overall health and progress of each part throughout the cell.</p></details><details><summary>HMI – Cell Automation – Safety Devices</summary><p>The Safety Diagnostics screen provides a comprehensive view of the machine\'s safety system, allowing operators and maintenance personnel to quickly identify the status of all safety inputs, outputs, and safety functions. Each safety device includes contextual diagnostics and detailed status information to clearly indicate the current operating condition, fault state, or reason for a safety stop. By presenting meaningful diagnostic information alongside each device, the system reduces troubleshooting time, improves maintenance efficiency, and helps restore the machine to operation safely and quickly.</p></details>' );

// Layered Controls: append the Standardized Software block under Layer 3 if absent.
$store = acf_get_store( 'values' );
if ( $store ) {
	$store->reset();
}
$ai_rows = get_field( 'field_ai_cards', $ids['ai'] );
if ( is_array( $ai_rows ) ) {
	$std = '<details><summary>Standardized Software Design Methodology</summary><p>Our automation solutions are developed using a standardized software design methodology that has been refined through years of real-world manufacturing applications. This proven approach provides a consistent programming structure, operator experience, and diagnostic philosophy across our automation platforms.</p><p>By developing from a common software foundation and adapting it to the selected control platform, we can deliver custom automation solutions more efficiently while maintaining proven functionality, consistent operation, and high-quality software.</p><p><strong>Key Benefits:</strong></p><ul><li>Proven software foundation</li><li>Standardized programming methodology</li><li>Consistent HMI navigation and operator experience</li><li>Common alarms, diagnostics, and fault recovery</li><li>Faster project development</li><li>Reduced project risk</li><li>Simplified troubleshooting and maintenance</li><li>Easier operator training</li><li>Flexible deployment across multiple control platforms</li><li>Scalable design for future expansion</li></ul></details>';
	$changed = false;
	foreach ( $ai_rows as $i => $row ) {
		if ( isset( $row['title'] ) && 'layered controls solutions' === gx_norm( $row['title'] ) ) {
			if ( false === strpos( isset( $row['detail'] ) ? $row['detail'] : '', 'Standardized Software Design Methodology' ) ) {
				$ai_rows[ $i ]['detail'] = rtrim( isset( $row['detail'] ) ? $row['detail'] : '' ) . $std;
				$changed = true;
			}
			break;
		}
	}
	if ( $changed ) {
		update_field( 'field_ai_cards', $ai_rows, $ids['ai'] );
		echo "  ~ Layered Controls Solutions: Standardized Software added\n";
	} else {
		echo "  = Layered Controls Solutions: unchanged\n";
	}
}

gx_set( $ids['ai'], 'field_ai_gallery_eyebrow', '', 'Gallery eyebrow (remove)' );
gx_set( $ids['ai'], 'field_ai_gallery_title', 'Automation &amp; Controls <em>Gallery</em>', 'Gallery title' );

// Clear gallery gray subtexts on every collection.
$store = acf_get_store( 'values' );
if ( $store ) {
	$store->reset();
}
$ai_cols = get_field( 'field_ai_collections', $ids['ai'] );
if ( is_array( $ai_cols ) ) {
	$changed = false;
	foreach ( $ai_cols as $i => $col ) {
		if ( ! empty( $col['meta'] ) ) {
			$ai_cols[ $i ]['meta'] = '';
			$changed = true;
		}
	}
	if ( $changed ) {
		update_field( 'field_ai_collections', $ai_cols, $ids['ai'] );
		echo "  ~ Gallery metas cleared\n";
	} else {
		echo "  = Gallery metas: unchanged\n";
	}
}

gx_set( $ids['ai'], 'field_ai_cta_subhead', $CTA_SUB, 'CTA subhead' );
gx_set( $ids['ai'], 'field_ai_cta_body', $CTA_BODY, 'CTA body' );
gx_set( $ids['ai'], 'field_ai_cta_button_label', 'Talk to an Engineer', 'CTA button label' );
gx_set( $ids['ai'], 'field_ai_cta_call_number', '', 'CTA call number (remove)' );

/* ── Flush caches ────────────────────────────────────────── */

$store = acf_get_store( 'values' );
if ( $store ) {
	$store->reset();
}
wp_cache_flush();

echo "\nDone.\n";
