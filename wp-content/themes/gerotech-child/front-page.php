<?php
/**
 * Homepage (front page).
 *
 * Content is driven by ACF (`inc/acf-fields.php`) with the current design as
 * defaults, so the page renders correctly whether or not fields are populated.
 *
 * @package GerotechChild
 */

get_header();

$home_id = get_the_ID();

/** Read an ACF field (null-safe if ACF is inactive), falling back to a default. */
$pick = function ( $key, $default ) use ( $home_id ) {
	return gerotech_field( $key, $default, $home_id );
};

/* ── Hero slides ───────────────────────────────────────────── */
$slides = $pick(
	'home_hero_slides',
	array(
		array(
			'eyebrow'         => 'A Division of Gerotech',
			'headline'        => '<em>Haas</em> Factory Outlet',
			'accent_color'    => 'haas',
			'body'            => '',
			'cta_label'       => 'Explore the Haas Line',
			'cta_url'         => '#machine-browse',
			'cta_color'       => 'haas',
			'image'           => 'assets/images/hero-slide-1.jpg',
			'image_position'  => 'right',
			'title_alt'       => 'Gerotech and Haas F1 Team vans at Gerotech headquarters',
			'peek_eyebrow'    => 'Haas Factory Outlet',
			'peek_accent'     => 'Haas', /* intra-card accent word → brand red eyebrow (matches headline accent) */
			'peek_title'      => 'A Division of Gerotech',
		),
		array(
			'eyebrow'         => 'Floor Inventory',
			'headline'        => "Our Showroom Machines Are\n<em>Ready To Ship</em>",
			'accent_color'    => 'orange',
			'body'            => 'Showroom Machines Are Backed By Our 1-Year Warranty. Confidence Comes Standard.',
			'cta_label'       => 'Browse Inventory',
			'cta_url'         => 'https://www.haascnc.com/HFO/HFO-Gerotech/Showroom-Inventory.html#gsc.tab=0',
			'cta_color'       => 'orange',
			'image'           => 'assets/images/hero-showroom.jpg',
			'image_position'  => 'default',
			'peek_eyebrow'    => 'Floor Inventory',
			'peek_title'      => 'In-Stock & Ready',
		),
		array(
			'eyebrow'         => 'Engineered Solutions',
			'headline'        => "<em>Automation</em> Built\nfor Your\nShop Floor",
			'accent_color'    => 'orange',
			'body'            => '',
			'cta_label'       => 'Explore Solutions',
			'cta_url'         => gerotech_page_url( 'engineered-solutions' ),
			'cta_color'       => 'orange',
			'image'           => 'assets/images/hero-automation-cell.jpg',
			'image_position'  => 'right',
			'peek_eyebrow'    => 'Engineered Solutions',
			'peek_title'      => 'On Our Floor',
		),
	)
);

/* ── Stats ─────────────────────────────────────────────────── */
$stats = $pick(
	'home_stats',
	array(
		array( 'value' => '39+', 'count' => 39, 'suffix' => '+', 'label' => 'Years in Michigan' ),
		array( 'value' => '14,000+', 'count' => 14000, 'suffix' => '+', 'label' => 'Machines Placed' ),
	)
);

/* ── Haas relationship ─────────────────────────────────────── */
$haas_eyebrow  = $pick( 'haas_eyebrow', 'The Haas Relationship' );
$haas_eyebrow_color = gerotech_accent_choice( $pick( 'haas_eyebrow_color', 'haas' ) );
$haas_headline = $pick( 'haas_headline', "Proud to Be Michigan's\n<em>Haas Factory Outlet</em>" );
$haas_accent_color = $pick( 'haas_accent_color', 'haas' );
$haas_lede     = $pick( 'haas_lede', "Gerotech is proud to serve as Michigan's Haas Factory Outlet, bringing together Haas CNC technology with the local expertise, engineering, service, training, and support manufacturers need. Since 1987, we've worked alongside manufacturers to understand their challenges and deliver solutions that make sense for their operation—from CNC machinery and automation to engineered solutions and ongoing support." );
$haas_logo_value = $pick( 'haas_brand_logo', '' );
$haas_logo     = gerotech_image_url( $haas_logo_value, 'assets/images/haas-f1-team.jpg' );
$haas_logo_alt = gerotech_image_alt( $haas_logo_value, 'Haas Automation, Official Machine Tool of Haas F1 Team' );

/* ── Machine lineup ────────────────────────────────────────── */
$lineup_eyebrow  = $pick( 'lineup_eyebrow', 'Haas Factory Outlet' );
$lineup_headline = $pick( 'lineup_headline', 'Browse the <em>Machine Lineup</em>' );
$panels          = $pick(
	'lineup_panels',
	array(
		array(
			'tab_label'   => 'Machining Centers',
			'badge'       => 'Vertical & Horizontal',
			'category'    => '',
			'title'       => 'Vertical Mills',
			'description' => '',
			'tags_label'  => 'Featured series',
			'tags'        => "VF Series | https://www.haascnc.com/machines/vertical-mills/vf-series.html#gsc.tab=0\nUniversal Machines | https://www.haascnc.com/machines/vertical-mills/universal-machine.html#gsc.tab=0\nVR Series | https://www.haascnc.com/machines/vertical-mills/vr-series.html\nMini Mills | https://www.haascnc.com/machines/vertical-mills/mini-mills.html\nMold Machines | https://www.haascnc.com/machines/vertical-mills/mold-machines.html#gsc.tab=0\nDrill/Tap/Mill Series | https://www.haascnc.com/machines/vertical-mills/drill-tap-mill.html#gsc.tab=0",
			'cta_label'   => 'View All Mills →',
			'cta_url'     => 'https://www.haascnc.com/machines/vertical-mills.html',
			'cta2_label'  => '',
			'cta2_url'    => '',
			'photo_style' => 'default',
			'photo'       => 'assets/images/haas-umc-750.jpg',
		),
		array(
			'tab_label'   => 'Turning Centers',
			'badge'       => 'Production Turning',
			'category'    => 'Production Turning',
			'title'       => 'CNC Lathes',
			'description' => '',
			'tags_label'  => 'Featured series',
			'tags'        => "ST Series | https://www.haascnc.com/machines/lathes/st.html#gsc.tab=0\nToolroom Lathe | https://www.haascnc.com/machines/lathes/toolroom-lathe.html#gsc.tab=0\nDual Spindle | https://www.haascnc.com/machines/lathes/dual-spindle.html#gsc.tab=0\nBox Way Series | https://www.haascnc.com/machines/lathes/box-way-series.html#gsc.tab=0\nChucker Lathe | https://www.haascnc.com/machines/lathes/chucker-lathe.html#gsc.tab=0",
			'cta_label'   => 'View All Lathes →',
			'cta_url'     => 'https://www.haascnc.com/machines/lathes.html#gsc.tab=0',
			'cta2_label'  => '',
			'cta2_url'    => '',
			'photo_style' => 'default',
			'photo'       => 'assets/images/haas-st-25y.jpg',
		),
		array(
			'tab_label'   => 'Rotaries & Indexers',
			'badge'       => 'Rotaries & Indexers',
			'category'    => 'Rotaries & Indexers',
			'title'       => 'Rotaries & Indexers',
			'description' => 'Machine more sides in fewer setups with Haas rotary tables and indexers—giving you greater flexibility for complex parts and multi-axis machining.',
			'tags_label'  => 'Featured products',
			'tags'        => "Rotary Tables | https://www.haascnc.com/machines/rotaries-indexers/rotary-tables.html#gsc.tab=0\n5-Axis Rotaries | https://www.haascnc.com/machines/rotaries-indexers/5-axis-rotaries.html#gsc.tab=0",
			'cta_label'   => 'View All Rotaries & Indexers',
			'cta_url'     => 'https://www.haascnc.com/machines/rotaries-indexers.html',
			'cta2_label'  => '',
			'cta2_url'    => '',
			'photo_style' => 'default',
			'photo'       => 'assets/images/lineup-rotaries-indexers.jpg',
		),
		array(
			'tab_label'   => 'Haas Automation',
			'badge'       => 'Robotics & Pallets',
			'category'    => 'Haas Automation',
			'title'       => 'Haas Automation',
			'description' => 'Explore Haas automation solutions, including robotic systems, pallet changers, bar feeders, and other options designed to maximize machine productivity.',
			'tags_label'  => 'Featured products',
			'tags'        => "Automation Models | https://www.haascnc.com/machines/automation-systems/automation-models.html#gsc.tab=0\nBar Feeders | https://www.haascnc.com/machines/automation-systems/automation-models.html#barfeeder\nCobots | https://www.haascnc.com/machines/automation-systems/automation-models.html#robot",
			'cta_label'   => 'View Automation →',
			'cta_url'     => 'https://www.haascnc.com/machines/automation-systems.html#gsc.tab=0',
			'cta2_label'  => '',
			'cta2_url'    => '',
			'photo_style' => 'default',
			'photo'       => 'assets/images/lineup-haas-automation.jpg',
		),
		array(
			'tab_label'   => 'Haas Tooling',
			'badge'       => 'Haas Tooling',
			'category'    => 'Haas Tooling',
			'title'       => 'Tooling & Workholding',
			'description' => 'Your one-stop shop for tooling. Explore categories for mills, lathes, inspection, workholding, and more.',
			'tags_label'  => '',
			'tags'        => '',
			'cta_label'   => 'Haas Tooling',
			'cta_url'     => 'https://www.haastooling.com',
			'cta2_label'  => "Winner's Circle",
			'cta2_url'    => 'https://www.haastooling.com/p/WINNERS_CIRCLE-1Y',
			'photo_style' => 'logo',
			'photo'       => 'assets/images/haas-winners-circle.png',
		),
	)
);

/* ── CTA band ──────────────────────────────────────────────── */
$cta_eyebrow  = $pick( 'cta_eyebrow', 'Get Started' );
$cta_headline = $pick( 'cta_headline', 'Put Gerotech to work on your project.' );
$cta_body     = $pick( 'cta_body', "From Haas CNC machines to Engineered Solutions — tell us about your project and we'll connect you with the right expert." );
$cta_btn_lbl  = $pick( 'cta_button_label', 'Engage with us today' );
$cta_btn_url  = $pick( 'cta_button_url', gerotech_quote_mailto( 'Gerotech Expert Inquiry' ) );
$cta_image_value = $pick( 'cta_image', '' );
$cta_image    = gerotech_image_url( $cta_image_value, 'assets/images/cta-home-figma.jpg' );
$cta_image_alt = gerotech_image_alt( $cta_image_value, 'Orange industrial robot arms on a factory line' );
$cta_call_label  = $pick( 'cta_call_label', 'Prefer to talk it through?' );
$cta_call_number = $pick( 'cta_call_number', '(734) 379-7788' );
$cta_call_note   = $pick( 'cta_call_note', 'Talk to a person, not a form.' );

/* ── Email signup ──────────────────────────────────────────── */
$signup_title = $pick( 'signup_title', 'Join Our <em>Mailing List</em>' );
$signup_sub   = $pick( 'signup_sub', 'Projects, machine updates, and service news — delivered to your inbox.' );

// Shared mailing-list form strings — global fields (Site Content → Forms).
$signup_email_label = gerotech_field( 'signup_email_label', 'Email address', 'option' );
$signup_email_ph    = gerotech_field( 'signup_email_placeholder', 'your@email.com', 'option' );
$signup_submit     = gerotech_field( 'signup_submit_label', 'Sign Up', 'option' );
?>

<main id="main">

	<!-- ============================================================
		 Hero Carousel — Figma peek-card (Make)
		 ============================================================ -->
	<section class="hero-slider hero-slider--peek" aria-label="<?php esc_attr_e( 'Homepage hero carousel', 'gerotech-child' ); ?>">
		<div class="hero-slider__track">
			<?php $total = count( $slides ); ?>
			<?php foreach ( $slides as $i => $s ) : ?>
				<?php
				$is_first = ( 0 === $i );
				$pos      = isset( $s['image_position'] ) ? $s['image_position'] : 'default';
				$img_val  = isset( $s['image'] ) ? $s['image'] : '';
				$img      = gerotech_image_url( $img_val, '' );
				$img_set  = gerotech_image_srcset( $img_val, '' );
				// Accent colour: White (default) | Haas Red | Brand Orange.
				// Neither colour field carries an ACF default_value on purpose: ACF injects a
				// default on read, and saving the page would then persist it over a slide whose
				// design colour differs (slide 1 is Haas Red, the rest Brand Orange). So an empty
				// value means "no explicit choice" — fall back to the retired `accent_class` meta
				// (read raw: that field is unregistered, so it is absent from $s), then to the
				// original design treatment (slide 1 Haas red, everything else brand orange).
				$accent_raw = isset( $s['accent_color'] ) ? trim( (string) $s['accent_color'] ) : '';
				if ( '' === $accent_raw ) {
					$accent_raw = (string) get_post_meta( $home_id, "home_hero_slides_{$i}_accent_class", true );
				}
				$accent_raw = ( '' !== $accent_raw ) ? $accent_raw : ( $is_first ? 'haas' : 'orange' );
				$accent     = gerotech_accent_class( $accent_raw );
				$accent_key = gerotech_accent_choice( $accent_raw );

				// Button colour is independent of the accent. Empty keeps the original
				// treatment: slide 1 Haas red, everything else brand orange.
				$cta_choice = isset( $s['cta_color'] ) ? trim( (string) $s['cta_color'] ) : '';
				if ( '' === $cta_choice ) {
					$cta_choice = $is_first ? 'haas' : 'orange';
				}
				$cta_btn = 'btn ' . gerotech_btn_class( $cta_choice );
				// ACF repeats every sub-field, so an untouched alt is an empty string — fall back to the eyebrow.
				$img_alt  = ! empty( $s['title_alt'] ) ? $s['title_alt'] : ( ! empty( $s['eyebrow'] ) ? $s['eyebrow'] : '' );
				?>
				<div
					class="slide<?php echo $is_first ? ' is-active' : ''; ?>"
					role="tabpanel"
					aria-label="<?php echo esc_attr( sprintf( 'Slide %d of %d', $i + 1, $total ) ); ?>"
					data-peek-eyebrow="<?php echo esc_attr( isset( $s['peek_eyebrow'] ) ? $s['peek_eyebrow'] : '' ); ?>"
					data-peek-accent="<?php echo esc_attr( isset( $s['peek_accent'] ) ? $s['peek_accent'] : '' ); ?>"
					data-peek-accent-color="<?php echo esc_attr( $accent_key ); ?>"
					data-peek-title="<?php echo esc_attr( isset( $s['peek_title'] ) ? $s['peek_title'] : '' ); ?>"
				>
					<?php if ( $img ) : ?>
						<img class="slide__bg<?php echo 'right' === $pos ? ' slide__bg--right' : ''; ?>" src="<?php echo esc_url( $img ); ?>"<?php echo $img_set ? ' srcset="' . esc_attr( $img_set ) . '" sizes="100vw"' : ''; ?> alt="<?php echo esc_attr( $img_alt ); ?>" <?php echo $is_first ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'; ?> decoding="async" />
					<?php endif; ?>
					<div class="slide__overlay slide__overlay--left" aria-hidden="true"></div>
					<div class="slide__content slide__content--left">
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<p class="slide__eyebrow<?php echo $is_first ? ' slide__eyebrow--white' : ''; ?>"><?php echo esc_html( $s['eyebrow'] ); ?></p>
						<?php endif; ?>
						<?php if ( $is_first ) : ?>
							<h1 class="slide__headline"><?php echo gerotech_accent( $s['headline'], $accent ); ?></h1>
						<?php else : ?>
							<h2 class="slide__headline"><?php echo gerotech_accent( $s['headline'], $accent ); ?></h2>
						<?php endif; ?>
						<?php if ( ! empty( $s['body'] ) ) : ?>
							<p class="slide__body"><?php echo esc_html( $s['body'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $s['cta_label'] ) ) : ?>
							<a class="<?php echo esc_attr( $cta_btn ); ?>" href="<?php echo esc_url( $s['cta_url'] ); ?>"><?php echo esc_html( $s['cta_label'] ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="hero-slider__index" aria-hidden="true">
			<svg class="hero-slider__progress" viewBox="0 0 48 48" focusable="false">
				<circle class="hero-slider__progress-track" cx="24" cy="24" r="21" fill="none" />
				<circle class="hero-slider__progress-bar" cx="24" cy="24" r="21" fill="none" />
			</svg>
			<span class="hero-slider__index-num">01</span>
		</div>
		<div class="hero-slider__peeks" role="tablist" aria-label="<?php esc_attr_e( 'Slide navigation', 'gerotech-child' ); ?>"></div>
	</section>

	<!-- ============================================================
		 Stat Counter
		 ============================================================ -->
	<?php if ( $stats ) : ?>
	<section class="stat-counter" aria-label="<?php esc_attr_e( 'Gerotech by the numbers', 'gerotech-child' ); ?>">
		<div class="container">
			<div class="stat-counter__grid stat-counter__grid--<?php echo esc_attr( min( 2, count( $stats ) ) ); ?>">
				<?php foreach ( $stats as $st ) : ?>
					<div class="stat-counter__item">
						<p class="stat-counter__value"<?php echo ( isset( $st['count'] ) && '' !== $st['count'] ) ? ' data-count="' . esc_attr( $st['count'] ) . '"' : ''; ?><?php echo ( isset( $st['suffix'] ) && '' !== $st['suffix'] ) ? ' data-suffix="' . esc_attr( $st['suffix'] ) . '"' : ''; ?>><?php echo esc_html( $st['value'] ); ?></p>
						<p class="stat-counter__label"><?php echo esc_html( $st['label'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ============================================================
		 Haas Relationship
		 ============================================================ -->
	<section class="haas-relationship" aria-labelledby="haas-rel-headline">
		<div class="haas-relationship__top">
			<div class="haas-relationship__bg" aria-hidden="true">
				<img class="haas-relationship__watermark" src="<?php echo esc_url( GEROTECH_CHILD_URI . '/assets/images/haas-wordmark-watermark.svg' ); ?>" alt="" width="2776" height="664" decoding="async" />
			</div>
			<div class="haas-relationship__content">
				<div class="container">
					<div class="eyebrow-row eyebrow-row--<?php echo esc_attr( $haas_eyebrow_color ); ?>">
						<span class="eyebrow-row__rule" aria-hidden="true"></span>
						<p class="eyebrow"><?php echo esc_html( $haas_eyebrow ); ?></p>
					</div>
					<div class="haas-relationship__intro">
						<div class="haas-relationship__copy-col">
							<h2 id="haas-rel-headline" class="haas-relationship__headline"><?php echo gerotech_accent( $haas_headline, gerotech_accent_class( $haas_accent_color ) ); ?></h2>
							<p class="haas-relationship__lede"><?php echo esc_html( $haas_lede ); ?></p>
						</div>
						<div class="haas-relationship__brand">
							<img class="haas-relationship__brand-logo" src="<?php echo esc_url( $haas_logo ); ?>" alt="<?php echo esc_attr( $haas_logo_alt ); ?>" width="1738" height="500" loading="lazy" decoding="async" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============================================================
		 Machine lineup
		 ============================================================ -->
	<?php if ( $panels ) : ?>
	<section class="machine-lineup" id="machine-browse" aria-labelledby="machine-lineup-headline">
		<div class="container">
			<div class="machine-lineup__header">
				<div class="machine-lineup__intro">
					<div class="eyebrow-row">
						<span class="eyebrow-row__rule" aria-hidden="true"></span>
						<p class="eyebrow"><?php echo esc_html( $lineup_eyebrow ); ?></p>
					</div>
					<h2 id="machine-lineup-headline" class="machine-lineup__headline"><?php echo gerotech_accent( $lineup_headline, 'accent' ); ?></h2>
				</div>
			</div>

			<div class="machine-tabs machine-tabs--lineup" role="tablist" aria-label="<?php esc_attr_e( 'Machine categories', 'gerotech-child' ); ?>">
				<?php foreach ( $panels as $i => $p ) : ?>
					<button type="button" class="machine-tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-controls="machine-panel-<?php echo (int) $i; ?>" id="machine-tab-<?php echo (int) $i; ?>" data-target="machine-panel-<?php echo (int) $i; ?>"><?php echo esc_html( $p['tab_label'] ); ?></button>
				<?php endforeach; ?>
			</div>

			<div class="machine-lineup__panels">
				<?php foreach ( $panels as $i => $p ) : ?>
					<?php
					$is_first    = ( 0 === $i );
					$photo_style = isset( $p['photo_style'] ) ? $p['photo_style'] : 'default';
					$photo       = gerotech_image_url( isset( $p['photo'] ) ? $p['photo'] : '', '' );
					$tags        = gerotech_parse_tags( isset( $p['tags'] ) ? $p['tags'] : '' );
					$photo_mod   = '';
					if ( 'logo' === $photo_style ) {
						$photo_mod = ' machine-panel__photo--logo';
					} elseif ( $is_first ) {
						$photo_mod = ' machine-panel__photo--umc';
					}
					?>
					<article class="machine-panel machine-panel--lineup<?php echo $is_first ? ' is-active' : ''; ?>" id="machine-panel-<?php echo (int) $i; ?>" role="tabpanel" aria-labelledby="machine-tab-<?php echo (int) $i; ?>"<?php echo $is_first ? '' : ' hidden'; ?>>
						<div class="machine-panel__photo<?php echo $photo_mod; ?>">
							<?php if ( $photo ) : ?>
								<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>" loading="lazy" decoding="async" />
							<?php endif; ?>
							<?php if ( 'logo' !== $photo_style ) : ?>
								<div class="machine-panel__photo-shade" aria-hidden="true"></div>
							<?php endif; ?>
							<?php if ( ! empty( $p['badge'] ) ) : ?>
								<p class="machine-panel__badge"><span class="machine-panel__badge-dot" aria-hidden="true"></span> <?php echo esc_html( $p['badge'] ); ?></p>
							<?php endif; ?>
						</div>
						<div class="machine-panel__body">
							<div class="machine-panel__copy">
								<?php if ( ! empty( $p['category'] ) ) : ?>
									<p class="machine-panel__cat"><?php echo esc_html( $p['category'] ); ?></p>
								<?php endif; ?>
								<h3 class="machine-panel__title"><?php echo esc_html( $p['title'] ); ?></h3>
								<?php if ( ! empty( $p['description'] ) ) : ?>
									<p class="machine-panel__desc"><?php echo esc_html( $p['description'] ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $p['tags_label'] ) && $tags ) : ?>
									<p class="machine-panel__tags-label"><?php echo esc_html( $p['tags_label'] ); ?></p>
									<ul class="model-tags model-tags--dark model-tags--grid" aria-label="<?php echo esc_attr( $p['tags_label'] ); ?>">
										<?php foreach ( $tags as $t ) : ?>
											<li>
												<?php if ( $t['url'] ) : ?>
													<a class="model-tag" href="<?php echo esc_url( $t['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $t['label'] ); ?></a>
												<?php else : ?>
													<span class="model-tag"><?php echo esc_html( $t['label'] ); ?></span>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
							<div class="machine-panel__footer">
								<div class="machine-panel__cta-row">
									<?php if ( ! empty( $p['cta_label'] ) ) : ?>
										<a class="btn btn--primary machine-panel__cta" href="<?php echo esc_url( $p['cta_url'] ); ?>"<?php echo ( 0 === strpos( (string) $p['cta_url'], 'http' ) ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $p['cta_label'] ); ?></a>
									<?php endif; ?>
									<?php if ( ! empty( $p['cta2_label'] ) ) : ?>
										<a class="btn btn--outline-white machine-panel__cta" href="<?php echo esc_url( $p['cta2_url'] ); ?>"<?php echo ( 0 === strpos( (string) $p['cta2_url'], 'http' ) ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $p['cta2_label'] ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/sections/testimonials' ); ?>

	<!-- ============================================================
		 CTA Band
		 ============================================================ -->
	<section class="cta-band cta-band--cinema cta-band--cinema-lockup" aria-label="<?php esc_attr_e( 'Call to action', 'gerotech-child' ); ?>">
		<img class="cta-band__bg" src="<?php echo esc_url( $cta_image ); ?>" alt="<?php echo esc_attr( $cta_image_alt ); ?>" loading="lazy" decoding="async" />
		<div class="cta-band__overlay" aria-hidden="true"></div>
		<div class="cta-band__content">
			<div class="cta-band__copy">
				<div class="eyebrow-row">
					<span class="eyebrow-row__rule" aria-hidden="true"></span>
					<p class="eyebrow eyebrow--orange"><?php echo esc_html( $cta_eyebrow ); ?></p>
				</div>
				<h2 class="cta-band__headline"><?php echo esc_html( $cta_headline ); ?></h2>
				<span class="cta-band__rule" aria-hidden="true"></span>
				<p class="cta-band__body"><?php echo esc_html( $cta_body ); ?></p>
				<div class="cta-band__actions">
					<a class="btn btn--primary btn--lg" href="<?php echo esc_url( $cta_btn_url ); ?>"><?php echo esc_html( $cta_btn_lbl ); ?></a>
				</div>
			</div>
			<a class="cta-band__call" href="<?php echo esc_url( gerotech_tel_link( $cta_call_number ) ); ?>">
				<span class="cta-band__call-label"><?php echo esc_html( $cta_call_label ); ?></span>
				<span class="cta-band__call-number"><?php echo esc_html( $cta_call_number ); ?></span>
				<span class="cta-band__call-note"><?php echo esc_html( $cta_call_note ); ?></span>
			</a>
		</div>
	</section>

	<!-- ============================================================
		 Email Signup
		 ============================================================ -->
	<section class="email-signup" aria-label="<?php esc_attr_e( 'Mailing list signup', 'gerotech-child' ); ?>">
		<div class="email-signup__inner">
			<div class="email-signup__copy">
				<h2 class="email-signup__title"><?php echo gerotech_accent( $signup_title, 'accent' ); ?></h2>
				<p class="email-signup__sub"><?php echo esc_html( $signup_sub ); ?></p>
			</div>
			<form class="email-signup__form" action="#" method="post" novalidate>
				<label for="email-input" class="sr-only"><?php echo esc_html( $signup_email_label ); ?></label>
				<input class="email-signup__input" id="email-input" type="email" name="email" placeholder="<?php echo esc_attr( $signup_email_ph ); ?>" required autocomplete="email" />
				<button class="email-signup__submit" type="submit"><?php echo esc_html( $signup_submit ); ?></button>
			</form>
		</div>
	</section>

</main>

<?php
get_footer();
