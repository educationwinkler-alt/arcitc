<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_products_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function baspa_products_metabox_register( $meta_boxes ): array {

		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--product-type',
			'title'      => esc_html_x( 'Product', 'admin', 'baspa' ),
			'post_types' => array( 'product' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name'    => esc_html_x( 'Type', 'admin', 'baspa' ),
					'desc'    => esc_html_x( 'Controls whether the product has a detail page, acts as a landing item, external CTA, or retired item.', 'admin', 'baspa' ),
					'id'      => 'product_type',
					'type'    => 'select',
					'options' => function_exists( 'baspa_products_types' ) ? baspa_products_types() : array(),
				),
				array(
					'name' => esc_html_x( 'Affiliate URL', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'URL to the external product page.', 'admin', 'baspa' ),
					'id'   => 'product_url',
					'type' => 'url',
				),
				array(
					'name' => esc_html_x( 'Original URL', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Source URL from the old Arctic website for migration and redirects.', 'admin', 'baspa' ),
					'id'   => 'product_original_url',
					'type' => 'url',
				),
				array(
					'name' => esc_html_x( 'CTA Text', 'admin', 'baspa' ),
					'id'   => 'product_cta_text',
					'type' => 'text',
					'std'  => esc_html_x( 'Ask for offer', 'admin', 'baspa' ),
				),
			),
		);

		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--product',
			'title'      => esc_html_x( 'Product', 'admin', 'baspa' ),
			'post_types' => array( 'product' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name' => esc_html_x( 'Short Title', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Zobrazí se na kartě varianty produktu.', 'admin', 'baspa' ),
					'id'   => 'product_title_short',
					'type' => 'text',
				),
				array(
					'name' => esc_html_x( 'Price', 'admin', 'baspa' ),
					'id'   => 'product_price',
					'type' => 'number',
					'min'  => 0,
					'max'  => 10000000,
					'step' => 100,
				),
				array(
					'name' => esc_html_x( 'Price Text', 'admin', 'baspa' ),
					'id'   => 'product_price_text',
					'type' => 'text',
				),
				array(
					'name' => esc_html_x( 'Price Suffix', 'admin', 'baspa' ),
					'std'  => esc_html_x( 'incl. assembly', 'admin', 'baspa' ),
					'id'   => 'product_price_suffix',
					'type' => 'text',
				),
				array(
					'name'    => esc_html_x( 'Short Description', 'admin', 'baspa' ),
					'desc'    => esc_html_x( 'Zobrazí se na kartě varianty produktu.', 'admin', 'baspa' ),
					'id'      => 'product_description_short',
					'type'    => 'textarea',
					'options' => array(
						'textarea_rows' => 3,
						'teeny'         => true,
						'quicktags'     => false,
						'media_buttons' => false,
						'tinymce'       => array(
							'toolbar1' => 'bold,italic,underline,bullist,undo,redo',
							'toolbar2' => '',
							'toolbar3' => '',
						),
					),
				),
				array(
					'name'    => esc_html_x( 'Description', 'admin', 'baspa' ),
					'desc'    => esc_html_x( 'Zobrazí se na detailní stránce produktu.', 'admin', 'baspa' ),
					'id'      => 'product_description',
					'type'    => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 10,
						'teeny'         => true,
						'quicktags'     => false,
						'media_buttons' => false,
						'tinymce'       => array(
							'toolbar1' => 'bold,italic,underline,bullist,undo,redo',
							'toolbar2' => '',
							'toolbar3' => '',
						),
					),
				),
				array(
					'name'    => esc_html_x( 'Contacts', 'admin', 'baspa' ),
					'desc'    => esc_html_x( 'Zobrazený kontakt na detailní stránce produktu.', 'admin', 'baspa' ),
					'id'      => 'product_contacts',
					'type'    => 'select',
					'options' => array(
						''         => esc_html_x( 'Default', 'admin', 'baspa' ),
						'pools'    => esc_html_x( 'Pools', 'admin', 'baspa' ),
						'jacuzzis' => esc_html_x( 'Jacuzzis', 'admin', 'baspa' ),
					),
				),
			),
		);

		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--product-params',
			'title'      => esc_html_x( 'Product Parameters', 'admin', 'baspa' ),
			'post_types' => array( 'product' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name'       => esc_html__( 'Model', 'baspa' ),
					'id'         => 'product_model',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html__( '+ Add Model', 'baspa' ),
				),
				//				array(
				//					'name'     => esc_html__( 'Color', 'baspa' ),
				//					'id'       => 'product_color',
				//					'type'     => 'select',
				//					'multiple' => true,
				//					'options'  => function_exists( 'baspa_products_colors' ) ? baspa_products_colors() : array(),
				//				),
				array(
					'name'       => esc_html__( 'Number of Seats', 'baspa' ),
					'id'         => 'product_seats',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html__( '+ Add Number', 'baspa' ),
				),
				array(
					'name'       => esc_html__( 'Number of Nozzles/Pumps', 'baspa' ),
					'id'         => 'product_nozzles',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html__( '+ Add Number', 'baspa' ),
				),
				array(
					'name'       => esc_html__( 'Internal Dimensions', 'baspa' ),
					'id'         => 'product_dimensions_internal',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html__( '+ Add Dimensions', 'baspa' ),
				),
				array(
					'name'       => esc_html__( 'External Dimensions', 'baspa' ),
					'id'         => 'product_dimensions_external',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html__( '+ Add Dimensions', 'baspa' ),
				),
				array(
					'name'       => esc_html__( 'Water Depth', 'baspa' ),
					'id'         => 'product_water_depth',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html__( '+ Add Depth', 'baspa' ),
				),
				array(
					'name'       => esc_html__( 'Water Volume', 'baspa' ),
					'id'         => 'product_water_volume',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html__( '+ Add Volume', 'baspa' ),
				),
				array(
					'name'       => esc_html_x( 'Configurations', 'admin', 'baspa' ),
					'id'         => 'product_configurations',
					'type'       => 'fieldset_text',
					'clone'      => true,
					'add_button' => esc_html_x( '+ Add Configuration', 'admin', 'baspa' ),
					'options'    => array(
						'name'        => esc_html_x( 'Name', 'admin', 'baspa' ),
						'price'       => esc_html_x( 'Price', 'admin', 'baspa' ),
						'seats'       => esc_html_x( 'Seats', 'admin', 'baspa' ),
						'jets'        => esc_html_x( 'Jets', 'admin', 'baspa' ),
						'pumps'       => esc_html_x( 'Pumps', 'admin', 'baspa' ),
						'dimensions'  => esc_html_x( 'Dimensions', 'admin', 'baspa' ),
						'description' => esc_html_x( 'Description', 'admin', 'baspa' ),
					),
				),
				array(
					'name'       => esc_html_x( 'Acrylic Colors', 'admin', 'baspa' ),
					'id'         => 'product_acrylic_colors',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html_x( '+ Add Color', 'admin', 'baspa' ),
				),
			),
		);

		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--product-images',
			'title'      => esc_html_x( 'Product Images', 'admin', 'baspa' ),
			'post_types' => array( 'product' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name'             => esc_html_x( 'Product Image', 'admin', 'baspa' ),
					'desc'             => esc_html_x( 'Zobrazí se na kartě varianty produktu.', 'admin', 'baspa' ),
					'id'               => 'product_image',
					'type'             => 'image_advanced',
					'image_size'       => 'thumbnail',
					'max_file_uploads' => 1,
				),
				array(
					'name'       => esc_html_x( 'Product Images', 'admin', 'baspa' ),
					'id'         => 'product_images',
					'type'       => 'image_advanced',
					'image_size' => 'thumbnail',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_products_metabox_register' );

}
