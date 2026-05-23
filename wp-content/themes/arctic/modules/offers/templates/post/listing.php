<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-listing', 'f-listing--offer' );

if ( !has_post_thumbnail() ) {
	$post_class[] = 'f-listing--no-image';
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="a-flex a-flex--align-center a-gap--s a-gap--l:m">
		<div class="a-flex__item--100 a-flex__item--40:s">

			<?php get_template_part( 'templates/image/listing' ); ?>

		</div>
		<div class="a-flex__item--100 a-flex__item--auto:s">

			<div class="f-listing__container a-stack a-stack--justify-start a-gap--xs">

				<?php get_template_part( 'modules/offers/templates/post/uni/type' ); ?>

				<header class="f-listing__header">
					<?php get_template_part( 'modules/offers/templates/post/listing/header' ); ?>
				</header>

				<?php get_template_part( 'modules/offers/templates/post/listing/excerpt' ); ?>

				<?php get_template_part( 'modules/offers/templates/post/listing/button' ); ?>
			</div>

		</div>
	</div>

</article>
