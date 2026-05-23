<?php

/**
 * Scripts
 *
 * @package     forqys/search
 * @since       1.0.0
 */

if ( ! function_exists( 'forqy_search_scripts' ) ) {

	function forqy_search_scripts(): void {

		/**
		 * Search
		 */
		wp_enqueue_script( get_template() . '-search', str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/js/search.js' ), array(), '1.0.0', true );

	}

	add_action( 'wp_enqueue_scripts', 'forqy_search_scripts', 30 );

}
