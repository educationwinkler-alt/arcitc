<?php

/**
 * Styles
 */

if ( !function_exists( 'baspa_styles' ) ) {

	function baspa_styles(): void {

		/**
		 * Theme
		 */
		$is_local       = function_exists( 'wp_get_environment_type' ) && 'local' === wp_get_environment_type();
		$theme_css_path = get_theme_file_path( 'dist/css/style.css' );
		$theme_css_ver  = file_exists( $theme_css_path ) ? filemtime( $theme_css_path ) : wp_get_theme()->get( 'Version' );
		$theme_css_ver  = $is_local ? $theme_css_ver . '-' . time() : $theme_css_ver;
		wp_enqueue_style(
			get_template(),
			get_theme_file_uri( 'dist/css/style.css' ),
			array(),
			$theme_css_ver
		);

		/**
		 * Arctic skin
		 */
		$arctic_css_path = get_theme_file_path( 'dist/css/arctic.css' );
		if ( file_exists( $arctic_css_path ) ) {
			$arctic_css_ver = filemtime( $arctic_css_path );
			$arctic_css_ver = $is_local ? $arctic_css_ver . '-' . time() : $arctic_css_ver;
			wp_enqueue_style(
				get_template() . '-skin',
				get_theme_file_uri( 'dist/css/arctic.css' ),
				array( get_template() ),
				$arctic_css_ver
			);
		}

		$content_overflow_css_path = get_theme_file_path( 'dist/css/content-overflow-guard.css' );
		if ( file_exists( $content_overflow_css_path ) ) {
			$content_overflow_css_ver = filemtime( $content_overflow_css_path );
			$content_overflow_css_ver = $is_local ? $content_overflow_css_ver . '-' . time() : $content_overflow_css_ver;
			wp_enqueue_style(
				get_template() . '-content-overflow-guard',
				get_theme_file_uri( 'dist/css/content-overflow-guard.css' ),
				array( get_template() . '-skin' ),
				$content_overflow_css_ver
			);
		}

		$catalog_request_css_path = get_theme_file_path( 'dist/css/catalog-request.css' );
		if ( $is_local && file_exists( $catalog_request_css_path ) ) {
			$catalog_request_css_ver = filemtime( $catalog_request_css_path );
			$catalog_request_css_ver = $is_local ? $catalog_request_css_ver . '-' . time() : $catalog_request_css_ver;
			wp_enqueue_style(
				get_template() . '-catalog-request',
				get_theme_file_uri( 'dist/css/catalog-request.css' ),
				array( get_template() . '-skin' ),
				$catalog_request_css_ver
			);
		}

		$product_colors_css_path = get_theme_file_path( 'dist/css/product-colors-mobile.css' );
		if ( is_singular( 'product' ) && file_exists( $product_colors_css_path ) ) {
			$product_colors_css_ver = filemtime( $product_colors_css_path );
			$product_colors_css_ver = $is_local ? $product_colors_css_ver . '-' . time() : $product_colors_css_ver;
			wp_enqueue_style(
				get_template() . '-product-colors-mobile',
				get_theme_file_uri( 'dist/css/product-colors-mobile.css' ),
				array( get_template() . '-skin' ),
				$product_colors_css_ver
			);
		}

		$homepage_mobile_slider_css_path = get_theme_file_path( 'dist/css/homepage-mobile-slider.css' );
		if ( is_front_page() && file_exists( $homepage_mobile_slider_css_path ) ) {
			$homepage_mobile_slider_css_ver = filemtime( $homepage_mobile_slider_css_path );
			$homepage_mobile_slider_css_ver = $is_local ? $homepage_mobile_slider_css_ver . '-' . time() : $homepage_mobile_slider_css_ver;
			wp_enqueue_style(
				get_template() . '-homepage-mobile-slider',
				get_theme_file_uri( 'dist/css/homepage-mobile-slider.css' ),
				array( get_template() . '-skin' ),
				$homepage_mobile_slider_css_ver
			);
		}

		/**
		 * Admin Bar
		 */
		if ( is_admin_bar_showing() ) {

			$admin_bar_css = "
				#wpadminbar { z-index: 90 !important; }
				@media (max-width: 600px) { #wpadminbar { position: fixed !important; } }
			";

			wp_add_inline_style( get_template(), $admin_bar_css );

		}

		/**
		 * Comments
		 */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

	}

	add_action( 'wp_enqueue_scripts', 'baspa_styles', 20 );

}

if ( !function_exists( 'arctic_dequeue_external_frontend_fonts' ) ) {

	function arctic_dequeue_external_frontend_fonts(): void {
		wp_dequeue_style( 'visao-css-poppins-font' );
		wp_deregister_style( 'visao-css-poppins-font' );
	}

	add_action( 'wp_enqueue_scripts', 'arctic_dequeue_external_frontend_fonts', 999 );

}
