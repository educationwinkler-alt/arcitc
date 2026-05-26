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
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'   => 'page_product_category',
					'value' => $product_category_id,
				),
			),
		);

		$pages = get_posts( $pages_query_args );

		if ( !empty( $pages ) ) {
			$page_id = (int) $pages[0];
		}

		return $page_id;

	}

}

if ( !function_exists( 'baspa_pages_filter_product_category_link' ) ) {

	/**
	 * Use the matching public page as the canonical URL for product categories.
	 *
	 * @param string  $term_link
	 * @param WP_Term $term
	 * @param string  $taxonomy
	 *
	 * @return string
	 */
	function baspa_pages_filter_product_category_link( string $term_link, WP_Term $term, string $taxonomy ): string {

		if ( 'product-category' !== $taxonomy ) {
			return $term_link;
		}

		$page_id = baspa_pages_get_page_by_product_category( $term->term_id );

		if ( empty( $page_id ) ) {
			return $term_link;
		}

		$page_link = get_permalink( $page_id );

		return !empty( $page_link ) ? $page_link : $term_link;

	}

	add_filter( 'term_link', 'baspa_pages_filter_product_category_link', 10, 3 );

}

if ( !function_exists( 'baspa_pages_redirect_more_info_hub' ) ) {

	/**
	 * The Figma wireframe defines "Další informace" as a header dropdown, not a
	 * separate tile hub. Keep the old local page URL as a stable redirect.
	 *
	 * @return void
	 */
	function baspa_pages_redirect_more_info_hub(): void {

		if ( !is_page( 'dalsi-informace' ) ) {
			return;
		}

		wp_safe_redirect( home_url( '/#order-progress' ), 301 );
		exit;

	}

	add_action( 'template_redirect', 'baspa_pages_redirect_more_info_hub', 1 );

}
