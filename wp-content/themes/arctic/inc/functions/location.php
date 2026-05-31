<?php

/**
 * Shared location helpers.
 */

if ( !function_exists( 'arctic_get_default_map_url' ) ) {

	/**
	 * Canonical Google Maps link for BASPA/Arctic showroom.
	 *
	 * @return string
	 */
	function arctic_get_default_map_url(): string {
		return 'https://maps.app.goo.gl/ZsYfoZ2aQGF1JnZG6';
	}

}

if ( !function_exists( 'arctic_get_map_url' ) ) {

	/**
	 * Resolve the public map URL. Empty Customizer values fall back to the
	 * canonical Google Maps URL so map CTAs never silently point internally.
	 *
	 * @return string
	 */
	function arctic_get_map_url(): string {
		$map_url = trim( (string) get_theme_mod( 'baspa_map', arctic_get_default_map_url() ) );

		return $map_url !== '' ? esc_url_raw( $map_url ) : arctic_get_default_map_url();
	}

}
