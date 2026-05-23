<?php

/**
 * Admin
 */

if ( !function_exists( 'baspa_references_admin_page' ) ) {

	/**
	 * Add Admin Subpage
	 *
	 * @return void
	 */
	function baspa_references_admin_page(): void {

		// Add Sub-page
		add_submenu_page(
			'edit.php?post_type=reference',
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			'manage_options',
			'settings-references',
			'baspa_references_admin_page_content',
		);
	}

	add_action( 'admin_menu', 'baspa_references_admin_page' );

}

if ( !function_exists( 'baspa_references_admin_page_content' ) ) {

	/**
	 * Admin Page Content
	 *
	 * @return void
	 */
	function baspa_references_admin_page_content(): void {

		$references_title    = get_option( 'baspa_references_title' ) !== null ? get_option( 'baspa_references_title' ) : __( 'References', 'baspa' );
		$references_subtitle = get_option( 'baspa_references_subtitle' );

		if ( isset( $_POST[ 'submit' ] ) ) {

			if ( isset( $_POST[ 'references_title' ] ) ) {
				update_option( 'baspa_references_title', wp_kses_post( $_POST[ 'references_title' ] ) );
			}
			if ( isset( $_POST[ 'references_subtitle' ] ) ) {
				update_option( 'baspa_references_subtitle', wp_kses_post( $_POST[ 'references_subtitle' ] ) );
			}
		} ?>

		<div class="wrap">

			<h1><?php echo esc_html_x( 'Settings', 'admin', 'baspa' ); ?></h1>

			<form method="post" action="">

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="references_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="references_title"
							       name="references_title"
							       value="<?php echo $_POST[ 'references_title' ] ?? wp_kses_post( $references_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="references_subtitle">
								<?php echo esc_html_x( 'Subtitle', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="references_subtitle"
				          name="references_subtitle"
				          rows="3"
				          class="large-text"><?php echo $_POST[ 'references_subtitle' ] ?? wp_kses_post( $references_subtitle ); ?></textarea>
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
