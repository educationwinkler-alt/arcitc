<?php

/**
 * ReCAPTCHA
 *
 * @package forqys/form
 * @since   1.0.0
 */

/*

DEBUG
-----
add_filter( 'forqy_form_recaptcha_debug', '__return_true' );

*/

if ( !function_exists( 'forqy_form_recaptcha_get_keys' ) ) {

	/**
	 * Get reCAPTCHA Keys
	 *
	 * @return array
	 */
	function forqy_form_recaptcha_get_keys(): array {

		$config         = apply_filters( 'forqy_theme', array() ); // Theme config
		$recaptcha_keys = apply_filters( 'forqy_form_recaptcha_keys', array() ); // reCAPTCHA keys config

		return array(
			'key_site'   => $config[ 'recaptcha_key_site' ] ?? $recaptcha_keys[ 'key_site' ] ?? '',
			'key_secret' => $config[ 'recaptcha_key_secret' ] ?? $recaptcha_keys[ 'key_secret' ] ?? '',
		);

	}

}

if ( !function_exists( 'forqy_form_recaptcha_keys_are_set' ) ) {

	/**
	 * Check if reCAPTCHA Keys are Set
	 *
	 * @return bool
	 */
	function forqy_form_recaptcha_keys_are_set(): bool {

		// Get keys
		$keys = function_exists( 'forqy_form_recaptcha_get_keys' ) ? forqy_form_recaptcha_get_keys() : array();

		if ( !empty( $keys[ 'key_site' ] ) && !empty( $keys[ 'key_secret' ] ) ) {
			return true;
		} else {
			return false;
		}

	}

}

if ( !function_exists( 'forqy_form_recaptcha_input' ) ) {

	/**
	 * Add reCAPTCHA Inputs to the Form
	 */
	function forqy_form_recaptcha_input() {

		$keys  = function_exists( 'forqy_form_recaptcha_get_keys' ) ? forqy_form_recaptcha_get_keys() : array();
		$debug = apply_filters( 'forqy_form_recaptcha_debug', false );

		// Add a reCAPTCHA input for generated token as value
		if ( function_exists( 'forqy_form_recaptcha_keys_are_set' ) && forqy_form_recaptcha_keys_are_set() ) {
			echo '<input type="hidden" name="recaptcha" class="f-recaptcha js-recaptcha">';
		}
		// Add a reCAPTCHA input for the site key
		if ( !empty( $keys[ 'key_site' ] ) ) {
			echo '<input type="hidden" name="recaptcha_k" class="f-recaptcha__k js-recaptcha__k" value="' . esc_attr( $keys[ 'key_site' ] ) . '">';
		}
		if ( $debug ) {
			echo '<input type="hidden" name="recaptcha_debug" class="f-recaptcha__debug js-recaptcha__debug">';
		}

	}

	add_action( 'forqy_form_submit_before', 'forqy_form_recaptcha_input', 5 );

}

if ( !function_exists( 'forqy_form_recaptcha_is_valid' ) ) {

	/**
	 * Check If ReCAPTCHA is Valid
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	function forqy_form_recaptcha_is_valid( $input ): bool {

		// Config
		$config = apply_filters( 'forqy_theme', array() ); // Theme config
		$score  = $config[ 'recaptcha_score' ] ?? 0.5;

		// Get a response
		$response = forqy_form_recaptcha_get_response( $input );

		// Return if no response
		if ( empty( $response ) ) {
			return false;
		}

		/**
		 * Check the Response
		 */
		if ( $response[ 'success' ] && $response[ 'score' ] >= $score ) {
			return true;
		} else {
			return false;
		}

	}

}

if ( !function_exists( 'forqy_form_recaptcha_get_response' ) ) {

	/**
	 * Get a ReCAPTCHA Response
	 *
	 * @param $input
	 *
	 * @return array
	 */
	function forqy_form_recaptcha_get_response( $input ): array {

		// Get keys
		$keys = forqy_form_recaptcha_get_keys();

		$response = array();

		// Get a response
		if ( !empty( $keys[ 'key_secret' ] ) ) {
			$response = forqy_form_recaptcha_verify( $keys[ 'key_secret' ], $input );
		}

		return $response;

	}

}

if ( !function_exists( 'forqy_form_recaptcha_verify' ) ) {

	/**
	 * Verify reCAPTCHA
	 *
	 * @param $key
	 * @param $input
	 *
	 * @return array
	 */
	function forqy_form_recaptcha_verify( $key, $input ): array {

		$response_body = array();

		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' && isset( $input ) ) {

			// Get a reCAPTCHA response
//			$response = (array)wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $key . '&response=' . $input );
			// Get a response
			$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
				'body' => array(
					'secret'   => $key,
					'response' => $input,
					'remoteip' => $_SERVER[ 'REMOTE_ADDR' ],
				)
			) );

			// Cache variables for the debugging
			wp_cache_set( 'forqy_form_recaptcha_response', $response );

			$response_body = isset( $response[ 'body' ] ) ? json_decode( wp_remote_retrieve_body( $response ), true ) : array(
				'success'     => false,
				'error-codes' => array(
					'general-fail',
				)
			);

		}

		return $response_body;

	}

}

if ( !function_exists( 'forqy_form_recaptcha_debug' ) ) {

	/**
	 * Debug reCAPTCHA
	 *
	 */
	function forqy_form_recaptcha_debug(): void {

		$config          = apply_filters( 'forqy_theme', array() ); // Theme config
		$debug           = $config[ 'recaptcha_debug' ] ?? apply_filters( 'forqy_form_recaptcha_debug', false );
		$debug_log       = $config[ 'recaptcha_debug_log' ] ?? apply_filters( 'forqy_form_recaptcha_debug_log', false );
		$debug_log_error = $config[ 'recaptcha_debug_log_error' ] ?? apply_filters( 'forqy_form_recaptcha_debug_log_error', false );

		// Get a response from cache
		$response = wp_cache_get( 'forqy_form_recaptcha_response' );

		// Get error message if is an error
		$error_code    = $response[ 'error-codes' ][ 0 ] ?? null;
		$error_message = forqy_form_recaptcha_error_message( $error_code );

		// Construct debug messages
		if ( isset( $response[ 'success' ] ) && (int)$response[ 'success' ] === 1 ) {
			$message = '<div class="f-alert f-alert--success a-alert a-alert--success a-stack a-gap--xxs">';
			$message .= '<p>' . esc_html_x( 'Debug Information:', 'recaptcha', 'forqy-recaptcha' ) . '</p>';
			$message .= '<h5>' . esc_html_x( 'reCAPTCHA Verification Successful!', 'recaptcha', 'forqy-recaptcha' ) . '</h5>';
		} else {
			$message = '<div class="f-alert f-alert--error a-alert a-alert--error a-stack a-gap--xxs">';
			$message .= '<p>' . esc_html_x( 'Debug Information:', 'recaptcha', 'forqy-recaptcha' ) . '</p>';
			$message .= '<h5>' . esc_html_x( 'reCAPTCHA Verification Failed!', 'recaptcha', 'forqy-recaptcha' ) . '</h5>';
			if ( $error_code && $error_message ) {
				$message .= '<p>' . $error_code . ' - ' . $error_message . '</p>';
			}
		}
		$message .= '<pre style="max-height:5ch;padding: 10px;margin:0;">' . var_export( $response, true ) . '</pre>';
		$message .= '</div>';

		// If debug enabled
		if ( $debug ) {
			// Display debugging information for logged in super admin user
			if ( is_user_logged_in() && is_super_admin() ) {
				echo apply_filters( 'forqy_form_recaptcha_debug_message', $message );
			}
		}

		// If debug to log enabled
		if ( $debug_log ) {
			// Print response to error log
			error_log( print_r( $response, true ) );
		}

		// If debug errors to log enabled
		if ( $debug_log_error && isset( $response[ 'success' ] ) && (int)$response[ 'success' ] === 0 ) {
			// Print response to error log only if NOT success [1]
			error_log( print_r( $response, true ) );
		}

	}

	add_action( 'forqy_form_submit_before', 'forqy_form_recaptcha_debug', 10 );
	add_action( 'forqy_form_sent', 'forqy_form_recaptcha_debug', 10 );

}

if ( !function_exists( 'forqy_form_recaptcha_error_message' ) ) {

	/**
	 * Get reCAPTCHA Error Message
	 *
	 * @param $code
	 *
	 * @return string|null
	 */
	function forqy_form_recaptcha_error_message( $code ): ?string {

		$message = null;

		switch ( $code ) {

			case 'missing-input-secret':
				$message = esc_html_x( 'The secret parameter is missing.', 'recaptcha', 'forqy-recaptcha' );
				break;

			case 'invalid-input-secret':
				$message = esc_html_x( 'The secret parameter is invalid or malformed.', 'recaptcha', 'forqy-recaptcha' );
				break;

			case 'missing-input-response':
				$message = esc_html_x( 'The response parameter is missing.', 'recaptcha', 'forqy-recaptcha' );
				break;

			case 'invalid-input-response':
				$message = esc_html_x( 'The response parameter is invalid or malformed.', 'recaptcha', 'forqy-recaptcha' );
				break;

			case 'bad-request':
				$message = esc_html_x( 'The request is invalid or malformed.', 'recaptcha', 'forqy-recaptcha' );
				break;

			case 'timeout-or-duplicate':
				$message = esc_html_x( 'The response is no longer valid: either is too old or has been used previously.', 'recaptcha', 'forqy-recaptcha' );
				break;

			case 'general-fail':
				$message = esc_html_x( 'General fail!', 'recaptcha', 'forqy-recaptcha' );
				break;

		}

		return $message;

	}

}
