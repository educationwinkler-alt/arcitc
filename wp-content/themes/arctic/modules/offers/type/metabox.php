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
		$member_options = array(
			0 => esc_html__( 'Use global Offer/sidebar member', 'baspa' ),
		);
		$member_posts   = get_posts( array(
			'post_type'      => 'member',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		) );

		foreach ( $member_posts as $member_post ) {
			$member_options[ (int) $member_post->ID ] = get_the_title( $member_post );
		}

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
					'name'             => esc_html_x( 'Promo Image', 'admin', 'baspa' ),
					'desc'             => esc_html_x( 'Small product image used in the homepage and mega-menu promo cards. If empty, the featured image is used.', 'admin', 'baspa' ),
					'id'               => 'offer_promo_image_id',
					'type'             => 'image_advanced',
					'max_file_uploads' => 1,
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
					'type' => 'heading',
					'name' => esc_html_x( 'Offer Details', 'admin', 'baspa' ),
				),
				array(
					'name' => esc_html_x( 'Status', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Example: Skladem, Na cestě z Kanady, Showroomový kus.', 'admin', 'baspa' ),
					'id'   => 'offer_status',
					'type' => 'text',
				),
				array(
					'name' => esc_html_x( 'Discount', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Example: Sleva 120 000 Kč or Individuální zvýhodnění.', 'admin', 'baspa' ),
					'id'   => 'offer_discount',
					'type' => 'text',
				),
				array(
					'name' => esc_html_x( 'Price', 'admin', 'baspa' ),
					'id'   => 'offer_price',
					'type' => 'text',
				),
				array(
					'name' => esc_html_x( 'Original Price', 'admin', 'baspa' ),
					'id'   => 'offer_old_price',
					'type' => 'text',
				),
				array(
					'name' => esc_html_x( 'Valid Until', 'admin', 'baspa' ),
					'id'   => 'offer_valid_until',
					'type' => 'date',
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
					'name'    => esc_html__( 'Contact member', 'baspa' ),
					'desc'    => esc_html__( 'Overrides the global Offer/sidebar member for this offer detail. Leave empty to use Members -> Settings.', 'baspa' ),
					'id'      => 'offer_contact_member_id',
					'type'    => 'select',
					'options' => $member_options,
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
