<?php

/**
 * Customize About
 */

if ( !function_exists( 'baspa_customize_section_add_socials' ) ) {

	/**
	 * Add Section
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_section_add_socials( $wp_customize ): void {

		$wp_customize->add_section( 'socials', array(
			'title'    => esc_html__( 'Socials', 'baspa' ),
			'panel'    => 'theme',
			'priority' => 3,
		) );

	}

	add_action( 'customize_register', 'baspa_customize_section_add_socials' );

}

if ( !function_exists( 'baspa_customize_settings_add_socials' ) ) {

	/**
	 * Add Settings
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_settings_add_socials( $wp_customize ): void {

		$wp_customize->add_setting( 'baspa_instagram', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_url',
		) );

		$wp_customize->add_setting( 'baspa_instagram_shortcode', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_facebook', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_url',
		) );

		$wp_customize->add_setting( 'baspa_youtube', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_url',
		) );

	}

	add_action( 'customize_register', 'baspa_customize_settings_add_socials' );

}

if ( !function_exists( 'baspa_customize_controls_add_socials' ) ) {

	/**
	 * Add Controls
	 *
	 * @param $wp_customize
	 */
	function baspa_customize_controls_add_socials( $wp_customize ): void {


		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_instagram', array(
			'label'    => esc_html__( 'Instagram', 'baspa' ),
			'settings' => 'baspa_instagram',
			'type'     => 'text',
			'section'  => 'socials',
		) ) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_instagram_shortcode', array(
			'label'    => esc_html__( 'Instagram Feed Shortcode', 'baspa' ),
			'settings' => 'baspa_instagram_shortcode',
			'type'     => 'text',
			'section'  => 'socials',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_facebook', array(
			'label'    => esc_html__( 'Facebook', 'baspa' ),
			'settings' => 'baspa_facebook',
			'type'     => 'text',
			'section'  => 'socials',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_youtube', array(
			'label'    => esc_html__( 'YouTube', 'baspa' ),
			'settings' => 'baspa_youtube',
			'type'     => 'text',
			'section'  => 'socials',
		) ) );


	}

	add_action( 'customize_register', 'baspa_customize_controls_add_socials' );

}
