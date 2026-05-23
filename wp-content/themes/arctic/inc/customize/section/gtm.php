<?php

/**
 * Customize GTM
 */

if ( ! function_exists( 'baspa_customize_section_add_gtm' ) ) {

	/**
	 * Add Section
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_section_add_gtm( $wp_customize ): void {

		$wp_customize->add_section( 'gtm', array(
			'title'    => esc_html__( 'GTM', 'baspa' ),
			'panel'    => 'theme',
			'priority' => 10,
		) );

	}

	add_action( 'customize_register', 'baspa_customize_section_add_gtm' );

}

if ( ! function_exists( 'baspa_customize_settings_add_gtm' ) ) {

	/**
	 * Add Settings
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_settings_add_gtm( $wp_customize ): void {

		$wp_customize->add_setting( 'baspa_gtm', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

	}

	add_action( 'customize_register', 'baspa_customize_settings_add_gtm' );

}

if ( ! function_exists( 'baspa_customize_controls_add_gtm' ) ) {

	/**
	 * Add Controls
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_controls_add_gtm( $wp_customize ): void {

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_gtm', array(
			'label'    => esc_html__( 'GTM', 'baspa' ),
			'settings' => 'baspa_gtm',
			'type'     => 'text',
			'section'  => 'gtm',
		) ) );

	}

	add_action( 'customize_register', 'baspa_customize_controls_add_gtm' );

}
