<?php

/**
 * Customize Ecomail
 */

if ( !function_exists( 'baspa_customize_section_add_ecomail' ) ) {

	/**
	 * Add Section
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_section_add_ecomail( $wp_customize ): void {

		$wp_customize->add_section( 'ecomail', array(
			'title'    => esc_html__( 'Ecomail', 'baspa' ),
			'panel'    => 'theme',
			'priority' => 4,
		) );

	}

	add_action( 'customize_register', 'baspa_customize_section_add_ecomail' );

}

if ( !function_exists( 'baspa_customize_settings_add_ecomail' ) ) {

	/**
	 * Add Settings
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_settings_add_ecomail( $wp_customize ): void {

		$wp_customize->add_setting( 'baspa_ecomail_api_key', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_ecomail_list_id', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

	}

	add_action( 'customize_register', 'baspa_customize_settings_add_ecomail' );

}

if ( !function_exists( 'baspa_customize_controls_add_ecomail' ) ) {

	/**
	 * Add Controls
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_controls_add_ecomail( $wp_customize ): void {

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_ecomail_api_key', array(
			'label'    => esc_html__( 'API Key', 'baspa' ),
			'settings' => 'baspa_ecomail_api_key',
			'type'     => 'text',
			'section'  => 'ecomail',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_ecomail_list_id', array(
			'label'    => esc_html__( 'List ID', 'baspa' ),
			'settings' => 'baspa_ecomail_list_id',
			'type'     => 'number',
			'section'  => 'ecomail',
		) ) );

	}

	add_action( 'customize_register', 'baspa_customize_controls_add_ecomail' );

}
