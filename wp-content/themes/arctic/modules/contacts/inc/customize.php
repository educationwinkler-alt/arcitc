<?php

/**
 * Customize
 */

if ( !function_exists( 'baspa_contacts_customize_section_add' ) ) {

	/**
	 * Add Section
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function baspa_contacts_customize_section_add( WP_Customize_Manager $wp_customize ): void {

		$wp_customize->add_section( 'contacts', array(
			'title'    => esc_html_x( 'Contacts', 'admin', 'baspa' ),
			'panel'    => 'theme',
			'priority' => 2,
		) );

	}

	add_action( 'customize_register', 'baspa_contacts_customize_section_add', 20 );

}

if ( !function_exists( 'baspa_contacts_customize_settings_add' ) ) {

	/**
	 * Add Settings
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function baspa_contacts_customize_settings_add( WP_Customize_Manager $wp_customize ): void {

		$wp_customize->add_setting( 'baspa_form_from_name', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_form_from', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		) );

		$wp_customize->add_setting( 'baspa_form_to', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		) );

		$wp_customize->add_setting( 'baspa_form_to_pool', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		) );

		$wp_customize->add_setting( 'baspa_form_to_jacuzzi', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		) );

		$wp_customize->add_setting( 'baspa_form_to_service', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		) );

		/**
		 * ReCaptcha
		 */

		$wp_customize->add_setting( 'baspa_form_recaptcha_key_site', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_form_recaptcha_key_secret', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

	}

	add_action( 'customize_register', 'baspa_contacts_customize_settings_add', 20 );

}

if ( !function_exists( 'baspa_contacts_customize_controls_add' ) ) {

	/**
	 * Add Controls
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function baspa_contacts_customize_controls_add( WP_Customize_Manager $wp_customize ): void {

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_form_from_name', array(
			'label'   => esc_html_x( 'Send from Name', 'admin', 'baspa' ),
			'setting' => 'baspa_form_from_name',
			'type'    => 'text',
			'section' => 'contacts',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_form_from', array(
			'label'   => esc_html_x( 'Send from Email Address', 'admin', 'baspa' ),
			'setting' => 'baspa_form_from',
			'type'    => 'email',
			'section' => 'contacts',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_form_to', array(
			'label'   => esc_html_x( 'Send to Email Address', 'admin', 'baspa' ),
			'setting' => 'baspa_form_to',
			'type'    => 'email',
			'section' => 'contacts',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_form_to_pool', array(
			'label'   => esc_html_x( 'Send to Email Address', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Pool', 'admin', 'baspa' ),
			'setting' => 'baspa_form_to_pool',
			'type'    => 'email',
			'section' => 'contacts',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_form_to_jacuzzi', array(
			'label'   => esc_html_x( 'Send to Email Address', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Jacuzzi', 'admin', 'baspa' ),
			'setting' => 'baspa_form_to_jacuzzi',
			'type'    => 'email',
			'section' => 'contacts',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_form_to_service', array(
			'label'   => esc_html_x( 'Send to Email Address', 'admin', 'baspa' ) . ' - ' . esc_html_x( 'Service', 'admin', 'baspa' ),
			'setting' => 'baspa_form_to_service',
			'type'    => 'email',
			'section' => 'contacts',
		) ) );

		/**
		 * ReCaptcha
		 */

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_form_recaptcha_key_site', array(
			'label'   => esc_html_x( 'ReCaptcha Site Key', 'admin', 'baspa' ),
			'setting' => 'baspa_form_recaptcha_key_site',
			'type'    => 'text',
			'section' => 'contacts',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_form_recaptcha_key_secret', array(
			'label'   => esc_html_x( 'ReCaptcha Secret Key', 'admin', 'baspa' ),
			'setting' => 'baspa_form_recaptcha_key_secret',
			'type'    => 'text',
			'section' => 'contacts',
		) ) );

	}

	add_action( 'customize_register', 'baspa_contacts_customize_controls_add', 20 );

}
