<?php
/**
 * Service page template.
 *
 * Body content is ported from the dev site (gerotechdev) so the page matches
 * the live service design exactly, including the six tabbed CF7 forms. Each
 * rendered form was replaced with its CF7 shortcode so submissions work.
 *
 * Visible content is ACF-driven (inc/acf-legacy-fields.php) with the current
 * design as defaults. The inspection checklists and form internals stay
 * hardcoded.
 *
 * The parent theme's stylesheet is loaded scoped to `.legacy`
 * (assets/css/legacy.css) so it cannot leak into our new header/footer.
 * Header/footer are the child theme's (new design).
 *
 * @package GerotechChild
 */

get_header();

$intro_title = gerotech_field( 'service_intro_title', 'World Class Service and Support' );
$intro_body  = gerotech_field( 'service_intro_body', 'Gerotech has the best service technicians in the industry located right here in Michigan. We offer telephone support during business hours and 24/7 emergency service. From installation and training, to service and applications support—Gerotech is here to help.' );

$services = gerotech_field(
	'service_services',
	array(
		array( 'title' => 'Factory-trained Technicians', 'body' => 'Our service department is staffed with factory-trained and certified personnel. Every one of our technicians receives comprehensive training on a variety of topics, including new machine installation, electrical and mechanical repair, ball bar testing, and software upgrades.' ),
		array( 'title' => 'Telephone Support', 'body' => 'Gerotech always has telephone support personnel available during business hours to provide the information and assistance you need to maintain and service your equipment.' ),
		array( 'title' => 'After-Hours Service', 'body' => 'We are here to assist our customers 24/7. If you need after-hours support, call our service hotline at (248) 476-8787, then press 3 to leave a message for after-hours service. One of our factory-trained technicians will contact you within a half-hour.' ),
		array( 'title' => 'Immediate Parts Availability', 'body' => 'We stock replacement parts and maintenance items for every machine brand we represent. When it comes to replacement parts, we have you covered.' ),
		array( 'title' => 'Applications Support', 'body' => 'Gerotech has a fully staffed engineering and applications department and offers a wide variety of capabilities to our customers, including machine recommendations, time estimates, systems integration, project management, and focused onsite assistance with programming, tooling selection, process optimization, and/or runoff.' ),
	)
);

$assist_title = gerotech_field( 'service_assist_title', 'Need Assistance?' );
$assist_body  = gerotech_field( 'service_assist_body', 'Contact us for help with whatever issue or question you may have.' );

/* Tab strip labels — bound to fixed tab ids below, so they are individual fields. */
$tab_label_service = gerotech_field( 'service_tab_label_service', 'Submit a Service Request' );
$tab_label_general = gerotech_field( 'service_tab_label_general', 'General Service Inquiry' );
$tab_label_parts   = gerotech_field( 'service_tab_label_parts', 'Parts Order' );
$tab_label_rotary  = gerotech_field( 'service_tab_label_rotary', 'Rotary Repair' );
$tab_label_plan    = gerotech_field( 'service_tab_label_plan', 'Preventive Maintenance' );
$tab_label_support = gerotech_field( 'service_tab_label_support', 'Application Support' );

$tab_service_intro = gerotech_field( 'service_tab_service_intro', 'Please use the form below to submit a service request or call (248) 476-8787.' );
$tab_general_intro = gerotech_field( 'service_tab_general_intro', 'Use the form below to contact us, or call (248) 476-8787. Please complete as much information as possible.' );
$tab_parts_intro   = gerotech_field( 'service_tab_parts_intro', 'Use the form below to contact us, or call (734) 379-7788. Please complete as much information as possible.' );
$tab_rotary_title  = gerotech_field( 'service_tab_rotary_title', 'Request for Return Authorization' );
$tab_rotary_intro  = gerotech_field( 'service_tab_rotary_intro', 'Please complete as much information as possible. You will be contacted with a repair authorization number that must be attached to the indexer/rotary unit before it is shipped for repair.' );
$tab_plan_title    = gerotech_field( 'service_tab_plan_title', 'Platinum Total Care System' );
$tab_plan_intro    = gerotech_field( 'service_tab_plan_intro', 'We are pleased to offer Haas customers a comprehensive Planned Maintenance Program carried out by our Haas factory-certified service engineers. Protect your Haas investment and maximize your productivity by utilizing locally available Haas factory-certified professionals to keep your machine in the best possible condition. Essential items addressed at every planned inspection are listed below.' );
$tab_support_title = gerotech_field( 'service_tab_support_title', 'Applications Support' );
$tab_support_intro = gerotech_field( 'service_tab_support_intro', 'Our Applications Engineering Department is designed to provide both pre- and post-sale support to our customers. Pre-sale consists of machine recommendations, time estimates, and demonstrations. Post-sale support consists of programmer training, operator training, part programming, runoff, program optimization, and turnkey projects.' );

/* ── Planned Maintenance checklist ──────────────────────────────
   These defaults are the exact copy that used to be hardcoded HTML in this
   template, so the page renders identically until the client edits anything. */
$plan_inspect_title = gerotech_field( 'service_plan_inspect_title', 'Planned Maintenance Inspection Items' );
$plan_inspect_intro = gerotech_field( 'service_plan_inspect_intro', 'Typical inspection items are noted below. Planned maintenance plans are available for all Haas machines. Additional service, repairs, and parts for repairs are scheduled with the HFO and are subject to standard service rates. <b>The Planned Maintenance Service provides a list of necessary repairs and possible parts needed for future maintenance.</b>' );
$plan_inspect_footnote = gerotech_field( 'service_plan_inspect_footnote', '* if applicable' );
$plan_optional_title = gerotech_field( 'service_plan_optional_title', 'Optional Special Services' );
$plan_cta_title     = gerotech_field( 'service_plan_cta_title', 'Request a Planned Maintenance Plan' );
$plan_cta_body      = gerotech_field( 'service_plan_cta_body', 'Please use the form below or call (248) 476-8787.' );
$support_note       = gerotech_field( 'service_support_note', 'Please use the form below to contact our applications engineering department.' );
$locations_title    = gerotech_field( 'service_locations_title', 'Locations' );
$locations          = gerotech_field(
	'service_locations',
	array(
		array( 'name' => 'Grand Rapids, MI', 'anchor' => 'location_grand_rapids', 'address' => '2716 Courier Court NW, Grand Rapids, MI 49544', 'phone' => '616-735-1100', 'fax' => '616-735-0776' ),
		array( 'name' => 'Flat Rock, MI', 'anchor' => 'location_flat_rock', 'address' => '29220 Commerce Drive, Flat Rock, MI 48134', 'phone' => '734-379-7788', 'fax' => '734-379-2244' ),
	)
);

$plan_inspect_groups = gerotech_field(
	'service_plan_inspect_groups',
	array(
		array( 'heading' => 'Electrical System', 'column' => 'left', 'items' => "Check incoming voltage\nDC buss voltage\nLogic voltages\nCondition of wires and connections\nEnsure fans are working\nCheck regen resistors\nCheck vector drive\nCheck transformers\nCheck cabinet filter\nCheck motor connections and brushes*" ),
		array( 'heading' => 'Operator Panel', 'column' => 'left', 'items' => "Condition of keypad\nFunction of keys, buttons, remote handle jog*\nCondition of floppy drive*\nAdjust CRT if needed\nCheck door rollers, switches, rails\nOperation of chip auger/conveyor" ),
		array( 'heading' => 'Wipers/Seals/Windows/Bellows', 'column' => 'left', 'items' => 'Check if in good working condition' ),
		array( 'heading' => 'Geometry', 'column' => 'left', 'items' => "Check level\nCheck backlash in axis’ with ballscrews\nComplete an alignment report" ),
		array( 'heading' => 'Pneumatic System', 'column' => 'left', 'items' => "Check filters*\nCheck hoses and fittings\nCheck for leaks\nCheck pressure switch" ),
		array( 'heading' => 'Way Lube System', 'column' => 'right', 'items' => "Inspect filters\nInspect lines and fittings\nCheck proper pump operation" ),
		array( 'heading' => 'Spindle/Transmission', 'column' => 'right', 'items' => "Check transmission oil\nCondition of belts\nCondition of air lube lines\nCondition of spindle taper or chuck" ),
		array( 'heading' => 'Hydraulic Power Unit*', 'column' => 'right', 'items' => "Check oil level and condition of oil\nCheck for leak\nCheck max pressure\nCheck low pressure switch\nCheck gauges\nCheck that filter has been changed" ),
		array( 'heading' => 'Coolant', 'column' => 'right', 'items' => "Check condition of hoses\nCheck for leaks\nCoolant pump and filters\nP-cool operation" ),
		array( 'heading' => 'Counterbalance*', 'column' => 'right', 'items' => "Check hoses and fill if necessary\nCheck chains, rollers, and weight" ),
	)
);

$plan_optional_groups = gerotech_field(
	'service_plan_optional_groups',
	array(
		array( 'heading' => 'Through Spindle Coolant (mills) High Pressure Coolant (lathes)', 'column' => 'left', 'items' => 'Check pre-charge pressure, hoses, pressure at pump, seal housing, and filters.' ),
		array( 'heading' => 'Pallet Changer or Parts Loader', 'column' => 'left', 'items' => 'Check for wear on rollers, status of switches, alignment to machine, condition of bumpers, and remote operator panel.' ),
		array( 'heading' => 'Bar Feed (Haas brand only)', 'column' => 'left', 'items' => 'Check alignment, switches, and repeatability.' ),
		array( 'heading' => 'Vibration Analyzer Plot', 'column' => 'right', 'items' => 'Verifies machine vibration against established criteria. Isolates potential problems while still manageable.' ),
		array( 'heading' => 'Ball Bar Plot', 'column' => 'right', 'items' => 'Used as a diagnostic tool, the ball bar tests circularity and verifies the positioning accuracy and repeatability of your machine tool. Only available on mills at this time.' ),
		array( 'heading' => 'Probe Calibration', 'column' => 'right', 'items' => 'By recalibrating your Visual Quick Code Probing System, you ensure the integrity of your measuring system.' ),
	)
);

$cta_text  = gerotech_field( 'service_cta_text', 'Put our engineers to work on your project' );
$cta_label = gerotech_field( 'service_cta_label', 'Engage with us today' );
$cta_url   = gerotech_field( 'service_cta_url', gerotech_page_url( 'contact' ) );
?>

<div class="legacy">

<section id="page_title" style="display: none;">
		<div class="row clearfix">
			<h1>Service</h1>
		</div>
    </section>

	<section class="bkg_white service_content">
		<div class="row clearfix">

			<div class="centered pb30">
<h3><?php echo esc_html( $intro_title ); ?></h3>
<p><?php echo wp_kses_post( $intro_body ); ?></p>
</div>
<div class="clearfix">
<?php foreach ( $services as $service ) : ?>
<div class="fifth">
<h5 class="orange"><?php echo esc_html( $service['title'] ); ?></h5>
<p><?php echo wp_kses_post( $service['body'] ); ?></p>
</div>
<?php endforeach; ?>
<h3><?php echo esc_html( $assist_title ); ?></h3>
<p><?php echo wp_kses_post( $assist_body ); ?></p>
</div>

			<div class="clearfix">
				<a class="to to_active" data-tab="tf_service"><?php echo esc_html( $tab_label_service ); ?></a><a class="to to_not_active" data-tab="tf_general"><?php echo esc_html( $tab_label_general ); ?></a><a class="to to_not_active" data-tab="tf_parts"><?php echo esc_html( $tab_label_parts ); ?></a><a class="to to_not_active" data-tab="tf_rotary"><?php echo esc_html( $tab_label_rotary ); ?></a><a class="to to_not_active" data-tab="tf_plan"><?php echo esc_html( $tab_label_plan ); ?></a><a class="to to_not_active" data-tab="tf_support"><?php echo esc_html( $tab_label_support ); ?></a>
			</div>

		</div>
	</section>

	<section class="bkg_white service_tabs">
		<div class="row_970 clearfix">

			<div class="service_tab" id="tf_service" class="clearfix" >

				<p><?php echo wp_kses_post( $tab_service_intro ); ?></p>


<?php echo gerotech_shortcode( '[gravityform id="1" title="false" ajax="true"]' ); ?>

			</div>

			<div class="service_tab" id="tf_general" class="clearfix" style="display: none;">

				<p><?php echo wp_kses_post( $tab_general_intro ); ?></p>


<?php echo do_shortcode( '[contact-form-7 id="500"]' ); ?>

			</div>

			<div class="service_tab" id="tf_parts" class="clearfix" style="display: none;">

				<p><?php echo wp_kses_post( $tab_parts_intro ); ?></p>


<?php echo do_shortcode( '[contact-form-7 id="322"]' ); ?>

			</div>

			<div class="service_tab" id="tf_rotary" class="clearfix" style="display: none;">

				<h3><?php echo esc_html( $tab_rotary_title ); ?></h3>
<p><?php echo wp_kses_post( $tab_rotary_intro ); ?></p>


<?php echo gerotech_shortcode( '[gravityform id="2" title="false" ajax="true"]' ); ?>

			</div>

			<div class="service_tab" id="tf_plan" class="clearfix" style="display: none;">

				<h3><?php echo esc_html( $tab_plan_title ); ?></h3>
<p><?php echo wp_kses_post( $tab_plan_intro ); ?></p>
<h3><?php echo esc_html( $plan_inspect_title ); ?></h3>
<p><?php echo wp_kses_post( $plan_inspect_intro ); ?></p>
<div class="t_left">
<?php gerotech_service_render_groups( gerotech_service_column( $plan_inspect_groups, 'left' ) ); ?>
<?php if ( '' !== trim( (string) $plan_inspect_footnote ) ) : ?>
<p><?php echo esc_html( $plan_inspect_footnote ); ?></p>
<?php endif; ?>
</div>
<div class="t_right">
<?php gerotech_service_render_groups( gerotech_service_column( $plan_inspect_groups, 'right' ) ); ?>
</div>
<div class="cl"></div>
<div class="hr"></div>
<h3><?php echo esc_html( $plan_optional_title ); ?></h3>
<div class="t_left">
<?php gerotech_service_render_groups( gerotech_service_column( $plan_optional_groups, 'left' ) ); ?>
</div>
<div class="t_right">
<?php gerotech_service_render_groups( gerotech_service_column( $plan_optional_groups, 'right' ) ); ?>
</div>
<div class="cl"></div>
<div class="hr"></div>
<h3><?php echo esc_html( $plan_cta_title ); ?></h3>
<p><?php echo esc_html( $plan_cta_body ); ?></p>


<?php echo gerotech_shortcode( '[gravityform id="3" title="false" ajax="true"]' ); ?>

			</div>

			<div class="service_tab" id="tf_support" class="clearfix" style="display: none;">

				<h3><?php echo esc_html( $tab_support_title ); ?></h3>
<p><?php echo wp_kses_post( $tab_support_intro ); ?></p>
<p><?php echo esc_html( $support_note ); ?></p>


<?php echo do_shortcode( '[contact-form-7 id="300"]' ); ?>

			</div>

		</div>
	</section>

	<section id="locations" style="display: none;">
		<div class="row clearfix">

			<h2><?php echo esc_html( $locations_title ); ?></h2>

			<div class="location_block">
				<?php foreach ( $locations as $i => $loc ) : ?>
				<div class="location_<?php echo 0 === $i ? 'left' : 'right'; ?>">
					<div id="<?php echo esc_attr( ! empty( $loc['anchor'] ) ? $loc['anchor'] : sanitize_title( $loc['name'] ) ); ?>"></div>
					<div class="location_head"><?php echo esc_html( $loc['name'] ); ?></div>
					<div class="location_address"><?php echo esc_html( $loc['address'] ); ?></div>
					<div class="location_phone_fax">P: <span class="orange"><?php echo esc_html( $loc['phone'] ); ?></span> &nbsp; F: <span class="orange"><?php echo esc_html( $loc['fax'] ); ?></span></div>
				</div>
				<?php endforeach; ?>
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
