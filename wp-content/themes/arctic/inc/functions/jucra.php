<?php

/**
 * Jucra Configurator helpers.
 */

if ( !function_exists( 'arctic_jucra_sanitize_relative_path' ) ) {

	/**
	 * Sanitize relative path used for pricing form links.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	function arctic_jucra_sanitize_relative_path( $value ): string {

		$path = trim( sanitize_text_field( (string)$value ) );

		if ( $path === '' ) {
			return '';
		}

		if ( preg_match( '#^[a-z][a-z0-9+\-.]*://#i', $path ) || strpos( $path, '//' ) === 0 ) {
			return '';
		}

		if ( strpos( $path, '/' ) !== 0 ) {
			$path = '/' . ltrim( $path, '/' );
		}

		return $path;

	}

}

if ( !function_exists( 'arctic_is_root_homepage_request' ) ) {

	/**
	 * True only for canonical homepage URL "/" request.
	 *
	 * @return bool
	 */
	function arctic_is_root_homepage_request(): bool {

		if ( !is_front_page() ) {
			return false;
		}

		$current_request_path = '/';

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$path_from_request = parse_url( wp_unslash( (string)$_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
			if ( is_string( $path_from_request ) && $path_from_request !== '' ) {
				$current_request_path = $path_from_request;
			}
		}

		return untrailingslashit( $current_request_path ) === '';

	}

}

if ( !function_exists( 'arctic_jucra_is_enabled' ) ) {

	/**
	 * Whether Jucra integration is enabled in theme settings.
	 *
	 * @return bool
	 */
	function arctic_jucra_is_enabled(): bool {
		return (bool)get_theme_mod( 'arctic_jucra_enabled', false );
	}

}

if ( !function_exists( 'arctic_jucra_has_shortcode' ) ) {

	/**
	 * Whether Visao shortcode is available.
	 *
	 * @return bool
	 */
	function arctic_jucra_has_shortcode(): bool {
		return function_exists( 'shortcode_exists' ) && shortcode_exists( 'visao_viewer' );
	}

}

if ( !function_exists( 'arctic_jucra_get_default_model' ) ) {

	/**
	 * Resolve default Jucra model from Customizer.
	 *
	 * @return string
	 */
	function arctic_jucra_get_default_model(): string {

		$model = sanitize_text_field( (string)get_theme_mod( 'arctic_jucra_default_model', '' ) );
		$model = str_replace( array( '[', ']', '"', '\'' ), '', $model );

		return trim( $model );

	}

}

if ( !function_exists( 'arctic_jucra_resolve_model_name' ) ) {

	/**
	 * Resolve explicit model name or fall back to global default.
	 *
	 * @param string $model_name
	 *
	 * @return string
	 */
	function arctic_jucra_resolve_model_name( string $model_name = '' ): string {

		$model = sanitize_text_field( $model_name );
		$model = str_replace( array( '[', ']', '"', '\'' ), '', $model );
		$model = trim( $model );

		if ( $model !== '' ) {
			return $model;
		}

		return arctic_jucra_get_default_model();

	}

}

if ( !function_exists( 'arctic_jucra_get_product_model_name' ) ) {

	/**
	 * Resolve product-specific model name used for the viewer.
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	function arctic_jucra_get_product_model_name( int $post_id ): string {

		$model = get_post_meta( $post_id, 'jucra_model_name', true );

		return arctic_jucra_resolve_model_name( (string)$model );

	}

}

if ( !function_exists( 'arctic_jucra_can_render_viewer' ) ) {

	/**
	 * Determine if we can render an inline viewer.
	 *
	 * @param string $model_name
	 *
	 * @return bool
	 */
	function arctic_jucra_can_render_viewer( string $model_name = '' ): bool {

		if ( !arctic_jucra_is_enabled() || !arctic_jucra_has_shortcode() ) {
			return false;
		}

		return arctic_jucra_resolve_model_name( $model_name ) !== '';

	}

}

if ( !function_exists( 'arctic_jucra_build_shortcode' ) ) {

	/**
	 * Build Visao shortcode string for rendering.
	 *
	 * @param string $model_name
	 *
	 * @return string
	 */
	function arctic_jucra_build_shortcode( string $model_name = '' ): string {

		$model = arctic_jucra_resolve_model_name( $model_name );

		if ( $model === '' ) {
			return '';
		}

		return sprintf( '[visao_viewer model_name="%s"]', $model );

	}

}

if ( !function_exists( 'arctic_jucra_get_pricing_relative_path' ) ) {

	/**
	 * Resolve configured pricing path for Jucra CTA.
	 *
	 * @param string $default_path
	 *
	 * @return string
	 */
	function arctic_jucra_get_pricing_relative_path( string $default_path = '/kontakt/' ): string {

		$configured = arctic_jucra_sanitize_relative_path( get_theme_mod( 'arctic_jucra_pricing_relative_url', $default_path ) );
		$fallback   = arctic_jucra_sanitize_relative_path( $default_path );

		return $configured !== '' ? $configured : ( $fallback !== '' ? $fallback : '/kontakt/' );

	}

}

if ( !function_exists( 'arctic_jucra_get_pricing_url' ) ) {

	/**
	 * Build absolute local pricing URL from configured relative path.
	 *
	 * @param string $default_path
	 *
	 * @return string
	 */
	function arctic_jucra_get_pricing_url( string $default_path = '/kontakt/' ): string {

		return home_url( arctic_jucra_get_pricing_relative_path( $default_path ) );

	}

}

if ( !function_exists( 'arctic_jucra_filter_sale_menu_items' ) ) {

	/**
	 * Keep "sale" menu item homepage-only and rename to "Akcni nabidka".
	 *
	 * @param array    $sorted_menu_items
	 * @param stdClass $args
	 *
	 * @return array
	 */
	function arctic_jucra_filter_sale_menu_items( array $sorted_menu_items, stdClass $args ): array {

		if ( is_admin() ) {
			return $sorted_menu_items;
		}

		$is_root_homepage = function_exists( 'arctic_is_root_homepage_request' ) && arctic_is_root_homepage_request();

		foreach ( $sorted_menu_items as $index => $item ) {
			$title = isset( $item->title ) ? wp_strip_all_tags( (string)$item->title ) : '';
			$url   = isset( $item->url ) ? (string)$item->url : '';

			$is_sale_item = strpos( strtolower( remove_accents( $title ) ), 'vyprodej' ) !== false || stripos( $url, '#vyprodej' ) !== false;
			if ( !$is_sale_item ) {
				continue;
			}

			if ( $is_root_homepage ) {
				$item->title               = 'Akcni nabidka';
				$sorted_menu_items[$index] = $item;
			} else {
				unset( $sorted_menu_items[$index] );
			}
		}

		return array_values( $sorted_menu_items );

	}

	add_filter( 'wp_nav_menu_objects', 'arctic_jucra_filter_sale_menu_items', 20, 2 );

}
