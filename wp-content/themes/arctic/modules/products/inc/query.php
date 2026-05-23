<?php

/**
 * Query
 */

if ( !function_exists( 'baspa_products_query_product_has_variations' ) ) {

	/**
	 * Check If Product Has Variations
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	function baspa_products_query_product_has_variations( int $product_id ): bool {

		$products_query_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'post_parent'    => $product_id,
			'posts_per_page' => -1,
		);

		$products_query = new WP_Query( $products_query_args );

		return (bool)$products_query->found_posts;

	}

}

if ( !function_exists( 'baspa_products_query_product_has_parameters' ) ) {

	/**
	 * Check If Product Has Parameters
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	function baspa_products_query_product_has_parameters( int $product_id ): bool {

		$product_models              = get_post_meta( $product_id, 'product_model', true );
		$product_dimensions_internal = get_post_meta( $product_id, 'product_dimensions_internal', true );
		$product_dimensions_external = get_post_meta( $product_id, 'product_dimensions_external', true );
		$product_water_depth         = get_post_meta( $product_id, 'product_water_depth', true );
		$product_water_volume        = get_post_meta( $product_id, 'product_water_volume', true );
		$product_seats               = get_post_meta( $product_id, 'product_seats', true );
		$product_nozzles             = get_post_meta( $product_id, 'product_nozzles', true );
		$product_configurations      = get_post_meta( $product_id, 'product_configurations' );
		$product_acrylic_colors      = get_post_meta( $product_id, 'product_acrylic_colors' );

		if (
			!empty( $product_models ) ||
			!empty( $product_seats ) ||
			!empty( $product_nozzles ) ||
			!empty( $product_dimensions_internal ) ||
			!empty( $product_dimensions_external ) ||
			!empty( $product_water_depth ) ||
			!empty( $product_water_volume ) ||
			!empty( $product_configurations ) ||
			!empty( $product_acrylic_colors ) ) {
			return true;
		} else {
			return false;
		}
	}

}

if ( !function_exists( 'baspa_products_query_product_has_accessories' ) ) {

	/**
	 * Check If Product Has Variations
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	function baspa_products_query_product_has_accessories( int $product_id ): bool {

		$accessories_category = baspa_products_get_accessories_category( $product_id );

		if ( empty( $accessories_category ) ) {
			return false;
		}

		$products_query_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'product_type',
					'value' => 'affiliate',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'product-category',
					'field'    => 'id',
					'terms'    => $accessories_category->term_id,
				),
			),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'posts_per_page' => -1,
		);

		$products_query = new WP_Query( $products_query_args );

		return (bool)$products_query->found_posts;

	}

}

if ( !function_exists( 'baspa_products_query_related' ) ) {

	/**
	 * Related Products Query
	 *
	 * @param int $product_id
	 *
	 * @return WP_Query
	 */
	function baspa_products_query_related( int $product_id ): WP_Query {

		// Parent
		$product = get_post( $product_id );
		$parent  = $product->post_parent;

		// Categories
		$categories   = get_the_terms( $product_id, 'product-category' );
		$category_ids = array();

		if ( !empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$category_ids[] = $category->term_id;
			}
		}

		// Manufacturers
		$manufacturers    = get_the_terms( $product_id, 'product-manufacturer' );
		$manufacturer_ids = array();

		if ( !empty( $manufacturers ) ) {
			foreach ( $manufacturers as $manufacturer ) {
				$manufacturer_ids[] = $manufacturer->term_id;
			}
		}

		$products_query_args = array(
			'post_type'    => 'product',
			'post_status'  => 'publish',
			'post_parent'  => $parent,
			'post__not_in' => array( $product_id ),
			'tax_query'    => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'product-category',
					'field'    => 'id',
					'terms'    => $category_ids,
				),
				array(
					'taxonomy' => 'product-manufacturer',
					'field'    => 'id',
					'terms'    => $manufacturer_ids,
				),
			),
			'orderby'      => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
		);

//		do_action( 'qm/debug', new WP_Query( $products_query_args ) );

		return new WP_Query( $products_query_args );

	}

}

if ( !function_exists( 'baspa_products_query_related_has_products' ) ) {

	/**
	 * Check If Product Has Related Products
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	function baspa_products_query_related_has_products( int $product_id ): bool {

		$products_query = baspa_products_query_related( $product_id );

		return (bool)$products_query->found_posts;

	}

}
