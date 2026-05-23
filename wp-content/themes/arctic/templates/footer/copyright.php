<?php

/**
 * Footer Copyright
 */

?>

<div class="f-section f-section--copyright">
	<div class="f-section__container">

		<div class="a-flex a-flex--align-start">
			<div class="a-flex__item--100 a-flex__item--auto:m">

				<div class="f-footer__copyright">
					<?php echo '&copy;&nbsp;' . date( 'Y' ) . '&nbsp;' . '<a href="' . esc_url( home_url( '/' ) ) . '">' . get_bloginfo( 'name' ) . '</a>. <br class="a-hide a-show:min a-show:xs a-show:s">' . esc_html__( 'Všechna práva vyhrazena.', 'baspa' ); ?>
				</div>

			</div>
			<?php if ( has_nav_menu( 'navigation_footer' ) && function_exists( 'baspa_navigation_footer' ) ) { ?>
				<div class="a-flex__item--100 a-flex__item:m">

					<nav class="f-footer__navigation" itemscope itemtype="https://schema.org/SiteNavigationElement"
					     aria-label="<?php echo esc_attr__( 'Navigace v patičce', 'baspa' ); ?>">
						<?php baspa_navigation_footer(); ?>
					</nav>

				</div>
			<?php } ?>
		</div>

	</div>
</div>
