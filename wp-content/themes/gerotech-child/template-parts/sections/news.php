<?php
/**
 * Latest Projects & News — editorial split section.
 *
 * PARKED at the client's request (Sep 2026): "Sadly, we just don't know if we
 * can support the Latest Projects & News right now. Can we remove this for now?"
 *
 * The section is OFF by default but nothing has been deleted — markup, ACF
 * fields and content are all intact, so it can be switched back on from
 * wp-admin without a developer.
 *
 * HOW THE CLIENT BRINGS IT BACK
 * -----------------------------
 * Edit the Engineered Solutions page → "News" tab → set
 * "Show the Latest Projects & News section" to **Show** → Update.
 * That is the only step; every field below already holds its content.
 *
 * HOW TO ADD IT TO ANOTHER PAGE
 * -----------------------------
 * 1. Add one line to that page template:
 *        get_template_part( 'template-parts/sections/news' );
 * 2. Register the toggle on that page's ACF group (copy the
 *    `field_es_show_news` entry in inc/acf-fields.php) so the client keeps the
 *    same on/off control. Without it the section stays hidden by default.
 *
 * GOTCHA: while the toggle is off this file never runs, so the content seeder's
 * capture hook ($GLOBALS['gerotech_capture_defaults']) no longer records these
 * defaults. Local + Dev are already seeded; if a re-seed of these fields is ever
 * needed, switch the toggle on for one render first.
 *
 * @package GerotechChild
 */

// Read through gerotech_field() so the section still renders from the code
// defaults when fields are empty. Note gerotech_field() treats a stored `false`
// as "use the default", which is exactly what we want here: default `false`
// keeps the section hidden unless the client explicitly turns it on.
$news_show = gerotech_field( 'es_show_news', false );

if ( ! $news_show ) {
	return;
}

$news_eyebrow    = gerotech_field( 'es_news_eyebrow', 'Stay Informed' );
$news_headline   = gerotech_field( 'es_news_headline', 'Latest <em>Projects &amp; News</em>' );
$news_lead_tag   = gerotech_field( 'es_news_lead_tag', 'Project' );
$news_lead_date  = gerotech_field( 'es_news_lead_date', 'June 2025' );
$news_lead_title = gerotech_field( 'es_news_lead_title', 'Automated Robotic Cell Delivered to a Tier-1 Automotive Supplier' );
$news_lead_excerpt = gerotech_field( 'es_news_lead_excerpt', 'Gerotech engineers designed and integrated a complete FANUC robotic automation cell, reducing cycle times by 38% for a major Michigan supplier.' );
$news_lead_value   = gerotech_field( 'es_news_lead_image', 'https://images.unsplash.com/photo-1716191299980-a6e8827ba10b?q=80&w=1400&auto=format&fit=crop' );
$news_lead_image   = gerotech_image_url( $news_lead_value );
$news_lead_stats   = gerotech_field(
	'es_news_lead_stats',
	array(
		array( 'value' => '38%', 'label' => 'Cycle-time reduction' ),
		array( 'value' => 'FANUC', 'label' => 'Integration partner' ),
		array( 'value' => 'Turnkey', 'label' => 'Cell delivery' ),
	)
);
$news_items = gerotech_field(
	'es_news_items',
	array(
		array(
			'tag'     => 'News',
			'date'    => 'May 2025',
			'title'   => 'Gerotech Expands Grand Rapids Service Territory',
			'excerpt' => 'Our Grand Rapids office is now fully staffed with factory-trained service technicians serving manufacturers across West Michigan.',
			'image'   => 'https://images.unsplash.com/photo-1647427060118-4911c9821b82?q=80&w=500&auto=format&fit=crop',
		),
		array(
			'tag'     => 'Project',
			'date'    => 'April 2025',
			'title'   => 'Hydraulic Workholding System Installed on Legacy Okuma',
			'excerpt' => "A custom hydraulic workholding and auto-door system extended a legacy machining center's productive life by years.",
			'image'   => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?q=80&w=500&auto=format&fit=crop',
		),
		array(
			'tag'     => 'Training',
			'date'    => 'March 2025',
			'title'   => 'Spring Haas Operator Sessions Open at Macomb',
			'excerpt' => 'Complimentary operator and programming courses return to Macomb Community College for Haas owners across Southeast Michigan.',
			'image'   => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=500&auto=format&fit=crop',
		),
	)
);

?>

    <section class="news-section news-section--editorial section section--gray" aria-labelledby="news-headline">
      <div class="container container--es">
        <div class="section-header">
          <p class="eyebrow"><?php echo esc_html( $news_eyebrow ); ?></p>
          <h2 class="section-title" id="news-headline"><?php echo gerotech_accent( $news_headline, 'accent--deep' ); ?></h2>
          <span class="headline-rule headline-rule--deep" aria-hidden="true"></span>
        </div>

        <div class="news-editorial">
          <!-- Lead story — photo card, left gradient (matches hero + CTA treatment) -->
          <article class="news-feature">
            <img class="news-feature__bg" src="<?php echo esc_url( $news_lead_image ); ?>" alt="<?php echo esc_attr( gerotech_image_alt( $news_lead_value, 'FANUC robotic automation cell on a Michigan production floor' ) ); ?>" loading="lazy" decoding="async" />
            <span class="news-feature__overlay" aria-hidden="true"></span>
            <div class="news-feature__content">
              <div class="news-feature__meta">
                <span class="news-tag"><?php echo esc_html( $news_lead_tag ); ?></span>
                <span class="news-feature__date"><?php echo esc_html( $news_lead_date ); ?></span>
              </div>
              <h3 class="news-feature__title"><?php echo esc_html( $news_lead_title ); ?></h3>
              <p class="news-feature__excerpt"><?php echo esc_html( $news_lead_excerpt ); ?></p>
              <?php if ( $news_lead_stats ) : ?>
              <ul class="news-feature__stats">
                <?php foreach ( $news_lead_stats as $st ) : ?>
                <li class="news-feature__stat">
                  <span class="news-feature__stat-value"><?php echo esc_html( $st['value'] ); ?></span>
                  <span class="news-feature__stat-label"><?php echo esc_html( $st['label'] ); ?></span>
                </li>
                <?php endforeach; ?>
              </ul>
              <?php endif; ?>
            </div>
          </article>

          <div class="news-list">
            <?php foreach ( $news_items as $i => $n ) : ?>
            <?php $thumb = gerotech_image_url( isset( $n['image'] ) ? $n['image'] : '' ); ?>
            <article class="news-item">
              <span class="news-item__index" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 2 ) ); ?></span>
              <div class="news-item__body">
                <div class="news-item__meta">
                  <span class="news-tag news-tag--light"><?php echo esc_html( $n['tag'] ); ?></span>
                  <span class="news-item__date"><?php echo esc_html( $n['date'] ); ?></span>
                </div>
                <h3 class="news-item__title"><?php echo esc_html( $n['title'] ); ?></h3>
                <p class="news-item__excerpt"><?php echo esc_html( $n['excerpt'] ); ?></p>
              </div>
              <div class="news-item__media">
                <img class="news-item__thumb" src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $n['title'] ); ?>" loading="lazy" decoding="async" />
              </div>
            </article>
            <?php endforeach; ?>
          </div>
        </div>
        <!-- Links + Show More pending dedicated news page — client TBD -->
      </div>
    </section>
