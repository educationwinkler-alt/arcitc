<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_pages_metabox_register' ) ) {

	function baspa_pages_metabox_current_post_id(): int {

		$candidates = array(
			$_GET['post'] ?? null,
			$_POST['post_ID'] ?? null,
			$_POST['ID'] ?? null,
			$_REQUEST['post'] ?? null,
			$_REQUEST['post_ID'] ?? null,
		);

		foreach ( $candidates as $candidate ) {
			if ( null === $candidate ) {
				continue;
			}

			$post_id = absint( wp_unslash( $candidate ) );
			if ( $post_id > 0 ) {
				return $post_id;
			}
		}

		return 0;

	}

	function baspa_pages_metabox_current_template( int $post_id ): string {

		$posted_template = isset( $_POST['page_template'] ) ? sanitize_text_field( wp_unslash( $_POST['page_template'] ) ) : '';
		if ( '' !== $posted_template ) {
			return $posted_template;
		}

		return $post_id > 0 ? (string) get_post_meta( $post_id, '_wp_page_template', true ) : '';

	}

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_pages_metabox_register( array $meta_boxes ): array {

		$page_id       = baspa_pages_metabox_current_post_id();
		$page_template = baspa_pages_metabox_current_template( $page_id );

		if ( 'template-homepage.php' === $page_template ) {

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
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Homepage Benefits', 'admin', 'baspa' ),
					),
					array(
						'name'       => esc_html_x( 'Benefit', 'admin', 'baspa' ),
						'id'         => 'homepage_benefits',
						'type'       => 'fieldset_text',
						'clone'      => true,
						'sort_clone' => true,
						'add_button' => esc_html_x( '+ Add Benefit', 'admin', 'baspa' ),
						'options'    => array(
							'title' => esc_html_x( 'Title', 'admin', 'baspa' ),
							'text'  => esc_html_x( 'Short text', 'admin', 'baspa' ),
							'icon'  => esc_html_x( 'Icon slug (box, support, service)', 'admin', 'baspa' ),
						),
					),
					array(
						'name'             => esc_html_x( 'Benefit Images', 'admin', 'baspa' ),
						'desc'             => esc_html_x( 'Images are matched to benefit rows by order.', 'admin', 'baspa' ),
						'id'               => 'homepage_benefit_images',
						'type'             => 'image_advanced',
						'image_size'       => 'thumbnail',
						'max_file_uploads' => 3,
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Homepage Showroom Section', 'admin', 'baspa' ),
					),
					array(
						'name' => esc_html_x( 'Showroom Title', 'admin', 'baspa' ),
						'id'   => 'homepage_showroom_title',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Showroom Text', 'admin', 'baspa' ),
						'id'   => 'homepage_showroom_text',
						'type' => 'textarea',
						'rows' => 3,
					),
					array(
						'name' => esc_html_x( 'Showroom Button Text', 'admin', 'baspa' ),
						'id'   => 'homepage_showroom_button_text',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Showroom Button URL', 'admin', 'baspa' ),
						'id'   => 'homepage_showroom_button_url',
						'type' => 'url',
					),
					array(
						'name' => esc_html_x( 'Showroom Address', 'admin', 'baspa' ),
						'id'   => 'homepage_showroom_address',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Showroom Badge Value', 'admin', 'baspa' ),
						'id'   => 'homepage_showroom_badge_value',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Showroom Badge Label', 'admin', 'baspa' ),
						'id'   => 'homepage_showroom_badge_label',
						'type' => 'text',
					),
					array(
						'name'             => esc_html_x( 'Showroom Images', 'admin', 'baspa' ),
						'desc'             => esc_html_x( 'The first three images build the homepage showroom collage.', 'admin', 'baspa' ),
						'id'               => 'homepage_showroom_images',
						'type'             => 'image_advanced',
						'image_size'       => 'thumbnail',
						'max_file_uploads' => 3,
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Homepage Progress Section', 'admin', 'baspa' ),
					),
					array(
						'name' => esc_html_x( 'Progress Title', 'admin', 'baspa' ),
						'id'   => 'homepage_progress_title',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Progress Text', 'admin', 'baspa' ),
						'id'   => 'homepage_progress_text',
						'type' => 'textarea',
						'rows' => 3,
					),
					array(
						'name'       => esc_html_x( 'Progress Step', 'admin', 'baspa' ),
						'id'         => 'homepage_progress_steps',
						'type'       => 'fieldset_text',
						'clone'      => true,
						'sort_clone' => true,
						'add_button' => esc_html_x( '+ Add Step', 'admin', 'baspa' ),
						'options'    => array(
							'title' => esc_html_x( 'Title', 'admin', 'baspa' ),
							'text'  => esc_html_x( 'Text', 'admin', 'baspa' ),
						),
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
						'name' => esc_html_x( 'Hero media', 'admin', 'baspa' ),
					),
					array(
						'name'    => esc_html_x( 'Media type', 'admin', 'baspa' ),
						'desc'    => esc_html_x( 'Image uses the featured image. Video uses a self-hosted MP4/WebM from the Media Library and keeps the featured image as fallback.', 'admin', 'baspa' ),
						'id'      => 'page_hero_media_type',
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
						'id'               => 'page_hero_video',
						'type'             => 'file_advanced',
						'mime_type'        => 'video/mp4,video/webm,video/quicktime',
						'max_file_uploads' => 1,
					),
					array(
						'name'             => esc_html_x( 'Video poster / fallback image', 'admin', 'baspa' ),
						'desc'             => esc_html_x( 'Used before the video loads, on reduced-motion devices and as visual fallback. If empty, the featured image is used.', 'admin', 'baspa' ),
						'id'               => 'page_hero_poster_image',
						'type'             => 'image_advanced',
						'image_size'       => 'thumbnail',
						'max_file_uploads' => 1,
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

		if ( 'template-showroom.php' === $page_template ) {
			$meta_boxes[] = array(
				'id'         => 'arctic-metabox--showroom',
				'title'      => esc_html_x( 'Showroom', 'admin', 'baspa' ),
				'post_types' => array( 'page' ),
				'priority'   => 'high',
				'fields'     => array(
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Hero and badge', 'admin', 'baspa' ),
					),
					array(
						'name' => esc_html_x( 'Gallery Button Text', 'admin', 'baspa' ),
						'id'   => 'showroom_gallery_button_text',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Area Value', 'admin', 'baspa' ),
						'id'   => 'showroom_area_value',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Area Label Line 1', 'admin', 'baspa' ),
						'id'   => 'showroom_area_label_1',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Area Label Line 2', 'admin', 'baspa' ),
						'id'   => 'showroom_area_label_2',
						'type' => 'text',
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Mini CTA', 'admin', 'baspa' ),
					),
					array(
						'name' => esc_html_x( 'Mini CTA Title', 'admin', 'baspa' ),
						'id'   => 'showroom_mini_cta_title',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Mini CTA Text', 'admin', 'baspa' ),
						'id'   => 'showroom_mini_cta_text',
						'type' => 'textarea',
						'rows' => 3,
					),
					array(
						'name' => esc_html_x( 'Mini CTA Button Text', 'admin', 'baspa' ),
						'id'   => 'showroom_mini_cta_button_text',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Mini CTA Button URL', 'admin', 'baspa' ),
						'id'   => 'showroom_mini_cta_button_url',
						'type' => 'url',
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Reasons', 'admin', 'baspa' ),
					),
					array(
						'name' => esc_html_x( 'Reasons Heading', 'admin', 'baspa' ),
						'id'   => 'showroom_reasons_heading',
						'type' => 'text',
					),
					array(
						'name'       => esc_html_x( 'Reason', 'admin', 'baspa' ),
						'id'         => 'showroom_reasons',
						'type'       => 'fieldset_text',
						'clone'      => true,
						'sort_clone' => true,
						'add_button' => esc_html_x( '+ Add Reason', 'admin', 'baspa' ),
						'options'    => array(
							'icon' => esc_html_x( 'Icon slug (pool, road, parking, coffee)', 'admin', 'baspa' ),
							'text' => esc_html_x( 'Text', 'admin', 'baspa' ),
						),
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Content Sections and Gallery', 'admin', 'baspa' ),
					),
					array(
						'name' => esc_html_x( 'Primary Section Title', 'admin', 'baspa' ),
						'id'   => 'showroom_primary_title',
						'type' => 'text',
					),
					array(
						'name' => esc_html_x( 'Secondary Section Content', 'admin', 'baspa' ),
						'id'   => 'showroom_secondary_content',
						'type' => 'wysiwyg',
						'options' => array(
							'media_buttons' => false,
							'textarea_rows' => 4,
							'teeny'         => true,
							'tinymce'       => array(
								'toolbar1' => 'bold,italic,link,unlink,bullist,undo,redo',
								'toolbar2' => '',
								'toolbar3' => '',
							),
						),
					),
					array(
						'name'             => esc_html_x( 'Gallery Images', 'admin', 'baspa' ),
						'desc'             => esc_html_x( 'The first two images are used by the two showroom content sections.', 'admin', 'baspa' ),
						'id'               => 'showroom_gallery_images',
						'type'             => 'image_advanced',
						'image_size'       => 'thumbnail',
						'max_file_uploads' => 12,
					),
				),
			);
		}

		if ( 'template-certificates.php' === $page_template ) {
			$meta_boxes[] = array(
				'id'         => 'arctic-metabox--certificates',
				'title'      => esc_html_x( 'Certificates', 'admin', 'baspa' ),
				'post_types' => array( 'page' ),
				'priority'   => 'high',
				'fields'     => array(
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Copy Sections', 'admin', 'baspa' ),
					),
					array(
						'name'       => esc_html_x( 'Section', 'admin', 'baspa' ),
						'id'         => 'certificates_sections',
						'type'       => 'fieldset_text',
						'clone'      => true,
						'sort_clone' => true,
						'add_button' => esc_html_x( '+ Add Section', 'admin', 'baspa' ),
						'options'    => array(
							'title' => esc_html_x( 'Title', 'admin', 'baspa' ),
							'text'  => esc_html_x( 'Text', 'admin', 'baspa' ),
						),
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Certificate Images', 'admin', 'baspa' ),
					),
					array(
						'name'             => esc_html_x( 'Images', 'admin', 'baspa' ),
						'id'               => 'certificates_images',
						'type'             => 'image_advanced',
						'image_size'       => 'thumbnail',
						'max_file_uploads' => 12,
					),
				),
			);
		}

		if ( 'template-feature-detail.php' === $page_template ) {
			$meta_boxes[] = array(
				'id'         => 'arctic-metabox--feature-detail',
				'title'      => esc_html_x( 'Feature Detail', 'admin', 'baspa' ),
				'post_types' => array( 'page' ),
				'priority'   => 'high',
				'fields'     => array(
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Media', 'admin', 'baspa' ),
					),
					array(
						'name'             => esc_html_x( 'Hero Image', 'admin', 'baspa' ),
						'id'               => 'feature_detail_hero_images',
						'type'             => 'image_advanced',
						'image_size'       => 'thumbnail',
						'max_file_uploads' => 1,
					),
					array(
						'name'             => esc_html_x( 'Diagram Image', 'admin', 'baspa' ),
						'id'               => 'feature_detail_diagram_images',
						'type'             => 'image_advanced',
						'image_size'       => 'thumbnail',
						'max_file_uploads' => 1,
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Related Cards', 'admin', 'baspa' ),
					),
					array(
						'name' => esc_html_x( 'Related Heading', 'admin', 'baspa' ),
						'id'   => 'feature_detail_related_heading',
						'type' => 'text',
					),
				),
			);
		}

		if ( 'template-warranty.php' === $page_template ) {
			$meta_boxes[] = array(
				'id'         => 'arctic-metabox--warranty',
				'title'      => esc_html_x( 'Warranty Matrix', 'admin', 'baspa' ),
				'post_types' => array( 'page' ),
				'priority'   => 'high',
				'fields'     => array(
					array(
						'name'       => esc_html_x( 'Warranty Tier', 'admin', 'baspa' ),
						'id'         => 'warranty_tiers',
						'type'       => 'fieldset_text',
						'clone'      => true,
						'sort_clone' => true,
						'add_button' => esc_html_x( '+ Add Warranty Tier', 'admin', 'baspa' ),
						'options'    => array(
							'name'    => esc_html_x( 'Series name', 'admin', 'baspa' ),
							'shell'   => esc_html_x( 'Shell', 'admin', 'baspa' ),
							'acrylic' => esc_html_x( 'Acrylic', 'admin', 'baspa' ),
							'floor'   => esc_html_x( 'Floor', 'admin', 'baspa' ),
							'parts'   => esc_html_x( 'Replacement parts', 'admin', 'baspa' ),
							'labor'   => esc_html_x( 'Labor', 'admin', 'baspa' ),
						),
					),
					array(
						'name'    => esc_html_x( 'Warranty Note', 'admin', 'baspa' ),
						'id'      => 'warranty_note',
						'type'    => 'wysiwyg',
						'options' => array(
							'media_buttons' => false,
							'textarea_rows' => 5,
							'teeny'         => true,
							'tinymce'       => array(
								'toolbar1' => 'bold,italic,link,unlink,undo,redo',
								'toolbar2' => '',
								'toolbar3' => '',
							),
						),
					),
				),
			);
		}

		if ( 'template-about.php' === $page_template ) {
			$meta_boxes[] = array(
				'id'         => 'arctic-metabox--about',
				'title'      => esc_html_x( 'About Page', 'admin', 'baspa' ),
				'post_types' => array( 'page' ),
				'priority'   => 'high',
				'fields'     => array(
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Intro', 'admin', 'baspa' ),
					),
					array(
						'name' => esc_html_x( 'Intro Title', 'admin', 'baspa' ),
						'id'   => 'about_intro_title',
						'type' => 'text',
					),
					array(
						'type' => 'heading',
						'name' => esc_html_x( 'Statistics', 'admin', 'baspa' ),
					),
					array(
						'name'       => esc_html_x( 'Statistic', 'admin', 'baspa' ),
						'id'         => 'about_stats',
						'type'       => 'fieldset_text',
						'clone'      => true,
						'sort_clone' => true,
						'add_button' => esc_html_x( '+ Add Statistic', 'admin', 'baspa' ),
						'options'    => array(
							'value' => esc_html_x( 'Value', 'admin', 'baspa' ),
							'label' => esc_html_x( 'Label', 'admin', 'baspa' ),
						),
					),
				),
			);
		}

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_pages_metabox_register' );

}
