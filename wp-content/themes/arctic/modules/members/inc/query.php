<?php

/**
 * Query
 */

if ( !function_exists( 'baspa_members_query' ) ) {

	/**
	 * Query
	 *
	 * @return WP_Query
	 */
	function baspa_members_query(): WP_Query {

		return new WP_Query( array(
			'post_type'      => 'member',
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'no_found_rows'  => -1,
			'posts_per_page' => -1,
		) );
	}

}

if ( !function_exists( 'baspa_members_query_contacts' ) ) {

	/**
	 * Query
	 *
	 * @return WP_Query
	 */
	function baspa_members_query_contacts(): WP_Query {

		return new WP_Query( array(
			'post_type'      => 'member',
			'meta_query'     => array(
				array(
					'key'   => 'member_contacts',
					'value' => 1,
				),
			),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'no_found_rows'  => -1,
			'posts_per_page' => -1,
		) );
	}

}
