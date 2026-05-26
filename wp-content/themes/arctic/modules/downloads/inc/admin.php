<?php

/**
 * Downloads admin settings.
 */

if ( !function_exists( 'arctic_downloads_get_option' ) ) {

	/**
	 * Read downloads option with default.
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	function arctic_downloads_get_option( string $key, string $default = '' ): string {

		$value = get_option( $key, null );

		if ( $value === null || $value === false || $value === '' ) {
			return $default;
		}

		return (string) $value;

	}

}

if ( !function_exists( 'arctic_downloads_filter_labels' ) ) {

	/**
	 * Shared downloads filter labels.
	 *
	 * @return array
	 */
	function arctic_downloads_filter_labels(): array {

		return array(
			arctic_downloads_get_option( 'arctic_downloads_filter_catalogs', 'Katalogy virivek' ),
			arctic_downloads_get_option( 'arctic_downloads_filter_manuals', 'Navody' ),
			arctic_downloads_get_option( 'arctic_downloads_filter_dimensions', 'Rozmery' ),
			arctic_downloads_get_option( 'arctic_downloads_filter_warranty', 'Zaruky' ),
		);

	}

}

if ( !function_exists( 'arctic_downloads_admin_fields' ) ) {

	/**
	 * Admin settings field definition.
	 *
	 * @return array
	 */
	function arctic_downloads_admin_fields(): array {

		return array(
			'arctic_downloads_page_title'           => array( 'Downloads page title', 'text', 'Dokumenty ke stazeni' ),
			'arctic_downloads_support_title'        => array( 'Support downloads title', 'text', 'Ke stazeni' ),
			'arctic_downloads_filter_catalogs'      => array( 'Filter label: catalogs', 'text', 'Katalogy virivek' ),
			'arctic_downloads_filter_manuals'       => array( 'Filter label: manuals', 'text', 'Navody' ),
			'arctic_downloads_filter_dimensions'    => array( 'Filter label: dimensions', 'text', 'Rozmery' ),
			'arctic_downloads_filter_warranty'      => array( 'Filter label: warranty', 'text', 'Zaruky' ),
			'arctic_downloads_featured_group_title' => array( 'Featured group title', 'text', 'Serie custom' ),
			'arctic_downloads_closed_group_1_title' => array( 'Closed group 1 title', 'text', 'Serie classic' ),
			'arctic_downloads_closed_group_2_title' => array( 'Closed group 2 title', 'text', 'Serie core' ),
			'arctic_downloads_group_tag'            => array( 'Group tag', 'text', 'Katalogy virivek' ),
			'arctic_downloads_card_description'     => array( 'Card description', 'textarea', 'Dokument Arctic Spas, PDF ke stazeni.' ),
			'arctic_downloads_button_text'          => array( 'Download button text', 'text', 'Stahnout' ),
		);

	}

}

if ( !function_exists( 'arctic_downloads_admin_page' ) ) {

	/**
	 * Add downloads settings page.
	 *
	 * @return void
	 */
	function arctic_downloads_admin_page(): void {

		add_submenu_page(
			'edit.php?post_type=download',
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			'manage_options',
			'settings-downloads',
			'arctic_downloads_admin_page_content'
		);

	}

	add_action( 'admin_menu', 'arctic_downloads_admin_page' );

}

if ( !function_exists( 'arctic_downloads_admin_page_content' ) ) {

	/**
	 * Render downloads settings page.
	 *
	 * @return void
	 */
	function arctic_downloads_admin_page_content(): void {

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'baspa' ) );
		}

		$fields = arctic_downloads_admin_fields();

		if ( isset( $_POST['submit'] ) ) {
			check_admin_referer( 'arctic_downloads_settings' );

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
				<?php wp_nonce_field( 'arctic_downloads_settings' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $fields as $key => $field ) {
						$value = arctic_downloads_get_option( $key, (string) $field[2] );
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
