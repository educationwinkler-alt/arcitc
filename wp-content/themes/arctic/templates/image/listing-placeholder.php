<?php

/**
 * Image Placeholder
 */

$placeholder_class = array( 'f-image', 'a-image', 'a-image--cover' );

if ( isset( $args[ 'ratio' ] ) ) {
	$placeholder_class[] = 'a-image--' . esc_attr( $args[ 'ratio' ] );
} else {
	$placeholder_class[] = 'a-image--landscape';
}

$placeholder_label = isset( $args['label'] ) ? trim( (string) $args['label'] ) : '';
if ( '' !== $placeholder_label ) {
	$placeholder_class[] = 'f-image--labeled-placeholder';
}
?>

<a <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $placeholder_class ); ?>
		href="<?php the_permalink(); ?>"
		tabindex="-1"
		aria-hidden="true">
	<?php if ( '' !== $placeholder_label ) { ?>
		<span class="f-image__placeholder-label"><?php echo esc_html( $placeholder_label ); ?></span>
	<?php } ?>
</a>
