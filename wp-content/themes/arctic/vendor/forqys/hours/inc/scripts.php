<?php

/**
 * Scripts
 *
 * @since 1.0.0
 */

if ( !function_exists( 'forqy_hours_scripts' ) ) {

	function forqy_hours_scripts(): void {

		$hours = apply_filters( 'forqy_hours', __return_empty_array() );

		if ( !empty( $hours ) ) {

			// Register
			wp_register_script( get_template() . '-hours', str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/dist/js/hours.js' ), array(), '1.0.0', array(
				'in_footer' => true,
				'strategy'  => 'defer',
			) );

			// Localize
			wp_localize_script( get_template() . '-hours', 'hours_data', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'hours'    => $hours,
			) );

			// Enqueue
			wp_enqueue_script( get_template() . '-hours' );

		}

	}

	add_action( 'wp_enqueue_scripts', 'forqy_hours_scripts', 20 );

}
