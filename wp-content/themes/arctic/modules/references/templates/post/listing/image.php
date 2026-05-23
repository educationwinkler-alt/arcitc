<?php

/**
 * Listing Image
 */

?>

<figure class="f-listing__image">
	<?php if ( has_post_thumbnail() ) {
		if ( get_post_meta( get_the_ID(), 'reference_single', true ) == 0 ) {
			$image_alt    = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
			$image_meta   = wp_get_attachment_metadata( get_post_thumbnail_id() );
			$image_url    = wp_get_attachment_url( get_post_thumbnail_id() );
			$image_width  = $image_meta[ 'width' ];
			$image_height = $image_meta[ 'height' ];
			?>
			<a href="<?php echo esc_url( $image_url ); ?>"
			   class="f-image a-image a-image--cover a-image--landscape js-image"
			   data-pswp-width="<?php echo esc_attr( $image_width ); ?>"
			   data-pswp-height="<?php echo esc_attr( $image_height ); ?>"
			   data-cropped="true"
			   target="_blank">
				<?php the_post_thumbnail( get_template() . '-medium' ); ?>
			</a>
		<?php } else { ?>
			<a href="<?php the_permalink(); ?>" tabindex="-1" class="f-image a-image a-image--cover a-image--landscape">
				<?php the_post_thumbnail( get_template() . '-medium' ); ?>
			</a>
		<?php }
	} else {
		get_template_part( 'templates/image/listing', 'placeholder' );
	} ?>
</figure>
