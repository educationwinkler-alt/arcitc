<?php

/**
 * Feature metabox.
 */

if ( !function_exists( 'arctic_feature_metabox_register' ) ) {

	/**
	 * Register feature edit fields.
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function arctic_feature_metabox_register( array $meta_boxes ): array {

		$meta_boxes[] = array(
			'id'         => 'arctic-metabox--feature',
			'title'      => esc_html_x( 'Feature Card', 'admin', 'baspa' ),
			'post_types' => array( 'feature' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name' => esc_html_x( 'Card Anchor', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Used for same-page links when no detail page or custom URL is selected.', 'admin', 'baspa' ),
					'id'   => 'feature_card_anchor',
					'type' => 'text',
				),
				array(
					'name'        => esc_html_x( 'Detail Page', 'admin', 'baspa' ),
					'desc'        => esc_html_x( 'The card links to this WordPress page. Leave empty when using a custom URL.', 'admin', 'baspa' ),
					'id'          => 'feature_detail_page_id',
					'type'        => 'select',
					'placeholder' => esc_html_x( 'Select a Page', 'admin', 'baspa' ),
					'options'     => function_exists( 'arctic_features_get_page_options' ) ? arctic_features_get_page_options() : array(),
				),
				array(
					'name' => esc_html_x( 'Custom URL', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Overrides the detail page link when filled.', 'admin', 'baspa' ),
					'id'   => 'feature_custom_url',
					'type' => 'url',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'arctic_feature_metabox_register' );

}
