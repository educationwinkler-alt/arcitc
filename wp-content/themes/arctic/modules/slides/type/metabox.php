<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_slides_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_slides_metabox_register( array $meta_boxes ): array {

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--slide',
			'title'      => esc_html__( 'Slide', 'baspa' ),
			'post_types' => array( 'slide' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'type' => 'heading',
					'name' => esc_html_x( 'Hero media', 'admin', 'baspa' ),
				),
				array(
					'name'    => esc_html_x( 'Media type', 'admin', 'baspa' ),
					'desc'    => esc_html_x( 'Image uses the featured image. Video uses a self-hosted MP4/WebM from the Media Library and keeps the featured image as fallback.', 'admin', 'baspa' ),
					'id'      => 'slide_hero_media_type',
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
					'id'               => 'slide_hero_video',
					'type'             => 'file_advanced',
					'mime_type'        => 'video/mp4,video/webm,video/quicktime',
					'max_file_uploads' => 1,
				),
				array(
					'name'             => esc_html_x( 'Video poster / fallback image', 'admin', 'baspa' ),
					'desc'             => esc_html_x( 'Used before the video loads, on reduced-motion devices and as visual fallback. If empty, the featured image is used.', 'admin', 'baspa' ),
					'id'               => 'slide_hero_poster_image',
					'type'             => 'image_advanced',
					'image_size'       => 'thumbnail',
					'max_file_uploads' => 1,
				),
				array(
					'name'              => esc_html_x( 'Button Text', 'admin', 'baspa' ),
					'id'                => 'button_text',
					'type'              => 'text',
					'sanitize_callback' => 'wp_kses_post',
				),
				array(
					'type' => 'heading',
					'name' => esc_html_x( 'Button URL', 'admin', 'baspa' ),
				),
				array(
					'name'        => esc_html_x( 'Button URL', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Product', 'admin', 'baspa' ),
					'id'          => 'button_url_post',
					'type'        => 'post',
					'post_type'   => 'product',
					'placeholder' => esc_html_x( 'Select a Product', 'admin', 'baspa' ),
				),
				array(
					'name'        => esc_html_x( 'Button URL', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Product Category', 'admin', 'baspa' ),
					'id'          => 'button_url_category',
					'type'        => 'taxonomy_advanced',
					'taxonomy'    => array( 'product-category' ),
					'placeholder' => esc_html_x( 'Select a Product Category', 'admin', 'baspa' ),
				),
				array(
					'name' => esc_html_x( 'Button URL', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Custom', 'admin', 'baspa' ),
					'id'   => 'button_url',
					'type' => 'url',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_slides_metabox_register' );

}
