<?php

/**
 * Listing Image
 */

$image_size = $args[ 'image_size' ] ?? 'product';

// Meta
$images = get_post_meta( get_the_ID(), 'product_image' );
$url    = get_post_meta( get_the_ID(), 'product_url', true );

?>
<figure class="f-listing__image">
	<a href="<?php echo !empty( $url ) ? esc_url( $url ) : get_the_permalink(); ?>"
	   class="f-image a-image a-image--contain a-image--landscape"
	   target="_blank"
	   tabindex="-1">
		<?php if ( has_post_thumbnail() ) {
			the_post_thumbnail( get_template() . '-' . esc_attr( $image_size ) );
		} else {
			get_template_part( 'templates/image/listing', 'placeholder' );
		} ?>
	</a>
</figure>
