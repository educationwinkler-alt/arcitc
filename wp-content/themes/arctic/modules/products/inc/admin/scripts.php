<?php

/**
 * Admin Scripts
 */

if ( !function_exists( 'baspa_products_admin_scripts' ) ) {

	/**
	 * Scripts
	 *
	 * @return void
	 */
	function baspa_products_admin_scripts(): void {

		if ( !did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}

		// Register
		wp_register_script( get_template() . '-category-upload', get_theme_file_uri( 'modules/products/js/admin/category.js' ), array(
			'jquery',
		), '1.0.2', true );

		// Localize
		wp_localize_script( get_template() . '-category-upload', 'parameter', array(
			'image_add'           => esc_html_x( 'Add Image', 'admin', 'baspa' ),
			'image_change'        => esc_html_x( 'Change Image', 'admin', 'baspa' ),
			'video_add'           => esc_html_x( 'Add Video', 'admin', 'baspa' ),
			'video_change'        => esc_html_x( 'Change Video', 'admin', 'baspa' ),
			'modal_window_title'  => esc_html_x( 'Insert Image', 'admin', 'baspa' ),
			'modal_window_button' => esc_html_x( 'Use Image', 'admin', 'baspa' ),
			'video_modal_title'   => esc_html_x( 'Insert Video', 'admin', 'baspa' ),
			'video_modal_button'  => esc_html_x( 'Use Video', 'admin', 'baspa' ),
		) );

		// Enqueue
		wp_enqueue_script( get_template() . '-category-upload' );

	}

	add_action( 'admin_enqueue_scripts', 'baspa_products_admin_scripts' );

}
