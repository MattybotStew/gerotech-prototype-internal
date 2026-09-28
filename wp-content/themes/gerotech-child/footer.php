<?php
/**
 * Site footer.
 *
 * Ported from partials/site-footer.html. Column links are hardcoded for v1
 * (see implementation plan §7). Social + legal URLs are placeholders pending
 * client values.
 *
 * @package GerotechChild
 */

?>
	<footer class="site-footer" role="contentinfo">
		<div class="site-footer__inner">
			<div class="site-footer__top">
				<div class="site-footer__brand">
					<img
						class="site-footer__logo-img"
						src="<?php echo esc_url( GEROTECH_CHILD_URI . '/assets/images/gerotech-logo-white.svg' ); ?>"
						alt="Gerotech — Machines, Solutions, Support"
						width="176"
						height="28"
					/>
					<p class="site-footer__tagline">
						Michigan's Premier CNC Machinery Distributor &amp; Engineering Solutions
						Provider — serving manufacturers since 1987.
					</p>
					<address class="site-footer__address">
						29220 Commerce Drive<br />
						Flat Rock, MI 48134<br />
						<a href="tel:+17343797788">734-379-7788</a>
					</address>
					<div class="site-footer__socials">
						<a class="social-icon" href="#" aria-label="LinkedIn">in</a>
						<a class="social-icon" href="#" aria-label="Instagram">ig</a>
						<a class="social-icon" href="#" aria-label="YouTube">▶</a>
					</div>
				</div>

				<div>
					<p class="site-footer__col-title"><?php esc_html_e( 'Machines', 'gerotech-child' ); ?></p>
					<ul class="site-footer__links">
						<li><a href="https://gerotech.com/machines" target="_blank" rel="noopener noreferrer">Machining Centers ↗</a></li>
						<li><a href="https://gerotech.com/machines" target="_blank" rel="noopener noreferrer">Turning Centers ↗</a></li>
						<li><a href="https://gerotech.com/machines" target="_blank" rel="noopener noreferrer">EDM ↗</a></li>
						<li><a href="<?php gerotech_page_link( 'automation-integration' ); ?>">Automation</a></li>
						<li><a href="https://gerotech.com/machines" target="_blank" rel="noopener noreferrer">All Machines ↗</a></li>
					</ul>
				</div>

				<div>
					<p class="site-footer__col-title"><?php esc_html_e( 'Solutions & Support', 'gerotech-child' ); ?></p>
					<ul class="site-footer__links">
						<li><a href="<?php gerotech_page_link( 'engineered-solutions' ); ?>">Engineered Solutions</a></li>
						<li><a href="<?php gerotech_page_link( 'support' ); ?>">Service Request</a></li>
						<li><a href="<?php gerotech_page_link( 'support' ); ?>">Parts</a></li>
						<li><a href="<?php gerotech_page_link( 'training' ); ?>">Training</a></li>
						<li><a href="<?php echo esc_url( gerotech_page_url( 'support' ) . '#documentation' ); ?>">Documentation</a></li>
					</ul>
				</div>

				<div>
					<p class="site-footer__col-title"><?php esc_html_e( 'Company', 'gerotech-child' ); ?></p>
					<ul class="site-footer__links">
						<li><a href="<?php gerotech_page_link( 'about' ); ?>">About Gerotech</a></li>
						<li><a href="<?php gerotech_page_link( 'about' ); ?>">Our Team</a></li>
						<li><a href="<?php gerotech_page_link( 'careers' ); ?>">Careers</a></li>
						<li><a href="<?php gerotech_page_link( 'about' ); ?>">News</a></li><!-- TODO: wire to client news/blog URL -->
						<li><a href="<?php gerotech_page_link( 'about' ); ?>">Contact</a></li>
					</ul>
				</div>
			</div>

			<div class="site-footer__bottom">
				<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Gerotech, Inc. All rights reserved.</span>
				<div class="site-footer__legal-links">
					<a href="<?php gerotech_page_link( 'about' ); ?>">Privacy Policy</a><!-- TODO: client legal URL -->
					<a href="<?php gerotech_page_link( 'about' ); ?>">Terms of Use</a><!-- TODO: client legal URL -->
				</div>
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
