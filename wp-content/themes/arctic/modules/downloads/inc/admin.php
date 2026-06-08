<?php

/**
 * Downloads admin settings.
 */

if ( !function_exists( 'arctic_downloads_option_defaults' ) ) {

	/**
	 * Canonical downloads option defaults.
	 *
	 * @return array<string, string>
	 */
	function arctic_downloads_option_defaults(): array {

		return array(
			'arctic_downloads_page_title'           => 'Dokumenty ke stažení',
			'arctic_downloads_support_title'        => 'Ke stažení',
			'arctic_downloads_filter_catalogs'      => 'Katalogy vířivek',
			'arctic_downloads_filter_manuals'       => 'Návody',
			'arctic_downloads_filter_dimensions'    => 'Rozměry',
			'arctic_downloads_filter_warranty'      => 'Záruky',
			'arctic_downloads_featured_group_title' => 'Série custom',
			'arctic_downloads_closed_group_1_title' => 'Série classic',
			'arctic_downloads_closed_group_2_title' => 'Série core',
			'arctic_downloads_group_tag'            => 'Katalogy vířivek',
			'arctic_downloads_card_description'     => 'Dokument Arctic Spas, PDF ke stažení.',
			'arctic_downloads_button_text'          => 'Stáhnout',
		);

	}

}

if ( !function_exists( 'arctic_downloads_normalize_legacy_value' ) ) {

	/**
	 * Normalize legacy ASCII/mojibake downloads values.
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 */
	function arctic_downloads_normalize_legacy_value( string $key, string $value ): string {

		$defaults = arctic_downloads_option_defaults();
		$legacy_map = array(
			'arctic_downloads_page_title'           => array( 'Dokumenty ke stazeni' ),
			'arctic_downloads_support_title'        => array( 'Ke stazeni' ),
			'arctic_downloads_filter_catalogs'      => array( 'Katalogy virivek' ),
			'arctic_downloads_filter_manuals'       => array( 'Navody' ),
			'arctic_downloads_filter_dimensions'    => array( 'Rozmery' ),
			'arctic_downloads_filter_warranty'      => array( 'Zaruky' ),
			'arctic_downloads_featured_group_title' => array( 'Serie custom' ),
			'arctic_downloads_closed_group_1_title' => array( 'Serie classic' ),
			'arctic_downloads_closed_group_2_title' => array( 'Serie core' ),
			'arctic_downloads_group_tag'            => array( 'Katalogy virivek' ),
			'arctic_downloads_card_description'     => array( 'Dokument Arctic Spas, PDF ke stazeni.' ),
			'arctic_downloads_button_text'          => array( 'Stahnout' ),
		);

		if ( isset( $legacy_map[ $key ] ) && in_array( $value, $legacy_map[ $key ], true ) && isset( $defaults[ $key ] ) ) {
			return $defaults[ $key ];
		}

		return $value;

	}

}

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

		return arctic_downloads_normalize_legacy_value( $key, (string) $value );

	}

}

if ( !function_exists( 'arctic_downloads_migrate_legacy_defaults' ) ) {

	/**
	 * One-way migration for legacy defaults stored in options.
	 *
	 * @return void
	 */
	function arctic_downloads_migrate_legacy_defaults(): void {

		$defaults = arctic_downloads_option_defaults();

		foreach ( $defaults as $key => $default ) {
			$current = get_option( $key, null );

			if ( !is_string( $current ) || $current === '' ) {
				continue;
			}

			$normalized = arctic_downloads_normalize_legacy_value( $key, $current );

			if ( $normalized !== $current ) {
				update_option( $key, $normalized );
			}
		}

	}

	add_action( 'init', 'arctic_downloads_migrate_legacy_defaults', 20 );

}

if ( !function_exists( 'arctic_downloads_filter_labels' ) ) {

	/**
	 * Shared downloads filter labels.
	 *
	 * @return array
	 */
	function arctic_downloads_filter_labels(): array {

		return array_values( wp_list_pluck( arctic_downloads_filter_definitions(), 'label' ) );

	}

}

if ( !function_exists( 'arctic_downloads_filter_definitions' ) ) {

	/**
	 * Shared downloads filter keys and labels.
	 *
	 * @return array<int, array{key: string, label: string, document_types: array<int, string>}>
	 */
	function arctic_downloads_filter_definitions(): array {

		$defaults = arctic_downloads_option_defaults();

		return array(
			array(
				'key'            => 'catalog',
				'label'          => arctic_downloads_get_option( 'arctic_downloads_filter_catalogs', $defaults['arctic_downloads_filter_catalogs'] ),
				'document_types' => array( 'catalog' ),
			),
			array(
				'key'            => 'manual',
				'label'          => arctic_downloads_get_option( 'arctic_downloads_filter_manuals', $defaults['arctic_downloads_filter_manuals'] ),
				'document_types' => array( 'manual', 'water-care', 'service', 'other', 'technical', 'water' ),
			),
			array(
				'key'            => 'dimensions',
				'label'          => arctic_downloads_get_option( 'arctic_downloads_filter_dimensions', $defaults['arctic_downloads_filter_dimensions'] ),
				'document_types' => array( 'dimensions', 'preparation' ),
			),
			array(
				'key'            => 'warranty',
				'label'          => arctic_downloads_get_option( 'arctic_downloads_filter_warranty', $defaults['arctic_downloads_filter_warranty'] ),
				'document_types' => array( 'warranty' ),
			),
		);

	}

}

if ( !function_exists( 'arctic_downloads_filter_key_for_document_type' ) ) {

	/**
	 * Resolve download CPT document type into the frontend filter key.
	 *
	 * @param string $document_type
	 *
	 * @return string
	 */
	function arctic_downloads_filter_key_for_document_type( string $document_type ): string {

		foreach ( arctic_downloads_filter_definitions() as $definition ) {
			if ( in_array( $document_type, $definition['document_types'], true ) ) {
				return $definition['key'];
			}
		}

		return 'manual';

	}

}

if ( !function_exists( 'arctic_downloads_admin_fields' ) ) {

	/**
	 * Admin settings field definition.
	 *
	 * @return array
	 */
	function arctic_downloads_admin_fields(): array {

		$defaults = arctic_downloads_option_defaults();

		return array(
			'arctic_downloads_page_title'           => array( 'Downloads page title', 'text', $defaults['arctic_downloads_page_title'] ),
			'arctic_downloads_support_title'        => array( 'Support downloads title', 'text', $defaults['arctic_downloads_support_title'] ),
			'arctic_downloads_filter_catalogs'      => array( 'Filter label: catalogs', 'text', $defaults['arctic_downloads_filter_catalogs'] ),
			'arctic_downloads_filter_manuals'       => array( 'Filter label: manuals', 'text', $defaults['arctic_downloads_filter_manuals'] ),
			'arctic_downloads_filter_dimensions'    => array( 'Filter label: dimensions', 'text', $defaults['arctic_downloads_filter_dimensions'] ),
			'arctic_downloads_filter_warranty'      => array( 'Filter label: warranty', 'text', $defaults['arctic_downloads_filter_warranty'] ),
			'arctic_downloads_featured_group_title' => array( 'Featured group title', 'text', $defaults['arctic_downloads_featured_group_title'] ),
			'arctic_downloads_closed_group_1_title' => array( 'Closed group 1 title', 'text', $defaults['arctic_downloads_closed_group_1_title'] ),
			'arctic_downloads_closed_group_2_title' => array( 'Closed group 2 title', 'text', $defaults['arctic_downloads_closed_group_2_title'] ),
			'arctic_downloads_group_tag'            => array( 'Group tag', 'text', $defaults['arctic_downloads_group_tag'] ),
			'arctic_downloads_card_description'     => array( 'Card description', 'textarea', $defaults['arctic_downloads_card_description'] ),
			'arctic_downloads_button_text'          => array( 'Download button text', 'text', $defaults['arctic_downloads_button_text'] ),
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
