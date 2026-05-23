<?php

/**
 * Emojis
 *
 * @package    	forqys/function
 * @since      	1.0.0
 */

if ( !function_exists( 'forqy_emojis' ) ) {

	/**
	 * Emojis
	 *
	 * @return void
	 */
	function forqy_emojis() {

		$config = array_replace( array(
			'emojis' => true,
		), apply_filters( 'forqy_theme', array() ) );

		if ( isset( $config[ 'emojis' ] ) && !$config[ 'emojis' ] ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'tiny_mce_plugins', 'forqy_emojis_tinymce' );
			add_filter( 'wp_resource_hints', 'forqy_emojis_dns_prefetch', 10, 2 );
		}
	}

	add_action( 'init', 'forqy_emojis' );

}

if ( !function_exists( 'forqy_emojis_tinymce' ) ) {

	/**
	 * Emojis TinyMCE
	 *
	 * @param array $plugins
	 *
	 * @return array
	 */
	function forqy_emojis_tinymce( array $plugins ): array {
		if ( $plugins ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
}

if ( !function_exists( 'forqy_emojis_dns_prefetch' ) ) {
	/**
	 * Emojis DNS Prefetch
	 *
	 * @param array $urls
	 * @param string $relation_type
	 *
	 * @return array
	 */
	function forqy_emojis_dns_prefetch( array $urls, string $relation_type ): array {

		if ( 'dns-prefetch' == $relation_type ) {
			// Filter documented in wp-includes/formatting.php
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
			$urls          = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}

}
