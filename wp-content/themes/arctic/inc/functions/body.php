<?php

/**
 * Body
 */

if ( !function_exists( 'baspa_body_class' ) ) {

	/**
	 * Add Custom Body Classes
	 *
	 * @param array<string> $classes
	 *
	 * @return array<string>
	 */
	function baspa_body_class( array $classes ): array {

		if ( baspa_offers_has_featured() ) {
			$classes[] = 'offers--featured';
		}

		if ( is_page_template( 'template-homepage.php' ) ) {
			$classes[] = 'template--homepage';
		}
		if ( is_page_template( 'template-contact.php' ) ) {
			$classes[] = 'template--contact';
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? parse_url( wp_unslash( (string)$_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
		if ( is_string( $path ) && preg_match( '#^/konfigurator(?:/|$)#', $path ) === 1 ) {
			$classes[] = 'template--jucra-builder';
		}
		if ( is_string( $path ) && preg_match( '#^/poptavka-konfigurace(?:/|$)#', $path ) === 1 ) {
			$classes[] = 'template--jucra-inquiry';
		}

		return $classes;

	}

	add_filter( 'body_class', 'baspa_body_class' );

}
