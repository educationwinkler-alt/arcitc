<?php

/**
 * Styles
 */

if ( !function_exists( 'baspa_slides_styles_admin' ) ) {

	/**
	 * Styles
	 *
	 * @return void
	 */
	function baspa_slides_styles_admin(): void {

		if (
			( stristr( $_SERVER[ 'REQUEST_URI' ], 'post-new.php' ) !== false && isset( $_GET[ 'post_type' ] ) && 'slide' === $_GET[ 'post_type' ] ) ||
			( false !== stristr( $_SERVER[ 'REQUEST_URI' ], 'post.php' ) && isset( $_GET[ 'post' ] ) && 'slide' === get_post_type( $_GET[ 'post' ] ) )
		) {
			add_editor_style( 'dist/css/editor/slide.css' );
		}

	}

	add_action( 'after_setup_theme', 'baspa_slides_styles_admin' );

}
