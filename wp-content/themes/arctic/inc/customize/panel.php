<?php

/**
 * Customize Panel
 */

if ( ! function_exists( 'baspa_customize_panel_add' ) ) {

	/**
	 * Add Panels
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function baspa_customize_panel_add( WP_Customize_Manager $wp_customize ): void {
		
		$wp_customize->add_panel( 'theme', array(
			'title'    => wp_get_theme()->get( 'Name' ),
			'priority' => 1,
		) );
		
	}

	add_action( 'customize_register', 'baspa_customize_panel_add' );

}
