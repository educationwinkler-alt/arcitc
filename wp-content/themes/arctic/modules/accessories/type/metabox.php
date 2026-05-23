<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_accessories_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_accessories_metabox_register( array $meta_boxes ): array {

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--accessory',
			'title'      => esc_html__( 'Accessory', 'baspa' ),
			'post_types' => array( 'accessory' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name' => esc_html_x( 'URL', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'URL to the external product page.', 'admin', 'baspa' ),
					'id'   => 'accessory_url',
					'type' => 'url',
				),
				array(
					'name'        => esc_html_x( 'Products', 'admin', 'baspa' ),
					'desc'        => esc_html_x( 'Please select the products for which it will be displayed.', 'admin', 'baspa' ),
					'placeholder' => esc_html_x( 'Select products', 'admin', 'baspa' ),
					'id'          => 'accessory_products',
					'type'        => 'post',
					'post_type'   => 'product',
					'field_type'  => 'checkbox_tree',
					'multiple'    => true,
					'query_args'  => array(
						'post_status'    => 'publish',
						'posts_per_page' => -1,
					),
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_accessories_metabox_register' );

}
