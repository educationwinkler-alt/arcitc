<?php

/**
 * Slide Caption
 */

?>

<div class="f-caption__container a-container">

	<div class="a-flex">
		<div class="a-flex__item--100 a-flex__item--60:m">

			<div class="f-caption a-stack a-stack--justify-start a-gap--s">

				<header class="f-caption__header">
					<h2><?php the_title(); ?></h2>
				</header>

				<?php if ( !empty( get_the_content() ) ) { ?>
					<div class="f-content a-content">
						<?php the_content(); ?>
					</div>
				<?php } ?>

				<footer class="f-caption__footer">
					<?php get_template_part( 'modules/slides/templates/slide/button' ); ?>
				</footer>

			</div>

		</div>
		<div class="a-flex__item--100 a-flex__item--auto:m">

		</div>
	</div>

</div>
