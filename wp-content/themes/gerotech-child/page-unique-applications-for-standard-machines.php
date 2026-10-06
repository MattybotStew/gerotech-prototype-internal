<?php
/**
 * application page template.
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
$hero_headline = $pick( 'app_hero_headline', 'Applications <em>Solutions</em>' );
$hero_breadcrumb = $pick( 'app_hero_breadcrumb', 'Applications' );
$hero_accent   = $pick( 'app_hero_accent_color', 'orange' ); // Blank (no stored choice) keeps the design colour.
$hero_image_value = $pick( 'app_hero_image', 'assets/images/app-hero.jpg' );
$hero_image    = gerotech_image_url( $hero_image_value, 'assets/images/app-hero.jpg' );

/* ── Services grid ────────────────────────────────────────── */
$grid_eyebrow = $pick( 'app_grid_eyebrow', 'What We Offer' );
$grid_title   = $pick( 'app_grid_title', '<em>Applications</em> Services' );
$cards        = $pick(
	'app_cards',
	array(
		array(
			'title'  => 'Part Programming',
			'image'  => 'assets/images/app-gallery-umc750.jpg',
			'detail' => '<p>We write programs that get the most out of your machine. From simple 2-axis turning and 3-axis milling work to complex multi-axis solutions, our application engineers handle the full range — G-code, conversational programming, and CAM-generated toolpaths.</p><p>Whether you need a one-off program or high-volume production, we will deliver a reliable toolpath that is right for your part.</p>',
		),
		array(
			'title'  => 'Process Troubleshooting',
			'image'  => 'assets/images/app-troubleshooting.jpg',
			'detail' => '<p>When you\'re struggling to resolve a tool path issue, our talented team of Application Engineers is here to assist.</p><p>If the root cause is not obvious from looking at the program, we can take your program and run it through our simulators or, when necessary, trial it on one of our showroom machines depending on the model fit.</p><p>Reach out at applications@gerotech.com with a brief description of your issue along with the necessary tooling and program information.</p>',
		),
		array(
			'title'  => 'Process Optimization',
			'image'  => 'assets/images/app-optimization.jpg',
			'detail' => '<p>Every program is optimized for cycle time, tool life, and part quality. We handle custom probing and macro programming when the standard available routines don\'t meet your needs.</p>',
		),
		array(
			'title'  => 'Tooling Recommendation',
			'image'  => 'assets/images/app-tooling-cart.jpg',
			'detail' => '<p>Our Application Engineers utilize Mfg Engineering backgrounds to help select tooling matched to your material, machine, and process — selected for your job, not the catalog. From standard tooling to custom tooling, we can help you optimize process performance on your shop floor.</p>',
		),
		array(
			'title'  => 'Demo',
			'image'  => 'assets/images/app-demo-showroom.jpg',
			'detail' => '<p>We can run machine demos for any machine in the showroom.</p><p>If you have a specific part you would like to see demoed, with a shared approach for materials and tooling we can accommodate it provided the machine model is the right fit to our showroom equipment.</p>',
		),
		array(
			'title'  => 'Training',
			'image'  => 'assets/images/app-training.jpg',
			'detail' => '<p>Gerotech offers, at no cost to our customers, instructor-led operator training for basic lathe/mill, VPS, Intro to G&amp;M code, and programming classes for both Mill and Lathe at our Grand Rapids location and our Macomb Community College partner.</p><p>If you need onsite training, our application engineers can tailor training to your needs for a fee. Contact your Account Manager to discuss.</p>',
		),
	)
);

/* ── Gallery ──────────────────────────────────────────────── */
$gallery_title = $pick( 'app_gallery_title', 'Applications Product <em>Gallery</em>' );
$collections   = $pick(
	'app_collections',
	array(
		array(
			'title' => 'Part Programming',
			'meta'  => '',
			// One media item per line. The collection `meta` is a static label describing
			// the whole set — the photo/video count is a separate badge the JS computes.
			'media' => "image | {$uri}/assets/images/app-gallery-umc750.jpg | | Haas UMC-750 5-axis machining | UMC-750 · 5-axis machining\n"
				. "image | {$uri}/assets/images/app-gallery-milling-coolant.jpg | | Milling operation under high-pressure coolant | Milling · high-pressure coolant\n"
				. "image | {$uri}/assets/images/app-gallery-turning-large.jpg | | Large-diameter turning operation | Turning · large-diameter work\n"
				. "image | {$uri}/assets/images/app-gallery-turning-drill.jpg | | Turning setup with a drilling operation | Turning · drilling\n"
				. "image | {$uri}/assets/images/app-gallery-coolant-impeller.jpg | | Coolant spray on an impeller being machined | Milling · coolant on impeller\n"
				. "image | {$uri}/assets/images/app-gallery-boring.jpg | | Boring bar on a large turned part | Turning · boring\n"
				. "image | {$uri}/assets/images/app-gallery-drill.jpg | | Drill approaching a chucked part | Turning · drilling a chucked part",
		),
		array(
			'title' => 'Process Troubleshooting',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/app-troubleshooting.jpg | | Coolant blasting a part while a tool cuts inside a CNC | Process Troubleshooting · in the cut",
		),
		array(
			'title' => 'Process Optimization',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/app-optimization-gallery.jpg | | Circular process optimization diagram over a machined part: cycle time reduction, part quality, tooling performance | Process Optimisation · cycle time, quality, tooling",
		),
		array(
			'title' => 'Tooling Recommendation',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/app-tooling-cart.jpg | | Haas Tooling.com red mobile cart with tool holders and pegboard display | Haas Tooling.com cart",
		),
		array(
			'title' => 'Demo',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/app-demo-showroom.jpg | | Gerotech showroom with a Haas UMC-750 | Showroom · Haas UMC-750\n"
				. "image | {$uri}/assets/images/app-gallery-demo-dc1.jpg | | Gerotech instructor demonstrating a Haas DC-1 to customers in the showroom | Demo · Haas DC-1\n"
				. "image | {$uri}/assets/images/app-gallery-demo-showroom-group.jpg | | Customers touring a Haas UMC machining center in the Gerotech showroom | Showroom · customer demo",
		),
		array(
			'title' => 'Training',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/app-training.jpg | | Gerotech instructor walking a customer through a Haas control | Training session · Haas control walkthrough\n"
				. "image | {$uri}/assets/images/app-gallery-training-control.jpg | | Operator programming at a Haas control | Programming at the control\n"
				. "image | {$uri}/assets/images/app-gallery-training-group.jpg | | Training session around a Haas VF-3 machining centre | Group session · Haas VF-3\n"
				. "image | {$uri}/assets/images/app-gallery-training-qr.jpg | | Instructor at a Haas control showing a QR code on screen | Haas control · programming\n"
				. "image | {$uri}/assets/images/app-gallery-training-vf3.jpg | | Training group gathered at a Haas VF-3 | Group session · VF-3, wide",
		),
	)
);

/* ── CTA band ─────────────────────────────────────────────── */
$cta_eyebrow      = $pick( 'app_cta_eyebrow', 'Application' );
$cta_headline     = $pick( 'app_cta_headline', 'Need <em>application support</em> for your shop floor?' );
$cta_subhead      = $pick( 'app_cta_subhead', "Let's Talk Through It. Prefer Email?" );
$cta_body         = $pick( 'app_cta_body', 'Tell us about your machine, part, process, and project goals, and include any drawings, photos, or specifications that may help. This will help our team come prepared to discuss your application.' );
$cta_button_label = $pick( 'app_cta_button_label', 'Talk to an Engineer' );
$cta_button_url   = $pick( 'app_cta_button_url', gerotech_quote_mailto() );
// Client (2026-09-22): "use the same picture that we have for the page header,
// the same for this footer" — so the CTA band intentionally shares app-hero.jpg.
$cta_image_value  = $pick( 'app_cta_image', 'assets/images/app-hero.jpg' );
$cta_image        = gerotech_image_url( $cta_image_value, 'assets/images/app-hero.jpg' );
// Content doc (2026): the phone call card is removed — blank number hides it.
$cta_call_label   = $pick( 'app_cta_call_label', 'Prefer to talk it through?' );
$cta_call_number  = $pick( 'app_cta_call_number', '' );
$cta_call_note    = $pick( 'app_cta_call_note', 'Talk to a person, not a form.' );

/* ── Email signup ─────────────────────────────────────────── */
$signup_title = $pick( 'app_signup_title', 'Join Our <em>Mailing List</em>' );
$signup_sub   = $pick( 'app_signup_sub', 'Projects, machine updates, and service news — delivered to your inbox.' );

// Shared mailing-list form strings — global fields (Site Content → Forms).
$signup_email_label = gerotech_field( 'signup_email_label', 'Email address', 'option' );
$signup_email_ph    = gerotech_field( 'signup_email_placeholder', 'your@email.com', 'option' );
$signup_submit     = gerotech_field( 'signup_submit_label', 'Sign Up', 'option' );
?>

<main id="main">
    <section class="page-hero" aria-labelledby="app-hero-headline">
      <img class="slide__bg slide__bg--right" src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( gerotech_image_alt( $hero_image_value, 'CNC machining application' ) ); ?>" loading="eager" decoding="async" />
      <div class="slide__overlay slide__overlay--left" aria-hidden="true"></div>
      <div class="slide__content slide__content--left">
        <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
          <a class="page-hero__crumb-link" href="<?php gerotech_page_link( 'home' ); ?>"><?php echo esc_html( gerotech_shared_ui( 'breadcrumb_home_label' ) ); ?></a>
          <span class="page-hero__crumb-sep" aria-hidden="true"></span>
          <a class="page-hero__crumb-link" href="<?php gerotech_page_link( 'engineered-solutions' ); ?>"><?php echo esc_html( gerotech_shared_ui( 'breadcrumb_engineered_label' ) ); ?></a>
          <span class="page-hero__crumb-sep" aria-hidden="true"></span>
          <span class="page-hero__crumb-current"><?php echo esc_html( $hero_breadcrumb ); ?></span>
        </nav>
        <h1 class="slide__headline" id="app-hero-headline"><?php echo gerotech_accent( $hero_headline, gerotech_accent_class( $hero_accent ) ); ?></h1>
      </div>
    </section>


    <section class="mcs-grid-section" id="app-grid" aria-labelledby="app-grid-headline">
      <div class="container container--es">
        <div class="section-header"><p class="eyebrow"><?php echo esc_html( $grid_eyebrow ); ?></p><h2 class="section-title" id="app-grid-headline"><?php echo gerotech_accent( $grid_title, 'accent--deep' ); ?></h2><span class="headline-rule headline-rule--deep" aria-hidden="true"></span></div>
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
    <section class="mcs-gallery-section" aria-labelledby="app-gallery-headline">
      <div class="container container--es">
        <div class="section-header">
          <h2 class="section-title" id="app-gallery-headline"><?php echo gerotech_accent( $gallery_title, 'accent--deep' ); ?></h2>
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
      <img class="cta-band__bg" src="<?php echo esc_url( $cta_image ); ?>" alt="<?php echo esc_attr( gerotech_image_alt( $cta_image_value, 'CNC machining and applications' ) ); ?>" loading="lazy" />
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
          <h2 class="email-signup__title"><?php echo gerotech_accent( $signup_title, 'accent' ); ?></h2>
          <p class="email-signup__sub"><?php echo esc_html( $signup_sub ); ?></p>
        </div>
        <form class="email-signup__form" action="#" method="post" novalidate>
          <label for="email-input-app" class="sr-only"><?php echo esc_html( $signup_email_label ); ?></label>
          <input class="email-signup__input" id="email-input-app" type="email" name="email" placeholder="<?php echo esc_attr( $signup_email_ph ); ?>" required autocomplete="email" />
          <button class="email-signup__submit" type="submit"><?php echo esc_html( $signup_submit ); ?></button>
        </form>
      </div>
    </section>
  </main>

<?php
get_footer();
