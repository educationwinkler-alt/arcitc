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
					'name' => esc_html_x( 'Jucra Model Name', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Model value used in [visao_viewer model_name="..."] shortcode.', 'admin', 'baspa' ),
					'id'   => 'jucra_model_name',
					'type' => 'text',
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
					'name'       => esc_html_x( 'Acrylic Colors', 'admin', 'baspa' ),
					'desc'       => esc_html_x( 'Legacy fallback. Prefer the global color catalog fields below.', 'admin', 'baspa' ),
					'id'         => 'product_acrylic_colors',
					'type'       => 'text',
					'clone'      => true,
					'add_button' => esc_html_x( '+ Add Color', 'admin', 'baspa' ),
				),
				array(
					'name'        => esc_html_x( 'Available Shell Colors', 'admin', 'baspa' ),
					'desc'        => esc_html_x( 'Select colors from the global Product Colors catalog.', 'admin', 'baspa' ),
					'placeholder' => esc_html_x( 'Select shell colors', 'admin', 'baspa' ),
					'id'          => 'product_shell_color_ids',
					'type'        => 'post',
					'post_type'   => 'spa_color',
					'field_type'  => 'checkbox_list',
					'multiple'    => true,
					'query_args'  => array(
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'orderby'        => array(
							'menu_order' => 'ASC',
							'title'      => 'ASC',
						),
						'meta_query'     => array(
							array(
								'key'   => 'spa_color_type',
								'value' => 'shell',
							),
						),
					),
				),
				array(
					'name'        => esc_html_x( 'Available Cabinet Colors', 'admin', 'baspa' ),
					'desc'        => esc_html_x( 'Select colors from the global Product Colors catalog.', 'admin', 'baspa' ),
					'placeholder' => esc_html_x( 'Select cabinet colors', 'admin', 'baspa' ),
					'id'          => 'product_cabinet_color_ids',
					'type'        => 'post',
					'post_type'   => 'spa_color',
					'field_type'  => 'checkbox_list',
					'multiple'    => true,
					'query_args'  => array(
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'orderby'        => array(
							'menu_order' => 'ASC',
							'title'      => 'ASC',
						),
						'meta_query'     => array(
							array(
								'key'   => 'spa_color_type',
								'value' => 'cabinet',
							),
						),
					),
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
					'type' => 'heading',
					'name' => esc_html_x( 'Hero media', 'admin', 'baspa' ),
				),
				array(
					'name'    => esc_html_x( 'Media type', 'admin', 'baspa' ),
					'desc'    => esc_html_x( 'Image keeps the current product gallery/featured image flow. Video uses a self-hosted MP4/WebM from the Media Library and takes priority over the gallery.', 'admin', 'baspa' ),
					'id'      => 'product_hero_media_type',
					'type'    => 'select',
					'std'     => 'image',
					'options' => function_exists( 'arctic_hero_media_type_options' ) ? arctic_hero_media_type_options() : array(
						'image' => esc_html_x( 'Image', 'admin', 'baspa' ),
						'video' => esc_html_x( 'Video', 'admin', 'baspa' ),
					),
				),
				array(
					'name'             => esc_html_x( 'Hero video', 'admin', 'baspa' ),
					'desc'             => esc_html_x( 'Use a short muted MP4/WebM. The frontend renders it autoplay, muted, looped and playsinline.', 'admin', 'baspa' ),
					'id'               => 'product_hero_video',
					'type'             => 'file_advanced',
					'mime_type'        => 'video/mp4,video/webm,video/quicktime',
					'max_file_uploads' => 1,
				),
				array(
					'name'             => esc_html_x( 'Video poster / fallback image', 'admin', 'baspa' ),
					'desc'             => esc_html_x( 'Used before the video loads, on reduced-motion devices and as visual fallback. If empty, the featured image is used.', 'admin', 'baspa' ),
					'id'               => 'product_hero_poster_image',
					'type'             => 'image_advanced',
					'image_size'       => 'thumbnail',
					'max_file_uploads' => 1,
				),
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

		$meta_boxes[] = array(
			'id'         => 'arctic-metabox--product-benefits',
			'title'      => esc_html_x( 'Product Benefits', 'admin', 'baspa' ),
			'post_types' => array( 'product' ),
			'priority'   => 'default',
			'fields'     => array(
				array(
					'name' => esc_html_x( 'Benefits Heading', 'admin', 'baspa' ),
					'id'   => 'product_benefits_heading',
					'type' => 'text',
				),
				array(
					'name' => esc_html_x( 'Benefits Description', 'admin', 'baspa' ),
					'id'   => 'product_benefits_description',
					'type' => 'textarea',
					'rows' => 3,
				),
				array(
					'name'       => esc_html_x( 'Benefit Card', 'admin', 'baspa' ),
					'id'         => 'product_benefit_items',
					'type'       => 'fieldset_text',
					'clone'      => true,
					'sort_clone' => true,
					'add_button' => esc_html_x( '+ Add Benefit', 'admin', 'baspa' ),
					'options'    => array(
						'title'         => esc_html_x( 'Title', 'admin', 'baspa' ),
						'summary'       => esc_html_x( 'Short text', 'admin', 'baspa' ),
						'icon'          => esc_html_x( 'Icon slug', 'admin', 'baspa' ),
						'interactive'   => esc_html_x( 'Interactive? 1/0', 'admin', 'baspa' ),
						'popup_title'   => esc_html_x( 'Popup title', 'admin', 'baspa' ),
						'popup_content' => esc_html_x( 'Popup HTML/text', 'admin', 'baspa' ),
					),
				),
				array(
					'name'             => esc_html_x( 'Benefit Images', 'admin', 'baspa' ),
					'desc'             => esc_html_x( 'Images are matched to benefit cards by order. Empty rows render without fake production media; local seed assets are used only in local/development fallback mode.', 'admin', 'baspa' ),
					'id'               => 'product_benefit_images',
					'type'             => 'image_advanced',
					'image_size'       => 'thumbnail',
					'max_file_uploads' => 24,
				),
				array(
					'name'             => esc_html_x( 'Benefit Popup Images', 'admin', 'baspa' ),
					'desc'             => esc_html_x( 'Images are matched to interactive benefit popups by order.', 'admin', 'baspa' ),
					'id'               => 'product_benefit_popup_images',
					'type'             => 'image_advanced',
					'image_size'       => 'thumbnail',
					'max_file_uploads' => 24,
				),
			),
		);

		$meta_boxes[] = array(
			'id'         => 'arctic-metabox--product-options',
			'title'      => esc_html_x( 'Product Optional Equipment', 'admin', 'baspa' ),
			'post_types' => array( 'product' ),
			'priority'   => 'default',
			'fields'     => array(
				array(
					'name' => esc_html_x( 'Options Heading', 'admin', 'baspa' ),
					'id'   => 'product_options_heading',
					'type' => 'text',
				),
				array(
					'name' => esc_html_x( 'Options Description', 'admin', 'baspa' ),
					'id'   => 'product_options_description',
					'type' => 'textarea',
					'rows' => 3,
				),
				array(
					'name'       => esc_html_x( 'Option Card', 'admin', 'baspa' ),
					'id'         => 'product_option_items',
					'type'       => 'fieldset_text',
					'clone'      => true,
					'sort_clone' => true,
					'add_button' => esc_html_x( '+ Add Option', 'admin', 'baspa' ),
					'options'    => array(
						'title'   => esc_html_x( 'Title', 'admin', 'baspa' ),
						'summary' => esc_html_x( 'Short text', 'admin', 'baspa' ),
						'icon'    => esc_html_x( 'Icon slug', 'admin', 'baspa' ),
					),
				),
				array(
					'name'             => esc_html_x( 'Option Images', 'admin', 'baspa' ),
					'desc'             => esc_html_x( 'Images are matched to option cards by order. Empty rows render without fake production media; local seed assets are used only in local/development fallback mode.', 'admin', 'baspa' ),
					'id'               => 'product_option_images',
					'type'             => 'image_advanced',
					'image_size'       => 'thumbnail',
					'max_file_uploads' => 24,
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_products_metabox_register' );

}
