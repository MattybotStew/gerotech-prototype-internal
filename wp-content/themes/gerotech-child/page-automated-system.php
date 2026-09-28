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
$hero_accent  = $pick( 'ai_hero_accent_color', 'orange' ); // Blank (no stored choice) keeps the design colour.
$hero_body    = $pick( 'ai_hero_body', 'From electrical controls and HMI design to full automation cells and pre-engineered packages, Gerotech delivers complete integration solutions — any make, any control, built around your production reality.' );
$hero_image   = gerotech_image_url( $pick( 'ai_hero_image', 'assets/images/automation-hero.jpg' ) );

/* ── Services grid ────────────────────────────────────────── */
$grid_eyebrow = $pick( 'ai_grid_eyebrow', 'What We Offer' );
$grid_title   = $pick( 'ai_grid_title', 'Automation and Controls <em>Solutions</em> Services' );
$cards        = $pick(
	'ai_cards',
	array(
		array(
			'title'  => 'HMI Design',
			'image'  => 'assets/images/hmi-design.jpg',
			'detail' => '<p>Every application is different, and the operator interface should reflect the needs of the people using it. Our HMI is fully configurable, providing a centralized location for the information and functions required for efficient day-to-day operation.</p><p>Our software library provides extensive I/O and Ethernet diagnostics directly on the HMI — giving operators and maintenance technicians clear visibility into machine status without a programming laptop. Where supported, device-specific diagnostics include fault codes, descriptions, and recommended corrective actions for faster troubleshooting and reduced downtime.</p>',
		),
		array(
			'title'  => 'Layered Controls Solutions',
			'image'  => 'assets/images/layered-controls.jpg',
			'detail' => '<p>Our Layered Controls approach organizes automation into three integrated levels, each building on the last to deliver a complete, coordinated manufacturing system.</p><details open><summary>Layer 1 — Machine Tool</summary><p>The OEM CNC control remains responsible for the machine\'s core manufacturing functions, including axis motion, spindle control, tool changes, and machining cycles. For applications requiring additional functionality, we specialize in implementing targeted enhancements to the existing control system, extending the machine\'s capabilities while preserving the OEM control architecture.</p></details><details><summary>Layer 2 — Machine Tool Automation</summary><p>Our Machine Automation package extends the capabilities of the CNC machine with features that are specific to your manufacturing process.</p><p><strong>Typical extended capabilities include:</strong></p><ul><li>Automatic door control</li><li>Part presence verification</li><li>Machine status monitoring</li><li>Custom I/O integration</li><li>Safety interfaces</li><li>Pneumatic and hydraulic systems</li><li>Coolant and chip management</li><li>Operator interfaces</li><li>Process-specific automation</li></ul><p><strong>Machine Tool Control Packages:</strong> Rather than designing every system from the ground up, we offer a family of pre-engineered automation solutions that can be configured to match your application\'s requirements. From cost-effective machine automation packages to fully featured control systems, each solution is designed to provide the right balance of functionality, performance, and investment.</p><p>Every platform is built on proven software, standardized engineering practices, and years of real-world manufacturing experience, allowing us to deliver custom solutions with reduced engineering time, lower project risk, and faster implementation.</p><ul><li>Reduced engineering time</li><li>Faster project delivery</li><li>Lower project risk</li><li>Proven, reliable software</li><li>Consistent operator experience</li><li>Flexible architecture that adapts to a wide range of machine types and applications</li><li>Simplified future enhancements and support</li></ul></details><details><summary>Layer 3 — Automation Cells</summary><p>The Cell Controller coordinates the entire manufacturing system by managing communication between machines, robots, conveyors, vision systems, and peripheral equipment.</p><p><strong>Responsibilities include:</strong></p><ul><li>Robot coordination</li><li>Part routing</li><li>Cell sequencing</li><li>Production scheduling</li><li>Vision integration</li><li>Data collection</li><li>Fault recovery</li><li>System diagnostics</li></ul></details>',
		),
		array(
			'title'  => 'Automation Cell Design',
			'image'  => 'assets/images/automation-cell-design.jpg',
			'detail' => '<p>The Cell Controller coordinates the entire manufacturing system by managing communication between machines, robots, conveyors, vision systems, and peripheral equipment.</p><p><strong>Responsibilities include:</strong></p><ul><li>Robot coordination</li><li>Part routing</li><li>Cell sequencing</li><li>Production scheduling</li><li>Vision integration</li><li>Data collection</li><li>Fault recovery</li><li>System diagnostics</li></ul>',
		),
		array(
			'title'  => 'Robot EOAT – Ancillary Material Handling',
			'image'  => 'assets/images/robot-eoat.jpg',
			'detail' => '<p>Custom end-of-arm tooling and ancillary material handling solutions engineered to match your part geometry, cycle time requirements, and production environment.</p>',
		),
		array(
			'title'  => 'Pre-Engineered Solutions',
			'image'  => 'assets/images/pre-engineered-card.jpg',
			'detail' => '<p>Rather than designing every system from the ground up, we offer a family of pre-engineered automation solutions that can be configured to match your application\'s requirements — from cost-effective machine automation packages to fully featured control systems.</p><p>Every platform is built on proven software, standardized engineering practices, and years of real-world manufacturing experience, delivering custom solutions with reduced engineering time, lower project risk, and faster implementation.</p><ul><li>Reduced engineering time &amp; faster project delivery</li><li>Lower project risk with proven, reliable software</li><li>Consistent operator experience across platforms</li><li>Flexible architecture — adapts to a wide range of machine types</li><li>Simplified future enhancements and support</li></ul><details><summary>Standardized Software Design Methodology</summary><p>Our automation solutions are developed using a standardized software design methodology that has been refined through years of real-world manufacturing applications. This proven approach provides a consistent programming structure, operator experience, and diagnostic philosophy across our automation platforms.</p><p>By developing from a common software foundation and adapting it to the selected control platform, we can deliver custom automation solutions more efficiently while maintaining proven functionality, consistent operation, and high-quality software.</p><p><strong>Key Benefits:</strong></p><ul><li>Proven software foundation</li><li>Standardized programming methodology</li><li>Consistent HMI navigation and operator experience</li><li>Common alarms, diagnostics, and fault recovery</li><li>Faster project development</li><li>Reduced project risk</li><li>Simplified troubleshooting and maintenance</li><li>Easier operator training</li><li>Flexible deployment across multiple control platforms</li><li>Scalable design for future expansion</li></ul></details>',
		),
	)
);

/* ── Gallery ──────────────────────────────────────────────── */
$gallery_eyebrow = $pick( 'ai_gallery_eyebrow', 'Gallery' );
$gallery_title   = $pick( 'ai_gallery_title', 'Installed Automation <em>Gallery</em>' );
$collections     = $pick(
	'ai_collections',
	array(
		array(
			'title' => 'HMI Design',
			'meta'  => 'Operator screens · cell overview · diagnostics',
			'media' => implode( "\n", array(
				"image | {$uri}/assets/images/automation-gallery/hmi-operator-1.jpg | | Gerotech operator screen showing part complete and fixture presence | Operator screen · part complete",
				"image | {$uri}/assets/images/automation-gallery/hmi-operator-2.jpg | | Gerotech operator screen, closer view of part complete | Operator screen · part complete",
				"image | {$uri}/assets/images/automation-gallery/hmi-operator-3.jpg | | Gerotech operator screen with the full button bar | Operator screen · full controls",
				"image | {$uri}/assets/images/automation-gallery/hmi-cell-overview.jpg | | Gerotech cell overview screen with mill and robot status | Cell overview",
				"image | {$uri}/assets/images/automation-gallery/hmi-diagnostics.jpg | | Gerotech diagnostics screen showing safety inputs and a door-open fault | Diagnostics · safety inputs",
			) ),
		),
		array(
			'title' => 'Layered Controls Solutions',
			'meta'  => 'Machine tool · controls · automation cells',
			'media' => "image | {$uri}/assets/images/automation-gallery/layered-controls-diagram.jpg | | Layered controls diagram: machine tool, machine tool controls, and automation cells | Layer 01 machine tool · Layer 02 controls · Layer 03 cells",
		),
		array(
			'title' => 'Automation Cell Design',
			'meta'  => 'FANUC M-20iD/25 · Haas ST-10',
			'media' => "image | {$uri}/assets/images/automation-cell-design.jpg | | FANUC M-20iD/25 tending a Haas ST-10 in a guarded cell | FANUC M-20iD/25 · Haas ST-10",
		),
		array(
			'title' => 'Robot EOAT – Ancillary Material Handling',
			'meta'  => 'Custom end-of-arm tooling',
			'media' => "image | {$uri}/assets/images/robot-eoat.jpg | | Custom dual-gripper end-of-arm tooling | Custom end-of-arm tooling",
		),
		array(
			'title' => 'Pre-Engineered Solutions',
			'meta'  => 'Control enclosure · integrated tooling',
			'media' => "image | {$uri}/assets/images/pre-engineered-gallery.jpg | | Open dual-door control cabinet on the shop floor beside a Haas machine | Control enclosure · integrated tooling",
		),
	)
);

/* ── CTA band ─────────────────────────────────────────────── */
$cta_eyebrow      = $pick( 'ai_cta_eyebrow', 'Automation &amp; Controls' );
$cta_headline     = $pick( 'ai_cta_headline', 'Need a <em>custom solution</em> for your machine?' );
$cta_body         = $pick( 'ai_cta_body', "Robot cells, workholding, and controls — designed, built, and installed by Gerotech's in-house engineering team." );
$cta_button_label = $pick( 'ai_cta_button_label', 'Talk to an Engineer' );
$cta_button_url   = $pick( 'ai_cta_button_url', gerotech_quote_mailto() );
// Client (2026-09-22): use the FANUC rail-robot photo for this CTA band too — the
// same asset as the Engineered Solutions CTA, so it is deliberately shared.
$cta_image_value  = $pick( 'ai_cta_image', 'assets/images/cta-rail-robot.jpg' );
$cta_image        = gerotech_image_url( $cta_image_value, 'assets/images/cta-rail-robot.jpg' );
$cta_image_srcset = gerotech_image_srcset( $cta_image_value, 'assets/images/cta-rail-robot.jpg' );
$cta_call_label   = $pick( 'ai_cta_call_label', 'Prefer to talk it through?' );
$cta_call_number  = $pick( 'ai_cta_call_number', '(734) 379-7788' );
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
      <img class="slide__bg slide__bg--right" src="<?php echo esc_url( $hero_image ); ?>" alt="Robotic automation cell with machine guarding" loading="eager" decoding="async" />
      <div class="slide__overlay slide__overlay--left" aria-hidden="true"></div>
      <div class="slide__content slide__content--left">
        <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
          <a class="page-hero__crumb-link" href="<?php gerotech_page_link( 'home' ); ?>">Home</a>
          <span class="page-hero__crumb-sep" aria-hidden="true">/</span>
          <a class="page-hero__crumb-link" href="<?php gerotech_page_link( 'engineered-solutions' ); ?>">Engineered Solutions</a>
          <span class="page-hero__crumb-sep" aria-hidden="true">/</span>
          <span class="page-hero__crumb-current"><span class="mcs-name-split__lead"><?php echo esc_html( $hero_lead ); ?></span> <span class="mcs-name-split__main"><?php echo esc_html( strip_tags( $hero_main ) ); ?></span></span>
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
          <?php $card_image = gerotech_image_url( isset( $card['image'] ) ? $card['image'] : '' ); ?>
          <article class="mcs-card" role="button" tabindex="0" aria-haspopup="dialog">
            <img class="mcs-card__image" src="<?php echo esc_url( $card_image ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy" />
            <div class="mcs-card__content"><h3 class="mcs-card__title"><?php echo esc_html( $card['title'] ); ?></h3><span class="mcs-card__cue">View Details →</span></div>
            <template><?php echo wp_kses_post( $card['detail'] ); ?><div class="mcs-modal__actions"><a class="btn btn--outline-orange" href="tel:+17343797788">Talk to an Engineer</a></div></template>
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
          <p class="eyebrow"><?php echo esc_html( $gallery_eyebrow ); ?></p>
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
      <img class="cta-band__bg" src="<?php echo esc_url( $cta_image ); ?>"<?php echo $cta_image_srcset ? ' srcset="' . esc_attr( $cta_image_srcset ) . '" sizes="100vw"' : ''; ?> alt="FANUC robot on an overhead rail system in a Michigan manufacturing facility" loading="lazy" decoding="async" />
      <div class="cta-band__overlay" aria-hidden="true"></div>
      <div class="cta-band__content">
        <div class="cta-band__copy">
          <div class="eyebrow-row">
            <span class="eyebrow-row__rule" aria-hidden="true"></span>
            <p class="eyebrow eyebrow--orange"><?php echo esc_html( $cta_eyebrow ); ?></p>
          </div>
          <h2 class="cta-band__headline"><?php echo gerotech_accent( $cta_headline, 'cta-band__accent' ); ?></h2>
          <span class="cta-band__rule" aria-hidden="true"></span>
          <?php if ( $cta_body ) : ?>
          <p class="cta-band__body"><?php echo esc_html( $cta_body ); ?></p>
          <?php endif; ?>
          <div class="cta-band__actions">
            <a class="btn btn--primary btn--lg" href="<?php echo esc_url( $cta_button_url ); ?>"><?php echo esc_html( $cta_button_label ); ?></a>
          </div>
        </div>
        <a class="cta-band__call" href="tel:+17343797788">
          <span class="cta-band__call-label"><?php echo esc_html( $cta_call_label ); ?></span>
          <span class="cta-band__call-number"><?php echo esc_html( $cta_call_number ); ?></span>
          <span class="cta-band__call-note"><?php echo esc_html( $cta_call_note ); ?></span>
        </a>
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
