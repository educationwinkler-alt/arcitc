<?php

/**
 * Icon
 *
 * @package    forqys/icon
 * @since      1.0.0
 */

if ( !function_exists( 'forqy_get_icon' ) ) {

	/**
	 * Get Icon
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	function forqy_get_icon( string $slug ): string {

		ob_start();
		get_template_part( 'vendor/forqys/icon/template/' . esc_attr( $slug ) );

		return ob_get_contents();

	}

}
