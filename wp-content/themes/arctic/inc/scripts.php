<?php

/**
 * Scripts
 */

if ( !function_exists( 'baspa_scripts' ) ) {

	/**
	 * Enqueue Scripts
	 *
	 * @return void
	 */
	function baspa_scripts(): void {

		/**
		 * Theme
		 */
		wp_enqueue_script( get_template(), get_theme_file_uri( 'dist/js/theme.js' ), array(), '1.0.0', true );

		/**
		 * Local section navigation handoff
		 */
		$section_nav_handoff_path = get_theme_file_path( 'dist/js/section-nav-handoff.js' );
		if ( file_exists( $section_nav_handoff_path ) ) {
			wp_enqueue_script(
				get_template() . '-section-nav-handoff',
				get_theme_file_uri( 'dist/js/section-nav-handoff.js' ),
				array( get_template() ),
				filemtime( $section_nav_handoff_path ),
				true
			);
		}

		/**
		 * About team Figma carousel
		 */
		$about_team_carousel_path = get_theme_file_path( 'dist/js/about-team-carousel.js' );
		if ( file_exists( $about_team_carousel_path ) ) {
			wp_enqueue_script(
				get_template() . '-about-team-carousel',
				get_theme_file_uri( 'dist/js/about-team-carousel.js' ),
				array( get_template() ),
				filemtime( $about_team_carousel_path ),
				true
			);
		}

		/**
		 * Arctic desktop mega menu hover stability
		 */
		$mega_hover_script_path = get_theme_file_path( 'dist/js/mega-hover-stability.js' );
		if ( file_exists( $mega_hover_script_path ) ) {
			wp_enqueue_script(
				get_template() . '-mega-hover',
				get_theme_file_uri( 'dist/js/mega-hover-stability.js' ),
				array( get_template() ),
				filemtime( $mega_hover_script_path ),
				true
			);
		}

		/**
		 * Support/download interactions + anchor integrity helpers
		 */
		$support_interactions_path = get_theme_file_path( 'dist/js/support-download-interactions.js' );
		if ( file_exists( $support_interactions_path ) ) {
			wp_enqueue_script(
				get_template() . '-support-download-interactions',
				get_theme_file_uri( 'dist/js/support-download-interactions.js' ),
				array( get_template() ),
				filemtime( $support_interactions_path ),
				true
			);
		}

		/**
		 * Carousel
		 */
		// Swiper
		if ( !wp_script_is( 'swiper', 'enqueued' ) ) {
			wp_enqueue_script( 'swiper', get_theme_file_uri( 'dist/js/plugin/swiper-bundle.js' ), array(), '11.2.6', true );
		}

		// Register
		wp_register_script( get_template() . '-carousel', get_theme_file_uri( 'dist/js/carousel.js' ), array(
			'swiper'
		), '1.0.0', true );

		// Localize
		wp_localize_script( get_template() . '-carousel', 'parameter', array(
			'slideshow_effect' => esc_js( 'slide' ),
			'slideshow_loop'   => esc_js( 'true' ),
			'slideshow_speed'  => esc_js( '600' ),

			'carousel_effect' => esc_js( 'slide' ),
			'carousel_loop'   => esc_js( 'true' ),
			'carousel_speed'  => esc_js( '400' ),

			'prevSlideMessage'        => esc_js( _x( 'Previous slide', 'slideshow accessibility', 'baspa' ) ),
			'nextSlideMessage'        => esc_js( _x( 'Next slide', 'slideshow accessibility', 'baspa' ) ),
			'firstSlideMessage'       => esc_js( _x( 'First slide', 'slideshow accessibility', 'baspa' ) ),
			'lastSlideMessage'        => esc_js( _x( 'Last slide', 'slideshow accessibility', 'baspa' ) ),
			'paginationBulletMessage' => esc_js( _x( 'Go to slide {{index}}', 'slideshow accessibility', 'baspa' ) ),
		) );

		// Enqueue
		wp_enqueue_script( get_template() . '-carousel' );

		$homepage_mobile_slider_path = get_theme_file_path( 'dist/js/homepage-mobile-slider.js' );
		if ( is_front_page() && file_exists( $homepage_mobile_slider_path ) ) {
			wp_enqueue_script(
				get_template() . '-homepage-mobile-slider',
				get_theme_file_uri( 'dist/js/homepage-mobile-slider.js' ),
				array( get_template() . '-carousel' ),
				filemtime( $homepage_mobile_slider_path ),
				true
			);
		}

	}

	add_action( 'wp_enqueue_scripts', 'baspa_scripts', 20 );

}

if ( !function_exists( 'baspa_scripts_remove' ) ) {

	/**
	 * Remove Scripts
	 *
	 * @param WP_Scripts $scripts
	 *
	 * @return void
	 */
	function baspa_scripts_remove( WP_Scripts $scripts ): void {

		if ( !is_admin() && isset( $scripts->registered[ 'jquery' ] ) ) {

			$script = $scripts->registered[ 'jquery' ];

			if ( $script->deps ) {
				// Remove jQuery Migrate
				$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
			}
		}
	}

	add_action( 'wp_default_scripts', 'baspa_scripts_remove' );

}
