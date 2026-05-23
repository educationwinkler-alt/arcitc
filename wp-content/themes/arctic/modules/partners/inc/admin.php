<?php

/**
 * Admin
 */

if ( !function_exists( 'baspa_partners_admin_page' ) ) {

	/**
	 * Add Admin Subpage
	 *
	 * @return void
	 */
	function baspa_partners_admin_page(): void {

		// Add Sub-page
		add_submenu_page(
			'edit.php?post_type=partner',
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			'manage_options',
			'settings-partners',
			'baspa_partners_admin_page_content',
		);
	}

	add_action( 'admin_menu', 'baspa_partners_admin_page' );

}

if ( !function_exists( 'baspa_partners_admin_page_content' ) ) {

	/**
	 * Admin Page Content
	 *
	 * @return void
	 */
	function baspa_partners_admin_page_content(): void {

		$partners_title    = get_option( 'baspa_partners_title' ) !== null ? get_option( 'baspa_partners_title' ) : __( 'Partners', 'baspa' );
		$partners_subtitle = get_option( 'baspa_partners_subtitle' );

		if ( isset( $_POST[ 'submit' ] ) ) {

			if ( isset( $_POST[ 'partners_title' ] ) ) {
				update_option( 'baspa_partners_title', wp_kses_post( $_POST[ 'partners_title' ] ) );
			}
			if ( isset( $_POST[ 'partners_subtitle' ] ) ) {
				update_option( 'baspa_partners_subtitle', wp_kses_post( $_POST[ 'partners_subtitle' ] ) );
			}
		} ?>

		<div class="wrap">

			<h1><?php echo esc_html_x( 'Settings', 'admin', 'baspa' ); ?></h1>

			<form method="post" action="">

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="partners_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="partners_title"
							       name="partners_title"
							       value="<?php echo $_POST[ 'partners_title' ] ?? wp_kses_post( $partners_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="partners_subtitle">
								<?php echo esc_html_x( 'Subtitle', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="partners_subtitle"
				          name="partners_subtitle"
				          rows="3"
				          class="large-text"><?php echo $_POST[ 'partners_subtitle' ] ?? wp_kses_post( $partners_subtitle ); ?></textarea>
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
