<?php

/**
 * Content Section
 */

if ( get_the_content() ) { ?>

	<section class="f-section f-section--content">
		<div class="f-section__container a-container">
			<?php get_template_part( 'templates/content' ); ?>
		</div>
	</section>

<?php }
