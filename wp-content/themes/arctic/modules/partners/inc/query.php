<?php

/**
 * Query
 */

if ( !function_exists( 'baspa_partners_query' ) ) {

	/**
	 * Query
	 *
	 * @return WP_Query
	 */
	function baspa_partners_query(): WP_Query {

		return new WP_Query( array(
			'post_type'      => 'partner',
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'no_found_rows'  => -1,
			'posts_per_page' => -1,
		) );
	}

}
