<?php
/**
 * automation-integration page template.
 *
 * Content is driven by ACF (`inc/acf-fields.php`) with the current design as
 * defaults, so the page renders correctly whether or not fields are populated.
 *
 * @package GerotechChild
 */

get_header();

$pick = function ( $key, $default ) {
	return gerotech_field( $key, $default );
};

$uri = GEROTECH_CHILD_URI;

/* ── Hero ─────────────────────────────────────────────────── */
$hero_lead    = $pick( 'ai_hero_lead', 'Automation' );
$hero_main    = $pick( 'ai_hero_main', 'and <em>Controls Solutions</em>' );
$hero_breadcrumb = $pick( 'ai_hero_breadcrumb', 'Automation and Controls Solutions' );
// The breadcrumb keeps the design's two-tone split (gray lead + primary rest).
$crumb_bits  = explode( ' ', (string) $hero_breadcrumb, 2 );
$crumb_lead  = $crumb_bits[0];
$crumb_main  = isset( $crumb_bits[1] ) ? $crumb_bits[1] : '';
$hero_accent  = $pick( 'ai_hero_accent_color', 'orange' ); // Blank (no stored choice) keeps the design colour.
$hero_body    = $pick( 'ai_hero_body', '' );
$hero_image_value = $pick( 'ai_hero_image', 'assets/images/automation-hero.jpg' );
$hero_image   = gerotech_image_url( $hero_image_value, 'assets/images/automation-hero.jpg' );

/* ── Services grid ────────────────────────────────────────── */
$grid_eyebrow = $pick( 'ai_grid_eyebrow', 'What We Offer' );
$grid_title   = $pick( 'ai_grid_title', 'Automation and Controls <em>Solutions</em> Services' );
$cards        = $pick(
	'ai_cards',
	array(
		array(
			'title'  => 'Electrical – Controls Solutions',
			'image'  => 'assets/images/pre-engineered-card.jpg',
			'detail' => '<p>From concept development to long-term production support, we provide complete electrical and controls engineering services for industrial automation systems. Whether you\'re upgrading a single machine or implementing a fully integrated manufacturing cell, we deliver solutions that are designed for performance, reliability, and maintainability.</p><details open><summary>Concept &amp; System Design</summary><p>Every successful automation project begins with a solid foundation. We work with customers to understand their manufacturing objectives, evaluate technical requirements, and develop practical automation concepts that balance performance, reliability, and cost.</p><ul><li>System concept development</li><li>Automation feasibility studies</li><li>Control system architecture</li><li>Safety system design</li><li>Hardware selection</li><li>Electrical power distribution</li><li>Network architecture</li></ul></details><details><summary>Electrical Engineering</summary><p>Our electrical engineering services transform concepts into complete manufacturing-ready documentation packages.</p><ul><li>Electrical schematics</li><li>Control panel design</li><li>I/O layouts</li><li>Bill of materials (BOM)</li><li>Device selection</li><li>Network layouts</li><li>Documentation packages</li></ul></details><details><summary>Panel Build &amp; System Integration</summary><p>From component procurement to final assembly, we build reliable control systems designed for long-term operation in demanding industrial environments.</p><ul><li>Control panel assembly</li><li>Electrical wiring</li><li>Hardware integration</li><li>FAT testing</li></ul></details><details><summary>PLC &amp; HMI Development</summary><p>Our controls engineers develop robust PLC and HMI software using standardized design practices and proven software frameworks to deliver reliable, maintainable automation systems.</p><ul><li>PLC programming</li><li>HMI development</li><li>Motion control</li><li>Industrial networking</li><li>Robot integration</li><li>Vision integration</li><li>Data collection</li><li>Process control</li></ul></details><details><summary>Commissioning &amp; Production Support</summary><p>Successful projects don\'t end when the equipment ships. We provide on-site commissioning, production startup, and ongoing technical support to ensure your automation system performs as intended.</p><ul><li>System commissioning</li><li>Startup assistance</li><li>Production support</li><li>System debugging</li><li>Performance optimization</li><li>Operator training</li><li>Troubleshooting</li><li>Remote support</li></ul></details><details><summary>Control Platform Options</summary><p>We develop automation solutions using a variety of industrial control platforms to meet the technical requirements, standards, and budget of each application. Our experience spans multiple manufacturers, allowing us to recommend and implement the solution that best fits your project.</p><p><strong>Common control platforms include:</strong></p><ul><li>Allen-Bradley</li><li>Siemens</li><li>Automation Direct</li></ul></details>',
		),
		array(
			'title'  => 'HMI Design',
			'image'  => 'assets/images/hmi-design.jpg',
			'detail' => '<details open><summary>Customizable Operator Screens</summary><p>Every application is different, and the operator interface should reflect the needs of the people using it. Our HMI is fully configurable, providing a centralized location for the information and functions required for efficient day-to-day operation.</p></details><details><summary>HMI – Ethernet Diagnostics</summary><p>When implemented on Allen-Bradley control platforms, our software library utilizes native EtherNet/IP diagnostic capabilities to provide operators and maintenance personnel with detailed device diagnostics directly from the HMI. Access to fault codes, device status, fault descriptions, and manufacturer diagnostic information helps reduce troubleshooting time while minimizing the need for a programming laptop.</p></details><details><summary>HMI – I/O Diagnostics</summary><p>When implemented on Allen-Bradley control platforms, our software library provides extensive I/O diagnostics directly on the HMI, giving operators and maintenance technicians clear visibility into machine status without the need for a programming laptop. Where supported, device-specific diagnostics include manufacturer fault information, descriptions, and recommended corrective actions, enabling faster troubleshooting and reduced downtime.</p></details><details><summary>HMI – Device Specific Diagnostics</summary><p>Where applicable, our library components include device-level diagnostics, providing immediate access to fault codes, fault descriptions, and manufacturer-recommended corrective actions.</p></details><details><summary>HMI – Device Centric Control / Feedback</summary><p>Our device-centric PLC and HMI design provides a consistent, intuitive, and flexible operator experience throughout the entire system. Every device utilizes a standardized interface that presents the information, diagnostics, and controls needed for efficient operation and maintenance.</p><p><strong>Typical device interface items:</strong></p><ul><li>Current operating status and operating mode</li><li>Manual operation and jog functions</li><li>Clear indication of manual operation inhibits and the conditions preventing device operation</li><li>Runtime statistics and performance information</li><li>Device configuration and setup parameters</li><li>Maintenance and service functions</li><li>Device health and communication status</li></ul></details><details><summary>HMI – Cell Automation – Overview</summary><p>The Cell Overview screen serves as the primary operational dashboard, presenting the most critical information required to monitor and operate the cell at a glance. Key production metrics, including part counts, cycle times, and active part status, are displayed alongside a high-level summary of the machine\'s safety system that corresponds directly with the detailed Safety Diagnostics screen. By consolidating essential production and safety information into a single interface, operators can quickly assess machine status, identify production bottlenecks, and respond to abnormal conditions without navigating through multiple screens.</p></details><details><summary>HMI – Cell Automation – Station</summary><p>Each station includes a dedicated detail screen that consolidates all relevant information into a single, easy-to-navigate interface. Operators and maintenance personnel can view and control the station operating mode, monitor active interlocks and permissives, access station-specific I/O diagnostics, review part tracking data, and interact with device-specific functions without navigating between areas of the HMI. By centralizing these tools in one location, troubleshooting is simplified, operator training is reduced, and critical machine information is always readily accessible.</p></details><details><summary>HMI – Cell Automation – Part Program</summary><p>The integrated Part Program system provides the flexibility to accommodate multiple product variants, manufacturing requirements, and configurable process options without requiring software modifications. Part Programs define the parameters and processing requirements for each product, allowing the automation system to automatically adjust machine behavior based on the selected part configuration. A guided Program Load screen simplifies changeovers by walking operators through the program selection and loading process. This streamlined workflow reduces setup time, minimizes the risk of operator error, and enables fast, repeatable product changeovers with minimal training.</p></details><details><summary>HMI – Cell Automation – Part Data View</summary><p>The cell-level Part Data screen provides a centralized view of the current status of each part as it progresses through the manufacturing process. Operators can quickly identify required and completed operations, review process-specific data, monitor part tracking information. By consolidating critical production data into a single interface, the system improves traceability, simplifies troubleshooting, and provides clear visibility into the overall health and progress of each part throughout the cell.</p></details><details><summary>HMI – Cell Automation – Safety Devices</summary><p>The Safety Diagnostics screen provides a comprehensive view of the machine\'s safety system, allowing operators and maintenance personnel to quickly identify the status of all safety inputs, outputs, and safety functions. Each safety device includes contextual diagnostics and detailed status information to clearly indicate the current operating condition, fault state, or reason for a safety stop. By presenting meaningful diagnostic information alongside each device, the system reduces troubleshooting time, improves maintenance efficiency, and helps restore the machine to operation safely and quickly.</p></details>',
		),
		array(
			'title'  => 'Layered Controls Solutions',
			'image'  => 'assets/images/layered-controls.jpg',
			'detail' => '<p>Our Layered Controls approach organizes automation into three integrated levels, each building on the last to deliver a complete, coordinated manufacturing system.</p><details open><summary>Layer 1 — Machine Tool</summary><p>The OEM CNC control remains responsible for the machine\'s core manufacturing functions, including axis motion, spindle control, tool changes, and machining cycles. For applications requiring additional functionality, we specialize in implementing targeted enhancements to the existing control system, extending the machine\'s capabilities while preserving the OEM control architecture.</p></details><details><summary>Layer 2 — Machine Tool Automation</summary><p>Our Machine Automation package extends the capabilities of the CNC machine with features that are specific to your manufacturing process.</p><p><strong>Typical extended capabilities include:</strong></p><ul><li>Automatic door control</li><li>Part presence verification</li><li>Machine status monitoring</li><li>Custom I/O integration</li><li>Safety interfaces</li><li>Pneumatic and hydraulic systems</li><li>Coolant and chip management</li><li>Operator interfaces</li><li>Process-specific automation</li></ul><p><strong>Machine Tool Control Packages:</strong> Rather than designing every system from the ground up, we offer a family of pre-engineered automation solutions that can be configured to match your application\'s requirements. From cost-effective machine automation packages to fully featured control systems, each solution is designed to provide the right balance of functionality, performance, and investment.</p><p>Every platform is built on proven software, standardized engineering practices, and years of real-world manufacturing experience, allowing us to deliver custom solutions with reduced engineering time, lower project risk, and faster implementation.</p><ul><li>Reduced engineering time</li><li>Faster project delivery</li><li>Lower project risk</li><li>Proven, reliable software</li><li>Consistent operator experience</li><li>Flexible architecture that adapts to a wide range of machine types and applications</li><li>Simplified future enhancements and support</li></ul></details><details><summary>Layer 3 — Automation Cells</summary><p>The Cell Controller coordinates the entire manufacturing system by managing communication between machines, robots, conveyors, vision systems, and peripheral equipment.</p><p><strong>Responsibilities include:</strong></p><ul><li>Robot coordination</li><li>Part routing</li><li>Cell sequencing</li><li>Production scheduling</li><li>Vision integration</li><li>Data collection</li><li>Fault recovery</li><li>System diagnostics</li></ul></details><details><summary>Standardized Software Design Methodology</summary><p>Our automation solutions are developed using a standardized software design methodology that has been refined through years of real-world manufacturing applications. This proven approach provides a consistent programming structure, operator experience, and diagnostic philosophy across our automation platforms.</p><p>By developing from a common software foundation and adapting it to the selected control platform, we can deliver custom automation solutions more efficiently while maintaining proven functionality, consistent operation, and high-quality software.</p><p><strong>Key Benefits:</strong></p><ul><li>Proven software foundation</li><li>Standardized programming methodology</li><li>Consistent HMI navigation and operator experience</li><li>Common alarms, diagnostics, and fault recovery</li><li>Faster project development</li><li>Reduced project risk</li><li>Simplified troubleshooting and maintenance</li><li>Easier operator training</li><li>Flexible deployment across multiple control platforms</li><li>Scalable design for future expansion</li></ul></details>',
		),
		array(
			'title'  => 'Automation Cell Design',
			'image'  => 'assets/images/automation-cell-design.jpg',
			'detail' => '<p>We design every automation cell in SolidWorks and validate it in RoboGuide. SolidWorks lets us model the robot, the machine, the workholding, and EOAT as one integrated assembly so interference, reach, and cycle time concerns are identified in the design phase. RoboGuide then simulates the full motion path confirming the robot path prior to build.</p>',
		),
		array(
			'title'  => 'Robot EOAT – Ancillary Material Handling',
			'image'  => 'assets/images/robot-eoat.jpg',
			'detail' => '<p>Our end-of-arm tools are engineered for your robot\'s payload, reach, and duty cycle. Whether it\'s multiple jaws on a Schunk gripper, Servo Onrobot gripper, vacuum, or magnetic, we design, build, and integrate the complete package around your process.</p>',
		),
		array(
			'title'  => 'Pre-Engineered Solutions',
			'image'  => 'assets/images/pre-engineered-card.jpg',
			'detail' => '<p>Rather than designing every system from the ground up, we offer a family of pre-engineered automation solutions that can be configured to match your application\'s requirements — from cost-effective machine automation packages to fully featured control systems.</p><p>Every platform is built on proven software, standardized engineering practices, and years of real-world manufacturing experience, delivering custom solutions with reduced engineering time, lower project risk, and faster implementation.</p><ul><li>Reduced engineering time &amp; faster project delivery</li><li>Lower project risk with proven, reliable software</li><li>Consistent operator experience across platforms</li><li>Flexible architecture — adapts to a wide range of machine types</li><li>Simplified future enhancements and support</li></ul><details><summary>Standardized Software Design Methodology</summary><p>Our automation solutions are developed using a standardized software design methodology that has been refined through years of real-world manufacturing applications. This proven approach provides a consistent programming structure, operator experience, and diagnostic philosophy across our automation platforms.</p><p>By developing from a common software foundation and adapting it to the selected control platform, we can deliver custom automation solutions more efficiently while maintaining proven functionality, consistent operation, and high-quality software.</p><p><strong>Key Benefits:</strong></p><ul><li>Proven software foundation</li><li>Standardized programming methodology</li><li>Consistent HMI navigation and operator experience</li><li>Common alarms, diagnostics, and fault recovery</li><li>Faster project development</li><li>Reduced project risk</li><li>Simplified troubleshooting and maintenance</li><li>Easier operator training</li><li>Flexible deployment across multiple control platforms</li><li>Scalable design for future expansion</li></ul></details>',
		),
	)
);

// Figma #297 (2026-10-06): "Electrical – Controls Solutions" sits directly after
// HMI Design. Enforce the client's card order regardless of stored row order.
$card_order = array( 'hmi design', 'electrical', 'layered', 'automation cell', 'robot eoat', 'pre-engineered' );
usort(
	$cards,
	function ( $a, $b ) use ( $card_order ) {
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
	}
);

/* ── Gallery ──────────────────────────────────────────────── */
$gallery_eyebrow = $pick( 'ai_gallery_eyebrow', '' );
$gallery_title   = $pick( 'ai_gallery_title', 'Automation &amp; Controls <em>Gallery</em>' );
$collections     = $pick(
	'ai_collections',
	array(
		array(
			'title' => 'HMI Design',
			'meta'  => '',
			'media' => implode( "\n", array(
				"image | {$uri}/assets/images/automation-gallery/hmi-operator-1.jpg | | Gerotech operator screen showing part complete and fixture presence | Operator screen · part complete",
				"image | {$uri}/assets/images/automation-gallery/hmi-operator-2.jpg | | Gerotech operator screen, closer view of part complete | Operator screen · part complete (close view)",
				"image | {$uri}/assets/images/automation-gallery/hmi-operator-3.jpg | | Gerotech operator screen with the full button bar | Operator screen · full controls",
				"image | {$uri}/assets/images/automation-gallery/hmi-cell-overview.jpg | | Gerotech cell overview screen with mill and robot status | Cell overview",
				"image | {$uri}/assets/images/automation-gallery/hmi-diagnostics.jpg | | Gerotech diagnostics screen showing safety inputs and a door-open fault | Diagnostics · safety inputs",
			) ),
		),
		array(
			'title' => 'Layered Controls Solutions',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/automation-gallery/layered-controls-diagram.jpg | | Layered controls diagram: machine tool, machine tool controls, and automation cells | Layer 01 machine tool · Layer 02 controls · Layer 03 cells",
		),
		array(
			'title' => 'Automation Cell Design',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/automation-cell-design.jpg | | FANUC M-20iD/25 tending a Haas ST-10 in a guarded cell | FANUC M-20iD/25 · Haas ST-10\n"
				. "image | {$uri}/assets/images/automation-gallery/automation-cell-lab.jpg | | Automation training lab with control cabinet, teach pendant, dual yellow robots on pedestals, EOAT tree, and CNC machines in the background | Training lab · dual robots · EOAT tree\n"
				. "image | {$uri}/assets/images/automation-gallery/automation-cell-guarded.jpg | | Guarded yellow robot cell with wire-mesh safety enclosure, vertical control cabinet with HMI, and floor controller | Guarded robot cell · control cabinet\n"
				. "image | {$uri}/assets/images/automation-gallery/automation-cell-vision.jpg | | Keyence overhead machine vision system with four green LED ring lights on a diamond mounting plate | Keyence vision · ring lights",
		),
		array(
			'title' => 'Robot EOAT – Ancillary Material Handling',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/robot-eoat.jpg | | Custom dual-gripper end-of-arm tooling | Custom end-of-arm tooling\n"
				. "image | {$uri}/assets/images/automation-gallery/eoat-vacuum-suction.jpg | | Vacuum suction end-of-arm tooling with orange cups on an aluminum frame | Vacuum EOAT · suction cups\n"
				. "image | {$uri}/assets/images/automation-gallery/eoat-gripper-pair.jpg | | Custom dual end-of-arm gripper tooling with pneumatic fittings on a workbench | Dual gripper EOAT",
		),
		array(
			'title' => 'Pre-Engineered Solutions',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/pre-engineered-card.jpg | | Open grey control cabinet with blue wiring, red terminals, and a VFD | Pre-Engineered Solutions · control cabinet\n"
				. "image | {$uri}/assets/images/pre-engineered-gallery.jpg | | Open dual-door control cabinet on the shop floor beside a Haas machine | Control enclosure · shop floor",
		),
	)
);

/* ── CTA band ─────────────────────────────────────────────── */
$cta_eyebrow      = $pick( 'ai_cta_eyebrow', 'Automation &amp; Controls' );
$cta_headline     = $pick( 'ai_cta_headline', 'Need a <em>custom solution</em> for your machine?' );
$cta_subhead      = $pick( 'ai_cta_subhead', "Let's Talk Through It. Prefer Email?" );
$cta_body         = $pick( 'ai_cta_body', 'Tell us about your machine, part, process, and project goals, and include any drawings, photos, or specifications that may help. This will help our team come prepared to discuss your application.' );
$cta_button_label = $pick( 'ai_cta_button_label', 'Talk to an Engineer' );
$cta_button_url   = $pick( 'ai_cta_button_url', gerotech_quote_mailto() );
// Client (2026-09-22): use the FANUC rail-robot photo for this CTA band too — the
// same asset as the Engineered Solutions CTA, so it is deliberately shared.
$cta_image_value  = $pick( 'ai_cta_image', 'assets/images/cta-rail-robot.jpg' );
$cta_image        = gerotech_image_url( $cta_image_value, 'assets/images/cta-rail-robot.jpg' );
$cta_image_srcset = gerotech_image_srcset( $cta_image_value, 'assets/images/cta-rail-robot.jpg' );
// Content doc (2026): the phone call card is removed — blank number hides it.
$cta_call_label   = $pick( 'ai_cta_call_label', 'Prefer to talk it through?' );
$cta_call_number  = $pick( 'ai_cta_call_number', '' );
$cta_call_note    = $pick( 'ai_cta_call_note', 'Talk to a person, not a form.' );

/* ── Email signup ─────────────────────────────────────────── */
$signup_title = $pick( 'ai_signup_title', 'Join Our <em>Mailing List</em>' );
$signup_sub   = $pick( 'ai_signup_sub', 'Projects, machine updates, and service news — delivered to your inbox.' );

// Shared mailing-list form strings — global fields (Site Content → Forms).
$signup_email_label = gerotech_field( 'signup_email_label', 'Email address', 'option' );
$signup_email_ph    = gerotech_field( 'signup_email_placeholder', 'your@email.com', 'option' );
$signup_submit     = gerotech_field( 'signup_submit_label', 'Sign Up', 'option' );
?>

<main id="main">
    <section class="page-hero" aria-labelledby="ai-hero-headline">
      <img class="slide__bg slide__bg--right" src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( gerotech_image_alt( $hero_image_value, 'Yellow FANUC robot in a guarded automation cell beside CNC equipment' ) ); ?>" loading="eager" decoding="async" />
      <div class="slide__overlay slide__overlay--left" aria-hidden="true"></div>
      <div class="slide__content slide__content--left">
        <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
          <a class="page-hero__crumb-link" href="<?php gerotech_page_link( 'home' ); ?>"><?php echo esc_html( gerotech_shared_ui( 'breadcrumb_home_label' ) ); ?></a>
          <span class="page-hero__crumb-sep" aria-hidden="true"></span>
          <a class="page-hero__crumb-link" href="<?php gerotech_page_link( 'engineered-solutions' ); ?>"><?php echo esc_html( gerotech_shared_ui( 'breadcrumb_engineered_label' ) ); ?></a>
          <span class="page-hero__crumb-sep" aria-hidden="true"></span>
          <span class="page-hero__crumb-current"><span class="mcs-name-split__lead"><?php echo esc_html( $crumb_lead ); ?></span><?php if ( '' !== $crumb_main ) : ?> <span class="mcs-name-split__main"><?php echo esc_html( $crumb_main ); ?></span><?php endif; ?></span>
        </nav>
        <h1 class="slide__headline" id="ai-hero-headline">
          <span class="mcs-name-split mcs-name-split--hero">
            <span class="mcs-name-split__lead"><?php echo esc_html( $hero_lead ); ?></span>
            <span class="mcs-name-split__main"><?php echo gerotech_accent( $hero_main, gerotech_accent_class( $hero_accent ) ); ?></span>
          </span>
        </h1>
        <?php if ( $hero_body ) : ?>
        <p class="slide__body"><?php echo esc_html( $hero_body ); ?></p>
        <?php endif; ?>
      </div>
    </section>


    <section class="mcs-grid-section" id="ai-grid" aria-labelledby="ai-grid-headline">
      <div class="container container--es">
        <div class="section-header"><p class="eyebrow"><?php echo esc_html( $grid_eyebrow ); ?></p><h2 class="section-title" id="ai-grid-headline"><?php echo gerotech_accent( $grid_title, 'accent--deep' ); ?></h2><span class="headline-rule headline-rule--deep" aria-hidden="true"></span></div>
        <div class="mcs-grid">

          <?php foreach ( $cards as $card ) : ?>
          <?php
			$card_image = gerotech_image_url( isset( $card['image'] ) ? $card['image'] : '' );
			$card_id    = gerotech_card_id( isset( $card['title'] ) ? $card['title'] : '' );
			?>
          <article class="mcs-card"<?php echo $card_id ? ' id="' . esc_attr( $card_id ) . '"' : ''; ?> role="button" tabindex="0" aria-haspopup="dialog">
            <img class="mcs-card__image" src="<?php echo esc_url( $card_image ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy" />
            <div class="mcs-card__content"><h3 class="mcs-card__title"><?php echo esc_html( $card['title'] ); ?></h3><span class="mcs-card__cue"><?php echo esc_html( gerotech_shared_ui( 'card_cue_label' ) ); ?></span></div>
            <template><?php echo wp_kses_post( $card['detail'] ); ?><div class="mcs-modal__actions"><a class="btn btn--outline-orange" href="<?php echo esc_url( gerotech_shared_ui( 'card_cta_url' ) ); ?>"><?php echo esc_html( gerotech_shared_ui( 'card_cta_label' ) ); ?></a></div></template>
          </article>
          <?php endforeach; ?>

        </div>
      </div>
    </section>

    <!-- ============================================================
         Product Gallery
         ============================================================ -->
    <section class="mcs-gallery-section" aria-labelledby="ai-gallery-headline">
      <div class="container container--es">
        <div class="section-header">
          <?php if ( $gallery_eyebrow ) : ?>
          <p class="eyebrow"><?php echo esc_html( $gallery_eyebrow ); ?></p>
          <?php endif; ?>
          <h2 class="section-title" id="ai-gallery-headline"><?php echo gerotech_accent( $gallery_title, 'accent--deep' ); ?></h2>
          <span class="headline-rule headline-rule--deep" aria-hidden="true"></span>
        </div>
        <div class="gallery-collections" data-gallery>

          <?php foreach ( $collections as $col ) : ?>
          <?php
          $media      = gerotech_parse_media( isset( $col['media'] ) ? $col['media'] : '' );
          $first      = isset( $media[0] ) ? $media[0] : null;
          $cover      = '';
          $cover_alt  = '';
          if ( $first ) {
              $cover     = ( 'video' === $first['type'] && ! empty( $first['poster'] ) ) ? $first['poster'] : $first['src'];
              $cover_alt = $first['alt'];
          }
          ?>
          <article class="gallery-collection">
            <span class="gallery-collection__media">
              <img class="gallery-collection__cover" src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $cover_alt ); ?>" loading="lazy" />
              <span class="gallery-collection__badge"></span>
              <span class="gallery-collection__play" aria-hidden="true"><svg viewBox="0 0 12 12" focusable="false"><path fill="currentColor" d="M2 1.2 10.4 6 2 10.8z"/></svg></span>
            </span>
            <div class="gallery-collection__label">
              <h3><button type="button" class="gallery-collection__trigger" aria-haspopup="dialog"><?php echo esc_html( $col['title'] ); ?></button></h3>
              <?php if ( ! empty( $col['meta'] ) ) : ?>
              <p class="gallery-collection__meta"><?php echo esc_html( $col['meta'] ); ?></p>
              <?php endif; ?>
            </div>
            <template class="gallery-collection__data">
              <?php foreach ( $media as $m ) : ?>
                <?php if ( 'video' === $m['type'] ) : ?>
              <span data-type="video" data-src="<?php echo esc_url( $m['src'] ); ?>" data-poster="<?php echo esc_url( $m['poster'] ); ?>" data-alt="<?php echo esc_attr( $m['alt'] ); ?>" data-caption="<?php echo esc_attr( $m['caption'] ); ?>"></span>
                <?php else : ?>
              <span data-type="image" data-src="<?php echo esc_url( $m['src'] ); ?>" data-alt="<?php echo esc_attr( $m['alt'] ); ?>" data-caption="<?php echo esc_attr( $m['caption'] ); ?>"></span>
                <?php endif; ?>
              <?php endforeach; ?>
            </template>
          </article>
          <?php endforeach; ?>

        </div>
      </div>
    </section>

    <!-- Customer testimonials (shared partial) -->
    <?php get_template_part( 'template-parts/sections/testimonials' ); ?>

    <section class="cta-band cta-band--cinema cta-band--cinema-lockup" aria-label="Call to action">
      <img class="cta-band__bg" src="<?php echo esc_url( $cta_image ); ?>"<?php echo $cta_image_srcset ? ' srcset="' . esc_attr( $cta_image_srcset ) . '" sizes="100vw"' : ''; ?> alt="<?php echo esc_attr( gerotech_image_alt( $cta_image_value, 'FANUC robot on an overhead rail system in a Michigan manufacturing facility' ) ); ?>" loading="lazy" decoding="async" />
      <div class="cta-band__overlay" aria-hidden="true"></div>
      <div class="cta-band__content">
        <div class="cta-band__copy">
          <div class="eyebrow-row">
            <span class="eyebrow-row__rule" aria-hidden="true"></span>
            <p class="eyebrow eyebrow--orange"><?php echo esc_html( $cta_eyebrow ); ?></p>
          </div>
          <h2 class="cta-band__headline"><?php echo gerotech_accent( $cta_headline, 'cta-band__accent' ); ?></h2>
          <span class="cta-band__rule" aria-hidden="true"></span>
          <?php if ( $cta_subhead ) : ?>
          <p class="cta-band__subhead"><?php echo esc_html( $cta_subhead ); ?></p>
          <?php endif; ?>
          <?php if ( $cta_body ) : ?>
          <p class="cta-band__body"><?php echo esc_html( $cta_body ); ?></p>
          <?php endif; ?>
          <div class="cta-band__actions">
            <a class="btn btn--primary btn--lg" href="<?php echo esc_url( $cta_button_url ); ?>"><?php echo esc_html( $cta_button_label ); ?></a>
          </div>
        </div>
        <?php if ( $cta_call_number ) : ?>
        <a class="cta-band__call" href="<?php echo esc_url( gerotech_tel_link( $cta_call_number ) ); ?>">
          <span class="cta-band__call-label"><?php echo esc_html( $cta_call_label ); ?></span>
          <span class="cta-band__call-number"><?php echo esc_html( $cta_call_number ); ?></span>
          <span class="cta-band__call-note"><?php echo esc_html( $cta_call_note ); ?></span>
        </a>
        <?php endif; ?>
      </div>
    </section>

    <section class="email-signup" aria-label="Mailing list signup">
      <div class="email-signup__inner">
        <div class="email-signup__copy">
          <h2 class="email-signup__title"><?php echo gerotech_accent( $signup_title, 'accent' ); ?></h2><p class="email-signup__sub"><?php echo esc_html( $signup_sub ); ?></p>
        </div>
        <form class="email-signup__form" action="#" method="post" novalidate><label for="email-input-ai" class="sr-only"><?php echo esc_html( $signup_email_label ); ?></label><input class="email-signup__input" id="email-input-ai" type="email" name="email" placeholder="<?php echo esc_attr( $signup_email_ph ); ?>" required autocomplete="email" /><button class="email-signup__submit" type="submit"><?php echo esc_html( $signup_submit ); ?></button></form>
      </div>
    </section>
  </main>

<?php
get_footer();
