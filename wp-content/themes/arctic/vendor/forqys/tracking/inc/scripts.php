<?php

/**
 * Scripts
 *
 * @package forqys/tracking
 * @since   1.0.6
 */

if ( !function_exists( 'forqy_tracking_pre' ) ) {

	/**
	 * Prefetch & Preconnect Google Tag Manager || Google Analytics 4 in <head>
	 */
	function forqy_tracking_pre() {

		$config = apply_filters( 'forqy_theme', array() ); // Theme config

		if ( !empty( $config[ 'gtm_id' ] ) || !empty( $config[ 'ga4_id' ] ) ) { ?>
			<link rel="dns-prefetch" href="//www.googletagmanager.com">
			<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
		<?php }
	}

	if ( function_exists( 'forqy_action_attach' ) ) {
		forqy_action_attach( 'forqy_head_pre', 'forqy_tracking_pre', 'wp_head', 10 );
	} else {
		add_action( 'wp_head', 'forqy_tracking_pre', 2 );
	}

}
