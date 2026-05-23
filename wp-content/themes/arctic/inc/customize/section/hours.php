<?php

/**
 * Customize hours
 */

if ( !function_exists( 'baspa_customize_section_add_hours' ) ) {

	/**
	 * Add Section
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_section_add_hours( $wp_customize ): void {

		$wp_customize->add_section( 'hours', array(
			'title'    => esc_html__( 'Hours', 'baspa' ),
			'panel'    => 'theme',
			'priority' => 2,
		) );

	}

	add_action( 'customize_register', 'baspa_customize_section_add_hours' );

}

if ( !function_exists( 'baspa_customize_settings_add_hours' ) ) {

	/**
	 * Add Settings
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_settings_add_hours( $wp_customize ): void {

		$wp_customize->add_setting( 'baspa_hours_monday', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_hours_tuesday', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_hours_wednesday', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_hours_thursday', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_hours_friday', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_hours_saturday', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_hours_sunday', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

	}

	add_action( 'customize_register', 'baspa_customize_settings_add_hours' );

}

if ( !function_exists( 'baspa_customize_controls_add_hours' ) ) {

	/**
	 * Add Controls
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_controls_add_hours( $wp_customize ): void {

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_hours_monday', array(
			'label'    => esc_html__( 'Monday', 'baspa' ),
			'description'    => esc_html__( 'Required format', 'baspa' ) . ': 8AM-5PM, 8-17, 08:00-17:00, 8:00-17:00. ' . esc_html__( 'Multiple intervals separated by comma.', 'baspa' ),
			'settings' => 'baspa_hours_monday',
			'type'     => 'text',
			'section'  => 'hours',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_hours_tuesday', array(
			'label'    => esc_html__( 'Tuesday', 'baspa' ),
			'settings' => 'baspa_hours_tuesday',
			'type'     => 'text',
			'section'  => 'hours',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_hours_wednesday', array(
			'label'    => esc_html__( 'Wednesday', 'baspa' ),
			'settings' => 'baspa_hours_wednesday',
			'type'     => 'text',
			'section'  => 'hours',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_hours_thursday', array(
			'label'    => esc_html__( 'Thursday', 'baspa' ),
			'settings' => 'baspa_hours_thursday',
			'type'     => 'text',
			'section'  => 'hours',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_hours_friday', array(
			'label'    => esc_html__( 'Friday', 'baspa' ),
			'settings' => 'baspa_hours_friday',
			'type'     => 'text',
			'section'  => 'hours',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_hours_saturday', array(
			'label'    => esc_html__( 'Saturday', 'baspa' ),
			'settings' => 'baspa_hours_saturday',
			'type'     => 'text',
			'section'  => 'hours',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_hours_sunday', array(
			'label'    => esc_html__( 'Sunday', 'baspa' ),
			'settings' => 'baspa_hours_sunday',
			'type'     => 'text',
			'section'  => 'hours',
		) ) );

	}

	add_action( 'customize_register', 'baspa_customize_controls_add_hours' );

}
