<?php

/**
 * JS
 *
 * @package     forqys/function
 * @since       1.0.4
 */

// Get theme config
$config = apply_filters( 'forqy_theme', array() );

if ( !function_exists( 'forqy_js_html_class' ) ) {

	/**
	 * Change 'no-js' to 'js' Class on <html> if JavaScript Enabled
	 *
	 * @return void
	 */
	function forqy_js_html_class(): void {
		echo '<script fetchpriority="high">document.documentElement.className = document.documentElement.className.replace("no-js","js");</script>' . "\n";
	}

	if ( !empty( $config ) && isset( $config[ 'js_check' ] ) && $config[ 'js_check' ] ) {
		add_action( 'wp_head', 'forqy_js_html_class', 0 );
	}

}
