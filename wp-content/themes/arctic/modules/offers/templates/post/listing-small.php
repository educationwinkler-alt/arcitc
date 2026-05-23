<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-listing', 'f-listing--offer', 'f-listing--offer-small' );

if ( !has_post_thumbnail() ) {
	$post_class[] = 'f-listing--no-image';
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="a-flex a-flex--align-center a-gap--s a-gap-row--0">
		<div class="a-flex__item--100 a-flex__item:s">

			<?php get_template_part( 'templates/image/listing' ); ?>

		</div>
		<div class="a-flex__item--100 a-flex__item--auto:s">
			<div class="f-listing__container">
				<div class="a-flex a-flex--align-center a-gap--s">
					<div class="a-flex__item--100 a-flex__item--auto:s">

						<div class="a-stack a-stack--row a-stack--align-center a-stack--justify-center a-gap--xs">
							<?php get_template_part( 'modules/offers/templates/post/uni/type' ); ?>
							<header class="f-listing__header">
								<?php get_template_part( 'modules/offers/templates/post/listing/header', '', array(
									'title_meta_key' => 'offer_title_short',
								) ); ?>
							</header>
						</div>

					</div>
					<div class="a-flex__item--100 a-flex__item:s">

						<?php get_template_part( 'modules/offers/templates/post/listing/button' ); ?>

					</div>
				</div>
			</div>
		</div>
	</div>

</article>
