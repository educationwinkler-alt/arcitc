<?php

/**
 * Scripts
 *
 * @package forqys/form
 * @since   1.0.4
 */

if ( !function_exists( 'forqy_form_scripts' ) ) {

	/**
	 * Enqueue Scripts
	 */
	function forqy_form_scripts(): void {

		// Register
		wp_enqueue_script(
			get_template() . '-form',
			str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/dist/js/form.js' ),
			array(),
			'1.1.2',
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			),
		);

	}

	add_action( 'wp_enqueue_scripts', 'forqy_form_scripts', 10 );

}

if ( !function_exists( 'forqy_form_pre' ) ) {

	/**
	 * Prefetch & Preconnect reCAPTCHA in <head>
	 *
	 * @url https://developers.google.com/recaptcha/docs/loading#using_resource_hints
	 */
	function forqy_form_pre() {
		// Get keys
		$keys = function_exists( 'forqy_form_recaptcha_get_keys' ) ? forqy_form_recaptcha_get_keys() : array();
		if ( !empty( $keys[ 'key_site' ] ) ) { ?>
			<link rel="dns-prefetch" href="//www.google.com">
			<link rel="dns-prefetch" href="//www.gstatic.com">
			<link rel="preconnect" href="https://www.google.com">
			<link rel="preconnect" href="https://www.gstatic.com" crossorigin="use-credentials">
		<?php }
	}

	if ( function_exists( 'forqy_action_attach' ) ) {
		forqy_action_attach( 'forqy_head_pre', 'forqy_form_pre', 'wp_head', 15 );
	} else {
		add_action( 'wp_head', 'forqy_form_pre', 3 );
	}

}
