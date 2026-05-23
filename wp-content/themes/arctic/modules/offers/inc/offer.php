<?php

/**
 * Offer
 */

if (!function_exists('baspa_offers_has_featured')) {

	/**
	 * Check If Has Featured Offers
	 *
	 * @return bool
	 */
	function baspa_offers_has_featured(): bool {

		$offers_query_args = array(
			'post_type'   => 'offer',
			'post_status' => 'publish',
			'meta_query'  => array(
				array(
					'key'   => 'offer_featured',
					'value' => 1,
				),
			),
		);

		$offers_query = new WP_Query( $offers_query_args );

		return $offers_query->have_posts();

	}

}
