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

		if ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'local' === WP_ENVIRONMENT_TYPE ) {
			return;
		}

		// Settings
		$api_key = get_theme_mod( 'baspa_ecomail_api_key' );
		$list_id = get_theme_mod( 'baspa_ecomail_list_id' );

		if ( empty( $api_key ) || empty( $list_id ) ) {
			return;
		}

		$data = array(
			'subscriber_data'        => array(
				'name'          => esc_attr( $name ),
				'email'         => esc_attr( $email ),
				'phone'         => esc_attr( $phone ),
				'source'        => 'API',
				'custom_fields' => array(
					'FORM' => array(
						'value' => esc_attr( $form ),
						'type'  => 'string',
					),
					'URL'  => array(
						'value' => esc_attr( $url ),
						'type'  => 'url',
					),
				),
				'tags'          => array(
					$interest,
				),
			),
			'trigger_autoresponders' => true,
			'update_existing'        => true,
			'resubscribe'            => true,
		);

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, 'https://api2.ecomailapp.cz/lists/' . esc_attr( $list_id ) . '/subscribe' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_HEADER, FALSE );
		curl_setopt( $ch, CURLOPT_POST, TRUE );

		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'key: ' . esc_attr( $api_key ),
		) );

		$response = curl_exec( $ch );
		curl_close( $ch );

		/**
		 * Debug
		 */
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			if ( is_user_logged_in() && is_super_admin() ) {
				echo '<div class="f-alert" role="alert"><h5>Ecomail Debug:</h5><br>';
				echo '<pre>' . var_export( $response, true ) . '</pre>';
				echo '</div>';
			}
		}

	}

}
