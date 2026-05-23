<?php

/**
 * Post Listing Footer
 */

$button_class = array( 'f-listing__button', 'a-button', 'a-button--outline', 'a-button--s' );
if ( isset( $args[ 'button_class' ] ) ) {
	$button_class[] = $args[ 'button_class' ];
} ?>

<footer class="f-listing__footer">

	<div class="f-listing__buttons a-buttons">
		<a href="<?php the_permalink(); ?>" <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $button_class ); ?>>
			<?php echo sprintf( __( 'Read article <span class="screen-reader-text">%s</span>', 'baspa' ), get_the_title() ); ?>
		</a>
	</div>

</footer>
