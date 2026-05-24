<?php

/**
 * Images
 */

if ( !function_exists( 'baspa_images_size_names_choose' ) ) {

	/**
	 * Add Image Sizes to the Media Uploader and the Gutenberg
	 *
	 * @param array<string> $sizes
	 *
	 * @return array<string>
	 */
	function baspa_images_size_names_choose( array $sizes ): array {

		return array_merge( $sizes, array(
			get_template() . '-medium' => esc_html_x( 'Medium', 'image size', 'baspa' ),
			get_template() . '-large'  => esc_html_x( 'Large', 'image size', 'baspa' ),
			get_template() . '-huge'   => esc_html_x( 'Huge', 'image size', 'baspa' ),
		) );

	}

	add_filter( 'image_size_names_choose', 'baspa_images_size_names_choose' );

}

if ( !function_exists( 'baspa_images_jpeg_quality' ) ) {

	/**
	 * Set JPEG Quality
	 * 100 == Disable Compression
	 *
	 * @return int
	 */
	function baspa_images_jpeg_quality(): int {
		return 100;
	}

	add_filter( 'jpeg_quality', 'baspa_images_jpeg_quality' );
	add_filter( 'wp_editor_set_quality', 'baspa_images_jpeg_quality' );

}

if ( !function_exists( 'baspa_images_allow_svg' ) ) {

	/**
	 * Allow SVG Uploads only when explicitly enabled for trusted admins.
	 *
	 * @param array<string> $mimes
	 *
	 * @return array<string>
	 */
	function baspa_images_allow_svg( array $mimes ): array {

		unset( $mimes[ 'svg' ] );

		$allow_svg_uploads = apply_filters(
			'baspa_images_allow_svg_uploads',
			defined( 'ARCTIC_ALLOW_SVG_UPLOADS' ) && ARCTIC_ALLOW_SVG_UPLOADS
		);

		if ( $allow_svg_uploads && current_user_can( 'manage_options' ) ) {
			$mimes[ 'svg' ] = 'image/svg+xml';
		}

		return $mimes;
	}

	add_filter( 'upload_mimes', 'baspa_images_allow_svg' );

}
