<?php
/**
 * Mobile navigation.
 *
 * Generated from the SAME tree as the desktop menu (gerotech_nav_items() +
 * the two panels), which is the point: header.php used to repeat every link a
 * second time here, so the two could silently drift.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gerotech_nav_items = gerotech_nav_items();
$machines_panel     = gerotech_machines_panel();
$es_panel           = gerotech_es_panel();
$gerotech_header    = gerotech_header_data();
?>
<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'gerotech-child' ); ?>">
	<?php
	foreach ( $gerotech_nav_items as $item ) :
		if ( empty( $item['show_mobile'] ) ) {
			continue;
		}

		$children = array();

		if ( 'machines-mega' === $item['style'] ) {
			// Machine groups are a phone-length list, not a model catalogue:
			// one sub-link per group, then the full catalog link. The order is
			// the group's own "mobile order" when set, because the single
			// column reads better than the desktop column order.
			$flat = array();
			foreach ( array( 1, 2, 3, 4 ) as $col ) {
				if ( empty( $machines_panel['groups'][ $col ] ) ) {
					continue;
				}
				foreach ( $machines_panel['groups'][ $col ] as $group ) {
					$flat[] = $group;
				}
			}

			usort(
				$flat,
				function ( $a, $b ) {
					$a_order = empty( $a['mobile_order'] ) ? 0 : (int) $a['mobile_order'];
					$b_order = empty( $b['mobile_order'] ) ? 0 : (int) $b['mobile_order'];
					if ( $a_order === $b_order ) {
						return 0;
					}
					// Unset (0) orders sort last rather than jumping to the top.
					if ( 0 === $a_order ) {
						return 1;
					}
					if ( 0 === $b_order ) {
						return -1;
					}
					return $a_order < $b_order ? -1 : 1;
				}
			);

			foreach ( $flat as $group ) {
				$children[] = array(
					'label' => $group['title'],
					'url'   => '#',
					'class' => '',
				);
			}

			$footer_label = '' !== trim( $machines_panel['footer']['mobile_label'] )
				? $machines_panel['footer']['mobile_label']
				: $machines_panel['footer']['label'];

			if ( '' !== trim( $machines_panel['footer']['label'] ) ) {
				$children[] = array(
					'label'   => $footer_label,
					'url'     => $machines_panel['footer']['url'],
					'class'   => 'mobile-nav__sublink--external',
					'new_tab' => $machines_panel['footer']['new_tab'],
				);
			}
		} elseif ( 'es-mega' === $item['style'] ) {
			foreach ( $es_panel['services'] as $service ) {
				$children[] = array(
					'label'    => '',
					'lead'     => $service['lead'],
					'main'     => $service['main'],
					'is_label' => true,
				);

				foreach ( $service['links'] as $link ) {
					$children[] = array(
						'label'   => '' !== trim( (string) ( $link['mobile_label'] ?? '' ) ) ? $link['mobile_label'] : $link['label'],
						'url'     => $link['url'],
						'new_tab' => $link['new_tab'],
						'class'   => '',
					);
				}
			}

			if ( '' !== trim( $es_panel['cta']['label'] ) ) {
				$children[] = array(
					'label' => $es_panel['cta']['mobile_label'] . ' →',
					'url'   => $es_panel['cta']['url'],
					'class' => 'mobile-nav__sublink--cta',
				);
			}
		} elseif ( ! empty( $item['links'] ) ) {
			foreach ( $item['links'] as $link ) {
				$children[] = array(
					'label'   => $link['label'],
					'url'     => $link['url'],
					'new_tab' => $link['new_tab'],
					'class'   => '',
				);
			}
		}

		if ( empty( $children ) ) :
			?>
			<a class="mobile-nav__link" href="<?php echo esc_url( $item['url'] ); ?>"<?php echo gerotech_link_attrs( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $item['label'] ); ?></a>
			<?php
			continue;
		endif;
		?>
		<details class="mobile-nav__group">
			<summary class="mobile-nav__link mobile-nav__summary"><?php echo esc_html( $item['label'] ); ?> <span class="arrow" aria-hidden="true">+</span></summary>
			<div class="mobile-nav__sublinks">
				<?php foreach ( $children as $child ) : ?>
					<?php if ( ! empty( $child['is_label'] ) ) : ?>
						<p class="mobile-nav__sublink mobile-nav__sublabel"><?php echo gerotech_split_title( $child['lead'], $child['main'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php else : ?>
						<a class="mobile-nav__sublink<?php echo '' !== $child['class'] ? ' ' . esc_attr( $child['class'] ) : ''; ?>" href="<?php echo esc_url( $child['url'] ); ?>"<?php echo ! empty( $child['new_tab'] ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $child['label'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</details>
	<?php endforeach; ?>

	<?php if ( '' !== trim( $gerotech_header['cta_label'] ) ) : ?>
		<a class="mobile-nav__link" href="<?php echo esc_url( $gerotech_header['cta_url'] ); ?>"<?php echo $gerotech_header['cta_new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $gerotech_header['cta_label'] ); ?></a>
	<?php endif; ?>
</nav>
