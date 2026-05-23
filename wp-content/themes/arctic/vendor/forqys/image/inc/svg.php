<?php

/**
 * Image SVG
 *
 * @package forqy/image
 * @since   1.0.0
 */

/*
FILTERS

Disable adding svg image dimensions
add_filter( 'forqy_image_svg_dimensions', '__return_false' );
*/

if ( !function_exists( 'forqy_image_svg_add_dimensions_content' ) ) {

	/**
	 * Add Width & Height Attributes to Content SVG Images
	 *
	 * @param $content
	 *
	 * @return array|string|string[]|null
	 */
	function forqy_image_svg_add_dimensions_content( $content ) {

		if ( apply_filters( 'forqy_image_svg_dimensions', true ) ) {

			$pattern = '/<img[^>]*src=["\']([^"\']+\.svg)["\'][^>]*>/i'; // Regular expression to find all SVG images

			// add width and height attributes
			$content = preg_replace_callback( $pattern, function ( $matches ) {
				$img_tag = $matches[ 0 ] ?? '';

				if ( isset( $matches[ 1 ] ) ) {
					$svg_url = $matches[ 1 ];

					// check if width and height attributes are already present
					if ( strpos( $img_tag, 'width=' ) === false || strpos( $img_tag, 'height=' ) === false ) {
						$response = wp_remote_get( $svg_url );

						if ( !is_wp_error( $response ) ) {
							$body = wp_remote_retrieve_body( $response );

							if ( $body ) {
								$xml = @simplexml_load_string( $body );

								if ( $xml !== false ) {
									$attributes = $xml->attributes();
									if ( $attributes !== false ) {
										$width  = (string)$attributes[ 'width' ];
										$height = (string)$attributes[ 'height' ];

										if ( $width && $height ) {
											$img_tag = str_replace( '<img', '<img width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '"', $img_tag );
										}
									}
								}
							}
						}
					}
				}

				return $img_tag;

			}, $content );

		}

		return $content;

	}

	add_filter( 'the_content', 'forqy_image_svg_add_dimensions_content' );
	// callback for image blocks
	add_filter( 'render_block', function ( $block_content, $block ) {
		if ( $block[ 'blockName' ] === 'core/image' || $block[ 'blockName' ] === 'core/cover' || $block[ 'blockName' ] === 'core/media-text' ) {
			return forqy_image_svg_add_dimensions_content( $block_content );
		} else {
			return $block_content;
		}
	}, 10, 2 );

}

if ( !function_exists( 'forqy_image_svg_add_dimensions_attachment' ) ) {

	/**
	 * Add Width & Height Attributes to SVG Images Loaded Using 'wp_get_attachment_image_src'
	 *
	 * @url https://www.lee-harris.co.uk/blog/wordpress-get-svg-image-dimensions/
	 *
	 * @param $image
	 * @param $attachment_id
	 * @param $size
	 * @param $icon
	 *
	 * @return mixed
	 */
	function forqy_image_svg_add_dimensions_attachment( $image, $attachment_id, $size, $icon ) {

		if ( apply_filters( 'forqy_image_svg_dimensions', true ) ) {

			if ( is_array( $image ) && preg_match( '/\.svg$/i', $image[ 0 ] ) && $image[ 1 ] <= 1 ) {
				if ( is_array( $size ) ) {
					$image[ 1 ] = $size[ 0 ];
					$image[ 2 ] = $size[ 1 ];
				} elseif ( ( $xml = simplexml_load_file( $image[ 0 ] ) ) !== false ) {
					$attributes = $xml->attributes();
					$viewBox    = explode( ' ', $attributes->viewBox );
					$image[ 1 ] = isset( $attributes->width ) && preg_match( '/\d+/', $attributes->width, $value ) ? (int)$value[ 0 ] : ( count( $viewBox ) == 4 ? (int)$viewBox[ 2 ] : null );
					$image[ 2 ] = isset( $attributes->height ) && preg_match( '/\d+/', $attributes->height, $value ) ? (int)$value[ 0 ] : ( count( $viewBox ) == 4 ? (int)$viewBox[ 3 ] : null );
				} else {
					$image[ 1 ] = $image[ 2 ] = null;
				}
			}

		}

		return $image;

	}

	add_filter( 'wp_get_attachment_image_src', 'forqy_image_svg_add_dimensions_attachment', 10, 4 );

}

if ( !function_exists( 'forqy_image_svg_add_dimensions_attachment_metadata' ) ) {

	/**
	 * Update SVG Attachment Metadata with Width & Height Attributes
	 *
	 * @url https://wordpress.stackexchange.com/a/256701
	 *
	 * @param $data
	 * @param $id
	 *
	 * @return mixed
	 */
	function forqy_image_svg_add_dimensions_attachment_metadata( $data, $id ) {

		$attachment = get_post( $id );
		$mime_type  = $attachment->post_mime_type;

		if ( $mime_type == 'image/svg+xml' ) {

			if ( empty( $data ) || empty( $data[ 'width' ] ) || empty( $data[ 'height' ] ) ) {
				$xml              = simplexml_load_file( wp_get_attachment_url( $id ) );
				$attributes       = $xml->attributes();
				$viewBox          = explode( ' ', $attributes->viewBox );
				$data[ 'width' ]  = isset( $attributes->width ) && preg_match( '/\d+/', $attributes->width, $value ) ? (int)$value[ 0 ] : ( count( $viewBox ) == 4 ? (int)$viewBox[ 2 ] : null );
				$data[ 'height' ] = isset( $attributes->height ) && preg_match( '/\d+/', $attributes->height, $value ) ? (int)$value[ 0 ] : ( count( $viewBox ) == 4 ? (int)$viewBox[ 3 ] : null );
			}
		}

		return $data;

	}

	add_filter( 'wp_update_attachment_metadata', 'forqy_image_svg_add_dimensions_attachment_metadata', 10, 2 );

}
