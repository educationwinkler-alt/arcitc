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

		$wp_customize->add_setting( 'baspa_copyright', array(
			'default'           => '',
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

		$wp_customize->add_setting( 'arctic_map_embed', array(
			'default'           => function_exists( 'arctic_get_default_map_embed_url' ) ? arctic_get_default_map_embed_url() : '',
			'sanitize_callback' => function_exists( 'arctic_sanitize_map_embed' ) ? 'arctic_sanitize_map_embed' : 'esc_url_raw',
		) );

		$wp_customize->add_setting( 'baspa_billing_title', array(
			'default'           => esc_html__( 'Fakturační údaje', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_billing_company', array(
			'default'           => esc_html__( 'BASPA s.r.o.', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_billing_address', array(
			'default'           => esc_html__( 'Bohunická cesta 727/15, 664 48 Moravany', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_billing_ico', array(
			'default'           => esc_html__( 'IČ 02257467', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_billing_dic', array(
			'default'           => esc_html__( 'DIČ CZ02257467', 'baspa' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_setting( 'baspa_billing_registry', array(
			'default'           => esc_html__( 'Společnost je zapsána v obchodním rejstříku vedeném u Krajského soudu v Brně, oddíl C, vložka 80736.', 'baspa' ),
			'sanitize_callback' => 'sanitize_textarea_field',
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

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_copyright', array(
			'label'       => esc_html__( 'Footer copyright', 'baspa' ),
			'description' => esc_html__( 'Leave empty to generate it from the company name and current year.', 'baspa' ),
			'settings'    => 'baspa_copyright',
			'type'        => 'text',
			'section'     => 'about',
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
			'label'       => esc_html__( 'Map link', 'baspa' ),
			'description' => esc_html__( 'Public Google Maps link used by map buttons.', 'baspa' ),
			'settings' => 'baspa_map',
			'type'     => 'url',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'arctic_map_embed', array(
			'label'       => esc_html__( 'Map embed iframe / URL', 'baspa' ),
			'description' => esc_html__( 'Paste the Google Maps embed URL or the full iframe code from Google Maps. This powers the interactive map background.', 'baspa' ),
			'settings'    => 'arctic_map_embed',
			'type'        => 'textarea',
			'section'     => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_billing_title', array(
			'label'    => esc_html__( 'Billing title', 'baspa' ),
			'settings' => 'baspa_billing_title',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_billing_company', array(
			'label'    => esc_html__( 'Billing company', 'baspa' ),
			'settings' => 'baspa_billing_company',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_billing_address', array(
			'label'    => esc_html__( 'Billing address', 'baspa' ),
			'settings' => 'baspa_billing_address',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_billing_ico', array(
			'label'    => esc_html__( 'Billing company ID', 'baspa' ),
			'settings' => 'baspa_billing_ico',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_billing_dic', array(
			'label'    => esc_html__( 'Billing VAT ID', 'baspa' ),
			'settings' => 'baspa_billing_dic',
			'type'     => 'text',
			'section'  => 'about',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'baspa_billing_registry', array(
			'label'    => esc_html__( 'Billing registry note', 'baspa' ),
			'settings' => 'baspa_billing_registry',
			'type'     => 'textarea',
			'section'  => 'about',
		) ) );

	}

	add_action( 'customize_register', 'baspa_customize_controls_add_about' );

}
