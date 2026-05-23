<?php

/**
 * Styles
 */

if ( !function_exists( 'baspa_styles' ) ) {

	function baspa_styles(): void {

		/**
		 * Theme
		 */
		wp_enqueue_style( get_template(), get_theme_file_uri( 'dist/css/style.css' ), array(), wp_get_theme()->get( 'Version' ) );

		/**
		 * Arctic skin
		 */
		$arctic_css_path = get_theme_file_path( 'dist/css/arctic.css' );
		if ( file_exists( $arctic_css_path ) ) {
			wp_enqueue_style(
				get_template() . '-skin',
				get_theme_file_uri( 'dist/css/arctic.css' ),
				array( get_template() ),
				filemtime( $arctic_css_path )
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
