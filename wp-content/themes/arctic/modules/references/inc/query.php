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

if ( !function_exists( 'baspa_references_recent_context_slug' ) ) {

	/**
	 * Return the product-family context for shared recent-reference sections.
	 *
	 * Homepage/archive can remain curated/global, but product category and product
	 * detail pages must not silently fall back to unrelated references.
	 *
	 * @return string
	 */
	function baspa_references_recent_context_slug(): string {

		if ( is_tax( 'product-category' ) ) {
			$term = get_queried_object();

			return ( $term instanceof WP_Term ) ? (string) $term->slug : '';
		}

		if ( is_singular( 'product' ) ) {
			$product_id = get_the_ID();

			if ( has_term( 'swimspa', 'product-category', $product_id ) ) {
				return 'swimspa';
			}

			if ( has_term( 'virivky', 'product-category', $product_id ) ) {
				return 'virivky';
			}

			if ( has_term( 'dalsi-sortiment', 'product-category', $product_id ) ) {
				return 'dalsi-sortiment';
			}
		}

		return '';

	}

}

if ( !function_exists( 'baspa_references_recent_query_args' ) ) {

	/**
	 * Build the shared recent-reference query.
	 *
	 * @param string|null $context_slug
	 *
	 * @return array<string, mixed>
	 */
	function baspa_references_recent_query_args( ?string $context_slug = null ): array {

		$context_slug = $context_slug ?? baspa_references_recent_context_slug();

		$query_args = array(
			'post_type'      => 'reference',
			'meta_query'     => array(
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'  => true,
			'posts_per_page' => 7,
		);

		if ( !empty( $context_slug ) ) {
			$context_term = get_term_by( 'slug', $context_slug, 'reference-category' );

			if ( $context_term instanceof WP_Term ) {
				$query_args[ 'tax_query' ] = array(
					array(
						'taxonomy' => 'reference-category',
						'field'    => 'term_id',
						'terms'    => array( $context_term->term_id ),
					),
				);
			} else {
				$query_args[ 'post__in' ] = array( 0 );
			}
		}

		return apply_filters( 'baspa_references_recent_query_args', $query_args, $context_slug );

	}

}

if ( !function_exists( 'baspa_references_recent_query' ) ) {

	/**
	 * Recent references query.
	 *
	 * @param string|null $context_slug
	 *
	 * @return WP_Query
	 */
	function baspa_references_recent_query( ?string $context_slug = null ): WP_Query {

		return new WP_Query( baspa_references_recent_query_args( $context_slug ) );

	}

}
