<?php

/**
 * Admin
 */

if ( !function_exists( 'baspa_contacts_admin_post_statuses' ) ) {

	/**
	 * Change Admin Status Labels
	 *
	 * @param array<string> $views
	 *
	 * @return array<string>
	 */
	function baspa_contacts_admin_post_statuses( array $views ): array {
		global $post_type;

		// Unset 'Mine'
		unset( $views[ 'mine' ] );

		if ( $post_type == 'contact' ) {
			if ( isset( $views[ 'publish' ] ) ) {
				// Rename 'Published'
				$views[ 'publish' ] = str_replace( array(
					'Published',
					'Publikováno',
					'Publikované'
				), esc_html_x( 'Received', 'admin', 'baspa' ), $views[ 'publish' ] );
			}
		}

		return $views;
	}

	add_filter( 'views_edit-contact', 'baspa_contacts_admin_post_statuses' );

}

if ( !function_exists( 'baspa_contacts_admin_page' ) ) {

	/**
	 * Add Admin Subpage
	 *
	 * @return void
	 */
	function baspa_contacts_admin_page(): void {

		// Add Sub-page
		add_submenu_page(
			'edit.php?post_type=contact',
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			'manage_options',
			'settings-contacts',
			'baspa_contacts_admin_page_content',
		);
	}

	add_action( 'admin_menu', 'baspa_contacts_admin_page' );

}

if ( !function_exists( 'baspa_contacts_admin_page_content' ) ) {

	/**
	 * Admin Page Content
	 *
	 * @return void
	 */
	function baspa_contacts_admin_page_content(): void {

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'baspa' ) );
		}

		$post_value = static function ( string $key, string $default = '' ): string {
			return isset( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : $default;
		};

		// Contacts
		$contacts_title    = get_option( 'baspa_contacts_title' );
		$contacts_subtitle = get_option( 'baspa_contacts_subtitle' );
		// Contacts - Form
		$contacts_form_title   = get_option( 'baspa_contacts_form_title' );
		$contacts_form_content = get_option( 'baspa_contacts_form_content' );
		// Service
		$service_title    = get_option( 'baspa_service_title' );
		$service_subtitle = get_option( 'baspa_service_subtitle' );
		// Service - Form
		$service_form_title   = get_option( 'baspa_service_form_title' );
		$service_form_content = get_option( 'baspa_service_form_content' );

		if ( isset( $_POST[ 'submit' ] ) ) {
			check_admin_referer( 'baspa_contacts_settings' );

			// Contacts
			if ( isset( $_POST[ 'contacts_title' ] ) ) {
				$contacts_title = sanitize_text_field( $post_value( 'contacts_title' ) );
				update_option( 'baspa_contacts_title', $contacts_title );
			}
			if ( isset( $_POST[ 'contacts_subtitle' ] ) ) {
				$contacts_subtitle = wp_kses_post( $post_value( 'contacts_subtitle' ) );
				update_option( 'baspa_contacts_subtitle', $contacts_subtitle );
			}
			if ( isset( $_POST[ 'contacts_form_title' ] ) ) {
				$contacts_form_title = sanitize_text_field( $post_value( 'contacts_form_title' ) );
				update_option( 'baspa_contacts_form_title', $contacts_form_title );
			}
			if ( isset( $_POST[ 'contacts_form_content' ] ) ) {
				$contacts_form_content = wp_kses_post( $post_value( 'contacts_form_content' ) );
				update_option( 'baspa_contacts_form_content', $contacts_form_content );
			}
			// Service
			if ( isset( $_POST[ 'service_title' ] ) ) {
				$service_title = sanitize_text_field( $post_value( 'service_title' ) );
				update_option( 'baspa_service_title', $service_title );
			}
			if ( isset( $_POST[ 'service_subtitle' ] ) ) {
				$service_subtitle = wp_kses_post( $post_value( 'service_subtitle' ) );
				update_option( 'baspa_service_subtitle', $service_subtitle );
			}
			if ( isset( $_POST[ 'service_form_title' ] ) ) {
				$service_form_title = sanitize_text_field( $post_value( 'service_form_title' ) );
				update_option( 'baspa_service_form_title', $service_form_title );
			}
			if ( isset( $_POST[ 'service_form_content' ] ) ) {
				$service_form_content = wp_kses_post( $post_value( 'service_form_content' ) );
				update_option( 'baspa_service_form_content', $service_form_content );
			}
		} ?>

		<div class="wrap">

			<h1><?php echo esc_html_x( 'Settings', 'admin', 'baspa' ); ?></h1>

			<form method="post" action="">

				<?php wp_nonce_field( 'baspa_contacts_settings' ); ?>

				<h2><?php echo esc_html_x( 'Contacts', 'admin', 'baspa' ); ?></h2>

				<hr>

				<h3 style="color: #2271B1"><?php echo esc_html_x( 'Section', 'admin', 'baspa' ); ?></h3>

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="contacts_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="contacts_title"
							       name="contacts_title"
							       value="<?php echo esc_attr( $contacts_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="contacts_subtitle">
								<?php echo esc_html_x( 'Subtitle', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="contacts_subtitle"
				          name="contacts_subtitle"
				          rows="2"
				          class="large-text"><?php echo esc_textarea( $contacts_subtitle ); ?></textarea>
						</td>
					</tr>
					</tbody>
				</table>

				<hr>

				<h3 style="color: #2271B1"><?php echo esc_html_x( 'Form', 'admin', 'baspa' ); ?></h3>

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="contacts_form_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="contacts_form_title"
							       name="contacts_form_title"
							       value="<?php echo esc_attr( $contacts_form_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="contacts_form_content">
								<?php echo esc_html_x( 'Content', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="contacts_form_content"
				          name="contacts_form_content"
				          rows="5"
				          class="large-text"><?php echo esc_textarea( $contacts_form_content ); ?></textarea>
						</td>
					</tr>
					</tbody>
				</table>

				<hr>

				<h2><?php echo esc_html_x( 'Service', 'admin', 'baspa' ); ?></h2>

				<hr>

				<h3 style="color: #2271B1"><?php echo esc_html_x( 'Section', 'admin', 'baspa' ); ?></h3>

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="service_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="service_title"
							       name="service_title"
							       value="<?php echo esc_attr( $service_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="service_subtitle">
								<?php echo esc_html_x( 'Subtitle', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="service_subtitle"
				          name="service_subtitle"
				          rows="2"
				          class="large-text"><?php echo esc_textarea( $service_subtitle ); ?></textarea>
						</td>
					</tr>
					</tbody>
				</table>

				<hr>

				<h3 style="color: #2271B1"><?php echo esc_html_x( 'Form', 'admin', 'baspa' ); ?></h3>

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="service_form_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="service_form_title"
							       name="service_form_title"
							       value="<?php echo esc_attr( $service_form_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="service_form_content">
								<?php echo esc_html_x( 'Content', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="service_form_content"
				          name="service_form_content"
				          rows="5"
				          class="large-text"><?php echo esc_textarea( $service_form_content ); ?></textarea>
						</td>
					</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary"
					       value="<?php echo esc_attr_x( 'Save', 'admin', 'baspa' ); ?>">
				</p>

			</form>

		</div>

		<?php
	}

}
