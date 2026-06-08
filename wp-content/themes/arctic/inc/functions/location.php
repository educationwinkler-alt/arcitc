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

if ( !function_exists( 'arctic_get_default_map_embed_url' ) ) {

	/**
	 * Canonical Google Maps iframe URL for the showroom map background.
	 *
	 * @return string
	 */
	function arctic_get_default_map_embed_url(): string {
		return 'https://www.google.com/maps?q=49.149%2C16.589&ll=49.149%2C16.407&z=11&output=embed';
	}

}

if ( !function_exists( 'arctic_sanitize_map_embed' ) ) {

	/**
	 * Accept a Google Maps embed URL or pasted iframe and store only a safe src.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	function arctic_sanitize_map_embed( string $value ): string {
		$value = trim( $value );

		if ( preg_match( '/<iframe[^>]+src=[\'"]([^\'"]+)[\'"]/i', $value, $matches ) ) {
			$value = html_entity_decode( $matches[1], ENT_QUOTES, get_bloginfo( 'charset' ) );
		}

		$value = trim( wp_strip_all_tags( $value ) );
		$value = esc_url_raw( $value, array( 'https' ) );

		if ( '' === $value ) {
			return '';
		}

		$host = strtolower( (string) wp_parse_url( $value, PHP_URL_HOST ) );
		$path = (string) wp_parse_url( $value, PHP_URL_PATH );

		$is_google_host = (bool) preg_match( '/(^|\.)google\./', $host ) || 0 === strpos( $host, 'maps.google.' );
		$is_maps_path   = 0 === strpos( $path, '/maps' ) || 0 === strpos( $host, 'maps.google.' );

		if ( !$is_google_host || !$is_maps_path ) {
			return '';
		}

		return $value;
	}

}

if ( !function_exists( 'arctic_get_map_embed_url' ) ) {

	/**
	 * Resolve the admin-editable Google Maps iframe URL.
	 *
	 * @return string
	 */
	function arctic_get_map_embed_url(): string {
		$map_embed = arctic_sanitize_map_embed( (string) get_theme_mod( 'arctic_map_embed', arctic_get_default_map_embed_url() ) );

		return '' !== $map_embed ? $map_embed : arctic_get_default_map_embed_url();
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
