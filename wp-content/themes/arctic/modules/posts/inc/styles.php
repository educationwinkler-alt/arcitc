<?php

/**
 * Styles
 */

if ( !function_exists( 'baspa_posts_styles_editor' ) ) {

	/**
	 * Styles
	 *
	 * @return void
	 */
	function baspa_posts_styles_editor(): void {

		if ( isset( $_GET[ 'post_type' ] ) || isset( $_GET[ 'post' ] ) ) {
			if ( false !== stristr( $_SERVER[ 'REQUEST_URI' ], 'post-new.php' ) && 'post' === $_GET[ 'post_type' ]
				|| false !== stristr( $_SERVER[ 'REQUEST_URI' ], 'post.php' ) && 'post' === get_post_type( $_GET[ 'post' ] ) ) {
				add_editor_style( 'dist/css/editor/post.css' );
			}
		}

	}

	add_action( 'after_setup_theme', 'baspa_posts_styles_editor' );

}
