<?php

/**
 * Categories
 */

if ( !function_exists( 'baspa_accessories_product_has_accessories' ) ) {

	/**
	 * Check If Product Has Accessories
	 *
	 * @param $product_id
	 *
	 * @return bool
	 */
	function baspa_accessories_product_has_accessories( $product_id ): bool {

		$query_args = array(
			'post_type'      => 'accessory',
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'accessory_products',
					'value' => $product_id,
				),
			),
			'posts_per_page' => -1,
		);

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			return true;
		} else {
			return false;
		}

	}

}

if ( !function_exists( 'baspa_accessories_get_product_accessory_categories' ) ) {

	/**
	 * Get Product Accessory Categories
	 *
	 * @param $product_id
	 *
	 * @return array|string|WP_Error
	 */
	function baspa_accessories_get_product_accessory_categories( $product_id ): array|string|WP_Error {

		$product_categories            = get_the_terms( $product_id, 'product-category' );
		$product_categories_parent_ids = array();

		if ( !empty( $product_categories ) && !is_wp_error( $product_categories ) ) {
			foreach ( $product_categories as $category ) {
				if ( $category->parent == 0 ) {
					$product_categories_parent_ids[] = $category->term_id;
				} else {
					$product_categories_parent_ids[] = wp_get_term_taxonomy_parent_id( $category->term_id, 'product-category' );
				}
			}
		}

		return get_terms( array(
			'taxonomy'   => 'accessory-category',
			'hide_empty' => false,
			'meta_query' => array(
				array(
					'key'     => 'product_category',
					'value'   => array_shift( $product_categories_parent_ids ),
					'compare' => '='
				),
			),
		) );

	}

}

if ( !function_exists( 'baspa_accessories_has_product_in_accessory_category' ) ) {

	/**
	 * Has Product in Accessory Category?
	 *
	 * @param $product_id
	 * @param $term_id
	 *
	 * @return bool
	 */
	function baspa_accessories_has_product_in_accessory_category( $product_id, $term_id ): bool {

		$query_args = array(
			'post_type'      => 'accessory',
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'accessory_products',
					'value' => $product_id,
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'accessory-category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			),
			'posts_per_page' => -1,
		);

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			return true;
		} else {
			return false;
		}

	}

}

if ( !function_exists( 'baspa_accessories_count_product_in_accessory_category' ) ) {

	/**
	 * Has Product in Accessory Category?
	 *
	 * @param $product_id
	 * @param $term_id
	 *
	 * @return int
	 */
	function baspa_accessories_count_product_in_accessory_category( $product_id, $term_id ): int {

		$query_args = array(
			'post_type'      => 'accessory',
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'accessory_products',
					'value' => $product_id,
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'accessory-category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			),
			'posts_per_page' => -1,
		);

		$query = new WP_Query( $query_args );

		return $query->found_posts;

	}

}
