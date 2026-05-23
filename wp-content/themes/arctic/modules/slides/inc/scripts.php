<?php


if ( !function_exists( 'baspa_slides_scripts' ) ) {

	/**
	 * Enqueue Scripts
	 *
	 * @return void
	 */
	function baspa_slides_scripts(): void {

		if ( is_page_template( 'template-homepage.php' ) ) {

			// Swiper
			if ( !wp_script_is( 'swiper', 'enqueued' ) ) {
				wp_enqueue_script( 'swiper', get_theme_file_uri( 'dist/js/plugin/swiper-bundle.js' ), array(), '11.2.4', true );
			}

			// Register
			wp_register_script( get_template() . '-carousel', get_theme_file_uri( 'dist/js/carousel.js' ), array(
				'swiper',
			), '1.0.0', true );

			// Localize
			wp_localize_script( get_template() . '-carousel', 'parameter', array(
				'slideshow_effect' => esc_js( 'slide' ),
				'slideshow_loop'   => esc_js( 'true' ),
				'slideshow_speed'  => esc_js( '600' ),

				'carousel_effect' => esc_js( 'slide' ),
				'carousel_loop'   => esc_js( 'false' ),
				'carousel_speed'  => esc_js( '400' ),

				'prevSlideMessage'        => esc_js( _x( 'Previous slide', 'slideshow accessibility', 'baspa' ) ),
				'nextSlideMessage'        => esc_js( _x( 'Next slide', 'slideshow accessibility', 'baspa' ) ),
				'firstSlideMessage'       => esc_js( _x( 'First slide', 'slideshow accessibility', 'baspa' ) ),
				'lastSlideMessage'        => esc_js( _x( 'Last slide', 'slideshow accessibility', 'baspa' ) ),
				'paginationBulletMessage' => esc_js( _x( 'Go to slide {{index}}', 'slideshow accessibility', 'baspa' ) ),
			) );

			// Enqueue
			wp_enqueue_script( get_template() . '-carousel' );

		}

	}

	add_action( 'wp_enqueue_scripts', 'baspa_slides_scripts', 30 );

}
