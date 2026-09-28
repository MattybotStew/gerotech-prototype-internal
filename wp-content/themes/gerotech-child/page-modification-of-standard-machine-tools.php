<?php
/**
 * machine-custom-solutions page template.
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
$hero_lead    = $pick( 'mcs_hero_lead', 'Machine' );
$hero_main    = $pick( 'mcs_hero_main', 'Custom <em>Solutions</em>' );
// Same accent behaviour as the homepage hero: blank choice keeps the design colour
// (Brand Orange on interior pages) and <em> words in the lead/main pick up that colour.
$hero_accent  = $pick( 'mcs_hero_accent_color', 'orange' );
$hero_accent_class = gerotech_accent_class( $hero_accent );
$hero_image_value = $pick( 'mcs_hero_image', 'assets/images/mcs-hero.jpg' );
$hero_image       = gerotech_image_url( $hero_image_value, 'assets/images/mcs-hero.jpg' );
$hero_srcset      = gerotech_image_srcset( $hero_image_value, 'assets/images/mcs-hero.jpg' );

/* ── Services grid ────────────────────────────────────────── */
$grid_eyebrow = $pick( 'mcs_grid_eyebrow', 'What We Offer' );
$grid_lead    = $pick( 'mcs_grid_lead', 'Machine' );
$grid_main    = $pick( 'mcs_grid_main', 'Custom Solutions' );
$cards        = $pick(
	'mcs_cards',
	array(
		array(
			'title'  => 'Machine Column Risers',
			'image'  => 'assets/images/mcs-gallery/column-riser.jpg',
			'detail' => '<p>A Mill column riser is a precision ground block installed between the machine base and the column along with custom sheet metal to accommodate the change in height. Column riser will increase the clearance height of the machine between the spindle and table. This can be beneficial when machining taller parts, adding 4th/5th rotary tables, that may limit access to part features or restrict tooling options. Additional advantage — avoid the need for larger machines that only require Z axis clearance.</p>',
		),
		array(
			'title'  => 'Safety &amp; Environmental Modifications',
			'image'  => 'assets/images/mcs-gallery/fire-suppression.jpg',
			'detail' => '<p>For applications that require additional safety controls, fully machine integrated fire protection, door and window interlocks, light curtains (Presence-Sensing) are required to ensure adequate protection for the operator and machine. To improve air quality from mist, dust, fumes, the appropriate collection system can collect air contaminants during the machining process.</p>',
		),
		array(
			'title'  => 'Sheet Metal Modifications',
			'image'  => 'assets/images/mcs-gallery/sheet-metal-stainless.jpg',
			'detail' => '<p>Sheet metal modifications become necessary for various reasons to expand working envelops, contain fluids, clearance to mention a few. Here are few sheet metal examples that may benefit your machining process.</p><ul><li><strong>X-Axis Bump Out:</strong> For longer parts that fit within the machining window of a smaller machine but due to total part length or workholding interference with side panels. The side panels can have extensions called bump outs.</li><li><strong>Tool Changer:</strong> When larger diameter tools or right-angle heads cannot fit through the tool changer modifications can be made to accommodate many situations.</li><li><strong>Tool Changer Door:</strong> To ensure coolant and chips cannot escape into the tool changer a custom shutter door solution can be integrated into the machine tool change sequence.</li><li><strong>Sealing Solutions:</strong> When utilizing high pressure coolant there may be a need for additional sealing to contain liquids. For problem leak areas that are a nuisance additional skirts and drip guarding can be an inexpensive fix.</li></ul>',
		),
		array(
			'title'  => 'Auto Doors',
			'image'  => 'assets/images/mcs-gallery/auto-door-haas.jpg',
			// Optional: when set, the card renders a muted looping video instead of the
			// photo. `image` is still used as the video poster (no blank frame on load,
			// and reduced-motion users keep the still).
			'video'  => 'assets/videos/auto-door.mp4',
			'detail' => '<p><strong>Horizontal Door:</strong> We offer a custom Servax door drive solution that provide enhanced safety and reliability. Fully integrated with your machine tool. This solution is ideal for single or double door machines. The intelligent self-monitoring feature reliable, integrated safety functions. Position, speed and torque are constantly monitored. Automatic reacts to obstacles immediately changing directions. Light curtains and two-hand buttons are not required.</p><p><strong>Vertical Door:</strong> Vertical doors are a great option for machine tending robot cells. They allow for operator full access into the primary door without having to enter the robot cell. Door is fully integrated with machine and output provided to robot cell.</p>',
		),
		array(
			'title'  => 'Hydraulic – Pneumatics',
			'image'  => 'assets/images/mcs-gallery/hydraulic-rotary.jpg',
			'detail' => '<p>Hydraulic and pneumatic circuit integration for workholding, clamping, actuators, and other production-enhancing machine functions.</p>',
		),
		array(
			'title'  => 'Custom Workholding',
			'image'  => 'assets/images/custom-workholding-plate.jpg',
			'detail' => '<p>Purpose-built fixtures designed for your specific part — improving repeatability, reducing setup time, and enabling automation-ready production.</p>',
		),
		array(
			'title'  => 'Process Engineering',
			'image'  => 'https://images.unsplash.com/photo-1666634157070-6fd830fb5672?q=80&w=800&auto=format&fit=crop',
			'detail' => '<p>On-site process development and engineering analysis to optimize your machining workflow, reduce cycle time, and improve part quality.</p>',
		),
		array(
			'title'  => 'Specialty Machine',
			'image'  => 'assets/images/mcs-gallery/specialty-machine.jpg',
			'detail' => '<p>One-off machine builds and custom engineering for unique production requirements where off-the-shelf equipment will not do.</p>',
		),
	)
);

/* ── Gallery ──────────────────────────────────────────────── */
$gallery_eyebrow = $pick( 'mcs_gallery_eyebrow', 'On the Floor' );
$gallery_title   = $pick( 'mcs_gallery_title', 'Installed <em>Gallery</em>' );
$gallery_body    = $pick( 'mcs_gallery_body', 'Recent customization and retrofit work from Gerotech engineers.' );
$collections     = $pick(
	'mcs_collections',
	array(
		array(
			'title' => 'Column Riser',
			'meta'  => 'Machine column risers',
			'media' => "image | {$uri}/assets/images/mcs-gallery/column-riser.jpg | | Machine column riser between base and column | Column riser · increased Z-axis clearance",
		),
		array(
			'title' => 'Fire Suppression',
			'meta'  => 'Machine-integrated fire protection',
			'media' => "image | {$uri}/assets/images/mcs-gallery/fire-suppression.jpg | | Kidde machine-integrated fire suppression | Kidde system · machine-integrated",
		),
		array(
			'title' => 'Sheet Metal Modification',
			'meta'  => 'Guards · enclosures · fabrication',
			'media' => "image | {$uri}/assets/images/mcs-gallery/sheet-metal-stainless.jpg | | Custom stainless sheet metal guards and covers | Stainless guards and covers\nimage | {$uri}/assets/images/mcs-gallery/sheet-metal-enclosure.jpg | | Painted sheet metal enclosure wrap on a machine column | Painted enclosure fabrication\nimage | {$uri}/assets/images/mcs-gallery/sheet-metal-machine.jpg | | Custom sheet metal enclosure wrapping a machining center | Full enclosure fabrication",
		),
		array(
			'title' => 'Auto Door Integration',
			'meta'  => 'Servak · vertical doors',
			'media' => "image | {$uri}/assets/images/mcs-gallery/auto-door-haas.jpg | | Servak auto door on a Haas mill | Servak auto door · Haas mill\nimage | {$uri}/assets/images/mcs-gallery/vertical-door-closed.jpg | | Vertical auto door closed on a mill | Vertical door · closed\nimage | {$uri}/assets/images/mcs-gallery/vertical-door-window.jpg | | Vertical auto door with window on a mill | Vertical door · windowed\nimage | {$uri}/assets/images/mcs-gallery/vertical-door-drive.jpg | | Vertical auto door drive assembly | Vertical door · drive\nimage | {$uri}/assets/images/mcs-gallery/auto-door-vf2yt.jpg | | Haas VF-2YT with both auto doors open and control pendant on the right | Haas VF-2YT · doors open\nimage | {$uri}/assets/images/mcs-gallery/auto-door-servax.jpg | | Servax Drives actuator on top of a Haas VF-2YT enclosure | Servax Drives actuator · VF-2YT\nimage | {$uri}/assets/images/mcs-gallery/auto-door-pendant.jpg | | Auto door control pendant with DOOR MANUAL / DOOR AUTO switch and status lights | Door control pendant\nvideo | {$uri}/assets/videos/auto-door.mp4 | {$uri}/assets/images/mcs-gallery/auto-door-haas.jpg | Auto door cycling video | Auto door cycling · bench test",
		),
		array(
			'title' => 'Hydraulic / Pneumatic',
			'meta'  => 'Rotary and workholding',
			'media' => "image | {$uri}/assets/images/mcs-gallery/hydraulic-rotary.jpg | | Hydraulic rotary and workholding integration | Rotary and workholding integration",
		),
		array(
			'title' => 'Custom Workholding',
			'meta'  => 'Tombstone fixtures · custom plates',
			'media' => "image | {$uri}/assets/images/mcs-gallery/workholding-p2.jpg | | Serrated tombstone fixture plate marked P2 | Tombstone plate · P2\nimage | {$uri}/assets/images/mcs-gallery/workholding-tombstone.jpg | | Dark tombstone fixture marked 259-300-15 | Tombstone fixture · 259-300-15\nimage | {$uri}/assets/images/mcs-gallery/workholding-gtd-11962.jpg | | Gerotech fixture GTD-11962 marked 1B | GTD-11962 · 1B\nimage | {$uri}/assets/images/mcs-gallery/workholding-gtd-11961.jpg | | Gerotech fixture GTD-11961 holding a part marked 1A | GTD-11961 · 1A",
		),
		array(
			'title' => 'Safety &amp; Environmental',
			'meta'  => 'Mist collection · air quality',
			'media' => "image | {$uri}/assets/images/mcs-gallery/mist-torit.jpg | | Donaldson Torit mist collector on a mill | Donaldson Torit mist collector\nimage | {$uri}/assets/images/mcs-gallery/mist-lina.jpg | | LINA3nine mist collector on a mill | LINA3nine mist collector",
		),
	)
);

/* ── CTA band ─────────────────────────────────────────────── */
$cta_eyebrow      = $pick( 'mcs_cta_eyebrow', 'Machine Custom Solutions' );
$cta_headline     = $pick( 'mcs_cta_headline', 'Need a <em>custom solution</em> for your machine?' );
$cta_body         = $pick( 'mcs_cta_body', '' );
$cta_button_label = $pick( 'mcs_cta_button_label', 'Talk to an Engineer' );
$cta_button_url   = $pick( 'mcs_cta_button_url', gerotech_quote_mailto() );
$cta_image        = gerotech_image_url( $pick( 'mcs_cta_image', 'assets/images/mcs-gallery/sheet-metal-machine.jpg' ) );
$cta_call_label   = $pick( 'mcs_cta_call_label', 'Prefer to talk it through?' );
$cta_call_number  = $pick( 'mcs_cta_call_number', '(734) 379-7788' );
$cta_call_note    = $pick( 'mcs_cta_call_note', 'Talk to a person, not a form.' );

/* ── Email signup ─────────────────────────────────────────── */
$signup_title = $pick( 'mcs_signup_title', 'Join Our <em>Mailing List</em>' );
$signup_sub   = $pick( 'mcs_signup_sub', 'Projects, machine updates, and service news — delivered to your inbox.' );

// Shared mailing-list form strings — global fields (Site Content → Forms).
$signup_email_label = gerotech_field( 'signup_email_label', 'Email address', 'option' );
$signup_email_ph    = gerotech_field( 'signup_email_placeholder', 'your@email.com', 'option' );
$signup_submit     = gerotech_field( 'signup_submit_label', 'Sign Up', 'option' );
?>

<main id="main">

    <!-- ============================================================
         SECTION 3: Page Hero (Detail page — Figma node 6227:289)
         ============================================================ -->
    <section class="page-hero" aria-labelledby="mcs-hero-headline">
      <img class="slide__bg slide__bg--right" src="<?php echo esc_url( $hero_image ); ?>"<?php echo $hero_srcset ? ' srcset="' . esc_attr( $hero_srcset ) . '" sizes="100vw"' : ''; ?> alt="Interior of a 5-axis machining center with a custom trunnion fixture holding a large workpiece" loading="eager" fetchpriority="high" decoding="async" />
      <div class="slide__overlay slide__overlay--left" aria-hidden="true"></div>
      <div class="slide__content slide__content--left">
        <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
          <a class="page-hero__crumb-link" href="<?php gerotech_page_link( 'home' ); ?>">Home</a>
          <span class="page-hero__crumb-sep" aria-hidden="true">/</span>
          <a class="page-hero__crumb-link" href="<?php gerotech_page_link( 'engineered-solutions' ); ?>">Engineered Solutions</a>
          <span class="page-hero__crumb-sep" aria-hidden="true">/</span>
          <span class="page-hero__crumb-current"><span class="mcs-name-split__lead"><?php echo esc_html( strip_tags( $hero_lead ) ); ?></span> <span class="mcs-name-split__main"><?php echo esc_html( strip_tags( $hero_main ) ); ?></span></span>
        </nav>
        <h1 class="slide__headline" id="mcs-hero-headline">
          <span class="mcs-name-split mcs-name-split--hero">
            <span class="mcs-name-split__lead"><?php echo gerotech_accent( $hero_lead, $hero_accent_class ); ?></span>
            <span class="mcs-name-split__main"><?php echo gerotech_accent( $hero_main, $hero_accent_class ); ?></span>
          </span>
        </h1>
      </div>
    </section>


    <!-- ============================================================
         SECTION 4: Services Grid (2-col, 8 cards — Figma node 6227:308)
         ============================================================ -->
    <section class="mcs-grid-section" id="mcs-grid" aria-labelledby="mcs-grid-headline">
      <div class="container container--es">
        <div class="section-header">
          <p class="eyebrow"><?php echo esc_html( $grid_eyebrow ); ?></p>
          <h2 class="section-title" id="mcs-grid-headline">
            <span class="mcs-name-split mcs-name-split--title">
              <span class="mcs-name-split__lead"><?php echo esc_html( $grid_lead ); ?></span>
              <span class="mcs-name-split__main"><?php echo esc_html( $grid_main ); ?></span>
            </span>
          </h2>
          <span class="headline-rule headline-rule--deep" aria-hidden="true"></span>
        </div>

        <div class="mcs-grid">
          <?php foreach ( $cards as $card ) : ?>
          <?php
			$card_image = gerotech_image_url( isset( $card['image'] ) ? $card['image'] : '' );
			// gerotech_image_url() just resolves a theme-relative path or URL, so it works
			// for the video asset too (it is not image-specific despite the name).
			$card_video = empty( $card['video'] ) ? '' : gerotech_image_url( $card['video'] );
			?>
          <article class="mcs-card" role="button" tabindex="0" aria-haspopup="dialog">
            <?php if ( $card_video ) : ?>
            <video class="mcs-card__image" data-card-video src="<?php echo esc_url( $card_video ); ?>" poster="<?php echo esc_url( $card_image ); ?>" autoplay muted loop playsinline preload="metadata" aria-hidden="true"></video>
            <?php else : ?>
            <img class="mcs-card__image" src="<?php echo esc_url( $card_image ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy" />
            <?php endif; ?>
            <div class="mcs-card__content">
              <h3 class="mcs-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
              <span class="mcs-card__cue">View Details →</span>
            </div>
            <template><?php echo wp_kses_post( $card['detail'] ); ?><div class="mcs-modal__actions"><a class="btn btn--outline-orange" href="tel:+17343797788">Talk to an Engineer</a></div></template>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ============================================================
         Installed Gallery (field photos — distinct from service cards)
         ============================================================ -->
    <section class="mcs-gallery-section" aria-labelledby="gallery-headline">
      <div class="container container--es">
        <div class="section-header">
          <p class="eyebrow"><?php echo esc_html( $gallery_eyebrow ); ?></p>
          <h2 class="section-title" id="gallery-headline"><?php echo gerotech_accent( $gallery_title, 'accent--deep' ); ?></h2>
          <span class="headline-rule headline-rule--deep" aria-hidden="true"></span>
          <?php if ( $gallery_body ) : ?>
          <p class="section-body section-body--spaced"><?php echo esc_html( $gallery_body ); ?></p>
          <?php endif; ?>
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

    <!-- ============================================================
         SECTION 6: CTA Band (Detail-specific)
         ============================================================ -->
    <section class="cta-band cta-band--cinema cta-band--cinema-lockup" aria-label="Call to action">
      <img class="cta-band__bg" src="<?php echo esc_url( $cta_image ); ?>" alt="Custom sheet metal enclosure on a machining center" loading="lazy" />
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
          <h2 class="email-signup__title"><?php echo gerotech_accent( $signup_title, 'accent' ); ?></h2>
          <p class="email-signup__sub"><?php echo esc_html( $signup_sub ); ?></p>
        </div>
        <form class="email-signup__form" action="#" method="post" novalidate>
          <label for="email-input-mcs" class="sr-only"><?php echo esc_html( $signup_email_label ); ?></label>
          <input class="email-signup__input" id="email-input-mcs" type="email" name="email" placeholder="<?php echo esc_attr( $signup_email_ph ); ?>" required autocomplete="email" />
          <button class="email-signup__submit" type="submit"><?php echo esc_html( $signup_submit ); ?></button>
        </form>
      </div>
    </section>
  </main>

<?php
get_footer();
