<?php

/**
 * Gallery
 */

$images_key = $args['meta_key'] ?? 'images';
$image_size = $args['image_size'] ?? 'large';
$images     = isset( $args['image_ids'] ) && is_array( $args['image_ids'] )
	? $args['image_ids']
	: get_post_meta( get_the_ID(), $images_key );

$images = array_values( array_filter( array_map( 'absint', $images ), static function ( int $image_id ): bool {
	return $image_id > 0 && wp_attachment_is_image( $image_id );
} ) );

if ( !empty( $images ) ) { ?>

	<div class="f-gallery f-gallery--slideshow f-slides swiper js-slides">
		<div class="f-gallery__wrapper f-slides__wrapper swiper-wrapper">

			<?php foreach ( $images as $image_id ) { ?>
				<figure class="f-gallery__slide swiper-slide a-image a-image--cover">
					<?php echo wp_get_attachment_image( $image_id, get_template() . '-' . esc_attr( $image_size ) ); ?>
				</figure>

			<?php } ?>

		</div>

		<div class="f-gallery__controls f-slides__controls f-slides__controls--navigation">
			<button type="button"
			        class="f-gallery__control f-gallery__control--prev f-slides__control f-slides__control--prev a-button a-button--accent a-button--icon js-slides__prev">
				<?php get_template_part( 'images/icon/arrow-left', 'xs' ); ?></button>

			<button type="button"
			        class="f-gallery__control f-gallery__control--next f-slides__control f-slides__control--next a-button a-button--accent a-button--icon js-slides__next">
				<?php get_template_part( 'images/icon/arrow-right', 'xs' ); ?></button>
		</div>
	</div>

<?php }
