<?php

/**
 * Photoswipe
 *
 * @package forqys/photoswipe
 * @since   1.0.0
 *
 * To disable the photoswipe of images:
 * add_filter( 'forqy_image_photoswipe', '__return_false' );
 * add_filter( 'forqy_image_photoswipe_content', '__return_false' );
 */

if ( !function_exists( 'forqy_photoswipe_body_class' ) ) {

	/**
	 * Add Body Class
	 *
	 * @param $classes
	 *
	 * @return mixed
	 */
	function forqy_photoswipe_body_class( $classes ) {

		if ( apply_filters( 'forqy_image_photoswipe', '__return_true' ) ) {
			$classes[] = 'image--photoswipe';
		}
		if ( apply_filters( 'forqy_image_photoswipe_content', '__return_true' ) ) {
			$classes[] = 'image-content--photoswipe';
		}

		return $classes;
	}

	add_filter( 'body_class', 'forqy_photoswipe_body_class' );

}

if ( !function_exists( 'forqy_photoswipe_content_link_attributes' ) ) {

	/**
	 * Content Images with a PhotoSwipe Support
	 *
	 * @param $content
	 *
	 * @return string|string[]|null
	 */
	function forqy_photoswipe_content_link_attributes( $content ) {

		if ( !is_admin() && apply_filters( 'forqy_image_photoswipe_content', '__return_true' ) ) {
			return preg_replace_callback( "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png|webp)('|\")(.*?)>/", 'forqy_photoswipe_content_link_attributes__callback', $content );
		} else {
			return $content;
		}

	}

	add_filter( 'the_content', 'forqy_photoswipe_content_link_attributes' );

}

if ( !function_exists( 'forqy_photoswipe_content_link_attributes__callback' ) ) {

	/**
	 * Content Images with a PhotoSwipe Support - Callback
	 *
	 * @param $matches
	 *
	 * @return mixed|string
	 */
	function forqy_photoswipe_content_link_attributes__callback( $matches ) {

		// $matches[0] = original
		// $matches[1] = space
		// $matches[2] = quote
		// $matches[3] = path
		// $matches[4] = extension
		// $matches[5] = quote
		// $matches[6] = attributes

		// check path to image
		if ( empty( $matches[ 3 ] ) ) {
			return $matches[ 0 ];
		}

		// get attachment url
		$url = esc_url( $matches[ 3 ] . '.' . $matches[ 4 ] );

		// get attachment size
		if ( !empty( $url ) ) {

			// get image data
			$attachment_id = attachment_url_to_postid( $url );

			if ( !empty( $attachment_id ) && $attachment_id > 0 ) {
				$attachment_src = wp_get_attachment_image_src( $attachment_id, 'full' );

				$href   = $attachment_src[ 0 ] ?? false;
				$width  = $attachment_src[ 1 ] ?? false;
				$height = $attachment_src[ 2 ] ?? false;
			} else {
				$href = $url;

				if ( is_file( $url ) ) {
					list( $width, $height ) = @getimagesize( $url );
				}
			}
		} else {
			return $matches[ 0 ];
		}

		// check required attributes
		if ( empty( $href ) || empty( $width ) || empty( $height ) ) {
			return $matches[ 0 ];
		}

		// original attributes
		$attributes_o_kses_hair = wp_kses_hair( $matches[ 6 ], wp_allowed_protocols() );
		$attributes_o           = array();

		foreach ( $attributes_o_kses_hair as $name => $attribute ) {
			$attributes_o[ $name ] = $attribute[ 'value' ];
		}

		// attributes
		$attributes = $attributes_o;

		// class
		if ( !empty( $attributes[ 'class' ] ) ) {
			$attributes[ 'class' ] = 'js-image ' . $attributes[ 'class' ];
		} else {
			$attributes[ 'class' ] = 'js-image';
		}

		// new attributes
		$attributes_n = array();

		foreach ( $attributes as $attribute => $value ) {

			if ( '' === $value ) {
				$attributes_n[] = sprintf( '%s', $attribute );
			} else {
				$attributes_n[] = sprintf( '%s="%s"', $attribute, esc_attr( $value ) );
			}
		}

		$attributes_n = implode( ' ', $attributes_n );

		return sprintf( '<a href="%1$s" data-size="%2$sx%3$s" %4$s>', $href, $width, $height, $attributes_n );

	}

}
