<?php

/**
 * Preload
 *
 * @since 1.0.4
 */

/*
FILTERS

Disable preloading
add_filter( 'forqy_image_preload', '__return_false' );

Config preloading

if ( !function_exists( 'THEME_images_preload_config' ) ) {

	// @return array<string, array<string, bool|string>>

	function THEME_images_preload_config(): array {
		return array();
	}

	add_filter( 'forqy_image_preload_config', 'THEME_images_preload_config' );

}
*/

if ( !function_exists( 'forqy_image_preload' ) ) {

	/**
	 * Preload Image with Meta Tag '<link>'
	 *
	 * @return void
	 */
	function forqy_image_preload(): void {
		global $post;

		if ( apply_filters( 'forqy_image_preload', true ) ) {

			// get config
			$config = array_replace( array(
				'default' => array(
					'size'   => get_template() . '-large',
					'srcset' => true,
					'sizes'  => true,
				),
				'page'    => array(
					'size'   => get_template() . '-huge',
					'srcset' => false,
					'sizes'  => false,
				),
				'post'    => array(
					'size'   => get_template() . '-large',
					'srcset' => true,
					'sizes'  => true,
				),
			), apply_filters( 'forqy_image_preload_config', array() ) );

			// Current ID
			$id = ( is_home() && !is_front_page() ) ? get_option( 'page_for_posts' ) : ( !empty( $post ) && isset( $post->ID ) ? $post->ID : null );

			if ( isset( $id ) && is_int( $id ) && has_post_thumbnail( $id ) ) {

				$thumbnail_id = get_post_thumbnail_id( $id );

				foreach ( $config as $type => $settings ) {

					if ( $type !== 'default' ) {

						// Single or Index (Blog) Page
						if ( is_singular( $type ) || is_home() ) {

							$size   = !empty( $settings[ 'size' ] ) ? $settings[ 'size' ] : $config[ 'default' ][ 'size' ];
							$srcset = (bool)$settings[ 'srcset' ];
							$sizes  = (bool)$settings[ 'sizes' ];

							if ( $size ) {
								$thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, $size );

								/**
								 * Check WebP Format of the Image
								 */
								if ( isset( $thumbnail_src[ 0 ] ) ) {
									$webp_src = $thumbnail_src[ 0 ] . '.webp';
									if ( file_exists( get_attached_file( $thumbnail_id ) . '.webp' ) ) {
										$thumbnail_src[ 0 ] = $webp_src;
									}
								}

								// webp srcset
								if ( $srcset ) {
									$thumbnail_srcset = function_exists( 'forqy_image_get_srcset' ) ? forqy_image_get_srcset( $thumbnail_id, $settings[ 'size' ] ) : wp_get_attachment_image_srcset( $thumbnail_id, $settings[ 'size' ] );
								}

								// sizes
								if ( $sizes ) {
									$thumbnail_sizes = wp_get_attachment_image_sizes( $thumbnail_id, $settings[ 'size' ] );
								}
							}

							// define <link> attributes
							$link_attributes = array(
								'rel'           => 'preload',
								'as'            => 'image',
								'fetchpriority' => 'high',
							);

							if ( isset( $thumbnail_src ) ) {
								$link_attributes[ 'href' ] = esc_url( $thumbnail_src[ 0 ] );
							}
							if ( isset( $thumbnail_srcset ) && $thumbnail_srcset ) {
								$link_attributes[ 'imagesrcset' ] = esc_attr( $thumbnail_srcset );
							}
							if ( isset( $thumbnail_sizes ) && $thumbnail_sizes ) {
								$link_attributes[ 'imagesizes' ] = esc_attr( $thumbnail_sizes );
							}

							// construct <link> tag
							if ( !empty( $thumbnail_src ) && function_exists( 'forqy_attributes' ) ) {
								echo '<link ' . forqy_attributes( $link_attributes ) . '>' . "\n";
							}

						}

					}

				}

			}

		}

	}

	if ( function_exists( 'forqy_action_attach' ) ) {
		forqy_action_attach( 'forqy_head_pre', 'forqy_image_preload', 'wp_head', 5 );
	} else {
		add_action( 'wp_head', 'forqy_image_preload', 5 );
	}

}
