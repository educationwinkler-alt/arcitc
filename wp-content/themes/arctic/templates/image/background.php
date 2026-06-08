<?php

/**
 * Background Image
 */

$page_id = is_home() && !is_front_page() ? get_option( 'page_for_posts' ) : get_the_ID();

// Media
$media_class = array( 'f-background', 'f-background__image' );

if ( is_tax() ) {
	$term     = get_queried_object();
	$taxonomy = $term instanceof WP_Term ? $term->taxonomy : '';
	$media    = function_exists( 'arctic_hero_media_from_term' )
		? arctic_hero_media_from_term( get_queried_object_id(), $taxonomy, array(
			'fallback_image_key' => 'product-category' === $taxonomy ? 'category_image' : '',
			'image_size'         => get_template() . '-huge',
		) )
		: array( 'type' => 'none' );

	if ( function_exists( 'arctic_render_hero_media' ) && 'none' !== ( $media['type'] ?? 'none' ) ) {
		arctic_render_hero_media( $media, $media_class );
	}

} else {
	$thumbnail_id = get_post_thumbnail_id( $page_id );
	$media        = function_exists( 'arctic_hero_media_from_post' )
		? arctic_hero_media_from_post( (int) $page_id, null, (int) $thumbnail_id, array(
			'source'     => 'post-hero',
			'image_size' => get_template() . '-huge',
		) )
		: array( 'type' => 'none' );

	if ( function_exists( 'arctic_render_hero_media' ) && 'none' !== ( $media['type'] ?? 'none' ) ) {

		/**
		 * Admin hero media.
		 */
		$media_class[] = 'f-background__image--thumb';
		if ( is_home() && !is_front_page() ) {
			$media_class[] = 'f-background__image--index';
		}

		arctic_render_hero_media( $media, $media_class );

	} else if ( has_header_image() ) {

		/**
		 * Header
		 */
		$media_class[] = 'f-background__image--header'; ?>

		<figure <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $media_class ); ?>>
			<?php
			add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
			the_header_image_tag();
			remove_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
			?>
		</figure>

	<?php }
}
