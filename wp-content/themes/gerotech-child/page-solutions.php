<?php
/**
 * Dispatcher for the pages the legacy theme assigned to `page-solutions.php`.
 *
 * WHY THIS FILE EXISTS
 * --------------------
 * Eight published pages carry `page-solutions.php` in `_wp_page_template`, and
 * an assigned template BEATS the `page-<slug>.php` hierarchy. So on a
 * database that still has that meta (staging does; dev does not), WordPress
 * resolved those pages to the parent theme's legacy template and IGNORED our
 * `page-engineered-solutions.php`, `page-automated-system.php` and friends.
 * That is why the ES pages rendered the old design on staging.
 *
 * A child file with the same name would otherwise take over all eight pages,
 * which would break the four that are not ported. So: route the four ported
 * slugs to our templates, and hand every other page straight back to the
 * parent's original file, untouched.
 *
 * The four not in scope (`engineering-process-optimization`,
 * `custom-workholding-and-tooling`, `custom-training-program`,
 * `integration-partners`) keep the legacy rendering they have always had.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gerotech_ported = array(
	'engineered-solutions'                      => 'page-engineered-solutions.php',
	'modification-of-standard-machine-tools'    => 'page-modification-of-standard-machine-tools.php',
	'unique-applications-for-standard-machines' => 'page-unique-applications-for-standard-machines.php',
	'automated-system'                          => 'page-automated-system.php',
);

$gerotech_slug = get_post_field( 'post_name', get_queried_object_id() );

if ( '' !== $gerotech_slug && isset( $gerotech_ported[ $gerotech_slug ] ) ) {
	$gerotech_template = locate_template( array( $gerotech_ported[ $gerotech_slug ] ) );
	if ( '' !== (string) $gerotech_template ) {
		require $gerotech_template;
		return;
	}
}

/*
 * Not one of ours. Render exactly what the legacy theme rendered — including
 * anything it prints itself, such as the Gravity Forms on those pages.
 */
$gerotech_legacy = trailingslashit( get_template_directory() ) . 'page-solutions.php';

if ( is_readable( $gerotech_legacy ) ) {
	require $gerotech_legacy;
	return;
}

// The parent file is gone (parent theme replaced?): fall back to a plain page.
get_header();
?>
<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
