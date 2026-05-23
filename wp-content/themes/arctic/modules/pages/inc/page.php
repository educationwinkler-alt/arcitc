<?php

/**
 * Page
 */

if ( !function_exists( 'baspa_pages_get_page_by_product_category' ) ) {

	/**
	 * Get Page ID by Product Category ID
	 *
	 * @param $product_category_id
	 *
	 * @return int
	 */
	function baspa_pages_get_page_by_product_category( $product_category_id ): int {

		$page_id = 0;

		if ( empty( $product_category_id ) ) {
			return $page_id;
		}

		$pages_query_args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'   => 'page_product_category',
					'value' => $product_category_id,
				),
			),
		);

		$pages_query = new WP_Query( $pages_query_args );

		if ( $pages_query->have_posts() ) {
			while ( $pages_query->have_posts() ) {
				$pages_query->the_post();

				$page_id = get_the_ID();
			}
			$pages_query->reset_postdata();
			wp_reset_query();
		}

		return $page_id;

	}

}
