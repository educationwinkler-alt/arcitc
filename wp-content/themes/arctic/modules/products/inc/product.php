<?php

/**
 * Product
 */

if ( !function_exists( 'baspa_products_types' ) ) {

	/**
	 * Define Types
	 *
	 * @return array
	 */
	function baspa_products_types(): array {

		return array(
			'standard'          => esc_html_x( 'Full detail', 'product type', 'baspa' ),
			'landing_section'   => esc_html_x( 'Landing section', 'product type', 'baspa' ),
			'external_shop'     => esc_html_x( 'External shop / CTA', 'product type', 'baspa' ),
			'hidden_or_retired' => esc_html_x( 'Hidden or retired', 'product type', 'baspa' ),
			'affiliate'         => esc_html_x( 'Affiliate', 'product type', 'baspa' ),
		);

	}

}

if ( !function_exists( 'baspa_products_colors' ) ) {

	/**
	 * Define Colors
	 *
	 * @return array
	 */
	function baspa_products_colors(): array {

		return array(
			'white'  => esc_html__( 'White', 'baspa' ),
			'grey'   => esc_html__( 'Grey', 'baspa' ),
			'black'  => esc_html__( 'Black', 'baspa' ),
			'blue'   => esc_html__( 'Blue', 'baspa' ),
			'green'  => esc_html__( 'Green', 'baspa' ),
			'beige'  => esc_html__( 'Beige', 'baspa' ),
			'yellow' => esc_html__( 'Yellow', 'baspa' ),
			'red'    => esc_html__( 'Red', 'baspa' ),
			'pink'   => esc_html__( 'Pink', 'baspa' ),
			'purple' => esc_html__( 'Purple', 'baspa' ),
			'brown'  => esc_html__( 'Brown', 'baspa' ),
		);

	}

}

if ( !function_exists( 'baspa_products_get_product_colors' ) ) {

	/**
	 * Get Product Colors
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	function baspa_products_get_product_colors( $product_id ): array {

		if ( empty( $product_id ) ) {
			return array();
		}

		$product_colors_defined = baspa_products_colors();
		$product_colors_new     = array();
		$product_colors         = get_post_meta( $product_id, 'product_color' );

		if ( !empty( $product_colors ) ) {
			foreach ( $product_colors as $color ) {
				$product_colors_new[ $color ] = $product_colors_defined[ $color ];
			}
		}

//		do_action( 'qm/debug', $product_colors_new );

		return $product_colors_new;

	}

}

if ( !function_exists( 'baspa_product_is_by_baspawood' ) ) {

	/**
	 * Check If Product is by Baspawood Manufacturer
	 *
	 * @param $product_id
	 *
	 * @return bool
	 */
	function baspa_product_is_by_baspawood( $product_id ): bool {

		if ( empty( $product_id ) ) {
			return false;
		}

		$baspawood_slugs = array(
			'baspawood',
			'baspa-wood',
		);

		$product_manufacturers = get_the_terms( $product_id, 'product-manufacturer' );

		if ( !empty( $product_manufacturers ) && !is_wp_error( $product_manufacturers ) ) {
			foreach ( $product_manufacturers as $manufacturer ) {
				if ( in_array( $manufacturer->slug, $baspawood_slugs ) ) {
					return true;
				}
			}
		}

		return false;

	}

}
