<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_partners_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_partners_metabox_register( array $meta_boxes ): array {

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--partner',
			'title'      => esc_html__( 'Partner', 'baspa' ),
			'post_types' => array( 'partner' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name' => esc_html__( 'URL', 'baspa' ),
					'id'   => 'partner_url',
					'type' => 'url',
				),
				array(
					'name'    => esc_html__( 'Description', 'baspa' ),
					'id'      => 'partner_description',
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
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_partners_metabox_register' );

}
