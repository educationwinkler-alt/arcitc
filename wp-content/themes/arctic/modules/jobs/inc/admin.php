<?php

/**
 * Admin
 */

if ( !function_exists( 'baspa_jobs_admin_page' ) ) {

	/**
	 * Add Admin Subpage
	 *
	 * @return void
	 */
	function baspa_jobs_admin_page(): void {

		// Add Sub-page
		add_submenu_page(
			'edit.php?post_type=job',
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			esc_html_x( 'Settings', 'admin', 'baspa' ),
			'manage_options',
			'settings-jobs',
			'baspa_jobs_admin_page_content',
		);
	}

	add_action( 'admin_menu', 'baspa_jobs_admin_page' );

}

if ( !function_exists( 'baspa_jobs_admin_page_content' ) ) {

	/**
	 * Admin Page Content
	 *
	 * @return void
	 */
	function baspa_jobs_admin_page_content(): void {

		$jobs_title    = get_option( 'baspa_jobs_title' ) !== null ? get_option( 'baspa_jobs_title' ) : __( 'Jobs', 'baspa' );
		$jobs_subtitle = get_option( 'baspa_jobs_subtitle' );

		if ( isset( $_POST[ 'submit' ] ) ) {

			if ( isset( $_POST[ 'jobs_title' ] ) ) {
				update_option( 'baspa_jobs_title', wp_kses_post( $_POST[ 'jobs_title' ] ) );
			}
			if ( isset( $_POST[ 'jobs_subtitle' ] ) ) {
				update_option( 'baspa_jobs_subtitle', wp_kses_post( $_POST[ 'jobs_subtitle' ] ) );
			}
		} ?>

		<div class="wrap">

			<h1><?php echo esc_html_x( 'Settings', 'admin', 'baspa' ); ?></h1>

			<form method="post" action="">

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="jobs_title">
								<?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="jobs_title"
							       name="jobs_title"
							       value="<?php echo $_POST[ 'jobs_title' ] ?? wp_kses_post( $jobs_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="jobs_subtitle">
								<?php echo esc_html_x( 'Subtitle', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
				<textarea id="jobs_subtitle"
				          name="jobs_subtitle"
				          rows="3"
				          class="large-text"><?php echo $_POST[ 'jobs_subtitle' ] ?? wp_kses_post( $jobs_subtitle ); ?></textarea>
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
