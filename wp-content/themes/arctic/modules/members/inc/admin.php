<?php

/**
 * Admin
 */

if ( !function_exists( 'baspa_members_admin_page' ) ) {

	/**
	 * Add Admin Subpage
	 *
	 * @return void
	 */
	function baspa_members_admin_page(): void {

		// Add Sub-page
		add_submenu_page(
			'edit.php?post_type=member',
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			'manage_options',
			'settings-members',
			'baspa_members_admin_page_content',
		);
	}

	add_action( 'admin_menu', 'baspa_members_admin_page' );

}

if ( !function_exists( 'baspa_members_admin_page_content' ) ) {

	/**
	 * Admin Page Content
	 *
	 * @return void
	 */
	function baspa_members_admin_page_content(): void {

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'baspa' ) );
		}

		$post_value = static function ( string $key, string $default = '' ): string {
			return isset( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : $default;
		};

		$members_title    = get_option( 'baspa_members_title' ) !== null ? get_option( 'baspa_members_title' ) : __( 'Members', 'baspa' );
		$members_subtitle = get_option( 'baspa_members_subtitle' );

		if ( isset( $_POST[ 'submit' ] ) ) {
			check_admin_referer( 'baspa_members_settings' );

			if ( isset( $_POST[ 'members_title' ] ) ) {
				$members_title = sanitize_text_field( $post_value( 'members_title' ) );
				update_option( 'baspa_members_title', $members_title );
			}
			if ( isset( $_POST[ 'members_subtitle' ] ) ) {
				$members_subtitle = wp_kses_post( $post_value( 'members_subtitle' ) );
				update_option( 'baspa_members_subtitle', $members_subtitle );
			}
		} ?>

		<div class="wrap">

			<h1><?php echo esc_html_x( 'Settings', 'admin', 'baspa' ); ?></h1>

			<form method="post" action="">

				<?php wp_nonce_field( 'baspa_members_settings' ); ?>

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="members_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="members_title"
							       name="members_title"
							       value="<?php echo esc_attr( $members_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="members_subtitle">
								<?php echo esc_html_x( 'Subtitle', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="members_subtitle"
				          name="members_subtitle"
				          rows="3"
				          class="large-text"><?php echo esc_textarea( $members_subtitle ); ?></textarea>
						</td>
					</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit"
					       name="submit"
					       id="submit"
					       class="button button-primary"
					       value="<?php echo esc_attr_x( 'Save', 'admin', 'baspa' ); ?>">
				</p>

			</form>

		</div>

		<?php
	}

}
