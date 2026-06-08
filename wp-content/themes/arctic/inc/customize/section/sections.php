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

if ( !function_exists( 'arctic_sections_theme_mod_defaults' ) ) {

	/**
	 * Canonical defaults for Arctic section copy.
	 *
	 * @return array<string, string>
	 */
	function arctic_sections_theme_mod_defaults(): array {

		return array(
			'arctic_home_promo_title'       => 'Výprodej skladových vířivek',
			'arctic_home_promo_button_text' => 'Zobrazit nabídku',
			'arctic_configurator_title'     => 'Nakonfigurujte si vlastní vířivku',
			'arctic_configurator_text'      => 'Vyberte si model, výbavu a barvy. Připravíme vám konkrétní doporučení i cenovou nabídku.',
			'arctic_showroom_contact_name'  => 'Lukáš Dušek',
			'arctic_showroom_hours_title'   => 'Otevírací doba',
			'arctic_showroom_hours_label'   => 'Úterý - Pátek',
			'arctic_showroom_hours_line_1'  => '9:00 - 11:30',
			'arctic_showroom_hours_line_2'  => '12:30 - 16:00',
		);

	}

}

if ( !function_exists( 'arctic_sections_normalize_theme_mod_value' ) ) {

	/**
	 * Normalize legacy ASCII/mojibake defaults to canonical Czech copy.
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 */
	function arctic_sections_normalize_theme_mod_value( string $key, string $value ): string {

		$mojibake_values = function_exists( 'arctic_legacy_mojibake_values' )
			? 'arctic_legacy_mojibake_values'
			: static function (): array {
				return array();
			};

		$legacy_map = array(
			'arctic_home_promo_title'       => array_merge( array( 'Akční nabídka skladových vířivek', 'Akcni nabidka skladovych virivek' ), $mojibake_values( 'Akční nabídka skladových vířivek' ) ),
			'arctic_home_promo_button_text' => array_merge( array( 'Zobrazit nabídku', 'Zobrazit nabidku' ), $mojibake_values( 'Zobrazit nabídku' ) ),
			'arctic_configurator_title'     => array( 'Nakonfigurujte si vlastni virivku' ),
			'arctic_configurator_text'      => array( 'Vyberte si model, vybavu a barvy. Pripravime vam konkretni doporuceni i cenovou nabidku.' ),
			'arctic_showroom_contact_name'  => array( 'Lukas Dusek' ),
			'arctic_showroom_hours_title'   => array( 'Oteviraci doba' ),
			'arctic_showroom_hours_label'   => array( 'Utery - Patek' ),
		);

		if ( isset( $legacy_map[ $key ] ) && in_array( $value, $legacy_map[ $key ], true ) ) {
			$defaults = arctic_sections_theme_mod_defaults();

			if ( isset( $defaults[ $key ] ) ) {
				return $defaults[ $key ];
			}
		}

		return $value;

	}

}

if ( !function_exists( 'arctic_sections_get_theme_mod' ) ) {

	/**
	 * Read theme mod with normalized legacy fallback.
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	function arctic_sections_get_theme_mod( string $key, string $default ): string {

		$value = get_theme_mod( $key, $default );
		$value = is_string( $value ) ? $value : $default;

		return arctic_sections_normalize_theme_mod_value( $key, $value );

	}

}

if ( !function_exists( 'arctic_sections_migrate_legacy_theme_mod_defaults' ) ) {

	/**
	 * One-way migration for legacy defaults stored in DB.
	 *
	 * @return void
	 */
	function arctic_sections_migrate_legacy_theme_mod_defaults(): void {

		$defaults = arctic_sections_theme_mod_defaults();

		foreach ( $defaults as $key => $default ) {
			$current = get_theme_mod( $key, null );

			if ( !is_string( $current ) || $current === '' ) {
				continue;
			}

			$normalized = arctic_sections_normalize_theme_mod_value( $key, $current );

			if ( $normalized !== $current ) {
				set_theme_mod( $key, $normalized );
			}
		}

	}

	add_action( 'init', 'arctic_sections_migrate_legacy_theme_mod_defaults', 20 );

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
			'arctic_home_promo_title'           => array( 'Výprodej skladových vířivek', 'sanitize_text_field' ),
			'arctic_home_promo_button_text'     => array( 'Zobrazit nabídku', 'sanitize_text_field' ),
			'arctic_home_promo_url'             => array( '/akcni-nabidky/', 'arctic_sections_sanitize_url_path' ),
			'arctic_configurator_title'         => array( 'Nakonfigurujte si vlastní vířivku', 'sanitize_text_field' ),
			'arctic_configurator_text'          => array( 'Vyberte si model, výbavu a barvy. Připravíme vám konkrétní doporučení i cenovou nabídku.', 'wp_kses_post' ),
			'arctic_configurator_button_text'   => array( 'Konfigurovat', 'sanitize_text_field' ),
			'arctic_configurator_fallback_url'  => array( '/virivky/', 'arctic_sections_sanitize_url_path' ),
			'arctic_showroom_contact_name'      => array( 'Lukáš Dušek', 'sanitize_text_field' ),
			'arctic_showroom_hours_title'       => array( 'Otevírací doba', 'sanitize_text_field' ),
			'arctic_showroom_hours_label'       => array( 'Úterý - Pátek', 'sanitize_text_field' ),
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
