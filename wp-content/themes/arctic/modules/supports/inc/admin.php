<?php

/**
 * Support admin settings.
 */

if ( !function_exists( 'arctic_support_option_defaults' ) ) {

	/**
	 * Canonical support option defaults.
	 *
	 * @return array<string, string>
	 */
	function arctic_support_option_defaults(): array {

		return array(
			'arctic_support_faq_title'    => 'Časté dotazy',
			'arctic_support_form_title'   => 'Servisní formulář',
			'arctic_support_form_content' => 'Samozřejmostí je pro nás záruční i pozáruční servis u zákazníka, k dispozici je Vám formulář servisního požadavku, na který budeme co nejdříve reagovat. Objednat si u nás můžete odborné zazimování bazénu či vířivky stejně jako jarní zprovoznění.',
			'arctic_support_help_title'   => 'Potřebujete poradit?',
			'arctic_support_help_name'    => 'Lukáš Dušek',
			'arctic_support_help_role'    => 'Bazénový specialista',
			'arctic_support_help_hours'   => 'Po - Pá 8:00-17:00 h',
			'arctic_support_help_button'  => 'Napsat zprávu',
		);

	}

}

if ( !function_exists( 'arctic_support_normalize_legacy_value' ) ) {

	/**
	 * Normalize legacy ASCII/mojibake support values.
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 */
	function arctic_support_normalize_legacy_value( string $key, string $value ): string {

		$defaults = arctic_support_option_defaults();
		$legacy_map = array(
			'arctic_support_faq_title'    => array( 'Caste dotazy' ),
			'arctic_support_form_title'   => array( 'Servisni formular' ),
			'arctic_support_form_content' => array(
				'Samozřejmostí je pro nás záruční i pozáruční servis u zákazníka, k dispozici je Vám formulář servisního požadavku, na který budeme co nejdříve reagovat. Objednat si u nás můžete odborné zazimování bazénu či vířivky stejně jako jarní zprovoznění.',
				'SamozĹ™ejmostĂ­ je pro nĂˇs zĂˇruÄŤnĂ­ i pozĂˇruÄŤnĂ­ servis u zĂˇkaznĂ­ka, k dispozici je VĂˇm formulĂˇĹ™ servisnĂ­ho poĹľadavku, na kterĂ˝ budeme co nejdĹ™Ă­ve reagovat. Objednat si u nĂˇs mĹŻĹľete odbornĂ© zazimovĂˇnĂ­ bazĂ©nu ÄŤi vĂ­Ĺ™ivky stejnÄ› jako jarnĂ­ zprovoznÄ›nĂ­.',
			),
			'arctic_support_help_title'   => array( 'Potrebujete poradit?' ),
			'arctic_support_help_name'    => array( 'Lukas Dusek' ),
			'arctic_support_help_role'    => array( 'Bazenovy specialista' ),
			'arctic_support_help_hours'   => array( 'Po - Pa 8:00-17:00 h' ),
			'arctic_support_help_button'  => array( 'Napsat zpravu' ),
			'baspa_service_form_title'    => array( 'Servisni formular' ),
			'baspa_service_form_content'  => array(
				'SamozĹ™ejmostĂ­ je pro nĂˇs zĂˇruÄŤnĂ­ i pozĂˇruÄŤnĂ­ servis u zĂˇkaznĂ­ka, k dispozici je VĂˇm formulĂˇĹ™ servisnĂ­ho poĹľadavku, na kterĂ˝ budeme co nejdĹ™Ă­ve reagovat. Objednat si u nĂˇs mĹŻĹľete odbornĂ© zazimovĂˇnĂ­ bazĂ©nu ÄŤi vĂ­Ĺ™ivky stejnÄ› jako jarnĂ­ zprovoznÄ›nĂ­.',
			),
		);

		if ( isset( $legacy_map[ $key ] ) && in_array( $value, $legacy_map[ $key ], true ) ) {
			if ( $key === 'baspa_service_form_title' ) {
				return $defaults['arctic_support_form_title'];
			}

			if ( $key === 'baspa_service_form_content' ) {
				return $defaults['arctic_support_form_content'];
			}

			if ( isset( $defaults[ $key ] ) ) {
				return $defaults[ $key ];
			}
		}

		return $value;

	}

}

if ( !function_exists( 'arctic_support_get_option' ) ) {

	/**
	 * Read support option with default.
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	function arctic_support_get_option( string $key, string $default = '' ): string {

		$value = get_option( $key, null );

		if ( $value === null || $value === false || $value === '' ) {
			return $default;
		}

		return arctic_support_normalize_legacy_value( $key, (string) $value );

	}

}

if ( !function_exists( 'arctic_support_migrate_legacy_defaults' ) ) {

	/**
	 * One-way migration for legacy defaults stored in options.
	 *
	 * @return void
	 */
	function arctic_support_migrate_legacy_defaults(): void {

		$defaults = arctic_support_option_defaults();

		foreach ( $defaults as $key => $default ) {
			$current = get_option( $key, null );

			if ( !is_string( $current ) || $current === '' ) {
				continue;
			}

			$normalized = arctic_support_normalize_legacy_value( $key, $current );

			if ( $normalized !== $current ) {
				update_option( $key, $normalized );
			}
		}

	}

	add_action( 'init', 'arctic_support_migrate_legacy_defaults', 20 );

}

if ( !function_exists( 'arctic_support_admin_fields' ) ) {

	/**
	 * Admin settings field definition.
	 *
	 * @return array
	 */
	function arctic_support_admin_fields(): array {

		$defaults = arctic_support_option_defaults();
		$legacy_form_title = arctic_support_get_option( 'baspa_service_form_title', $defaults['arctic_support_form_title'] );
		$legacy_form_content = arctic_support_get_option( 'baspa_service_form_content', $defaults['arctic_support_form_content'] );

		return array(
			'baspa_supports_title'           => array( 'Support module title', 'text', 'Podpora' ),
			'baspa_supports_subtitle'        => array( 'Support module subtitle', 'textarea', '' ),
			'arctic_support_faq_title'       => array( 'FAQ section title', 'text', $defaults['arctic_support_faq_title'] ),
			'arctic_support_form_title'      => array( 'Service form title', 'text', arctic_support_normalize_legacy_value( 'arctic_support_form_title', $legacy_form_title ) ),
			'arctic_support_form_content'    => array( 'Service form text', 'textarea', arctic_support_normalize_legacy_value( 'arctic_support_form_content', $legacy_form_content ) ),
			'arctic_support_help_title'      => array( 'Help card title', 'text', $defaults['arctic_support_help_title'] ),
			'arctic_support_help_name'       => array( 'Help card person', 'text', $defaults['arctic_support_help_name'] ),
			'arctic_support_help_role'       => array( 'Help card role', 'text', $defaults['arctic_support_help_role'] ),
			'arctic_support_help_hours'      => array( 'Help card hours', 'text', $defaults['arctic_support_help_hours'] ),
			'arctic_support_help_button'     => array( 'Help card button text', 'text', $defaults['arctic_support_help_button'] ),
			'arctic_support_help_button_url' => array( 'Help card button URL', 'text', '/kontakt/' ),
		);

	}

}

if ( !function_exists( 'arctic_support_admin_page' ) ) {

	/**
	 * Add support settings page.
	 *
	 * @return void
	 */
	function arctic_support_admin_page(): void {

		add_submenu_page(
			'edit.php?post_type=support',
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			'manage_options',
			'settings-supports',
			'arctic_support_admin_page_content'
		);

	}

	add_action( 'admin_menu', 'arctic_support_admin_page' );

}

if ( !function_exists( 'arctic_support_admin_page_content' ) ) {

	/**
	 * Render support settings page.
	 *
	 * @return void
	 */
	function arctic_support_admin_page_content(): void {

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'baspa' ) );
		}

		$fields = arctic_support_admin_fields();

		if ( isset( $_POST['submit'] ) ) {
			check_admin_referer( 'arctic_support_settings' );

			foreach ( $fields as $key => $field ) {
				if ( !isset( $_POST[ $key ] ) ) {
					continue;
				}

				$value = (string) wp_unslash( $_POST[ $key ] );
				$value = $field[1] === 'textarea' ? wp_kses_post( $value ) : sanitize_text_field( $value );

				update_option( $key, $value );
			}
		}
		?>

		<div class="wrap">
			<h1><?php echo esc_html_x( 'Settings', 'admin', 'baspa' ); ?></h1>

			<form method="post" action="">
				<?php wp_nonce_field( 'arctic_support_settings' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $fields as $key => $field ) {
						$value = arctic_support_get_option( $key, (string) $field[2] );
						?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label>
							</th>
							<td>
								<?php if ( $field[1] === 'textarea' ) { ?>
									<textarea id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="4" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
								<?php } else { ?>
									<input id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( $value ); ?>">
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr_x( 'Save', 'admin', 'baspa' ); ?>">
				</p>
			</form>
		</div>

		<?php

	}

}