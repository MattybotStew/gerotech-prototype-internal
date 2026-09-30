<?php
/**
 * Desktop navigation: the top-level menu plus the two mega panels.
 *
 * Driven entirely by inc/global-content.php — the class names, the four-column
 * machines grid and the two-column Engineered Solutions grid are all unchanged
 * from the markup that used to live inline in header.php, so no CSS or JS
 * change accompanies this extraction.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gerotech_nav_items = gerotech_nav_items();
$machines_panel     = gerotech_machines_panel();
$es_panel           = gerotech_es_panel();

/**
 * Attributes for one top-level link row.
 *
 * @param array $item Normalised nav item.
 * @return string
 */
$gerotech_nav_item_attrs = function ( $item ) {
	$attrs = gerotech_link_attrs( $item );
	if ( 'plain' !== $item['style'] ) {
		$attrs .= ' aria-haspopup="true"';
	}
	return $attrs;
};
?>
<nav aria-label="<?php esc_attr_e( 'Main navigation', 'gerotech-child' ); ?>">
	<ul class="site-nav">
		<?php foreach ( $gerotech_nav_items as $item ) : ?>
			<?php
			$item_classes = 'site-nav__item';
			$panel_class  = '';
			$panel_mod    = '';

			if ( 'dropdown' === $item['style'] ) {
				$item_classes .= ' has-dropdown';
				$panel_class   = 'dropdown';
			} elseif ( 'machines-mega' === $item['style'] ) {
				$item_classes .= ' has-mega has-mega--machines';
				$panel_class   = 'mega-nav mega-nav--machines';
				$panel_mod     = 'mega-nav__inner--machines';
			} elseif ( 'es-mega' === $item['style'] ) {
				$item_classes .= ' has-mega';
				$panel_class   = 'mega-nav mega-nav--es';
				$panel_mod     = 'mega-nav__inner--es';
			}
			?>
			<li class="<?php echo esc_attr( $item_classes ); ?>">
				<a class="site-nav__link" href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $gerotech_nav_item_attrs( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $item['label'] ); ?><?php if ( 'plain' !== $item['style'] ) : ?> <span class="arrow" aria-hidden="true">+</span><?php endif; ?></a>

				<?php if ( 'dropdown' === $item['style'] && ! empty( $item['links'] ) ) : ?>
					<div class="dropdown" role="menu" aria-label="<?php echo esc_attr( $item['label'] . ' ' . __( 'menu', 'gerotech-child' ) ); ?>">
						<?php foreach ( $item['links'] as $link ) : ?>
							<a class="dropdown__link" href="<?php echo esc_url( $link['url'] ); ?>" role="menuitem"<?php echo gerotech_link_attrs( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $link['label'] ); ?></a>
						<?php endforeach; ?>
					</div>

				<?php elseif ( 'machines-mega' === $item['style'] ) : ?>
					<div class="<?php echo esc_attr( $panel_class ); ?>" role="menu" aria-label="<?php echo esc_attr( $item['label'] . ' ' . __( 'menu', 'gerotech-child' ) ); ?>">
						<div class="mega-nav__inner <?php echo esc_attr( $panel_mod ); ?>"<?php echo 4 === (int) $machines_panel['columns'] ? '' : ' style="grid-template-columns: repeat(' . (int) $machines_panel['columns'] . ', 1fr);"'; ?>>

							<?php for ( $col = 1; $col <= (int) $machines_panel['columns']; $col++ ) : ?>
								<?php if ( empty( $machines_panel['groups'][ $col ] ) ) { continue; } ?>
								<div>
									<?php foreach ( $machines_panel['groups'][ $col ] as $group ) : ?>
										<div class="mega-nav__machine-group">
											<p class="mega-nav__machine-title"><?php echo esc_html( $group['title'] ); ?></p>
											<?php foreach ( $group['links'] as $link ) : ?>
												<a class="mega-nav__machine-link" href="<?php echo esc_url( $link['url'] ); ?>" role="menuitem"<?php echo gerotech_link_attrs( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $link['label'] ); ?></a>
											<?php endforeach; ?>
										</div>
									<?php endforeach; ?>

									<?php if ( $col === (int) $machines_panel['columns'] && '' !== trim( $machines_panel['help']['title'] ) && '' !== trim( $machines_panel['help']['label'] ) ) : ?>
										<div class="mega-nav__machine-help">
											<p class="mega-nav__machine-help-title"><?php echo esc_html( $machines_panel['help']['title'] ); ?></p>
											<a class="mega-nav__cta-btn" href="<?php echo esc_url( $machines_panel['help']['url'] ); ?>"><?php echo esc_html( $machines_panel['help']['label'] ); ?></a>
										</div>
									<?php endif; ?>
								</div>
							<?php endfor; ?>

							<?php if ( '' !== trim( $machines_panel['footer']['label'] ) ) : ?>
								<div class="mega-nav__machines-footer">
									<a class="mega-nav__machines-footer-link" href="<?php echo esc_url( $machines_panel['footer']['url'] ); ?>"<?php echo $machines_panel['footer']['new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $machines_panel['footer']['label'] ); ?> <?php echo gerotech_ext_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG markup. ?></a>
								</div>
							<?php endif; ?>

						</div><!-- /.mega-nav__inner -->
					</div>

				<?php elseif ( 'es-mega' === $item['style'] ) : ?>
					<div class="<?php echo esc_attr( $panel_class ); ?>" role="menu" aria-label="<?php echo esc_attr( $item['label'] . ' ' . __( 'menu', 'gerotech-child' ) ); ?>">
						<div class="mega-nav__inner <?php echo esc_attr( $panel_mod ); ?>">

							<!-- Col 1: Categories -->
							<div class="mega-nav__col mega-nav__col--categories">
								<?php if ( '' !== trim( $es_panel['col1_title'] ) ) : ?>
									<p class="mega-nav__col-title"><?php echo esc_html( $es_panel['col1_title'] ); ?></p>
								<?php endif; ?>

								<?php foreach ( $es_panel['categories'] as $category ) : ?>
									<div class="mega-nav__category<?php echo ! empty( $category['last'] ) ? ' mega-nav__category--last' : ''; ?>">
										<a class="mega-nav__cat-link" href="<?php echo esc_url( $category['url'] ); ?>" role="menuitem"<?php echo gerotech_link_attrs( $category ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo gerotech_split_title( $category['lead'], $category['main'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
										<?php if ( '' !== trim( $category['description'] ) ) : ?>
											<p class="mega-nav__cat-desc"><?php echo esc_html( $category['description'] ); ?></p>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>

								<?php if ( '' !== trim( $es_panel['cta']['label'] ) ) : ?>
									<a class="mega-nav__cta-btn" href="<?php echo esc_url( $es_panel['cta']['url'] ); ?>"><?php echo esc_html( $es_panel['cta']['label'] ); ?></a>
								<?php endif; ?>
							</div>

							<!-- Col 2: All Services -->
							<div>
								<?php if ( '' !== trim( $es_panel['col2_title'] ) ) : ?>
									<p class="mega-nav__col-title"><?php echo esc_html( $es_panel['col2_title'] ); ?></p>
								<?php endif; ?>

								<?php foreach ( $es_panel['services'] as $service ) : ?>
									<div class="mega-nav__services-group">
										<p class="mega-nav__services-label"><?php echo gerotech_split_title( $service['lead'], $service['main'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
										<?php foreach ( $service['links'] as $link ) : ?>
											<a class="mega-nav__service-link" href="<?php echo esc_url( $link['url'] ); ?>" role="menuitem"<?php echo gerotech_link_attrs( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $link['label'] ); ?></a>
										<?php endforeach; ?>
									</div>
								<?php endforeach; ?>
							</div>

						</div><!-- /.mega-nav__inner -->
					</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
