<?php

/**
 * Theme
 */

if ( !function_exists( 'baspa_theme' ) ) {

	/**
	 * Theme Config
	 *
	 * @return array<string, string|false>
	 */
	function baspa_theme(): array {

		$settings[ 'prefix' ] = 'f';

		// Fonts
		$settings[ 'font_api' ]              = 'google';
		$settings[ 'font_primary' ]          = 'Unbounded';
		$settings[ 'font_primary_styles' ]   = '200,400,500,700';
		$settings[ 'font_secondary' ]        = 'Poppins';
		$settings[ 'font_secondary_styles' ] = '400,700';
		$settings[ 'font_display' ]          = 'swap';

		// Tracking
		$settings[ 'consent' ] = true;
		$settings[ 'gtm_id' ]  = get_theme_mod( 'baspa_gtm' );
		$settings[ 'ga4_id' ]  = get_theme_mod( 'baspa_ga4' );

		// reCAPTCHA
		$settings[ 'recaptcha_key_site' ]        = get_theme_mod( 'baspa_form_recaptcha_key_site' );
		$settings[ 'recaptcha_key_secret' ]      = get_theme_mod( 'baspa_form_recaptcha_key_secret' );
		$settings[ 'recaptcha_score' ]           = 0.4;
		$settings[ 'recaptcha_debug' ]           = false;
		$settings[ 'recaptcha_debug_log' ]       = false;
		$settings[ 'recaptcha_debug_log_error' ] = true;

		// Ecomail
		$settings[ 'ecomail_api_key' ] = get_theme_mod( 'baspa_ecomail_api_key' );
		$settings[ 'ecomail_list_id' ] = get_theme_mod( 'baspa_ecomail_list_id' );

		// Misc
		$settings[ 'search' ]   = true;
		$settings[ 'comments' ] = false;
		$settings[ 'emojis' ]   = false;

		$settings[ 'js_check' ] = false; // add 'class="no-js"' to <html>

		if ( function_exists( 'wp_get_environment_type' ) && wp_get_environment_type() === 'local' ) {
			$settings[ 'font_api' ] = 'local';
		}

		return $settings;

	}

	add_filter( 'forqy_theme', 'baspa_theme', 5 );

}

if ( function_exists( 'wp_get_environment_type' ) && wp_get_environment_type() === 'local' ) {
	add_filter( 'forqy_fonts_url', '__return_false', 20 );
	add_filter( 'forqy_fonts_google_enqueue', '__return_false', 20 );
	add_filter( 'forqy_fonts_google_preconnect', '__return_false', 20 );
	add_filter( 'forqy_fonts_google_preload', '__return_false', 20 );
	add_filter( 'forqy_fonts_fontshare_enqueue', '__return_false', 20 );
	add_filter( 'forqy_fonts_fontshare_preconnect', '__return_false', 20 );
	add_filter( 'forqy_fonts_fontshare_preload', '__return_false', 20 );
}
