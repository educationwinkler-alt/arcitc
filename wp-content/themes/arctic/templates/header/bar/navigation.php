<?php

/**
 * Bar Navigation
 */

if ( has_nav_menu( 'navigation_bar' ) && function_exists( 'baspa_navigation_bar' ) ) { ?>

	<nav class="f-bar__navigation" itemscope itemtype="https://schema.org/SiteNavigationElement"
	     aria-label="<?php echo esc_attr__( 'Bar Navigation', 'baspa' ); ?>">
		<?php baspa_navigation_bar(); ?>
	</nav>

<?php }
