<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_offers_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_offers_metabox_register( array $meta_boxes ): array {

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--offer',
			'title'      => esc_html__( 'offer', 'baspa' ),
			'post_types' => array( 'offer' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name' => esc_html_x( 'Featured on Homepage?', 'admin', 'baspa' ),
					'id'   => 'offer_featured',
					'type' => 'checkbox',
					'std'  => 0,
				),
				array(
					'name'        => esc_html_x( 'Type', 'admin', 'baspa' ),
					'id'          => 'offer_type',
					'type'        => 'select',
					'placeholder' => esc_html_x( 'Select a Type', 'admin', 'baspa' ),
					'options'     => array(
						'spring' => esc_html_x( 'Spring', 'admin', 'baspa' ),
						'summer' => esc_html_x( 'Summer', 'admin', 'baspa' ),
						'autumn' => esc_html_x( 'Autumn', 'admin', 'baspa' ),
						'winter' => esc_html_x( 'Winter', 'admin', 'baspa' ),
					),
				),
				array(
					'name' => esc_html__( 'Custom Type', 'baspa' ),
					'id'   => 'offer_type_custom',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'Short Title', 'baspa' ),
					'id'   => 'offer_title_short',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'Title', 'baspa' ),
					'id'   => 'offer_title',
					'type' => 'textarea',
				),
				array(
					'name'    => esc_html__( 'Description', 'baspa' ),
					'id'      => 'offer_description',
					'type'    => 'wysiwyg',
					'options' => array(
						'textarea_rows' => 5,
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
					'name'        => esc_html_x( 'Display for', 'admin', 'baspa' ),
					'id'          => 'offer_display',
					'type'        => 'select',
					'placeholder' => esc_html_x( 'Select a Type', 'admin', 'baspa' ),
					'options'     => array(
						'default'  => esc_html_x( 'Default', 'admin', 'baspa' ),
						'pools'    => esc_html_x( 'Pools', 'admin', 'baspa' ),
						'jacuzzis' => esc_html_x( 'Jacuzzis', 'admin', 'baspa' ),
					),
				),
				array(
					'name'    => esc_html__( 'Contact', 'baspa' ),
					'id'      => 'offer_contact',
					'type'    => 'select',
					'options' => array(
						'none'     => esc_html__( 'None', 'baspa' ),
						'default'  => esc_html__( 'Default', 'baspa' ),
						'pools'    => esc_html__( 'Pools', 'baspa' ),
						'jacuzzis' => esc_html__( 'Jacuzzis', 'baspa' ),
					),
				),
				array(
					'type' => 'heading',
					'name' => esc_html_x( 'Button', 'admin', 'baspa' ),
				),
				array(
					'name'              => esc_html_x( 'Button Text', 'admin', 'baspa' ),
					'id'                => 'offer_button_text',
					'type'              => 'text',
					'sanitize_callback' => 'wp_kses_post',
				),
				array(
					'name' => esc_html_x( 'Button URL', 'admin', 'baspa' ),
					'id'   => 'offer_button_url',
					'type' => 'url',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_offers_metabox_register' );

}
