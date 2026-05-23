<?php

/**
 * Slide Background
 */

// Background
$background_class = array( 'f-slide__background', 'a-image--cover' );
$background_args  = array();
if ( isset( $args[ 'slide_count' ] ) && $args[ 'slide_count' ] == 1 ) {
	$background_args = array(
		'data-slide'    => $args[ 'slide_count' ],
		'data-lazy'     => false,
		'fetchpriority' => 'high',
		'loading'       => 'eager',
	);
}

if ( has_post_thumbnail() ) { ?>

	<figure <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $background_class ); ?>>
		<?php
		add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
		the_post_thumbnail( get_template() . '-huge', $background_args );
		remove_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
		?>

		<?php if ( get_the_post_thumbnail_caption() ) { ?>
			<figcaption class="f-background__caption">
				<?php the_post_thumbnail_caption(); ?>
			</figcaption>
		<?php } ?>
	</figure>

<?php }
