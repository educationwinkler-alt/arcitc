<?php

/**
 * Slide Background
 */

// Background
$background_class = array( 'f-slide__background', 'a-image--cover' );
$background_args  = array();
$thumbnail_id     = get_post_thumbnail_id();
$use_figma_hero   = is_front_page() && 'home-hero-arctic' === get_post_meta( get_the_ID(), '_arctic_seed_key', true );
$hero_media       = function_exists( 'arctic_hero_media_from_post' )
	? arctic_hero_media_from_post( get_the_ID(), 'slide_hero', $thumbnail_id, array(
		'source'     => 'slide-cpt',
		'image_size' => get_template() . '-huge',
	) )
	: array( 'type' => 'none' );
if ( isset( $args[ 'slide_count' ] ) && $args[ 'slide_count' ] == 1 ) {
	$background_args = array(
		'data-slide'    => $args[ 'slide_count' ],
		'data-lazy'     => false,
		'fetchpriority' => 'high',
		'loading'       => 'eager',
	);
}

if ( 'video' === ( $hero_media['type'] ?? 'none' ) && function_exists( 'arctic_render_hero_media' ) ) {
	arctic_render_hero_media(
		$hero_media,
		$background_class,
		array(
			'data-slide' => isset( $args[ 'slide_count' ] ) ? (string) $args[ 'slide_count' ] : '1',
		)
	);
} else if ( has_post_thumbnail() ) { ?>

	<figure <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $background_class ); ?>>
		<?php
		if ( $use_figma_hero ) {
			$hero_alt = trim( (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) );
			?>
			<picture>
				<source media="(max-width: 767px)" srcset="<?php echo esc_url( content_url( 'uploads/import/figma/mobile-hp-hero-750-v2.jpg' ) ); ?>">
				<img width="1920" height="795"
				     src="<?php echo esc_url( content_url( 'uploads/import/figma/hp-hero-arctic-spas-07.jpg' ) ); ?>"
				     alt="<?php echo esc_attr( $hero_alt ?: get_the_title( $thumbnail_id ) ); ?>"
				     data-slide="<?php echo isset( $args[ 'slide_count' ] ) ? esc_attr( $args[ 'slide_count' ] ) : '1'; ?>"
				     fetchpriority="high" loading="eager" decoding="async">
			</picture>
			<?php
		} else {
			add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
			the_post_thumbnail( get_template() . '-huge', $background_args );
			remove_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
		}
		?>

		<?php if ( get_the_post_thumbnail_caption() ) { ?>
			<figcaption class="f-background__caption">
				<?php the_post_thumbnail_caption(); ?>
			</figcaption>
		<?php } ?>
	</figure>

<?php }
