<?php
/**
 * Testimonials section (global).
 *
 * Quotes are edited once in wp-admin under "Site Content" and shared across
 * the homepage and every Engineered Solutions page. Falls back to the v1
 * static quotes until an editor saves the repeater.
 *
 * @package GerotechChild
 */

$testimonials_eyebrow = gerotech_field( 'testimonials_eyebrow', 'What Customers Say', 'option' );
$testimonials_title   = gerotech_field( 'testimonials_title', 'Trusted by <em>Michigan</em> Manufacturers', 'option' );
$testimonials         = gerotech_field(
	'testimonials',
	array(
		array(
			'quote' => 'I have never had such consistent, quality customer support from a company and an overall great experience. Every contact has been timely, there has been good communication, friendly service, and each time they are happy to educate me along the way.',
			'name'  => 'Michael Rudisill',
			'sub'   => 'Gerotech Customer',
		),
		array(
			'quote' => 'I wanted to communicate my deepest appreciation for your partnership. I very much enjoyed working with you. It was great to team up to overcome hurdles and accomplish different goals.',
			'name'  => 'Ford Motor Company',
			'sub'   => 'Gerotech Customer',
		),
		array(
			'quote' => 'The quality of service that Gerotech provided us with. I had the privilege, in a rough situation, to work with Don on our machine issue. What a knowledgeable and diligent technician. He was able to work through our difficult situation with software to machine function issues and I wanted to make sure he was recognized for his great work. Thank you.',
			'name'  => 'Kingsbury Professional Services',
			'sub'   => 'Gerotech Customer',
		),
	),
	'option'
);

?>
<section class="testimonial-section" aria-label="<?php esc_attr_e( 'Customer testimonials', 'gerotech-child' ); ?>">
	<div class="container">
		<div class="section-header section-header--centered">
			<p class="eyebrow"><?php echo esc_html( $testimonials_eyebrow ); ?></p>
			<h2 class="section-title"><?php echo gerotech_accent( $testimonials_title, 'accent--deep' ); ?></h2>
		</div>

		<ul class="testimonial-grid">
			<?php foreach ( $testimonials as $item ) : ?>
			<li class="testimonial-card">
				<p class="testimonial-card__quote"><?php echo esc_html( $item['quote'] ); ?></p>
				<p class="testimonial-card__name"><?php echo esc_html( $item['name'] ); ?></p>
				<p class="testimonial-card__sub"><?php echo esc_html( $item['sub'] ); ?></p>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
