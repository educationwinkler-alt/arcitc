<?php

/**
 * Listing Image
 */

// Meta
$url = get_post_meta( get_the_ID(), 'accessory_url', true );

?>
<figure class="f-listing__image">
	<a href="<?php echo !empty( $url ) ? esc_url( $url ) : get_the_permalink(); ?>"
	   class="f-image a-image a-image--contain a-image--square" target="_blank" tabindex="-1">
		<?php if ( has_post_thumbnail() ) {
			the_post_thumbnail( get_template() . '-small-square' );
		} else {
			get_template_part( 'templates/image/listing', 'placeholder', array(
				'ratio' => 'square',
			) );
		} ?>
	</a>
</figure>
