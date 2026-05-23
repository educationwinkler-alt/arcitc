<?php

/**
 * Search Listing
 */

// Class
$post_class = array( 'f-post', 'f-listing', 'f-listing--search' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="a-flex a-flex--align-center a-gap-row--0">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="a-flex__item--25 a-flex__item--20:m">

				<?php get_template_part( 'templates/image/listing' ); ?>

			</div>
		<?php } ?>
		<div class="a-flex__item--auto">

			<div class="f-listing__container a-stack a-gap--xxs">

				<header class="f-listing__header">
					<?php get_template_part( 'templates/listing/header' ); ?>
				</header>

				<?php if ( has_excerpt() ) { ?>
					<div class="f-listing__excerpt">
						<?php the_excerpt(); ?>
					</div>
				<?php } ?>
			</div>

		</div>
	</div>

</article>
