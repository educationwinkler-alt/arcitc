<?php

/**
 * Admin
 */

if ( !function_exists( 'baspa_faqs_admin_page' ) ) {

	/**
	 * Add Admin Subpage
	 *
	 * @return void
	 */
	function baspa_faqs_admin_page(): void {

		// Add Sub-page
		add_submenu_page(
			'edit.php?post_type=faq',
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			'manage_options',
			'settings-faqs',
			'baspa_faqs_admin_page_content',
		);
	}

	add_action( 'admin_menu', 'baspa_faqs_admin_page' );

}

if ( !function_exists( 'baspa_faqs_admin_page_content' ) ) {

	/**
	 * Admin Page Content
	 *
	 * @return void
	 */
	function baspa_faqs_admin_page_content(): void {

		$faqs_title    = get_option( 'baspa_faqs_title' ) !== null ? get_option( 'baspa_faqs_title' ) : __( 'Faqs', 'baspa' );
		$faqs_subtitle = get_option( 'baspa_faqs_subtitle' );

		if ( isset( $_POST[ 'submit' ] ) ) {

			if ( isset( $_POST[ 'faqs_title' ] ) ) {
				update_option( 'baspa_faqs_title', wp_kses_post( $_POST[ 'faqs_title' ] ) );
			}
			if ( isset( $_POST[ 'faqs_subtitle' ] ) ) {
				update_option( 'baspa_faqs_subtitle', wp_kses_post( $_POST[ 'faqs_subtitle' ] ) );
			}
		} ?>

		<div class="wrap">

			<h1><?php echo esc_html_x( 'Settings', 'admin', 'baspa' ); ?></h1>

			<form method="post" action="">

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="faqs_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="faqs_title"
							       name="faqs_title"
							       value="<?php echo $_POST[ 'faqs_title' ] ?? wp_kses_post( $faqs_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="faqs_subtitle">
								<?php echo esc_html_x( 'Subtitle', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="faqs_subtitle"
				          name="faqs_subtitle"
				          rows="3"
				          class="large-text"><?php echo $_POST[ 'faqs_subtitle' ] ?? wp_kses_post( $faqs_subtitle ); ?></textarea>
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
