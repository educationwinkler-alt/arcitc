<?php

/**
 * Gallery
 */

$images_key = $args[ 'meta_key' ] ?? 'images';
$image_size = $args[ 'image_size' ] ?? 'medium';
$images     = get_post_meta( get_the_ID(), $images_key );

if ( !empty( $images ) ) { ?>

	<div class="f-gallery__container a-container">
		<div class="f-gallery f-gallery--thumbs f-slides swiper js-slides--thumbs">
			<div class="f-gallery__wrapper f-slides__wrapper swiper-wrapper">

				<?php foreach ( $images as $image_id ) {
					$image_alt    = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					$image_meta   = wp_get_attachment_metadata( $image_id );
					$image_url    = wp_get_attachment_url( $image_id );
					$image_width  = $image_meta[ 'width' ];
					$image_height = $image_meta[ 'height' ];
					?>

					<figure class="f-gallery__slide f-gallery__slide--thumb swiper-slide a-image a-image--cover">
						<?php echo wp_get_attachment_image( $image_id, get_template() . '-' . esc_attr( $image_size ) ); ?>
					</figure>

				<?php } ?>

			</div>
		</div>
	</div>

<?php }
