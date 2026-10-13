<?php
/**
 * Contact page template.
 *
 * Body content is ported from the live/dev site so the page matches the
 * production contact design exactly: "Contact" title, intro band, the Contact
 * Form (CF7 60, swapped to its shortcode), department details, and Locations
 * with Google Maps.
 *
 * Visible content is ACF-driven (inc/acf-legacy-fields.php) with the current
 * design as defaults. The form and Google Maps wiring stay hardcoded.
 *
 * The parent theme's stylesheet is loaded scoped to `.legacy`
 * (assets/css/legacy.css) so it cannot leak into our new header/footer.
 * Header/footer are the child theme's (new design).
 *
 * NOTE: the Google Maps API key is the dev key, overridable via the
 * `gerotech_google_maps_key` filter — replace before production.
 *
 * @package GerotechChild
 */

get_header();

// Page title was hardcoded here.
$page_title = gerotech_field( 'contact_page_title', 'Contact' );
$contact_form_title = gerotech_field( 'contact_form_title', 'Contact Form' );

$intro_title = gerotech_field( 'contact_intro_title', 'Let’s make manufacturing better. Together.' );
$intro_body  = gerotech_field( 'contact_intro_body', 'For service and parts, including emergency service, please call (734) 379-7788 or complete a <a href="/service/">service request form</a>. To learn how we can help improve your business with engineered solutions, please share your contact information below.' );

$departments = gerotech_field(
	'contact_departments',
	array(
		array( 'title' => 'Headquarters &amp; Sales', 'phone' => '734-379-7788', 'email' => 'sales@gerotech.com' ),
		array( 'title' => 'Engineering &amp; Automation', 'phone' => '734-379-7788', 'email' => 'sales@gerotech.com' ),
		array( 'title' => 'Service', 'phone' => '248-476-8787', 'email' => 'service@gerotech.com' ),
		array( 'title' => 'Parts', 'phone' => '734-379-7788', 'email' => 'parts@gerotech.com' ),
		array( 'title' => 'Tooling', 'phone' => '734-379-7788', 'email' => 'sales@gerotech.com' ),
		array( 'title' => 'Grand Rapids Office', 'phone' => '616-735-1100', 'email' => 'sales@gerotech.com' ),
	)
);

$locations_title = gerotech_field( 'contact_locations_title', 'Locations' );
$locations       = gerotech_field(
	'contact_locations',
	array(
		array( 'name' => 'Flat Rock, MI', 'address' => '29220 Commerce Drive, Flat Rock, MI 48134', 'phone' => '734-379-7788', 'fax' => '734-379-2244' ),
		array( 'name' => 'Grand Rapids, MI', 'address' => '2716 Courier Court NW, Grand Rapids, MI 49544', 'phone' => '616-735-1100', 'fax' => '616-735-0776' ),
	)
);
?>

<div class="legacy">

<section id="page_title">
		<div class="row clearfix">
			<h1><?php echo esc_html( $page_title ); ?></h1>
		</div>
    </section>

	<section class="bkg_grey_shapes">

		<div class="row_970">

			<h3><?php echo esc_html( $intro_title ); ?></h3>
<p><?php echo wp_kses_post( $intro_body ); ?></p>

		</div>

	</section>

    <section class="bkg_white">
		<div class="row clearfix contact_form">

			<div class="contact_left">

				<h5><?php echo esc_html( $contact_form_title ); ?></h5>


<?php echo gerotech_shortcode( '[gravityform id="4" title="false" ajax="true"]' ); ?>

			</div>

			<div class="contact_right">

				<?php foreach ( $departments as $dept ) : ?>
					<h5><?php echo wp_kses_post( $dept['title'] ); ?></h5>
<p><?php echo esc_html( $dept['phone'] ); ?><br />
<a href="mailto:<?php echo esc_attr( $dept['email'] ); ?>"><?php echo esc_html( $dept['email'] ); ?></a></p>
				<?php endforeach; ?>

			</div>

		</div>
    </section>

	<section id="locations">
		<div class="row clearfix centered">

			<h2><?php echo esc_html( $locations_title ); ?></h2>

			<div class="location_block">

				<?php foreach ( $locations as $i => $loc ) : ?>
					<div class="<?php echo 0 === $i % 2 ? 'location_left' : 'location_right'; ?>">
						<iframe
							id="location_<?php echo 0 === $i % 2 ? 'flat_rock' : 'grand_rapids'; ?>"
							class="location_map"
							src="https://www.google.com/maps?q=<?php echo rawurlencode( $loc['address'] ); ?>&amp;output=embed"
							style="border:0;display:block;"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"
							title="<?php echo esc_attr( $loc['name'] . ' map' ); ?>"
						></iframe>
						<div class="location_head"><?php echo esc_html( $loc['name'] ); ?></div>
						<div class="location_address"><?php echo esc_html( $loc['address'] ); ?></div>
						<div class="location_phone_fax">P: <span class="orange"><?php echo esc_html( $loc['phone'] ); ?></span> &nbsp; F: <span class="orange"><?php echo esc_html( $loc['fax'] ); ?></span></div>
					</div>
				<?php endforeach; ?>

			</div>

		</div>
	</section>
</div><!-- /.legacy -->

<?php
get_footer();
