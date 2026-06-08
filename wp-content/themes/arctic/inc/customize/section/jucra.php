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

		$wp_customize->add_setting( 'arctic_jucra_model_definitions', array(
			'default'           => function_exists( 'arctic_jucra_model_definitions_to_text' ) ? arctic_jucra_model_definitions_to_text() : '',
			'sanitize_callback' => function_exists( 'arctic_jucra_sanitize_model_definitions_text' ) ? 'arctic_jucra_sanitize_model_definitions_text' : 'sanitize_textarea_field',
		) );

		$wp_customize->add_setting( 'arctic_jucra_pricing_relative_url', array(
			'default'           => '/poptavka-konfigurace/',
			'sanitize_callback' => 'arctic_jucra_sanitize_relative_path',
		) );

		$wp_customize->add_setting( 'arctic_jucra_gravity_form_id', array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_setting( 'arctic_jucra_gravity_field_id', array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_setting( 'arctic_jucra_hide_version_section', array(
			'default'           => 1,
			'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_setting( 'arctic_jucra_hide_pricing_button', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
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

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_model_definitions', array(
			'label'       => esc_html__( 'Builder model list', 'baspa' ),
			'description' => esc_html__( 'One model per line: slug|Label|Jucra model name|product-slug. Order here controls the frontend selector.', 'baspa' ),
			'settings'    => 'arctic_jucra_model_definitions',
			'type'        => 'textarea',
			'section'     => 'jucra',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_pricing_relative_url', array(
			'label'       => esc_html__( 'Pricing form relative URL', 'baspa' ),
			'description' => esc_html__( 'Keep relative path only (default: /poptavka-konfigurace/).', 'baspa' ),
			'settings'    => 'arctic_jucra_pricing_relative_url',
			'type'        => 'text',
			'section'     => 'jucra',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_gravity_form_id', array(
			'label'       => esc_html__( 'Gravity Forms ID', 'baspa' ),
			'description' => esc_html__( 'Matches the Visao plugin setting when the request form is handled by Gravity Forms.', 'baspa' ),
			'settings'    => 'arctic_jucra_gravity_form_id',
			'type'        => 'number',
			'section'     => 'jucra',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_gravity_field_id', array(
			'label'       => esc_html__( 'Gravity Forms Field ID', 'baspa' ),
			'description' => esc_html__( 'Field that receives model and option data from the builder.', 'baspa' ),
			'settings'    => 'arctic_jucra_gravity_field_id',
			'type'        => 'number',
			'section'     => 'jucra',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_hide_version_section', array(
			'label'    => esc_html__( 'Hide Visao version section', 'baspa' ),
			'settings' => 'arctic_jucra_hide_version_section',
			'type'     => 'checkbox',
			'section'  => 'jucra',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_jucra_hide_pricing_button', array(
			'label'    => esc_html__( 'Hide plugin pricing button', 'baspa' ),
			'settings' => 'arctic_jucra_hide_pricing_button',
			'type'     => 'checkbox',
			'section'  => 'jucra',
		) ) );

	}

	add_action( 'customize_register', 'baspa_customize_controls_add_jucra' );

}
