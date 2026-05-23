<?php

/**
 * Admin Styles
 */

if ( ! function_exists( 'baspa_admin_styles' ) ) {

	function baspa_admin_styles(): void {

		wp_enqueue_style( get_template(), get_theme_file_uri( 'dist/css/admin.css' ), array(), wp_get_theme()->get( 'Version' ) );

	}

	add_action( 'admin_enqueue_scripts', 'baspa_admin_styles', 50 );

}
