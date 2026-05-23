<?php

/**
 * Functions
 *
 * @package forqys/form
 * @since   1.0.6
 */

if ( !function_exists( 'forqy_form_is_ajax' ) ) {

	/**
	 * Check If Form Using AJAX
	 *
	 * @return bool
	 */
	function forqy_form_is_ajax(): bool {
		return wp_doing_ajax() || ( !empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest' );
	}

}

if ( !function_exists( 'forqy_form_get_number' ) ) {

	/**
	 * Get Unique Number of the Form
	 *
	 * @param bool $random
	 *
	 * @return string
	 */
	function forqy_form_get_number( bool $random = true ): string {
		$date = wp_date( 'ymd' );
		$time = wp_date( 'Hi' );
		$rand = rand( 10, 99 );

		return $date . $time . ( $random ? $rand : '' );
	}

}
