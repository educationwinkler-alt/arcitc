<?php

/**
 * Image Placeholder
 */

$placeholder_class = array( 'f-image', 'a-image', 'a-image--cover' );

if ( isset( $args[ 'ratio' ] ) ) {
	$placeholder_class[] = 'a-image--' . esc_attr( $args[ 'ratio' ] );
} else {
	$placeholder_class[] = 'a-image--landscape';
} ?>

<a <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $placeholder_class ); ?>
		href="<?php the_permalink(); ?>"
		tabindex="-1"
		aria-hidden="true"></a>
