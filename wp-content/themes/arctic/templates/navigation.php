<?php

/**
 * Navigation
 *
 * @since 1.0.0
 */

?>

<div id="<?php echo sanitize_title( esc_attr_x( 'navigation', 'anchor', 'baspa' ) ); ?>"
     class="f-off f-off--navigation a-off js-off"
     data-off="navigation"
     data-off-anchors="true"
     data-off-breakpoint="1400"
     data-off-position="top">

	<?php if ( has_nav_menu( 'navigation' ) && function_exists( 'baspa_navigation' ) ) { ?>

		<nav class="f-navigation" itemscope itemtype="https://schema.org/SiteNavigationElement"
		     aria-label="<?php echo esc_attr_x( 'Primary Navigation', 'navigation', 'baspa' ); ?>">
			<?php baspa_navigation(); ?>
		</nav>
	<?php } else {

		wp_page_menu( array(
			'menu_class' => 'f-navigation',
			'container'  => 'nav',
			'before'     => '<ul class="f-navigation__list--primary f-navigation__list f-navigation__list--off">',
			'after'      => '</ul>',
		) );
	} ?>

	<?php get_template_part( 'templates/header/bar/navigation' ); ?>

	<div class="f-buttons a-buttons">
		<?php get_template_part( 'templates/button/contact' ); ?>
	</div>

	<?php
	set_query_var( 'baspa_search_id', 'search-mobile' );
	get_search_form();

	get_template_part( 'templates/header/bar/contacts' );
	?>
</div>
