<?php

/**
 * Listing Image
 */

$image_size     = $args['image_size'] ?? ( is_tax( 'product-category' ) ? 'large' : 'medium' );
$image_ratio    = $args['image_ratio'] ?? 'landscape-16-9';
$image_position = $args['image_position'] ?? 'cover';
$image_variant  = is_tax( 'product-category' ) ? 'large' : get_template() . '-' . esc_attr( $image_size );

$image_ids = array_values( array_filter( array_map( 'absint', get_post_meta( get_the_ID(), 'product_image' ) ), static function ( int $image_id ): bool {
	return $image_id > 0 && wp_attachment_is_image( $image_id );
} ) );

$image_id     = $image_ids[0] ?? 0;
$thumbnail_id = absint( get_post_thumbnail_id() );

if ( 0 === $image_id && $thumbnail_id > 0 && wp_attachment_is_image( $thumbnail_id ) ) {
	$image_id = $thumbnail_id;
}

$figure_class = array( 'f-listing__image' );
$media_status = 'missing';

if ( $image_id > 0 ) {
	$figure_class[] = !empty( $image_ids ) ? 'f-listing__image--product-media' : 'f-listing__image--featured-media';
	$media_status   = !empty( $image_ids ) ? 'product-image' : 'featured-image';
} else {
	$figure_class[] = 'f-listing__image--product-missing';
}
?>

<figure <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $figure_class ); ?> data-product-media="<?php echo esc_attr( $media_status ); ?>">
	<?php if ( $image_id > 0 ) { ?>
		<a href="<?php the_permalink(); ?>"
		   tabindex="-1"
		   class="f-image a-image a-image--<?php echo esc_attr( $image_position ); ?> a-image--<?php echo esc_attr( $image_ratio ); ?>">
			<?php echo wp_get_attachment_image( $image_id, $image_variant, false, array(
				'data-lazy' => 'false',
				'loading'   => is_tax( 'product-category' ) ? 'eager' : 'lazy',
				'decoding'  => 'async',
			) ); ?>
		</a>
	<?php } else {
		get_template_part( 'templates/image/listing', 'placeholder', array(
			'ratio' => esc_attr( $image_ratio ),
			'label' => 'Arctic Spas',
		) );
	} ?>
</figure>
