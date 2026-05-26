<?php

/**
 * Listing Image
 */

$image_size  = $args[ 'image_size' ] ?? ( is_tax( 'product-category' ) ? 'large' : 'medium' );
$image_ratio = $args[ 'image_ratio' ] ?? 'landscape-16-9';
$image_position = $args[ 'image_position' ] ?? 'cover';
$image_variant = is_tax( 'product-category' ) ? 'large' : get_template() . '-' . esc_attr( $image_size );

// Meta
$images = get_post_meta( get_the_ID(), 'product_image' );

if ( !empty( $images ) ) { ?>
	<figure class="f-listing__image f-listing__image--alt">
		<?php foreach ( $images as $image_id ) {
			$image_alt    = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			$image_meta   = wp_get_attachment_metadata( $image_id );
			$image_url    = wp_get_attachment_url( $image_id );
			$image_width  = $image_meta[ 'width' ];
			$image_height = $image_meta[ 'height' ];
			?>
			<a href="<?php the_permalink(); ?>"
			   tabindex="-1"
			   class="f-image a-image a-image--<?php echo esc_attr( $image_position ); ?> a-image--<?php echo esc_attr( $image_ratio ); ?>">
				<?php echo wp_get_attachment_image( $image_id, $image_variant, false, array(
					'data-lazy' => 'false',
					'loading'   => 'eager',
					'decoding'  => 'async',
				) ); ?>
			</a>
		<?php } ?>
	</figure>
<?php } else if ( has_post_thumbnail() ) { ?>
	<figure class="f-listing__image">
		<?php if ( has_post_thumbnail() ) { ?>
			<a href="<?php the_permalink(); ?>"
			   tabindex="-1"
			   class="f-image a-image a-image--<?php echo esc_attr( $image_position ); ?> a-image--<?php echo esc_attr( $image_ratio ); ?>">
				<?php the_post_thumbnail( $image_variant, array(
					'data-lazy' => 'false',
					'loading'   => 'eager',
					'decoding'  => 'async',
				) ); ?>
			</a>
		<?php } else {
			get_template_part( 'templates/image/listing', 'placeholder', array(
				'ratio' => esc_attr( $image_ratio ),
			) );
		} ?>
	</figure>
<?php } else { ?>
	<figure class="f-listing__image">
		<?php get_template_part( 'templates/image/listing', 'placeholder', array(
			'ratio' => esc_attr( $image_ratio ),
		) ); ?>
	</figure>
<?php }
