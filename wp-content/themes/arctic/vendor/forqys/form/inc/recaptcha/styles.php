<?php

/**
 * ReCAPTCHA Styles
 *
 * @package forqys/form
 * @since   1.0.0
 */


if ( ! function_exists( 'forqy_form_recaptcha_badge_hide' ) ) {

	/**
	 * Hide ReCAPTCHA Badge
	 */
	function forqy_form_recaptcha_badge_hide() {

		if ( !apply_filters( 'forqy_form_recaptcha_hide_badge', false ) ) {
			return;
		}

		echo "<style>.grecaptcha-badge { display: none; content-visibility: hidden; visibility: hidden; pointer-events: none; }</style>";

	}

	add_action( 'wp_head', 'forqy_form_recaptcha_badge_hide' );

}
