<?php

/**
 * ReCAPTCHA Scripts
 *
 * @package forqys/form
 * @since   1.0.0
 */

if ( !function_exists( 'forqy_form_recaptcha_scripts' ) ) {

	/**
	 * Enqueue ReCAPTCHA Scripts
	 */
	function forqy_form_recaptcha_scripts(): void {

		// Get keys
		$keys = function_exists( 'forqy_form_recaptcha_get_keys' ) ? forqy_form_recaptcha_get_keys() : array();

		if ( !empty( $keys[ 'key_site' ] ) ) {

			// Enqueue ReCAPTCHA
			wp_enqueue_script(
				get_template() . '-grecaptcha',
				esc_url_raw( 'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $keys[ 'key_site' ] ) ),
				array(
					get_template() . '-form',
				),
				null,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				),
			);

			// Init ReCAPTCHA
			// https://developers.google.com/recaptcha/docs/loading
			$recaptcha = "
			    document.addEventListener('DOMContentLoaded', function() {
                    if (typeof grecaptcha !== 'undefined') {
				        const recaptchaInputs = document.querySelectorAll('.js-recaptcha');
				        if (!recaptchaInputs.length) {
				            return;
				        }
				        recaptchaInputs.forEach(function (recaptchaInput) {
			                grecaptcha.ready(function () {
			                    grecaptcha.execute('" . esc_js( $keys[ 'key_site' ] ) . "', {
			                        action: 'form'
			                    }).then(function (token) {
			                        if (token) {
			                            recaptchaInput.value = token;
			                        }
			                    }).catch(function (error) {
			                        console.error('reCAPTCHA error:', error);
			                    });
			                });
				        });
				    }
			    });
			";
			wp_add_inline_script( get_template() . '-grecaptcha', $recaptcha, 'before' );

		}

	}

	add_action( 'wp_enqueue_scripts', 'forqy_form_recaptcha_scripts', 9 ); // Higher priority than "form" script

}
