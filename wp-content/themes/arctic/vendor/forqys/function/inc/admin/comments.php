<?php

/**
 * Comments
 *
 * @package     forqys/function
 * @since       1.0.3
 */

// Get theme config
$config = apply_filters( 'forqy_theme', array() );


if ( !function_exists( 'forqy_admin_comments_disable' ) ) {

	/**
	 * Disable Comments in Admin
	 */
	function forqy_admin_comments_disable(): void {

		// Disable comments for post types
		add_action( 'admin_init', function () {
			global $pagenow;

			// Redirect comments requests
			if ( $pagenow === 'edit-comments.php' ) {
				wp_redirect( admin_url() );
				exit;
			}

			// Remove comments support for post types
			foreach ( get_post_types() as $post_type ) {
				if ( post_type_supports( $post_type, 'comments' ) ) {
					remove_post_type_support( $post_type, 'comments' );
					remove_post_type_support( $post_type, 'trackbacks' );
				}
			}
		} );

		// Remove 'Comments' from the admin menu
		add_action( 'admin_menu', function () {
			remove_menu_page( 'edit-comments.php' );
		} );

		// Remove 'Comments' widget from the dashboard
		add_action( 'wp_dashboard_setup', function () {
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		} );

		// Remove 'Comments' from the admin bar menu
		add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'comments' );
		}, 999 );

		// Remove Feed Links from <head>
		remove_action('wp_head', 'feed_links', 2 );
		remove_action('wp_head', 'feed_links_extra', 3 );

	}

	if ( !empty( $config ) && isset( $config[ 'comments' ] ) && !$config[ 'comments' ] ) {
		add_action( 'init', 'forqy_admin_comments_disable' );
	}

}
