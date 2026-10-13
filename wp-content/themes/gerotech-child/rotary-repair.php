<?php
/**
 * Rotary Repair page template.
 *
 * Body content is ported from the dev site (gerotechdev) so the page matches
 * the live rotary-repair design exactly: hero, intro, the Rotary Repair form
 * (CF7 299, swapped to its shortcode), and the Engage CTA.
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

$hero_title            = gerotech_field( 'rotary_hero_title', 'ROTARY REPAIR' );
$hero_subtitle         = gerotech_field( 'rotary_hero_subtitle', 'Gerotech offers world class repair of your HAAS rotary table.' );
$hero_subtitle_mobile  = gerotech_field( 'rotary_hero_subtitle_mobile', 'We are here to help.' );
$intro                 = gerotech_field( 'rotary_intro', 'Gerotech provides Haas certified rotary and indexer repair capabilities.  With more than 15 years of experience, our experts will provide comprehensive and quality service for all of your rotary maintenance needs.  Fill out the form below to contact the repair experts at Gerotech.' );
$form_title            = gerotech_field( 'rotary_form_title', 'Request for Return Authorization' );
$form_body             = gerotech_field( 'rotary_form_body', 'Please complete as much information as possible. You will be contacted with a repair authorization number that must be attached to the indexer/rotary unit before it is shipped for repair.' );

$cta_text  = gerotech_field( 'rotary_cta_text', 'Put our engineers to work on your project' );
$cta_label = gerotech_field( 'rotary_cta_label', 'Engage with us today' );
$cta_url   = gerotech_field( 'rotary_cta_url', gerotech_page_url( 'contact' ) );
?>

<div class="legacy">

<section id="page_title" style="display: none;">
		<div class="row clearfix">
			<h1>Rotary Repair</h1>
		</div>
    </section>

    <section id="head_image" class="service hide_on_mobile" style="background-image: url(<?php echo esc_url( $legacy_img . '/rotary-hero.jpg' ); ?>);">

		<div class="row clearfix">

			<div class="head_text">
				<h1><?php echo esc_html( $hero_title ); ?></h1>
				<h2><?php echo esc_html( $hero_subtitle ); ?></h2>
			</div>

		</div>

    </section>

    <section id="head_image_mobile" class="service hide_on_desktop" style="background-image: url(<?php echo esc_url( $legacy_img . '/rotary-hero.jpg' ); ?>);">

		<div class="row clearfix">

			<div class="head_text">
				<h1><?php echo esc_html( $hero_title ); ?></h1>
				<h2><?php echo esc_html( $hero_subtitle_mobile ); ?></h2>
			</div>

		</div>

    </section>



	<section class="bkg_white service_content">
		<div class="row clearfix">

			<p><?php echo wp_kses_post( $intro ); ?></p>


		</div>
	</section>




	<section class="bkg_white service_tabs">
		<div class="row_970 clearfix">
			<div class="service_tab" id="tf_rotary" class="clearfix">
				<h3><?php echo esc_html( $form_title ); ?></h3>
<p><?php echo wp_kses_post( $form_body ); ?></p>

<?php echo gerotech_shortcode( '[gravityform id="2" title="false" ajax="true"]' ); ?>
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
