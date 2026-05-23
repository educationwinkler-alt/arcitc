<?php

/**
 * Scripts
 *
 * @package forqys/photoswipe
 * @since   1.0.0
 */

if ( !function_exists( 'forqy_photoswipe_scripts' ) ) {

	function forqy_photoswipe_scripts() {

		if ( !wp_script_is( get_template() . '-images', 'enqueued' ) && ( apply_filters( 'forqy_image_photoswipe', '__return_true' ) || apply_filters( 'forqy_image_photoswipe_content', '__return_true' ) ) ) {

			/**
			 * PhotoSwipe
			 * @url https://github.com/dimsemenov/PhotoSwipe
			 * @source https://github.com/dimsemenov/PhotoSwipe/blob/master/dist/photoswipe.js
			 * @source https://github.com/dimsemenov/PhotoSwipe/blob/master/dist/photoswipe-ui-default.js
			 */
			wp_enqueue_script( 'photoswipe', str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/js/lib/photoswipe.js' ), array(
				'jquery',
			), '4.1.3', true );
			wp_enqueue_script( 'photoswipe-ui-default', str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/js/lib/photoswipe-ui-default.js' ), array(
				'jquery',
				'photoswipe',
			), '4.1.3', true );

			/**
			 * CORE Images
			 */
			wp_enqueue_script( get_template() . '-images', str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/js/images.js' ), array(
				'jquery',
				'photoswipe',
				'photoswipe-ui-default',
			), '1.0.0', true );

		}

	}

	add_action( 'wp_enqueue_scripts', 'forqy_photoswipe_scripts', 50 );

}
