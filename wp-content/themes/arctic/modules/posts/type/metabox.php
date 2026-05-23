<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_posts_type_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @return void
	 */
	function baspa_posts_type_metabox_register(): void {

		add_meta_box(
			'metabox-post',
			esc_html__( 'Post', 'baspa' ),
			'baspa_posts_type_metabox',
			'post',
			'normal',
			'high',
		);

	}

	add_action( 'add_meta_boxes', 'baspa_posts_type_metabox_register' );

}

if ( !function_exists( 'baspa_posts_type_metabox' ) ) {

	/**
	 * Metabox
	 *
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	function baspa_posts_type_metabox( WP_Post $post ): void {

		wp_nonce_field( basename( __FILE__ ), 'metabox_post_nonce' );

		$title   = get_post_meta( $post->ID, 'post_title_alter', true );
		$excerpt = $post->post_excerpt;
		?>
		<div class="f-metabox">

			<div class="f-metabox__field">
				<div class="f-metabox__label">
					<label for="post_title_alter"><?php echo esc_html_x( 'Title', 'admin', 'baspa' ); ?></label>
				</div>

				<div class="f-metabox__input">
					<input type="text"
					       id="post_title_alter" name="post_title_alter" class="regular-text"
					       placeholder="<?php echo esc_html_x( 'Alternative title of the post', 'admin', 'baspa' ) . ' ...'; ?>"
					       value="<?php echo esc_attr( $title ); ?>" required>
				</div>
			</div>

			<div class="f-metabox__field">
				<div class="f-metabox__label">
					<label for="post_excerpt"><?php echo esc_html_x( 'Description', 'admin', 'baspa' ); ?></label>
				</div>

				<div class="f-metabox__input">
					<textarea id="post_excerpt" name="post_excerpt" class="regular-text" cols="80" rows="6"
					          placeholder="<?php echo esc_html_x( 'Short description of the post', 'admin', 'baspa' ) . ' ...'; ?>"
					          required><?php echo esc_html( $excerpt ); ?></textarea>
				</div>
			</div>

		</div>
		<?php
	}

}

if ( !function_exists( 'baspa_posts_type_metabox_save' ) ) {

	/**
	 * Save Metabox
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	function baspa_posts_type_metabox_save( int $post_id, WP_Post $post ): void {

		// Verify nonce
		if ( !isset( $_POST[ 'metabox_post_nonce' ] ) || !wp_verify_nonce( $_POST[ 'metabox_post_nonce' ], basename( __FILE__ ) ) ) {
			return;
		}

		// Return if autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions
		$post_type = get_post_type_object( $post->post_type );

		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return;
		}

		/**
		 * Save Fields
		 */
		// Title
		if ( isset( $_POST[ 'post_title_alter' ] ) ) {
			update_post_meta( $post_id, 'post_title_alter', sanitize_text_field( $_POST[ 'post_title_alter' ] ) );
		}
		if ( isset( $_POST[ 'post_excerpt' ] ) ) {
			wp_update_post( $post_id, 'post_excerpt', sanitize_textarea_field( $_POST[ 'post_excerpt' ] ) );
		}

	}

	add_action( 'save_post', 'baspa_posts_type_metabox_save', 10, 2 );

}
