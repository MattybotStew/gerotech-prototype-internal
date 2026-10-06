<?php
/**
 * Careers page template.
 *
 * Ported from the static prototype `careers.html` (new design system). Content
 * is ACF-driven (inc/acf-fields.php → group_careers_content) with the prototype
 * as defaults, so the page renders correctly whether or not fields are set.
 *
 * @package GerotechChild
 */

get_header();

$pick = function ( $key, $default ) {
	return gerotech_field( $key, $default );
};

/* ── Hero ─────────────────────────────────────────────────── */
$hero_eyebrow = $pick( 'careers_hero_eyebrow', 'Careers' );
$hero_headline = $pick( 'careers_hero_headline', "We're changing the face of <em>manufacturing</em>. Join us." );
$hero_accent   = $pick( 'careers_hero_accent_color', 'orange' ); // Blank (no stored choice) keeps the design colour.
$hero_cta_color = $pick( 'careers_hero_cta_color', 'orange' );   // Hero button colour.
$hero_body = $pick( 'careers_hero_body', "We're not just a workplace; we're a family of dynamic individuals committed to pushing the boundaries of excellence. We pride ourselves in our family-like culture where every member of our team is committed to providing customers with the best machines, solutions, and support in the industry." );
$hero_cta_label = $pick( 'careers_hero_cta_label', 'View open positions' );
$hero_cta_url = $pick( 'careers_hero_cta_url', '#open-positions' );
$hero_image_value = $pick( 'careers_hero_image', 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?q=80&w=1920&auto=format&fit=crop' );
$hero_image = gerotech_image_url( $hero_image_value );
$hero_stats = $pick(
	'careers_hero_stats',
	array(
		array( 'value' => '35%+', 'label' => 'Team with 10+ years tenure' ),
		array( 'value' => 'Family Culture', 'label' => 'Flexible, growth-focused' ),
		array( 'value' => '401(k) Match', 'label' => 'Profit sharing &amp; PTO' ),
		array( 'value' => 'Volunteer Day', 'label' => 'Paid time to give back' ),
	)
);

/* ── Culture ──────────────────────────────────────────────── */
$culture_eyebrow = $pick( 'careers_culture_eyebrow', 'Our Culture' );
$culture_headline = $pick( 'careers_culture_headline', 'Hard work <em>recognized and rewarded</em>' );
$culture_body = $pick( 'careers_culture_body', 'We have the experience to back it up, as over 35% of our team has ten years or more of company service. Gerotech employees enjoy great benefits and a flexible environment where hard work is recognized and rewarded.' );
$culture_body2 = $pick( 'careers_culture_body2', "So if you're ready to launch your career working with some of the best people and companies in the industry, let's talk." );
$culture_cta_label = $pick( 'careers_culture_cta_label', 'Contact our team →' );
$culture_cta_url = $pick( 'careers_culture_cta_url', 'mailto:sales@gerotech.com?subject=Gerotech%20Careers%20Inquiry' );
$culture_image_value = $pick( 'careers_culture_image', 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=1200&auto=format&fit=crop' );
$culture_image = gerotech_image_url( $culture_image_value );

/* ── Open positions ───────────────────────────────────────── */
$positions_eyebrow = $pick( 'careers_positions_eyebrow', 'Opportunities' );
$positions_title = $pick( 'careers_positions_title', 'Open <em>Positions</em>' );
$col_job        = $pick( 'careers_col_job', 'Job Title' );
$col_location   = $pick( 'careers_col_location', 'Location' );
$col_department = $pick( 'careers_col_department', 'Department' );
$col_date       = $pick( 'careers_col_date', 'Post Date' );
$positions = $pick(
	'careers_positions',
	array(
		array( 'title' => 'Sales Manager', 'url' => 'mailto:sales@gerotech.com?subject=Application%3A%20Sales%20Manager', 'location' => 'Michigan', 'department' => 'Sales', 'date' => 'April 30, 2026' ),
		array( 'title' => 'Join Our Talent Community', 'url' => 'mailto:sales@gerotech.com?subject=Join%20Gerotech%20Talent%20Community', 'location' => 'Michigan', 'department' => '—', 'date' => '—' ),
	)
);

/* ── Benefits ─────────────────────────────────────────────── */
$benefits_eyebrow = $pick( 'careers_benefits_eyebrow', 'Benefits' );
$benefits_title = $pick( 'careers_benefits_title', 'More than just a <em>paycheck</em>' );
$benefits_body = $pick( 'careers_benefits_body', 'Working at Gerotech is more than just a paycheck. The overall well-being of our employees and their families is our top priority and we pride ourselves in our carefully selected benefits that go beyond your salary.' );
$benefits = $pick(
	'careers_benefits',
	array(
		array( 'title' => 'We reward your hard work', 'body' => 'A 401(k) match, profit sharing, and generous paid time off and holidays.' ),
		array( 'title' => 'We care about your well-being', 'body' => 'Excellent health insurance, employer-paid life insurance, short-term &amp; long-term disability, overall well-being resources, and more.' ),
		array( 'title' => "We're proud of our culture", 'body' => 'A flexible, family-like environment that allows for growth.' ),
	)
);
$benefits_note = $pick( 'careers_benefits_note', 'We care about our communities where we work and live. We provide our employees a paid volunteer day to make a difference to a program or charity of their choice.' );
$benefit_icons = array(
	'<svg viewBox="0 0 24 24" focusable="false"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
	'<svg viewBox="0 0 24 24" focusable="false"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
	'<svg viewBox="0 0 24 24" focusable="false"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
);

/* ── CTA band ─────────────────────────────────────────────── */
$cta_eyebrow = $pick( 'careers_cta_eyebrow', 'Join Our Team' );
$cta_headline = $pick( 'careers_cta_headline', 'Ready to launch your <em>career</em> with Gerotech?' );
$cta_body = $pick( 'careers_cta_body', "Work with some of the best people and companies in advanced manufacturing — Michigan's CNC distributor since 1987." );
$cta_primary_label = $pick( 'careers_cta_primary_label', 'View open positions' );
$cta_primary_url = $pick( 'careers_cta_primary_url', '#open-positions' );
$cta_secondary_label = $pick( 'careers_cta_secondary_label', "Let's talk" );
$cta_secondary_url = $pick( 'careers_cta_secondary_url', 'mailto:sales@gerotech.com?subject=Gerotech%20Careers%20Inquiry' );
$cta_image_value = $pick( 'careers_cta_image', 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?q=80&w=1920&auto=format&fit=crop' );
$cta_image = gerotech_image_url( $cta_image_value );

/* ── Mailing list ─────────────────────────────────────────── */
$signup_title = $pick( 'careers_signup_title', 'Join Our <em>Mailing List</em>' );
$signup_sub = $pick( 'careers_signup_sub', 'Projects, machine updates, and service news — delivered to your inbox.' );

// Shared mailing-list form strings — global fields (Site Content → Forms).
$signup_email_label = gerotech_field( 'signup_email_label', 'Email address', 'option' );
$signup_email_ph    = gerotech_field( 'signup_email_placeholder', 'your@email.com', 'option' );
$signup_submit     = gerotech_field( 'signup_submit_label', 'Sign Up', 'option' );
?>

<main id="main">

	<!-- Trust-integrated hero (About page pattern) -->
	<section class="page-hero-trust" aria-label="Careers at Gerotech">
		<img class="page-hero-trust__bg" src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( gerotech_image_alt( $hero_image_value, 'Engineering team collaborating in a manufacturing environment' ) ); ?>" loading="eager" fetchpriority="high" decoding="async" />
		<div class="page-hero-trust__overlay" aria-hidden="true"></div>
		<div class="page-hero-trust__content">
			<div class="page-hero-trust__copy">
				<p class="slide__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
				<h1 class="page-hero-trust__headline"><?php echo gerotech_accent( $hero_headline, gerotech_accent_class( $hero_accent ) ); ?></h1>
				<p class="page-hero-trust__body"><?php echo wp_kses_post( $hero_body ); ?></p>
				<a class="btn btn--lg <?php echo esc_attr( gerotech_btn_class( $hero_cta_color ) ); ?>" href="<?php echo esc_url( $hero_cta_url ); ?>"><?php echo esc_html( $hero_cta_label ); ?></a>
			</div>
			<div class="page-hero-trust__strip" aria-label="Working at Gerotech">
				<?php foreach ( $hero_stats as $stat ) : ?>
					<div class="page-hero-trust__stat">
						<span class="page-hero-trust__value"><?php echo wp_kses_post( $stat['value'] ); ?></span>
						<span class="page-hero-trust__label"><?php echo wp_kses_post( $stat['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Culture -->
	<section class="section section--white">
		<div class="container container--es">
			<div class="grid-2 grid-2--split">
				<div>
					<p class="eyebrow"><?php echo esc_html( $culture_eyebrow ); ?></p>
					<h2 class="section-title"><?php echo gerotech_accent( $culture_headline, 'accent--deep' ); ?></h2>
					<span class="headline-rule headline-rule--deep" aria-hidden="true"></span>
					<p class="section-body"><?php echo wp_kses_post( $culture_body ); ?></p>
					<p class="section-body section-body--spaced"><?php echo wp_kses_post( $culture_body2 ); ?></p>
					<a class="btn btn--outline-dark" href="<?php echo esc_url( $culture_cta_url ); ?>"><?php echo esc_html( $culture_cta_label ); ?></a>
				</div>
				<img class="about-photo" src="<?php echo esc_url( $culture_image ); ?>" alt="<?php echo esc_attr( gerotech_image_alt( $culture_image_value, 'Engineer working with CNC equipment on the shop floor' ) ); ?>" loading="lazy" decoding="async" />
			</div>
		</div>
	</section>

	<!-- Open Positions -->
	<section class="section section--gray" id="open-positions" aria-labelledby="open-positions-title">
		<div class="container container--es">
			<div class="section-header section-header--centered">
				<p class="eyebrow"><?php echo esc_html( $positions_eyebrow ); ?></p>
				<h2 class="section-title" id="open-positions-title"><?php echo gerotech_accent( $positions_title, 'accent--deep' ); ?></h2>
				<span class="headline-rule headline-rule--deep" aria-hidden="true"></span>
			</div>
			<div class="careers-table-wrap">
				<table class="careers-table">
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html( $col_job ); ?></th>
							<th scope="col"><?php echo esc_html( $col_location ); ?></th>
							<th scope="col"><?php echo esc_html( $col_department ); ?></th>
							<th scope="col"><?php echo esc_html( $col_date ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $positions as $position ) : ?>
							<tr>
								<td><a class="careers-table__link" href="<?php echo esc_url( $position['url'] ); ?>"><?php echo esc_html( $position['title'] ); ?></a></td>
								<td><?php echo esc_html( $position['location'] ); ?></td>
								<td><?php echo esc_html( $position['department'] ); ?></td>
								<td><?php echo esc_html( $position['date'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<!-- Benefits -->
	<section class="section section--white" aria-labelledby="benefits-title">
		<div class="container container--es">
			<div class="section-header section-header--centered">
				<p class="eyebrow"><?php echo esc_html( $benefits_eyebrow ); ?></p>
				<h2 class="section-title" id="benefits-title"><?php echo gerotech_accent( $benefits_title, 'accent--deep' ); ?></h2>
				<span class="headline-rule headline-rule--deep" aria-hidden="true"></span>
				<p class="section-body section-body--centered"><?php echo wp_kses_post( $benefits_body ); ?></p>
			</div>
			<div class="grid-3 grid--offset-top">
				<?php foreach ( $benefits as $i => $benefit ) : ?>
					<article class="category-card">
						<div class="category-card__icon" aria-hidden="true">
							<?php echo isset( $benefit_icons[ $i ] ) ? $benefit_icons[ $i ] : $benefit_icons[0]; ?>
						</div>
						<h3 class="category-card__title"><?php echo esc_html( $benefit['title'] ); ?></h3>
						<p class="category-card__body"><?php echo wp_kses_post( $benefit['body'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
			<p class="careers-benefits-note"><?php echo wp_kses_post( $benefits_note ); ?></p>
		</div>
	</section>

	<!-- Customer testimonials (shared partial) -->
	<?php get_template_part( 'template-parts/sections/testimonials' ); ?>

	<section class="cta-band cta-band--cinema cta-band--cinema-lockup" aria-label="Call to action">
		<img class="cta-band__bg" src="<?php echo esc_url( $cta_image ); ?>" alt="<?php echo esc_attr( gerotech_image_alt( $cta_image_value, 'Factory engineer inspecting industrial machinery' ) ); ?>" loading="lazy" />
		<div class="cta-band__overlay" aria-hidden="true"></div>
		<div class="cta-band__content">
			<div class="cta-band__copy">
				<div class="eyebrow-row">
					<span class="eyebrow-row__rule" aria-hidden="true"></span>
					<p class="eyebrow eyebrow--orange"><?php echo esc_html( $cta_eyebrow ); ?></p>
				</div>
				<h2 class="cta-band__headline"><?php echo gerotech_accent( $cta_headline, 'cta-band__accent' ); ?></h2>
				<span class="cta-band__rule" aria-hidden="true"></span>
				<p class="cta-band__body"><?php echo wp_kses_post( $cta_body ); ?></p>
				<div class="cta-band__actions">
					<a class="btn btn--primary btn--lg" href="<?php echo esc_url( $cta_primary_url ); ?>"><?php echo esc_html( $cta_primary_label ); ?></a>
					<a class="btn btn--outline-white" href="<?php echo esc_url( $cta_secondary_url ); ?>"><?php echo esc_html( $cta_secondary_label ); ?></a>
				</div>
			</div>
			<a class="cta-band__call" href="<?php echo esc_url( gerotech_tel_link( '(734) 379-7788' ) ); ?>">
				<span class="cta-band__call-label">Prefer to talk it through?</span>
				<span class="cta-band__call-number">(734) 379-7788</span>
				<span class="cta-band__call-note">Talk to a person, not a form.</span>
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
				<label for="email-input-cr" class="sr-only"><?php echo esc_html( $signup_email_label ); ?></label>
				<input class="email-signup__input" id="email-input-cr" type="email" name="email" placeholder="<?php echo esc_attr( $signup_email_ph ); ?>" required autocomplete="email" />
				<button class="email-signup__submit" type="submit"><?php echo esc_html( $signup_submit ); ?></button>
			</form>
		</div>
	</section>
</main>

<?php
get_footer();
