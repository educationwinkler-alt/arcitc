<?php

/**
 * Partners
 */

if ( !function_exists( 'baspa_partners_exists' ) ) {

	/**
	 * Check If Product Has Variations
	 *
	 * @return bool
	 */
	function baspa_partners_exists(): bool {

		$products_query_args = array(
			'post_type'      => 'partner',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$products_query = new WP_Query( $products_query_args );

		return (bool)$products_query->found_posts;

	}

}
