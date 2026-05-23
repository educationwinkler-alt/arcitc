<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_slides_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_slides_metabox_register( array $meta_boxes ): array {

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--slide',
			'title'      => esc_html__( 'Slide', 'baspa' ),
			'post_types' => array( 'slide' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name'              => esc_html_x( 'Button Text', 'admin', 'baspa' ),
					'id'                => 'button_text',
					'type'              => 'text',
					'sanitize_callback' => 'wp_kses_post',
				),
				array(
					'type' => 'heading',
					'name' => esc_html_x( 'Button URL', 'admin', 'baspa' ),
				),
				array(
					'name'        => esc_html_x( 'Button URL', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Product', 'admin', 'baspa' ),
					'id'          => 'button_url_post',
					'type'        => 'post',
					'post_type'   => 'product',
					'placeholder' => esc_html_x( 'Select a Product', 'admin', 'baspa' ),
				),
				array(
					'name'        => esc_html_x( 'Button URL', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Product Category', 'admin', 'baspa' ),
					'id'          => 'button_url_category',
					'type'        => 'taxonomy_advanced',
					'taxonomy'    => array( 'product-category' ),
					'placeholder' => esc_html_x( 'Select a Product Category', 'admin', 'baspa' ),
				),
				array(
					'name' => esc_html_x( 'Button URL', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Custom', 'admin', 'baspa' ),
					'id'   => 'button_url',
					'type' => 'url',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_slides_metabox_register' );

}
