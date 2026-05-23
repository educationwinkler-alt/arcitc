<?php

/**
 * Listing Image
 */

$image_class = array( 'f-image', 'a-image', 'a-image--cover' );
$image_size  = $args[ 'size' ] ?? 'medium';

if ( isset( $args[ 'ratio' ] ) ) {
	$image_class[] = 'a-image--' . esc_attr( $args[ 'ratio' ] );
} else {
	$image_class[] = 'a-image--landscape';
} ?>

<figure class="f-listing__image">
	<?php if ( has_post_thumbnail() ) { ?>
		<a <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $image_class ); ?>
				href="<?php the_permalink(); ?>"
				tabindex="-1">
			<?php the_post_thumbnail( get_template() . '-' . esc_attr( $image_size ) ); ?>
		</a>
	<?php } else {
		get_template_part( 'templates/image/listing', 'placeholder', array(
			'ratio' => $args[ 'ratio' ] ?? 'landscape',
		) );
	} ?>
</figure>
