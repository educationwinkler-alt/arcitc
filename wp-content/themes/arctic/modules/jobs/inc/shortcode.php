<?php

/**
 * Shortcode
 */

if ( !function_exists( 'baspa_jobs_shortcode_section' ) ) {

	/**
	 * Jobs
	 * [prace]
	 *
	 * @return bool|string
	 */
	function baspa_jobs_shortcode_section(): bool|string {

		ob_start();

		get_template_part( 'modules/jobs/templates/section' );

		return ob_get_clean();

	}

	add_shortcode( 'prace', 'baspa_jobs_shortcode_section' );

}
