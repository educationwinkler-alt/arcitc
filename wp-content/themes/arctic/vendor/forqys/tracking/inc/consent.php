<?php

/**
 * Consent
 *
 * @package forqys/tracking
 * @since   1.0.0
 */

if ( !function_exists( 'forqy_tracking_consent_scripts' ) ) {

	/**
	 * GTM Scripts
	 *
	 * @return void
	 */
	function forqy_tracking_consent_scripts(): void {

		$config         = apply_filters( 'forqy_theme', array() ); // Theme config
		$config_consent = $config[ 'consent' ] ?? true;

		if ( $config_consent && !empty( $config[ 'gtm_id' ] ) ) {

			wp_enqueue_script(
				get_template() . '-consent',
				str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/dist/js/consent.js' ),
				get_template() . '-gtm',
				'1.0.1',
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				),
			);

		}

	}

	add_action( 'wp_enqueue_scripts', 'forqy_tracking_consent_scripts', 3 ); // 1 == CookieYes init script

}
