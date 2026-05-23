<?php

/**
 * Images
 */

if ( !function_exists( 'baspa_images' ) ) {

	/**
	 * Image Sizes
	 *
	 * @return void
	 */
	function baspa_images(): void {

		// Avatar
		add_image_size( get_template() . '-avatar', 240, 240, true );

		// Logo
		add_image_size( get_template() . '-logo', 360, 180 );

		// Category
		add_image_size( get_template() . '-category', 960, 640, true );

		// Product
		add_image_size( get_template() . '-product', 420, 280 );

		// Small
		add_image_size( get_template() . '-small', 420, 280, true );
		add_image_size( get_template() . '-small-square', 420, 420, true );

		// Medium
		add_image_size( get_template() . '-medium', 720, 480, true );

		// Large
		add_image_size( get_template() . '-large', 1080, 720, true );

		// Huge
		add_image_size( get_template() . '-huge', 1920, 1280, true );

	}

	add_action( 'after_setup_theme', 'baspa_images', 5 );

}
