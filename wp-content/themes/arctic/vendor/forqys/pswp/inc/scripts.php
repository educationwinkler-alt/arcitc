<?php

/**
 * Scripts
 *
 * @package forqys/photoswipe
 * @since   1.0.0
 */

if ( !function_exists( 'forqy_photoswipe_scripts' ) ) {

	function forqy_photoswipe_scripts() {

		if ( !wp_script_is( get_template() . '-pswp', 'enqueued' ) && ( apply_filters( 'forqy_pswp', '__return_true' ) || apply_filters( 'forqy_pswp_content', '__return_true' ) ) ) {

			$translations = apply_filters( 'forqy_translations', '__return_empty_array' );

			/**
			 * PhotoSwipe
			 */

			// Register
			wp_register_script( get_template() . '-pswp', str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/dist/js/pswp.js' ), array(), '5.4.4', array(
				'in_footer' => true,
				'strategy'  => 'async',
			) );

			// Localize
			wp_localize_script( get_template() . '-pswp', 'pswp_settings', array(
				'text_close'     => $translations[ 'pswp_close' ] ? esc_js( $translations[ 'pswp_close' ] ) : esc_js( _x( 'Close', 'pswp' ) ),
				'text_prev'      => $translations[ 'pswp_prev' ] ? esc_js( $translations[ 'pswp_prev' ] ) : esc_js( _x( 'Previous', 'pswp' ) ),
				'text_next'      => $translations[ 'pswp_next' ] ? esc_js( $translations[ 'pswp_next' ] ) : esc_js( _x( 'Next', 'pswp' ) ),
				'text_zoom'      => $translations[ 'pswp_zoom' ] ? esc_js( $translations[ 'pswp_zoom' ] ) : esc_js( _x( 'Zoom', 'pswp' ) ),
				'text_error'     => $translations[ 'pswp_error' ] ? esc_js( $translations[ 'pswp_error' ] ) : esc_js( _x( 'Unfortunately, the item could not be loaded.', 'pswp' ) ),
				'text_separator' => $translations[ 'pswp_separator' ] ? esc_js( $translations[ 'pswp_separator' ] ) : esc_js( _x( ' / ', 'pswp' ) ),
			) );

			// Enqueue
			wp_enqueue_script( get_template() . '-pswp' );

		}

	}

	add_action( 'wp_enqueue_scripts', 'forqy_photoswipe_scripts', 50 );

}
