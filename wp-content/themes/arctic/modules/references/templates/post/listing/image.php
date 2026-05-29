<?php

/**
 * Listing Image
 */

?>

<figure class="f-listing__image">
	<?php if ( has_post_thumbnail() ) {
		$image_meta   = wp_get_attachment_metadata( get_post_thumbnail_id() );
		$image_url    = wp_get_attachment_url( get_post_thumbnail_id() );
		$image_width  = !empty( $image_meta[ 'width' ] ) ? (int) $image_meta[ 'width' ] : 1600;
		$image_height = !empty( $image_meta[ 'height' ] ) ? (int) $image_meta[ 'height' ] : 1067;
		?>
		<a href="<?php echo esc_url( $image_url ); ?>"
		   class="f-image a-image a-image--cover a-image--landscape js-image"
		   data-pswp-width="<?php echo esc_attr( $image_width ); ?>"
		   data-pswp-height="<?php echo esc_attr( $image_height ); ?>"
		   data-cropped="true"
		   target="_blank">
			<?php echo get_the_post_thumbnail( get_the_ID(), get_template() . '-medium', array(
				'data-lazy' => 'false',
				'loading'   => 'eager',
				'decoding'  => 'async',
			) ); ?>
		</a>
	<?php
	} else {
		get_template_part( 'templates/image/listing', 'placeholder' );
	} ?>
</figure>
