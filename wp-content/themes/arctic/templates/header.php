<?php

/**
 * Header
 *
 * @since 1.0.0
 */

?>

<header class="f-header js-autohide js-off__location" data-autohide-start="bottom">
	<?php get_template_part( 'templates/header/bar' ); ?>

	<div class="f-header__container a-container">

		<div class="a-flex a-flex--align-center a-gap--xxs">

			<div class="a-flex__item--auto a-flex__item:m">
				<?php get_template_part( 'templates/logo' ); ?>
			</div>

			<div class="a-flex__item a-flex__item--auto:m js-off__container">
				<?php get_template_part( 'templates/navigation' ); ?>
			</div>

			<div class="a-flex__item a-hide a-show:l a-show:xl">
				<?php get_template_part( 'templates/search/trigger' ); ?>
			</div>

			<div class="a-flex__item a-hide a-show:s a-show:m a-show:l a-show:xl">
				<div class="f-header__button">
					<?php get_template_part( 'templates/button/contact' ); ?>
				</div>
			</div>

			<div class="a-flex__item a-flex__item--0:l a-hide:l">
				<?php get_template_part( 'templates/navigation/trigger' ); ?>
			</div>

		</div>

	</div>
</header>
