<?php

/**
 * Gallery Shortcodes
 */

if ( !function_exists( 'baspa_shortcode_hours' ) ) {

	/**
	 * Hours
	 * [oteviraci-doba]
	 *
	 * @return bool|string
	 */
	function baspa_shortcode_hours(): bool|string {

		ob_start();

		get_template_part( 'templates/about/hours' );

		return ob_get_clean();

	}

	add_shortcode( 'oteviraci-doba', 'baspa_shortcode_hours' );

}
