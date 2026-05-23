<?php

/**
 * Categories
 */

if ( !function_exists( 'baspa_products_get_categories_as_metabox_options' ) ) {

	/**
	 * Get Product Categories as Metabox Options
	 *
	 * @return array
	 */
	function baspa_products_get_categories_as_metabox_options(): array {

		$product_categories = baspa_products_get_product_categories();
		$categories         = array();

		if ( !empty( $product_categories ) ) {
			foreach ( $product_categories as $category ) {
				if ( $category->parent == 0 ) {
					$categories[] = array(
						'value' => (string)$category->term_id,
						'label' => (string)$category->name,
					);
				} else {
					$categories[] = array(
						'value'  => (string)$category->term_id,
						'label'  => (string)$category->name,
						'parent' => (string)$category->parent,
					);
				}
			}
		}

//		do_action( 'qm/debug', $categories );

		return $categories;
	}

}

if ( !function_exists( 'baspa_products_get_categories' ) ) {

	/**
	 * Get Product Categories
	 *
	 * @return array|int[]|string|string[]|WP_Error|WP_Term[]
	 */
	function baspa_products_get_product_categories(): array|WP_Error|string {

		if ( !taxonomy_exists( 'product-category' ) ) {
			// Hook to 'wp_loaded' if taxonomy doesn't exist
			add_action( 'wp_loaded', function () {
				baspa_products_get_product_categories();
			} );

			return array();
		}

		return get_terms( array(
			'taxonomy'   => 'product-category',
			'hide_empty' => false,
		) );
	}

}

if ( !function_exists( 'baspa_products_get_accessories_category' ) ) {

	/**
	 * Get Accessories Category
	 *
	 * @param $product_id
	 *
	 * @return WP_Term|null
	 */
	function baspa_products_get_accessories_category( $product_id ): ?WP_Term {

		if ( empty( $product_id ) ) {
			return null;
		}

		$product_categories            = get_the_terms( $product_id, 'product-category' );
		$product_categories_parent_ids = array();

		if ( !empty( $product_categories ) && !is_wp_error( $product_categories ) ) {
			foreach ( $product_categories as $category ) {
				$product_categories_parent_ids[] = wp_get_term_taxonomy_parent_id( $category->term_id, 'product-category' );
			}
		}

		$accessories_terms = get_terms( array(
			'taxonomy'   => 'product-category',
			'hide_empty' => false,
			'parent'     => array_shift( $product_categories_parent_ids ),
			'meta_query' => array(
				array(
					'key'     => 'category_type',
					'value'   => 'accessories',
					'compare' => '='
				),
			),
		) );

		return array_shift( $accessories_terms );

	}

}

if ( !function_exists( 'baspa_products_is_term_or_product' ) ) {

	/**
	 * Check Term or Product
	 *
	 * @param string $term_slug
	 *
	 * @return bool
	 */
	function baspa_products_is_term_or_product( string $term_slug ): bool {

		if ( empty( $term_slug ) ) {
			return false;
		}

		/**
		 * On Category Page
		 */
		if ( is_tax( 'product-category' ) ) {
			$current_term = get_queried_object();

			if ( $current_term && $current_term->taxonomy === 'product-category' ) {

				if ( $current_term->slug === $term_slug ) {
					return true;
				}

				// Parent
				$current_term_parent_id = $current_term->parent;
				while ( $current_term_parent_id ) {
					$parent_term = get_term( $current_term_parent_id, 'product-category' );
					if ( $parent_term && $parent_term->slug === $term_slug ) {
						return true;
					}
					$current_term_parent_id = $parent_term->parent ?? 0;
				}
			}
		}

		/**
		 * On Product Page
		 */
		if ( is_singular( 'product' ) ) {
			$current_post = get_queried_object();

//			do_action( 'qm/debug', $current_post );

			if ( $current_post && $current_post->post_type === 'product' ) {
				if ( has_term( $term_slug, 'product-category', $current_post ) ) {
					return true;
				}

				$current_post_terms = get_the_terms( $current_post, 'product-category' );
				if ( !empty( $current_post_terms ) && !is_wp_error( $current_post_terms ) ) {

					foreach ( $current_post_terms as $term ) {
						$parent_id = $term->parent;
						while ( $parent_id ) {
							$parent_term = get_term( $parent_id, 'product-category' );
							if ( $parent_term && $parent_term->slug === $term_slug ) {
								return true;
							}
							$parent_id = $parent_term->parent ?? 0;
						}
					}
				}
			}
		}

		return false;

	}

}
