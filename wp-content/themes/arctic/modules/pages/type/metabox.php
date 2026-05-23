<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_pages_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_pages_metabox_register( array $meta_boxes ): array {

		if ( isset( $_GET[ 'post' ] ) && get_post_meta( intval( $_GET[ 'post' ] ), '_wp_page_template', true ) == 'template-homepage.php' ) {

			/**
			 * Homepage Metabox
			 */

			$meta_boxes[] = array(
				'id'         => 'baspa-metabox--homepage',
				'title'      => esc_html_x( 'Homepage', 'admin', 'baspa' ),
				'post_types' => array( 'page' ),
				'priority'   => 'high',
				'fields'     => array(
					array(
						'name'    => esc_html_x( 'Title', 'admin', 'baspa' ),
						'id'      => 'page_title_text',
						'type'    => 'wysiwyg',
						'options' => array(
							'media_buttons' => false,
							'textarea_rows' => 1,
							'teeny'         => true,
							'tinymce'       => array(
								'toolbar1' => 'bold,italic,undo,redo',
								'toolbar2' => '',
								'toolbar3' => '',
							),
						),
					),
					array(
						'name'    => esc_html_x( 'Description', 'admin', 'baspa' ),
						'id'      => 'page_description_text',
						'type'    => 'wysiwyg',
						'options' => array(
							'media_buttons' => false,
							'textarea_rows' => 1,
							'teeny'         => true,
							'tinymce'       => array(
								'toolbar1' => 'bold,italic,undo,redo',
								'toolbar2' => '',
								'toolbar3' => '',
							),
						),
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Button', 'admin', 'baspa' ),
					),
					array(
						'name'              => esc_html_x( 'Button Text', 'admin', 'baspa' ),
						'id'                => 'page_button_text',
						'type'              => 'text',
						'sanitize_callback' => 'wp_kses_post',
					),
					array(
						'name' => esc_html_x( 'Button URL', 'admin', 'baspa' ),
						'id'   => 'page_button_url',
						'type' => 'url',
					),
				)
			);

		} else {

			/**
			 * Page Metabox
			 */

			$meta_boxes[] = array(
				'id'         => 'baspa-metabox--page',
				'title'      => esc_html_x( 'Page', 'admin', 'baspa' ),
				'post_types' => array( 'page' ),
				'priority'   => 'high',
				'fields'     => array(
					array(
						'name'        => esc_html_x( 'Product Category', 'admin', 'baspa' ),
						'desc'        => esc_html_x( 'Page won\'t be available on the website, and will be redirected to a product category.', 'admin', 'baspa' ),
						'id'          => 'page_product_category',
						'type'        => 'select',
						'placeholder' => esc_html_x( 'Select a Category', 'admin', 'baspa' ),
						'flatten'     => false,
						'options'     => function_exists( 'baspa_products_get_categories_as_metabox_options' ) ? baspa_products_get_categories_as_metabox_options() : array(),
					),
					array(
						'name' => esc_html_x( 'Display Title', 'admin', 'baspa' ),
						'id'   => 'page_title',
						'type' => 'checkbox',
						'std'  => 1,
					),
					array(
						'name'    => esc_html_x( 'Title', 'admin', 'baspa' ),
						'id'      => 'page_title_text',
						'type'    => 'wysiwyg',
						'options' => array(
							'media_buttons' => false,
							'textarea_rows' => 1,
							'teeny'         => true,
							'tinymce'       => array(
								'toolbar1' => 'bold,italic,undo,redo',
								'toolbar2' => '',
								'toolbar3' => '',
							),
						),
					),
					array(
						'name'    => esc_html_x( 'Description', 'admin', 'baspa' ),
						'id'      => 'page_description_text',
						'type'    => 'wysiwyg',
						'options' => array(
							'media_buttons' => false,
							'textarea_rows' => 1,
							'teeny'         => true,
							'tinymce'       => array(
								'toolbar1' => 'bold,italic,undo,redo',
								'toolbar2' => '',
								'toolbar3' => '',
							),
						),
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Button', 'admin', 'baspa' ),
					),
					array(
						'name'              => esc_html_x( 'Button Text', 'admin', 'baspa' ),
						'id'                => 'page_button_text',
						'type'              => 'text',
						'sanitize_callback' => 'wp_kses_post',
					),
					array(
						'name' => esc_html_x( 'Button URL', 'admin', 'baspa' ),
						'id'   => 'page_button_url',
						'type' => 'url',
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Badge', 'admin', 'baspa' ),
					),
					array(
						'name'      => esc_html_x( 'Badge Title', 'admin', 'baspa' ),
						'id'        => 'page_badge_title',
						'type'      => 'text',
						'maxlength' => 10,
					),
					array(
						'name'      => esc_html_x( 'Badge Text', 'admin', 'baspa' ),
						'id'        => 'page_badge_text',
						'type'      => 'text',
						'maxlength' => 24,
					),
				)
			);
		}

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_pages_metabox_register' );

}
