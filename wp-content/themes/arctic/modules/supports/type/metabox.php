<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_supports_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_supports_metabox_register( array $meta_boxes ): array {

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--support',
			'title'      => esc_html__( 'Support', 'baspa' ),
			'post_types' => array( 'support' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name'       => esc_html__( 'Downloads', 'baspa' ),
					'id'         => 'support_downloads',
					'type'       => 'file_advanced',
					'mime_type'  => 'application/pdf',
					'max_status' => false,
				),
				array(
					'name'       => esc_html__( 'Display also in the price list?', 'baspa' ),
					'std'        => 'yes',
					'id'         => 'support_display_pricelist',
					'type'       => 'checkbox',
				),
				array(
					'name'       => esc_html__( 'Display only in the price list?', 'baspa' ),
					'id'         => 'support_display_pricelist_only',
					'type'       => 'checkbox',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_supports_metabox_register' );

}
