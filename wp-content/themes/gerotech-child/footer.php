<?php
/**
 * Site footer.
 *
 * Brand block, link columns and bottom bar are client-editable through the
 * Site Content options page (see inc/acf-global-fields.php); the defaults live
 * in inc/global-content.php so a blank field still renders the design copy.
 *
 * The link columns are edited separately from the main menu: the footer groups
 * links differently and points at the external Haas catalog.
 *
 * @package GerotechChild
 */

$gerotech_footer = gerotech_footer_data();
$gerotech_logo   = $gerotech_footer['logo']
	? gerotech_image_url( $gerotech_footer['logo'], '' )
	: GEROTECH_CHILD_URI . '/assets/images/gerotech-logo-white.svg';
?>
	<footer class="site-footer" role="contentinfo">
		<div class="site-footer__inner">
			<div class="site-footer__top">
				<div class="site-footer__brand">
					<img
						class="site-footer__logo-img"
						src="<?php echo esc_url( $gerotech_logo ); ?>"
						alt="<?php echo esc_attr( $gerotech_footer['logo_alt'] ); ?>"
						width="<?php echo (int) $gerotech_footer['logo_width']; ?>"
						height="<?php echo (int) $gerotech_footer['logo_height']; ?>"
					/>
					<?php if ( '' !== trim( $gerotech_footer['tagline'] ) ) : ?>
						<p class="site-footer__tagline">
							<?php echo nl2br( esc_html( $gerotech_footer['tagline'] ) ); ?>
						</p>
					<?php endif; ?>
					<?php if ( '' !== trim( $gerotech_footer['address'] ) || '' !== trim( $gerotech_footer['phone'] ) ) : ?>
						<address class="site-footer__address">
							<?php
							echo nl2br( esc_html( $gerotech_footer['address'] ) );
							if ( '' !== trim( $gerotech_footer['phone'] ) ) :
								?>
								<br /><a href="<?php echo esc_url( $gerotech_footer['phone_url'] ); ?>"><?php echo esc_html( $gerotech_footer['phone'] ); ?></a>
								<?php
							endif;
							?>
						</address>
					<?php endif; ?>
					<?php if ( ! empty( $gerotech_footer['socials'] ) ) : ?>
						<div class="site-footer__socials">
							<?php foreach ( $gerotech_footer['socials'] as $social ) : ?>
								<?php $social_svg = ! empty( $social['network'] ) ? gerotech_social_icon_svg( $social['network'] ) : ''; ?>
								<a class="social-icon<?php echo '' !== $social_svg ? ' social-icon--' . esc_attr( $social['network'] ) : ''; ?>" href="<?php echo esc_url( $social['url'] ); ?>" aria-label="<?php echo esc_attr( $social['label'] ); ?>"<?php echo gerotech_link_attrs( $social ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php
									if ( '' !== $social_svg ) {
										echo $social_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from a fixed path table, esc_attr'd.
									} else {
										echo esc_html( $social['icon'] );
									}
									?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php foreach ( $gerotech_footer['columns'] as $column ) : ?>
					<div>
						<?php if ( '' !== trim( $column['title'] ) ) : ?>
							<p class="site-footer__col-title"><?php echo esc_html( $column['title'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $column['links'] ) ) : ?>
							<ul class="site-footer__links">
								<?php foreach ( $column['links'] as $link ) : ?>
									<li><a href="<?php echo esc_url( $link['url'] ); ?>"<?php echo gerotech_link_attrs( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo gerotech_label( $link['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="site-footer__bottom">
				<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $gerotech_footer['copyright'] ); ?></span>
				<?php if ( ! empty( $gerotech_footer['legal_links'] ) ) : ?>
					<div class="site-footer__legal-links">
						<?php foreach ( $gerotech_footer['legal_links'] as $link ) : ?>
							<a href="<?php echo esc_url( $link['url'] ); ?>"<?php echo gerotech_link_attrs( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $link['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</footer>

	<?php if ( is_page( gerotech_modal_pages() ) ) : ?>
	<div class="mcs-modal" id="mcs-modal" role="dialog" aria-modal="true" aria-labelledby="mcs-modal-title" hidden>
		<div class="mcs-modal__backdrop"></div>
		<div class="mcs-modal__panel">
			<button type="button" class="mcs-modal__close" aria-label="Close">&#215;</button>
			<div class="mcs-modal__img"></div>
			<div class="mcs-modal__body">
				<h2 class="mcs-modal__title" id="mcs-modal-title"></h2>
				<div class="mcs-modal__detail"></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php wp_footer(); ?>
</body>
</html>
