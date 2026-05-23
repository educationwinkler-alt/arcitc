<?php

/**
 * Search - Disable
 *
 * @package     forqys/search
 * @since       1.0.3
 */

// Get theme config
$config = apply_filters( 'forqy_theme', array() );


if ( !empty( $config ) && isset( $config[ 'search' ] ) && !$config[ 'search' ] ) {
	add_action( 'template_redirect', 'forqy_search_disable_query' );
	add_action( 'wp_enqueue_scripts', 'forqy_search_disable_scripts', 99 );
	add_action( 'widgets_init', 'forqy_search_disable_widget' );
	add_action( 'admin_bar_menu', 'forqy_search_disable_admin_bar', 999 );
	add_action( 'init', 'forqy_search_disable_shortcode' );
}


if ( !function_exists( 'forqy_search_disable_query' ) ) {

	/**
	 * Disable Search Query
	 *
	 * @param $query
	 * @param bool $error
	 *
	 * @return void
	 */
	function forqy_search_disable_query( $query, bool $error = true ) {
		if ( is_search() ) {
			wp_redirect( home_url( '/' ) );
			exit();
		}
	}
}

if ( !function_exists( 'forqy_search_disable_scripts' ) ) {

	function forqy_search_disable_scripts() {
		wp_dequeue_script( get_template() . '-search' );
	}
}

if ( !function_exists( 'forqy_search_disable_widget' ) ) {

	function forqy_search_disable_widget() {
		unregister_widget( 'WP_Widget_Search' );
	}
}

if ( !function_exists( 'forqy_search_disable_admin_bar' ) ) {

	function forqy_search_disable_admin_bar( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'search' );
	}
}

if ( !function_exists( 'forqy_search_disable_shortcode' ) ) {

	function forqy_search_disable_shortcode() {
		remove_shortcode( 'wpsearch' );
	}
}
