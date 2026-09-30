<?php
/**
 * Header search modal.
 *
 * nav.js binds to #search-modal and the .search-modal__* children, so the
 * markup must keep those hooks.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gerotech_header = gerotech_header_data();
?>
	<div class="search-modal" id="search-modal" role="dialog" aria-modal="true" aria-labelledby="search-modal-title" hidden>
		<div class="search-modal__backdrop"></div>
		<div class="search-modal__panel">
			<button class="search-modal__close" type="button" aria-label="<?php esc_attr_e( 'Close search', 'gerotech-child' ); ?>">&times;</button>
			<h2 class="search-modal__title" id="search-modal-title"><?php echo esc_html( $gerotech_header['search_title'] ); ?></h2>
			<?php if ( '' !== trim( $gerotech_header['search_hint'] ) ) : ?>
				<p class="search-modal__hint"><?php echo esc_html( $gerotech_header['search_hint'] ); ?></p>
			<?php endif; ?>
			<input class="search-modal__input" type="search" placeholder="<?php echo esc_attr( $gerotech_header['search_placeholder'] ); ?>" aria-label="<?php esc_attr_e( 'Search', 'gerotech-child' ); ?>" />
			<?php if ( ! empty( $gerotech_header['search_links'] ) ) : ?>
				<nav class="search-modal__links" aria-label="<?php esc_attr_e( 'Quick links', 'gerotech-child' ); ?>">
					<?php foreach ( $gerotech_header['search_links'] as $link ) : ?>
						<a class="search-modal__link" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo gerotech_link_attrs( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo gerotech_label( $link['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
	</div>
