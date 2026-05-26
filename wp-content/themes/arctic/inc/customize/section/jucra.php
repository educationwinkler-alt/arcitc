<?php

/**
 * Customize Jucra
 */

if ( !function_exists( 'baspa_customize_section_add_jucra' ) ) {

	/**
	 * Add Section.
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function baspa_customize_section_add_jucra( WP_Customize_Manager $wp_customize ): void {

		$wp_customize->add_section( 'jucra', array(
			'title'       => esc_html__( 'Jucra Configurator', 'baspa' ),
			'description' => esc_html__( 'Controls fallback and model defaults for Visao 3D Viewer integration.', 'baspa' ),
			'panel'       => 'theme',
			'priority'    => 11,
		) );

	}

	add_action( 'customize_register', 'baspa_customize_section_add_jucra' );

}

if ( !function_exists( 'baspa_customize_settings_add_jucra' ) ) {

	/**
	 * Add Settings.
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function baspa_customize_settings_add_jucra( WP_Customize_Manager $wp_customize ): void {

		$wp_customize->add_setting( 'arctic_jucra_enabled', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_setting( 'arctic_jucra_default_model', array(
			'default'           => 'Summit',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'arctic_jucra_pricing_relative_url', array(
			'default'           => '/kontakt/',
			'sanitize_callback' => 'arctic_jucra_sanitize_relative_path',
		) );

	}

	add_action( 'customize_register', 'baspa_customize_settings_add_jucra' );

}

if ( !function_exists( 'baspa_customize_controls_add_jucra' ) ) {

	/**
	 * Add Controls.
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function baspa_customize_controls_add_jucra( WP_Customize_Manager $wp_customize ): void {

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_enabled', array(
			'label'    => esc_html__( 'Enable Jucra inline viewer', 'baspa' ),
			'settings' => 'arctic_jucra_enabled',
			'type'     => 'checkbox',
			'section'  => 'jucra',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_default_model', array(
			'label'       => esc_html__( 'Default model name', 'baspa' ),
			'description' => esc_html__( 'Used when a specific product does not define jucra_model_name.', 'baspa' ),
			'settings'    => 'arctic_jucra_default_model',
			'type'        => 'text',
			'section'     => 'jucra',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_pricing_relative_url', array(
			'label'       => esc_html__( 'Pricing form relative URL', 'baspa' ),
			'description' => esc_html__( 'Keep relative path only (example: /3d-pricing-form/).', 'baspa' ),
			'settings'    => 'arctic_jucra_pricing_relative_url',
			'type'        => 'text',
			'section'     => 'jucra',
		) ) );

	}

	add_action( 'customize_register', 'baspa_customize_controls_add_jucra' );

}
