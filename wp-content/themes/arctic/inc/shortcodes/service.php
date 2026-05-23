<?php

/**
 * Service Shortcodes
 */

if ( !function_exists( 'baspa_shortcode_service' ) ) {

	/**
	 * Service
	 * [servis]
	 *
	 * @return bool|string
	 */
	function baspa_shortcode_service(): bool|string {

		ob_start();

		get_template_part( 'templates/elements/service' );

		return ob_get_clean();

	}

	add_shortcode( 'servis', 'baspa_shortcode_service' );

}
