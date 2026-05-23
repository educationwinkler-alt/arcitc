<?php

/**
 * Terms
 */

if ( !function_exists( 'baspa_get_term_posts_number' ) ) {

	/**
	 * Get Term Number of Posts
	 *
	 * @param $term_id
	 * @param $type
	 * @param $taxonomy
	 *
	 * @return int
	 */
	function baspa_get_term_posts_number( $term_id, $type, $taxonomy, $meta_query = array() ): int {

		$posts_query_args = array(
			'post_type'      => $type,
			'tax_query'      => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'ids',
					'terms'    => array( $term_id ),
				),
			),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'no_found_rows'  => true,
			'posts_per_page' => -1,
		);

		if ( !empty( $meta_query ) ) {
			$posts_query_args[ 'meta_query' ] = $meta_query;
		}

		$posts_query = new WP_Query( $posts_query_args );

		return $posts_query->post_count;

	}

}
