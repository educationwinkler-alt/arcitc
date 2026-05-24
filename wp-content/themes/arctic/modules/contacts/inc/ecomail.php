<?php

/**
 * Ecomail
 */

if ( !function_exists( 'baspa_contacts_add_contact_to_ecomail' ) ) {

	/**
	 * Add Contact to Ecomail
	 *
	 * @param string $name
	 * @param string $email
	 * @param string $phone
	 * @param string $form
	 * @param string $url
	 * @param string $interest
	 *
	 * @return void
	 */
	function baspa_contacts_add_contact_to_ecomail( string $name, string $email, string $phone, string $form = '', string $url = '', string $interest = '' ): void {

		$is_local = function_exists( 'wp_get_environment_type' ) ? 'local' === wp_get_environment_type() : ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'local' === WP_ENVIRONMENT_TYPE );
		if ( $is_local ) {
			return;
		}

		$name     = sanitize_text_field( $name );
		$email    = sanitize_email( $email );
		$phone    = sanitize_text_field( $phone );
		$form     = sanitize_text_field( $form );
		$url      = esc_url_raw( $url );
		$interest = sanitize_text_field( $interest );

		if ( !is_email( $email ) ) {
			return;
		}

		// Settings
		$api_key = sanitize_text_field( get_theme_mod( 'baspa_ecomail_api_key' ) );
		$list_id = sanitize_text_field( get_theme_mod( 'baspa_ecomail_list_id' ) );

		if ( empty( $api_key ) || empty( $list_id ) ) {
			return;
		}

		$data = array(
			'subscriber_data'        => array(
				'name'          => $name,
				'email'         => $email,
				'phone'         => $phone,
				'source'        => 'API',
				'custom_fields' => array(
					'FORM' => array(
						'value' => $form,
						'type'  => 'string',
					),
					'URL'  => array(
						'value' => $url,
						'type'  => 'url',
					),
				),
				'tags'          => !empty( $interest ) ? array( $interest ) : array(),
			),
			'trigger_autoresponders' => true,
			'update_existing'        => true,
			'resubscribe'            => true,
		);

		$response = wp_remote_post( 'https://api2.ecomailapp.cz/lists/' . rawurlencode( $list_id ) . '/subscribe', array(
			'timeout' => 8,
			'headers' => array(
				'Content-Type' => 'application/json',
				'key'          => $api_key,
			),
			'body'    => wp_json_encode( $data ),
		) );

		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			if ( is_wp_error( $response ) ) {
				error_log( 'Ecomail subscribe failed: ' . $response->get_error_message() );
			} else if ( wp_remote_retrieve_response_code( $response ) >= 400 ) {
				error_log( 'Ecomail subscribe failed with HTTP ' . wp_remote_retrieve_response_code( $response ) );
			}
		}

	}

}
