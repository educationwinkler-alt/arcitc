<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-listing', 'f-listing--list', 'f-listing--post' );
if ( isset( $args[ 'post_number' ] ) ) {
	$post_class[] = 'f-listing--' . esc_attr( $args[ 'post_number' ] );

	if ( $args[ 'post_number' ] == 1 ) {
		$post_class[] = 'f-listing--first';
	}
}
if ( !has_post_thumbnail() ) {
	$post_class[] = 'f-listing--no-image';
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="a-flex a-flex--align-center a-gap--0">
		<div class="a-flex__item--100 a-flex__item--40:m">

			<div class="f-listing__image--container">
				<?php
				get_template_part( 'templates/image/listing', '', array(
					'ratio' => 'square',
				) );
				get_template_part( 'modules/posts/templates/post/common/categories', '', array(
					'questions' => true,
				) );
				?>
			</div>

		</div>
		<div class="a-flex__item--100 a-flex__item--auto:m">

			<div class="f-listing__container a-stack a-gap--xs">

				<?php get_template_part( 'modules/posts/templates/post/common/categories', '', array(
					'questions' => false,
				) ); ?>

				<header class="f-listing__header a-stack a-gap--xs">
					<?php
					get_template_part( 'templates/listing/header' );
					?>
				</header>

				<?php if ( has_excerpt() ) { ?>
					<div class="f-listing__excerpt">
						<?php the_excerpt(); ?>
					</div>
				<?php }

				get_template_part( 'modules/posts/templates/post/listing/footer' );
				?>
			</div>

		</div>
	</div>

</article>
