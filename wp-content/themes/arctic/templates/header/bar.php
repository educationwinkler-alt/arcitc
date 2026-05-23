<?php

/**
 * Header Bar
 */

?>

<div class="f-bar">
	<div class="f-bar__container a-container">

		<div class="a-flex a-flex--align-center a-gap--s a-gap--l:m">
			<?php if ( has_nav_menu( 'navigation_bar' ) && function_exists( 'baspa_navigation_bar' ) ) { ?>
				<div class="a-flex__item a-hide a-show:l a-show:xl">
					<?php get_template_part( 'templates/header/bar/navigation' ); ?>
				</div>
			<?php } ?>

			<div class="a-flex__item--auto">
				<?php get_template_part( 'templates/header/bar/contacts' ); ?>
			</div>
		</div>

	</div>
</div>
