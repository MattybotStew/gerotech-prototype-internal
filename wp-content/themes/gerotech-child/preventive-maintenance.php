<?php
/**
 * Planned Maintenance page template.
 *
 * Body content is ported from the dev site (gerotechdev) so the page matches
 * the live planned-maintenance design exactly: hero, intro, the Planned
 * Maintenance form (CF7 321, swapped to its shortcode), and the Engage CTA.
 *
 * Visible content is ACF-driven (inc/acf-legacy-fields.php) with the current
 * design as defaults. Form internals stay hardcoded.
 *
 * The parent theme's stylesheet is loaded scoped to `.legacy`
 * (assets/css/legacy.css) so it cannot leak into our new header/footer.
 * Header/footer are the child theme's (new design).
 *
 * @package GerotechChild
 */

get_header();

$legacy_img = GEROTECH_CHILD_URI . '/assets/images/legacy';

$hero_title        = gerotech_field( 'planned_hero_title', 'PLANNED MAINTENANCE' );
$hero_subtitle     = gerotech_field( 'planned_hero_subtitle', 'We are here to help.' );
$hero_title_mobile = gerotech_field( 'planned_hero_title_mobile', 'PREVENTIVE MAINTENEANCE' );

$intro = gerotech_field(
	'planned_intro',
	'<p><strong>PLANNED MAINTENANCE:</strong><br />
Gerotech is pleased to offer a comprehensive PM program that includes ball bar and vibration analysis to ensure your machine continues to produce precision parts with predictable uptime.</p>
<p>All systems are checked, and any conditions found are detailed, giving you a complete picture of your machine&#8217;s state of well- being. The process can take 1-2 days, depending upon the machine complexity.</p>
<p>Additional service, repairs, and parts for repairs are scheduled with Gerotech and are subject to standard service rates. The PM service provides a list of repairs and possible parts needed for future maintenance.</p>
<p>Contact <a href="mailto:service@gerotech.com">service@gerotech.com</a> to schedule your appointment today!</p>'
);

$flyer_label = gerotech_field( 'planned_flyer_label', 'PM FLYER' );
$flyer_url   = gerotech_field( 'planned_flyer_url', '/wp-content/uploads/2021/07/Gerotech-Planned-Maintenance-Flyer_FINAL.pdf' );

$cta_text  = gerotech_field( 'planned_cta_text', 'Put our engineers to work on your project' );
$cta_label = gerotech_field( 'planned_cta_label', 'Engage with us today' );
$cta_url   = gerotech_field( 'planned_cta_url', gerotech_page_url( 'contact' ) );
?>

<div class="legacy">

<section id="page_title" style="display: none;">
		<div class="row clearfix">
			<h1>Planned Maintenance</h1>
		</div>
    </section>

    <section id="head_image" class="service hide_on_mobile" style="background-image: url(<?php echo esc_url( $legacy_img . '/h_service.jpg' ); ?>);">

		<div class="row clearfix">

			<div class="head_text">
				<h1><?php echo esc_html( $hero_title ); ?></h1>
				<h2><?php echo esc_html( $hero_subtitle ); ?></h2>
			</div>

		</div>

    </section>

    <section id="head_image_mobile" class="service hide_on_desktop" style="background-image: url(<?php echo esc_url( $legacy_img . '/bkg_service_mobile.jpg' ); ?>);">

		<div class="row clearfix">

			<div class="head_text">
				<h1><?php echo esc_html( $hero_title_mobile ); ?></h1>
				<h2><?php echo esc_html( $hero_subtitle ); ?></h2>
			</div>

		</div>

    </section>



	<section class="bkg_white service_content">
		<div class="row clearfix">

			<?php echo wp_kses_post( $intro ); ?>

			<div id="dowload_button">
				<a href="<?php echo esc_url( $flyer_url ); ?>" ><?php echo esc_html( $flyer_label ); ?></a>
			</div>
			<br><br>
		</div>
	</section>




	<section class="bkg_white service_tabs">
		<div class="row_970 clearfix">
			<div class="service_tab" id="tf_plan" class="clearfix" style="display: block;">



<?php echo gerotech_shortcode( '[gravityform id="3" title="false" ajax="true"]' ); ?>

			</div>
		</div>
</section>






 
		<section id="engage">

		<div class="row clearfix">

			<p><span><?php echo esc_html( $cta_text ); ?></span></p>

			<a href="<?php echo esc_url( $cta_url ); ?>" class="btn_engage"><?php echo esc_html( $cta_label ); ?></a>

		</div>

	</section>
</div><!-- /.legacy -->

<?php
get_footer();
