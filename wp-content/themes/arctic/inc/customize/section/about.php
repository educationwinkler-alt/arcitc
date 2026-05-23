<?php

/**
 * Customize About
 */

if ( !function_exists( 'baspa_customize_section_add_about' ) ) {

	/**
	 * Add Section
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_section_add_about( $wp_customize ): void {

		$wp_customize->add_section( 'about', array(
			'title'    => esc_html__( 'About', 'baspa' ),
			'panel'    => 'theme',
			'priority' => 1,
		) );

	}

	add_action( 'customize_register', 'baspa_customize_section_add_about' );

}

if ( !function_exists( 'baspa_customize_settings_add_about' ) ) {

	/**
	 * Add Settings
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_settings_add_about( $wp_customize ): void {

		$wp_customize->add_setting( 'baspa_name', array(
			'default'           => esc_html__( 'Arctic Spas CZ', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_phone', array(
			'default'           => esc_html__( '+420 602 545 067', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_email', array(
			'default'           => esc_html__( 'info@arctic-spas.cz', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_street', array(
			'default'           => esc_html__( 'Bohunická cesta 15', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_zip', array(
			'default'           => esc_html__( '664 48', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_city', array(
			'default'           => esc_html__( 'Moravany u Brna', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_map', array(
			'default'           => esc_html__( 'https://maps.app.goo.gl/ZsYfoZ2aQGF1JnZG6', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

	}

	add_action( 'customize_register', 'baspa_customize_settings_add_about' );

}

if ( !function_exists( 'baspa_customize_controls_add_about' ) ) {

	/**
	 * Add Controls
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_controls_add_about( $wp_customize ): void {

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_name', array(
			'label'    => esc_html__( 'Name', 'baspa' ),
			'settings' => 'baspa_name',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_phone', array(
			'label'    => esc_html__( 'Phone', 'baspa' ),
			'settings' => 'baspa_phone',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_email', array(
			'label'    => esc_html__( 'Email', 'baspa' ),
			'settings' => 'baspa_email',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_street', array(
			'label'    => esc_html__( 'Street', 'baspa' ),
			'settings' => 'baspa_street',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_zip', array(
			'label'    => esc_html__( 'Zip', 'baspa' ),
			'settings' => 'baspa_zip',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_city', array(
			'label'    => esc_html__( 'City', 'baspa' ),
			'settings' => 'baspa_city',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_map', array(
			'label'    => esc_html__( 'Map', 'baspa' ),
			'settings' => 'baspa_map',
			'type'     => 'url',
			'section'  => 'about',
		) ) );

	}

	add_action( 'customize_register', 'baspa_customize_controls_add_about' );

}
