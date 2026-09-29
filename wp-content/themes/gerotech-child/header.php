<?php
/**
 * Site header: alert banner, sticky header, navigation, search modal.
 *
 * The navigation, search modal and banner are client-editable through the
 * Site Content options page (see inc/acf-global-fields.php); their defaults —
 * and therefore the exact design copy — live in inc/global-content.php.
 *
 * @package GerotechChild
 */

$gerotech_header = gerotech_header_data();
$gerotech_logo   = $gerotech_header['logo']
	? gerotech_image_url( $gerotech_header['logo'], '' )
	: GEROTECH_CHILD_URI . '/assets/images/gerotech-logo.svg';

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'gerotech-child' ); ?></a>

	<?php if ( ! empty( $gerotech_header['banner_items'] ) ) : ?>
		<!-- Alert Banner (Figma — black #000, centered links) -->
		<div class="alert-banner" aria-label="<?php esc_attr_e( 'Contact information', 'gerotech-child' ); ?>">
			<div class="alert-banner__inner">
				<?php foreach ( $gerotech_header['banner_items'] as $banner ) : ?>
					<div class="alert-banner__item">
						<?php if ( '' !== trim( $banner['label'] ) ) : ?>
							<span class="alert-banner__label"><?php echo esc_html( $banner['label'] ); ?></span>
						<?php endif; ?>
						<a class="alert-banner__link" href="<?php echo esc_url( $banner['url'] ); ?>"><?php echo esc_html( $banner['value'] ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<!-- Sticky Header (Figma — white bg, box shadow, bottom border) -->
	<header class="site-header" role="banner">
		<div class="site-header__inner">
			<a class="site-header__logo" href="<?php echo esc_url( $gerotech_header['logo_url'] ); ?>" aria-label="<?php esc_attr_e( 'Gerotech Home', 'gerotech-child' ); ?>">
				<img class="site-header__logo-img" src="<?php echo esc_url( $gerotech_logo ); ?>" alt="<?php echo esc_attr( $gerotech_header['logo_alt'] ); ?>" width="<?php echo (int) $gerotech_header['logo_width']; ?>" height="<?php echo (int) $gerotech_header['logo_height']; ?>" />
			</a>

			<?php get_template_part( 'template-parts/site-nav' ); ?>

			<?php if ( '' !== trim( $gerotech_header['cta_label'] ) ) : ?>
				<a class="btn-get-quote" href="<?php echo esc_url( $gerotech_header['cta_url'] ); ?>"<?php echo $gerotech_header['cta_new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $gerotech_header['cta_label'] ); ?></a>
			<?php endif; ?>
			<button class="header-search" aria-label="<?php esc_attr_e( 'Search', 'gerotech-child' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
					<path d="M8 13.5C11.0376 13.5 13.5 11.0376 13.5 8C13.5 4.96243 11.0376 2.5 8 2.5C4.96243 2.5 2.5 4.96243 2.5 8C2.5 11.0376 4.96243 13.5 8 13.5Z" stroke="currentColor" stroke-width="2"/>
					<path d="M12.2002 12.2L16.0002 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				</svg>
			</button>
			<button class="mobile-toggle" aria-label="<?php esc_attr_e( 'Open menu', 'gerotech-child' ); ?>" aria-expanded="false">&#9776;</button>
		</div>

		<?php get_template_part( 'template-parts/site-mobile-nav' ); ?>
	</header>

	<?php get_template_part( 'template-parts/search-modal' ); ?>
