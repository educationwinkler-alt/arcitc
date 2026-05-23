<?php

/**
 * Image
 *
 * @package    forqy/image
 * @version    1.0.8
 */

/**
 * Includes
 */
require_once( __DIR__ . '/inc/svg.php' );
require_once( __DIR__ . '/inc/lazy.php' );
require_once( __DIR__ . '/inc/srcset.php' );
require_once( __DIR__ . '/inc/preload.php' );


if ( !function_exists( 'forqy_image_placeholder' ) ) {

	/**
	 * Image Placeholder - Base64
	 *
	 * @param int|null $width
	 * @param int|null $height
	 *
	 * @return string
	 */
	function forqy_image_placeholder( ?int $width = 4, ?int $height = 3 ): string {

		return esc_attr( 'data:image/svg+xml,%3Csvg%20width%3D%22' . $width . '%22%20height%3D%22' . $height . '%22%20xmlns%3D%22http://www.w3.org/2000/svg%22%20viewBox%3D%220%200%20' . $width . '%20' . $height . '%22%3E%3C/svg%3E' );

	}

	add_filter( 'forqy_image_placeholder', 'forqy_image_placeholder', 10, 2 );

}

if ( !function_exists( 'forqy_image_loading' ) ) {

	/**
	 * Image Loading
	 *
	 * @return string
	 */
	function forqy_image_loading(): string {

		ob_start();

		get_template_part( 'templates/image/loading' );

		return ob_get_clean();

	}

	add_filter( 'forqy_image_loading', 'forqy_image_loading', 10 );

}

if ( !function_exists( 'forqy_image_get_ratio' ) ) {

	/**
	 * Image Aspect Ratio
	 *
	 * @param int $width
	 * @param int $height
	 * @param string $divider
	 *
	 * @return string
	 */
	function forqy_image_get_ratio( int $width, int $height, string $divider = '-' ): string {

		// search for greatest common divisor
		$greatestCommonDivisor = static function ( $width, $height ) use ( &$greatestCommonDivisor ) {
			return ( $width % $height ) ? $greatestCommonDivisor( $height, $width % $height ) : $height;
		};

		$divisor = $greatestCommonDivisor( $width, $height );

		return esc_attr( $width / $divisor . $divider . $height / $divisor );

	}

}
