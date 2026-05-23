<?php

/**
 * Favicon
 *
 * @package    forqys/function
 * @since      1.0.0
 */

if ( ! function_exists( 'forqy_favicon_url' ) ) {

	/**
	 * Set Default Favicon
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	function forqy_favicon_url( string $url ): string {

		if ( file_exists( get_theme_file_path( 'images/icon.png' ) ) ) {
			$url = get_theme_file_uri( 'images/icon.png' );
		}

		return $url;

	}

	if ( ! has_site_icon() ) {
		add_filter( 'get_site_icon_url', 'forqy_favicon_url', 10, 1 );
	}

}
