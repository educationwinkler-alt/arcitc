<?php

/**
 * Jobs
 */

if ( !function_exists( 'baspa_jobs_exists' ) ) {

	/**
	 * Check If Product Has Variations
	 *
	 * @return bool
	 */
	function baspa_jobs_exists(): bool {

		$products_query_args = array(
			'post_type'      => 'job',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$products_query = new WP_Query( $products_query_args );

		return (bool)$products_query->found_posts;

	}

}
