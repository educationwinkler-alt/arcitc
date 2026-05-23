<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_references_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_references_metabox_register( array $meta_boxes ): array {

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--reference',
			'title'      => esc_html__( 'Reference', 'baspa' ),
			'post_types' => array( 'reference' ),
			'priority'   => 'high',
			'fields'     => array(
//				array(
//					'name' => esc_html_x( 'Featured on Homepage?', 'admin', 'baspa' ),
//					'id'   => 'reference_featured',
//					'type' => 'checkbox',
//					'std'  => 0,
//				),
				array(
					'name' => esc_html_x( 'Display Single', 'admin', 'baspa' ),
					'id'   => 'reference_single',
					'type' => 'checkbox',
					'std'  => 1,
				),
				array(
					'name'    => esc_html__( 'Description', 'baspa' ),
					'id'      => 'reference_description',
					'type'    => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 10,
						'teeny'         => true,
						'quicktags'     => false,
						'media_buttons' => false,
						'tinymce'       => array(
							'toolbar1' => 'bold,italic,underline,undo,redo',
							'toolbar2' => '',
							'toolbar3' => '',
						),
					),
				),
				array(
					'name' => esc_html__( 'Location', 'baspa' ),
					'id'   => 'reference_location',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'Year', 'baspa' ),
					'id'   => 'reference_year',
					'type' => 'text',
				),
			),
		);

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--reference-gallery',
			'title'      => esc_html__( 'Gallery', 'baspa' ),
			'post_types' => array( 'reference' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name' => esc_html__( 'Images', 'baspa' ),
					'id'   => 'reference_images',
					'type' => 'image_advanced',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_references_metabox_register' );

}
