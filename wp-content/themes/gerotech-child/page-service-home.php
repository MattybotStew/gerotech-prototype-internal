<?php
/**
 * Support page template.
 *
 * Body content is ported from the dev site (gerotechdev) so the page matches
 * the live support design exactly. The parent theme's stylesheet is loaded
 * scoped to `.legacy` (assets/css/legacy.css) so it cannot leak into our new
 * header/footer.
 *
 * Visible content is ACF-driven (inc/acf-legacy-fields.php) with the current
 * design as defaults, so the page renders correctly whether or not fields are
 * populated.
 *
 * Header/footer are the child theme's (new design).
 *
 * @package GerotechChild
 */

get_header();

$legacy_img = GEROTECH_CHILD_URI . '/assets/images/legacy';

$hero_title    = gerotech_field( 'support_hero_title', 'SERVICE' );
$hero_subtitle = gerotech_field( 'support_hero_subtitle', 'We are here to help.' );
$intro         = gerotech_field( 'support_intro', 'Gerotech has the best service technicians in the industry located right here in Michigan. We offer telephone support during business hours and 24/7 emergency service. From installation and training, to service and applications support&mdash;Gerotech is here to help.' );

$services_title = gerotech_field( 'support_services_title', 'World Class Service and Support' );
$services_intro = gerotech_field( 'support_services_intro', 'Gerotech has service technicians strategically located throughout Michigan, allowing us to respond quickly to our customers.' );
$services       = gerotech_field(
	'support_services',
	array(
		array( 'title' => 'Factory-trained Technicians', 'body' => 'Our service department is staffed with factory-trained and certified personnel. Every one of our technicians receives comprehensive training on a variety of topics, including new machine installation, electrical and mechanical repair, ball bar testing, and software upgrades.' ),
		array( 'title' => 'Telephone Support', 'body' => 'Gerotech always has telephone support personnel available during business hours to provide the information and assistance you need to maintain and service your equipment.' ),
		array( 'title' => 'After-Hours Service', 'body' => 'We are here to assist our customers 24/7. If you need after-hours support, call our service hotline at (248) 476-8787, then press 3 to leave a message for after-hours service. One of our factory-trained technicians will contact you within a half-hour.' ),
		array( 'title' => 'Immediate Parts Availability', 'body' => 'We stock replacement parts and maintenance items for every machine brand we represent. When it comes to replacement parts, we have you covered.' ),
		array( 'title' => 'Applications Support', 'body' => 'Gerotech has a fully staffed engineering and applications department and offers a wide variety of capabilities to our customers, including machine recommendations, time estimates, systems integration, project management, and focused onsite assistance with programming, tooling selection, process optimization, and/or runoff.' ),
	)
);

$assist_title = gerotech_field( 'support_assist_title', 'Need Assistance?' );
$assist_body  = gerotech_field( 'support_assist_body', 'Contact us for help with whatever issue or question you may have.' );

$cta_text  = gerotech_field( 'support_cta_text', 'Put our engineers to work on your project' );
$cta_label = gerotech_field( 'support_cta_label', 'Engage with us today' );
$cta_url   = gerotech_field( 'support_cta_url', gerotech_page_url( 'contact' ) );
?>

<div class="legacy">

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
				<h1><?php echo esc_html( $hero_title ); ?></h1>
				<h2><?php echo esc_html( $hero_subtitle ); ?></h2>
			</div>
		</div>
	</section>

	<section class="bkg_grey_shapes">
		<div class="row_970 clearfix centered">
			<h3><?php echo wp_kses_post( $intro ); ?></h3>
		</div>
	</section>

	<section class="bkg_white service_content">
		<div class="row clearfix">

			<div class="centered pb30">
				<h3><?php echo esc_html( $services_title ); ?></h3>
				<p><?php echo wp_kses_post( $services_intro ); ?></p>
			</div>

			<div class="clearfix">
				<?php foreach ( $services as $service ) : ?>
					<div class="fifth">
						<h5 class="orange"><?php echo esc_html( $service['title'] ); ?></h5>
						<p><?php echo wp_kses_post( $service['body'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>

			<h3><?php echo esc_html( $assist_title ); ?></h3>
			<p><?php echo wp_kses_post( $assist_body ); ?></p>

		</div>
	</section>

	<section id="engage">
		<div class="row clearfix">
			<p><span><?php echo esc_html( $cta_text ); ?></span></p>
			<a href="<?php echo esc_url( $cta_url ); ?>" class="btn_engage"><?php echo esc_html( $cta_label ); ?></a>
		</div>
	</section>

</div>

<?php
get_footer();
