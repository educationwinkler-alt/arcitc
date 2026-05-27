<?php

/**
 * Support admin settings.
 */

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

		return (string) $value;

	}

}

if ( !function_exists( 'arctic_support_admin_fields' ) ) {

	/**
	 * Admin settings field definition.
	 *
	 * @return array
	 */
	function arctic_support_admin_fields(): array {

		return array(
			'baspa_supports_title'          => array( 'Support module title', 'text', 'Podpora' ),
			'baspa_supports_subtitle'       => array( 'Support module subtitle', 'textarea', '' ),
			'arctic_support_faq_title'      => array( 'FAQ section title', 'text', 'Caste dotazy' ),
			'arctic_support_form_title'     => array( 'Service form title', 'text', arctic_support_get_option( 'baspa_service_form_title', 'Servisni formular' ) ),
			'arctic_support_form_content'   => array( 'Service form text', 'textarea', arctic_support_get_option( 'baspa_service_form_content', 'Samozřejmostí je pro nás záruční i pozáruční servis u zákazníka, k dispozici je Vám formulář servisního požadavku, na který budeme co nejdříve reagovat. Objednat si u nás můžete odborné zazimování bazénu či vířivky stejně jako jarní zprovoznění.' ) ),
			'arctic_support_help_title'     => array( 'Help card title', 'text', 'Potrebujete poradit?' ),
			'arctic_support_help_name'      => array( 'Help card person', 'text', 'Lukas Dusek' ),
			'arctic_support_help_role'      => array( 'Help card role', 'text', 'Bazenovy specialista' ),
			'arctic_support_help_hours'     => array( 'Help card hours', 'text', 'Po - Pa 8:00-17:00 h' ),
			'arctic_support_help_button'    => array( 'Help card button text', 'text', 'Napsat zpravu' ),
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
