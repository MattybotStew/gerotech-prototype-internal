<?php
/**
 * Generic page template (new design system).
 *
 * The child theme has no page.php, so pages without a matching page-{slug}.php
 * fell through to the PARENT theme's page.php — whose fallback branch hardcodes
 * a "ROTARY REPAIR" hero and calls the_field('rotary_content'), which silently
 * discarded real content (notably the /privacy/ policy) and showed the wrong
 * hero on the "Under Construction" stubs. This template gives every remaining
 * default page a correct interior hero + its real content.
 *
 * @package GerotechChild
 */

get_header();

while ( have_posts() ) :
	the_post();

	$title  = get_the_title();
	$intro  = gerotech_field( 'intro', '' );
	$content = trim( (string) get_the_content() );

	// The three "Under Construction" stubs hold their placeholder text in an
	// `intro` ACF field, not post_content. Show it when the editor content is
	// empty so the page doesn't render a bare title.
	$body = '' !== $content ? $content : $intro;
	?>
	<main id="main">
		<section class="page-hero" aria-labelledby="page-hero-title">
			<div class="slide__overlay slide__overlay--left" aria-hidden="true"></div>
			<div class="slide__content slide__content--left">
				<nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
					<a class="page-hero__crumb-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( gerotech_shared_ui( 'breadcrumb_home_label' ) ); ?></a>
					<span class="page-hero__crumb-sep" aria-hidden="true"></span>
					<span class="page-hero__crumb-current"><?php echo esc_html( $title ); ?></span>
				</nav>
				<h1 class="slide__headline" id="page-hero-title"><?php echo esc_html( $title ); ?></h1>
			</div>
		</section>

		<section class="section section--white">
			<div class="container">
				<div class="section-body">
					<?php
					if ( '' !== $body ) {
						echo wp_kses_post( wpautop( $body ) );
					}
					?>
				</div>
			</div>
		</section>
	</main>
	<?php
endwhile;

get_footer();
