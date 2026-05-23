<?php

/**
 * Query
 */

if ( !function_exists( 'baspa_references_query' ) ) {

	/**
	 * Query
	 *
	 * @return WP_Query
	 */
	function baspa_references_query(): WP_Query {

		return new WP_Query( array(
			'post_type'      => 'reference',
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'  => -1,
			'posts_per_page' => -1,
		) );
	}

}
