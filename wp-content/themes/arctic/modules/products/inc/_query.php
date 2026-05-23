<?php

/**
 * Filter Query
 */

if ( ! function_exists( 'baspa_products_query_vars_register' ) ) {

	/**
	 * Register Query Vars for Filter
	 *
	 * @param $vars
	 *
	 * @return mixed
	 */
	function baspa_products_query_vars_register( $vars ): mixed {

		// Taxonomy
		$vars[] = 'kategorie'; // 'product-category' taxonomy
		$vars[] = 'vyrobce'; // 'product-manufacturer' taxonomy

		$vars[] = 'nejoblibenejsi'; // 'product_featured' meta
		$vars[] = 'poradi'; // sorting

		return $vars;
	}

	add_filter( 'query_vars', 'baspa_products_query_vars_register' );

}

if ( ! function_exists( 'baspa_products_query' ) ) {

	/**
	 * Main Filter Query
	 *
	 * @return WP_Query
	 */
	function baspa_products_query(): WP_Query {

		/**
		 * Default arguments
		 */
		$args = array(
			'post_type'      => 'product',
			'post_parent'    => 0,
			'paged'          => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
			'posts_per_page' => ( get_query_var( 'baspa_products_posts_per_page' ) ) ? get_query_var( 'baspa_products_posts_per_page' ) : 24,
		);

		/**
		 * Search query arguments
		 */
		if ( get_query_var( 's' ) !== null && get_query_var( 's' ) !== '' ) {
			$args = array_merge( $args, array(
				's' => get_query_var( 's' ),
			) );
		}

		/**
		 * -------------------
		 * Tax query arguments
		 * -------------------
		 */
		$tax_query = [];

		if ( get_query_var( 'kategorie' ) !== '' ) {

			do_action('qm/debug', get_query_var( 'kategorie' ));
			// Set 'product-category' term/s
			$tax_query[] = array(
				'taxonomy' => 'product-category',
				'terms'    => get_query_var( 'kategorie' ),
				'field'    => 'slug',
			);
		}

		if ( get_query_var( 'vyrobce' ) !== '' ) {

			// Set 'product-manufacturer' term/s
			$tax_query[] = array(
				'taxonomy' => 'product-manufacturer',
				'terms'    => get_query_var( 'vyrobce' ),
				'field'    => 'slug',
			);
		}

		// Set relation between tax queries
		if ( count( $tax_query ) > 1 ) {
			$tax_query[ 'relation' ] = 'AND';
		}
		// Merge arrays
		if ( count( $tax_query ) > 0 ) {
			$args = array_merge( $args, array(
				'tax_query' => $tax_query,
			) );
		}

		/**
		 * Meta query arguments
		 */
		$meta_query = [];
		// Set 'product_featured' meta
		if ( get_query_var( 'nejoblibenejsi' ) !== '' ) {
			$meta_query[] = array(
				'key'   => 'product_featured',
				'value' => 1,
			);
		}
		// Set relation between meta queries
		if ( count( $meta_query ) > 1 ) {
			$meta_query[ 'relation' ] = 'AND';
		}
		// Merge arrays
		if ( count( $meta_query ) > 0 ) {
			$args = array_merge( $args, array(
				'meta_query' => $meta_query,
			) );
		}

		/**
		 * Ordering Arguments
		 */
		if ( get_query_var( 'poradi' ) !== '' ) {

			if ( get_query_var( 'poradi' ) == 'predvolene' ) {

				$args = array_merge( $args, array(
					'orderby' => array(
						'menu_order' => 'ASC',
						'date'       => 'DESC',
					),
				) );

			}
		} else {

			$args = array_merge( $args, array(
				'orderby' => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
			) );

		}

//		do_action( 'qm/debug', $args );

		/**
		 * Query
		 */
		return new WP_Query( $args );

	}

}

if ( ! function_exists( 'baspa_products_query_modify' ) ) {

	/**
	 * FIX Query Pagination
	 *
	 * @param $query
	 *
	 * @return void
	 */
	function baspa_products_query_modify( $query ): void {

		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_tax( 'product-category' ) ) {
			$query->set( 'posts_per_page', ( get_query_var( 'baspa_posts_per_page' ) ) ? get_query_var( 'baspa_posts_per_page' ) : 24 );
		}
	}

	add_action( 'pre_get_posts', 'baspa_products_query_modify' );

}
