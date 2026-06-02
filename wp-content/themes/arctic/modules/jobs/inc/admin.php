<?php

/**
 * Admin
 */

if ( ! function_exists( 'baspa_jobs_admin_page' ) ) {

	/**
	 * Add Admin Subpage
	 *
	 * @return void
	 */
	function baspa_jobs_admin_page(): void {

		add_submenu_page(
			'edit.php?post_type=job',
			esc_html_x( 'Nastavení pracovních pozic', 'admin', 'baspa' ),
			esc_html_x( 'Nastavení', 'admin', 'baspa' ),
			'manage_options',
			'settings-jobs',
			'baspa_jobs_admin_page_content',
		);
	}

	add_action( 'admin_menu', 'baspa_jobs_admin_page' );

}

if ( ! function_exists( 'baspa_jobs_admin_page_content' ) ) {

	/**
	 * Admin Page Content
	 *
	 * @return void
	 */
	function baspa_jobs_admin_page_content(): void {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Nemáte oprávnění pro zobrazení této stránky.', 'baspa' ) );
		}

		$post_value = static function ( string $key, string $default = '' ): string {
			return isset( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : $default;
		};

		$jobs_title    = get_option( 'baspa_jobs_title' );
		$jobs_subtitle = get_option( 'baspa_jobs_subtitle' );

		if ( false === $jobs_title || '' === $jobs_title ) {
			$jobs_title = __( 'Kariéra v Arctic spas', 'baspa' );
		}

		if ( false === $jobs_subtitle || '' === $jobs_subtitle ) {
			$jobs_subtitle = __( 'Uplatnění u nás najdou šikovní lidé, kteří se nebojí komunikovat se zákazníky a odvádět dobrou práci každý den.', 'baspa' );
		}

		if ( isset( $_POST['submit'] ) ) {
			check_admin_referer( 'baspa_jobs_settings' );

			if ( isset( $_POST['jobs_title'] ) ) {
				$jobs_title = sanitize_text_field( $post_value( 'jobs_title' ) );
				update_option( 'baspa_jobs_title', $jobs_title );
			}
			if ( isset( $_POST['jobs_subtitle'] ) ) {
				$jobs_subtitle = wp_kses_post( $post_value( 'jobs_subtitle' ) );
				update_option( 'baspa_jobs_subtitle', $jobs_subtitle );
			}
		} ?>

		<div class="wrap">

			<h1><?php echo esc_html_x( 'Nastavení pracovních pozic', 'admin', 'baspa' ); ?></h1>

			<form method="post" action="">

				<?php wp_nonce_field( 'baspa_jobs_settings' ); ?>

				<table class="form-table" role="presentation">

					<tbody>
					<tr>
						<th scope="row">
							<label for="jobs_title">
								<?php echo esc_html_x( 'Nadpis sekce', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<input type="text"
							       id="jobs_title"
							       name="jobs_title"
							       value="<?php echo esc_attr( $jobs_title ); ?>"
							       class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="jobs_subtitle">
								<?php echo esc_html_x( 'Popis sekce', 'admin', 'baspa' ); ?>
							</label>
						</th>
						<td>
							<textarea id="jobs_subtitle"
							          name="jobs_subtitle"
							          rows="3"
							          class="large-text"><?php echo esc_textarea( $jobs_subtitle ); ?></textarea>
							<p class="description"><?php echo esc_html__( 'Jednotlivé pracovní pozice přidáte a seřadíte v seznamu pracovních pozic. První pozice se na stránce O nás otevře automaticky.', 'baspa' ); ?></p>
						</td>
					</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit"
					       name="submit"
					       id="submit"
					       class="button button-primary"
					       value="<?php echo esc_attr_x( 'Uložit nastavení', 'admin', 'baspa' ); ?>">
				</p>

			</form>

		</div>

		<?php
	}

}