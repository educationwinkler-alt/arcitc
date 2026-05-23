<?php

/**
 * Gallery
 */

$images_key = $args[ 'meta_key' ] ?? 'images';
$images     = get_post_meta( get_the_ID(), $images_key );

if ( !empty( $images ) ) { ?>

	<div class="f-gallery a-flex a-gap--xs js-images">

		<?php foreach ( $images as $image_id ) {
			$image_alt    = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			$image_meta   = wp_get_attachment_metadata( $image_id );
			$image_url    = wp_get_attachment_url( $image_id );
			$image_width  = $image_meta[ 'width' ];
			$image_height = $image_meta[ 'height' ];
			?>

			<figure class="a-flex__item--50 a-flex__item--33:m a-flex__item--25:l">
				<a href="<?php echo esc_url( $image_url ); ?>"
				   class="f-gallery__image a-image a-image--cover a-image--landscape js-image"
				   data-pswp-width="<?php echo esc_attr( $image_width ); ?>"
				   data-pswp-height="<?php echo esc_attr( $image_height ); ?>"
				   data-cropped="true"
				   target="_blank">
					<?php echo wp_get_attachment_image( $image_id, get_template() . '-medium' ); ?>
				</a>
			</figure>

		<?php } ?>

	</div>

<?php }
