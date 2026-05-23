<?php

/**
 * Downloads Metabox
 */

if ( !function_exists( 'arctic_downloads_metabox_register' ) ) {

	function arctic_downloads_metabox_register( array $meta_boxes ): array {

		$meta_boxes[] = array(
			'id'         => 'arctic-metabox--download',
			'title'      => esc_html_x( 'Download', 'admin', 'baspa' ),
			'post_types' => array( 'download' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name'      => esc_html_x( 'File URL', 'admin', 'baspa' ),
					'id'        => 'download_file_url',
					'type'      => 'file_input',
					'mime_type' => 'application/pdf',
				),
				array(
					'name' => esc_html_x( 'Original URL', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Source URL from the old Arctic website for migration and redirects.', 'admin', 'baspa' ),
					'id'   => 'download_original_url',
					'type' => 'url',
				),
				array(
					'name'    => esc_html_x( 'Document Type', 'admin', 'baspa' ),
					'id'      => 'download_document_type',
					'type'    => 'select',
					'options' => array(
						'catalog'      => esc_html_x( 'Catalog', 'download type', 'baspa' ),
						'manual'       => esc_html_x( 'Manual', 'download type', 'baspa' ),
						'dimensions'   => esc_html_x( 'Dimensions', 'download type', 'baspa' ),
						'warranty'     => esc_html_x( 'Warranty', 'download type', 'baspa' ),
						'preparation'  => esc_html_x( 'Site preparation', 'download type', 'baspa' ),
						'water-care'   => esc_html_x( 'Water care', 'download type', 'baspa' ),
						'service'      => esc_html_x( 'Service', 'download type', 'baspa' ),
						'other'        => esc_html_x( 'Other', 'download type', 'baspa' ),
					),
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'arctic_downloads_metabox_register' );

}
