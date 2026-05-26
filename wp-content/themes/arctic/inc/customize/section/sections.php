<?php

/**
 * Customize Arctic sections.
 */

if ( !function_exists( 'arctic_sections_sanitize_url_path' ) ) {

	/**
	 * Allow either a full URL or an internal relative path.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	function arctic_sections_sanitize_url_path( $value ): string {

		$url = trim( sanitize_text_field( (string) $value ) );

		if ( $url === '' ) {
			return '';
		}

		if ( preg_match( '#^[a-z][a-z0-9+\-.]*://#i', $url ) || strpos( $url, '//' ) === 0 ) {
			return esc_url_raw( $url );
		}

		if ( strpos( $url, '#' ) === 0 ) {
			return $url;
		}

		return '/' . ltrim( $url, '/' );

	}

}

if ( !function_exists( 'arctic_sections_url' ) ) {

	/**
	 * Convert saved URL/path option into a frontend URL.
	 *
	 * @param string $value
	 * @param string $default
	 *
	 * @return string
	 */
	function arctic_sections_url( string $value, string $default = '/' ): string {

		$value = arctic_sections_sanitize_url_path( $value );

		if ( $value === '' ) {
			$value = arctic_sections_sanitize_url_path( $default );
		}

		if ( preg_match( '#^[a-z][a-z0-9+\-.]*://#i', $value ) || strpos( $value, '//' ) === 0 ) {
			return $value;
		}

		if ( strpos( $value, '#' ) === 0 ) {
			return $value;
		}

		return home_url( $value );

	}

}

if ( !function_exists( 'arctic_customize_section_add_sections' ) ) {

	/**
	 * Add Arctic sections controls.
	 *
	 * @param WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	function arctic_customize_section_add_sections( WP_Customize_Manager $wp_customize ): void {

		$wp_customize->add_section( 'arctic_sections', array(
			'title'       => esc_html__( 'Arctic Sections', 'baspa' ),
			'description' => esc_html__( 'Editable copy for Arctic-specific Figma sections.', 'baspa' ),
			'panel'       => 'theme',
			'priority'    => 12,
		) );

	}

	add_action( 'customize_register', 'arctic_customize_section_add_sections' );

}

if ( !function_exists( 'arctic_customize_settings_add_sections' ) ) {

	/**
	 * Add Arctic section settings.
	 *
	 * @param WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	function arctic_customize_settings_add_sections( WP_Customize_Manager $wp_customize ): void {

		$settings = array(
			'arctic_home_promo_enabled'         => array( 1, 'absint' ),
			'arctic_home_promo_title'           => array( 'Akcni nabidka skladovych virivek', 'sanitize_text_field' ),
			'arctic_home_promo_button_text'     => array( 'Zobrazit nabidku', 'sanitize_text_field' ),
			'arctic_home_promo_url'             => array( '/virivky/', 'arctic_sections_sanitize_url_path' ),
			'arctic_configurator_title'         => array( 'Nakonfigurujte si vlastni virivku', 'sanitize_text_field' ),
			'arctic_configurator_text'          => array( 'Vyberte si model, vybavu a barvy. Pripravime vam konkretni doporuceni i cenovou nabidku.', 'wp_kses_post' ),
			'arctic_configurator_button_text'   => array( 'Konfigurovat', 'sanitize_text_field' ),
			'arctic_configurator_fallback_url'  => array( '/virivky/', 'arctic_sections_sanitize_url_path' ),
			'arctic_showroom_contact_name'      => array( 'Lukas Dusek', 'sanitize_text_field' ),
			'arctic_showroom_hours_title'       => array( 'Oteviraci doba', 'sanitize_text_field' ),
			'arctic_showroom_hours_label'       => array( 'Utery - Patek', 'sanitize_text_field' ),
			'arctic_showroom_hours_line_1'      => array( '9:00 - 11:30', 'sanitize_text_field' ),
			'arctic_showroom_hours_line_2'      => array( '12:30 - 16:00', 'sanitize_text_field' ),
		);

		foreach ( $settings as $setting => $config ) {
			$wp_customize->add_setting( $setting, array(
				'default'           => $config[0],
				'sanitize_callback' => $config[1],
			) );
		}

	}

	add_action( 'customize_register', 'arctic_customize_settings_add_sections' );

}

if ( !function_exists( 'arctic_customize_controls_add_sections' ) ) {

	/**
	 * Add Arctic section controls.
	 *
	 * @param WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	function arctic_customize_controls_add_sections( WP_Customize_Manager $wp_customize ): void {

		$controls = array(
			'arctic_home_promo_enabled'        => array( 'Homepage promo enabled', 'checkbox' ),
			'arctic_home_promo_title'          => array( 'Homepage promo title', 'text' ),
			'arctic_home_promo_button_text'    => array( 'Homepage promo button', 'text' ),
			'arctic_home_promo_url'            => array( 'Homepage promo URL', 'text' ),
			'arctic_configurator_title'        => array( 'Configurator title', 'text' ),
			'arctic_configurator_text'         => array( 'Configurator text', 'textarea' ),
			'arctic_configurator_button_text'  => array( 'Configurator button', 'text' ),
			'arctic_configurator_fallback_url' => array( 'Configurator fallback URL', 'text' ),
			'arctic_showroom_contact_name'     => array( 'Showroom contact name', 'text' ),
			'arctic_showroom_hours_title'      => array( 'Showroom hours title', 'text' ),
			'arctic_showroom_hours_label'      => array( 'Showroom hours label', 'text' ),
			'arctic_showroom_hours_line_1'     => array( 'Showroom hours line 1', 'text' ),
			'arctic_showroom_hours_line_2'     => array( 'Showroom hours line 2', 'text' ),
		);

		foreach ( $controls as $setting => $config ) {
			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, $setting, array(
				'label'    => esc_html__( $config[0], 'baspa' ),
				'settings' => $setting,
				'type'     => $config[1],
				'section'  => 'arctic_sections',
			) ) );
		}

	}

	add_action( 'customize_register', 'arctic_customize_controls_add_sections' );

}
