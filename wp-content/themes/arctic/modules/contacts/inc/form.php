<?php

/**
 * Form
 */

if ( !function_exists( 'baspa_contacts_default_interest_options' ) ) {

	/**
	 * Default Interest Options
	 *
	 * @return array<string, string>
	 */
	function baspa_contacts_default_interest_options(): array {

		return array(
			'pool'    => __( 'Swimspa', 'baspa' ),
			'jacuzzi' => __( 'Vířivka', 'baspa' ),
			'service' => __( 'Servis', 'baspa' ),
			'offer'   => __( 'Akční nabídka', 'baspa' ),
		);

	}

}

if ( !function_exists( 'baspa_contacts_parse_interest_options' ) ) {

	/**
	 * Parse textarea interest options.
	 *
	 * Each line uses `slug|Label`.
	 *
	 * @param string $value
	 *
	 * @return array<string, string>
	 */
	function baspa_contacts_parse_interest_options( string $value ): array {

		$options = array();
		$lines   = preg_split( '/\r\n|\r|\n/', $value );

		foreach ( $lines ?: array() as $line ) {
			$line = trim( (string) $line );

			if ( '' === $line ) {
				continue;
			}

			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			$key   = sanitize_key( $parts[0] ?? '' );
			$label = sanitize_text_field( $parts[1] ?? '' );

			if ( '' === $key || '' === $label ) {
				continue;
			}

			$options[ $key ] = $label;
		}

		return $options;

	}

}

if ( !function_exists( 'baspa_contacts_interest_options_to_text' ) ) {

	/**
	 * Format interest options for textarea editing.
	 *
	 * @param array<string, string>|null $options
	 *
	 * @return string
	 */
	function baspa_contacts_interest_options_to_text( ?array $options = null ): string {

		$options = $options ?: baspa_contacts_default_interest_options();
		$lines   = array();

		foreach ( $options as $key => $label ) {
			$lines[] = sanitize_key( $key ) . '|' . sanitize_text_field( (string) $label );
		}

		return implode( "\n", $lines );

	}

}

if ( !function_exists( 'baspa_contacts_sanitize_interest_options_text' ) ) {

	/**
	 * Sanitize interest options textarea for storage.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	function baspa_contacts_sanitize_interest_options_text( string $value ): string {

		$options = baspa_contacts_parse_interest_options( $value );

		if ( empty( $options ) ) {
			$options = baspa_contacts_default_interest_options();
		}

		return baspa_contacts_interest_options_to_text( $options );

	}

}

if ( !function_exists( 'baspa_contacts_form_interest_options' ) ) {

	/**
	 * Set Interest Options
	 *
	 * @return array<string, string>
	 */
	function baspa_contacts_form_interest_options(): array {

		$stored = (string) get_option( 'baspa_contacts_interest_options', '' );

		if ( '' === trim( $stored ) ) {
			return baspa_contacts_default_interest_options();
		}

		$options = baspa_contacts_parse_interest_options( $stored );

		return !empty( $options ) ? $options : baspa_contacts_default_interest_options();

	}

}

if ( !function_exists( 'baspa_contacts_form_text_defaults' ) ) {

	/**
	 * Editable form text defaults.
	 *
	 * @return array<string, string>
	 */
	function baspa_contacts_form_text_defaults(): array {

		return array(
			'contact_header'              => __( 'Kontaktní formulář', 'baspa' ),
			'service_header'              => __( 'Servisní formulář', 'baspa' ),
			'label_name'                  => __( 'Jméno a příjmení', 'baspa' ),
			'placeholder_name'            => __( 'Vyplňte jméno a příjmení ...', 'baspa' ),
			'label_email'                 => __( 'Email', 'baspa' ),
			'placeholder_email'           => __( 'Vyplňte e-mail ...', 'baspa' ),
			'label_phone'                 => __( 'Telefon', 'baspa' ),
			'placeholder_phone'           => __( 'Vyplňte telefon ...', 'baspa' ),
			'label_interest'              => __( 'O co máte zájem?', 'baspa' ),
			'placeholder_interest'        => __( 'Vyberte, o co máte zájem ...', 'baspa' ),
			'jucra_interest_note'         => __( 'Typ poptávky: vířivka z 3D konfigurátoru', 'baspa' ),
			'label_message'               => __( 'Dotaz nebo poptávka', 'baspa' ),
			'placeholder_message'         => __( 'Napište dotaz nebo poptávku ...', 'baspa' ),
			'label_service_message'       => __( 'Dotaz nebo servisní požadavek', 'baspa' ),
			'placeholder_service_message' => __( 'Napište dotaz nebo servisní požadavek ...', 'baspa' ),
			'submit_contact'              => __( 'Odeslat', 'baspa' ),
			'submit_jucra'                => __( 'Odeslat poptávku', 'baspa' ),
			'submit_service'              => __( 'Odeslat požadavek', 'baspa' ),
		);

	}

}

if ( !function_exists( 'baspa_contacts_form_text' ) ) {

	/**
	 * Get editable form text.
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	function baspa_contacts_form_text( string $key, string $default = '' ): string {

		$defaults = baspa_contacts_form_text_defaults();
		$default  = '' !== $default ? $default : ( $defaults[ $key ] ?? '' );
		$value    = (string) get_option( 'baspa_contacts_form_text_' . sanitize_key( $key ), '' );

		return '' !== trim( $value ) ? $value : $default;

	}

}

if ( !function_exists( 'baspa_contacts_get_interest_title' ) ) {

	/**
	 * Get Interest Title
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	function baspa_contacts_get_interest_title( $key ): string {

		$interests = baspa_contacts_form_interest_options();

		return $interests[ $key ] ?? '';

	}

}
