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

		return $classes;

	}

	add_filter( 'body_class', 'baspa_body_class' );

}
