<?php

/**
 * Common
 *
 * @package    forqys/function
 * @since      1.0.0
 */

if ( !function_exists( 'forqy_class' ) ) {

	/**
	 * Class
	 *
	 * @param array<string> $class
	 */
	function forqy_class( array $class = array() ): void {

		if ( !empty( $class ) ) {
			echo 'class="' . join( ' ', $class ) . '"';
		}

	}

}

if ( !function_exists( 'forqy_itemprop' ) ) {

	/**
	 * Property for Structured Data
	 *
	 * @param array<string> $property
	 */
	function forqy_itemprop( array $property = array() ): void {

		if ( !empty( $property ) ) {
			echo 'itemprop="' . join( ' ', $property ) . '"';
		}

	}

}

if ( !function_exists( 'forqy_attributes' ) ) {

	/**
	 * HTML Attributes
	 *
	 * @param array<string> $attr
	 */
	function forqy_attributes( array $attr ): string {

		return implode( ' ', array_map( function ( $key ) use ( $attr ) {
			$value = is_object( $attr[ $key ] ) ? (string)$attr[ $key ] : $attr[ $key ];
			$value = is_array( $value ) ? implode( ' ', array_map( function ( $item ) {
				return (string)$item; // cast all second level array value to string.
			}, $value ) ) : (string)$value;

			return sprintf( '%s="%s"', htmlspecialchars( $key ), strip_tags( $value ) );
		}, array_keys( $attr ) ) );

	}

}

if ( !function_exists( 'forqy_title_to_slug' ) ) {

	/**
	 * Convert Title to Slug
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	function forqy_title_to_slug( string $title ): string {

		// Strip HTML and PHP tags
		$title = strip_tags( $title );
		// Convert special characters to html entities
		$title = htmlspecialchars( $title );
		// Replace non-letter or digits with dash
		$title = preg_replace( '~[^\\pL\d]+~u', '-', $title );

		// Make a string lowercase
		return mb_strtolower( $title );

	}

}

if ( !function_exists( 'forqy_mmss_to_ms' ) ) {

	/**
	 * Convert "mm:ss" to "ms"
	 * @url https://stackoverflow.com/a/5334114
	 *
	 * @param $minutes_seconds // mm:ss
	 *
	 * @return float
	 */
	function forqy_mmss_to_ms( string $minutes_seconds ): float {

		list( $minutes, $seconds ) = explode( ':', $minutes_seconds );

		return floatval( $minutes ) * 60000 + floatval( $seconds ) * 1000;

	}

}

if ( !function_exists( 'forqy_hex_to_rgb' ) ) {

	/**
	 * Convert Hex to RGB
	 *
	 * @param string $hex
	 *
	 * @return array<string>|null
	 */
	function forqy_hex_to_rgb( string $hex ): ?array {

		return sscanf( $hex, "#%02x%02x%02x" );

	}

}
