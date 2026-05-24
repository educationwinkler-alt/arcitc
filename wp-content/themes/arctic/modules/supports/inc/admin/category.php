<?php

/**
 * Admin Category
 */

if ( !function_exists( 'baspa_supports_admin_category_fields' ) ) {

	/**
	 * Term Fields
	 *
	 * @param $term
	 * @param $taxonomy
	 *
	 * @return void
	 */
	function baspa_supports_admin_category_fields( $term, $taxonomy ): void {

		$display_pricelist      = get_term_meta( $term->term_id, 'display_pricelist', true );
		$display_pricelist_only = get_term_meta( $term->term_id, 'display_pricelist_only', true );
		?>

		<tr class="form-field">
			<th>
				<label for="display_pricelist">
					<?php echo esc_html_x( 'Display also in a price list?', 'admin', 'baspa' ); ?>
				</label>
			</th>
			<td>
				<select id="display_pricelist" name="display_pricelist">
					<option value=""></option>
					<option value="yes" <?php echo selected( $display_pricelist, 'yes' ); ?>><?php echo esc_html__( 'Yes', 'baspa' ); ?></option>
					<option value="no" <?php echo selected( $display_pricelist, 'no' ); ?>><?php echo esc_html__( 'No', 'baspa' ); ?></option>
				</select>
				<p class="description">
					<?php echo esc_html_x( 'Please select whether the category will be displayed also in a price list or not.', 'admin', 'baspa' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th>
				<label for="display_pricelist_only">
					<?php echo esc_html_x( 'Display only in a price list?', 'admin', 'baspa' ); ?>
				</label>
			</th>
			<td>
				<select id="display_pricelist_only" name="display_pricelist_only">
					<option value=""></option>
					<option value="yes" <?php echo selected( $display_pricelist_only, 'yes' ); ?>><?php echo esc_html__( 'Yes', 'baspa' ); ?></option>
					<option value="no" <?php echo selected( $display_pricelist_only, 'no' ); ?>><?php echo esc_html__( 'No', 'baspa' ); ?></option>
				</select>
				<p class="description">
					<?php echo esc_html_x( 'Please select whether the category will be displayed only in a price list or not.', 'admin', 'baspa' ); ?>
				</p>
			</td>
		</tr>
	<?php }

	add_action( 'support-category_edit_form_fields', 'baspa_supports_admin_category_fields', 10, 2 );

}

if ( !function_exists( 'baspa_supports_admin_category_fields_save' ) ) {

	/**
	 * Term Fields - Save
	 *
	 * @param $term_id
	 *
	 * @return void
	 */
	function baspa_supports_admin_category_fields_save( $term_id ): void {

		$term = get_term( $term_id );
		$tax  = $term && !is_wp_error( $term ) ? get_taxonomy( $term->taxonomy ) : null;
		if ( !$tax || !current_user_can( $tax->cap->edit_terms ) ) {
			return;
		}

		$post_value = static function ( string $key, string $default = '' ): string {
			return isset( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : $default;
		};

		if ( isset( $_POST[ 'display_pricelist' ] ) ) {
			$display_pricelist = sanitize_key( $post_value( 'display_pricelist' ) );
			update_term_meta( $term_id, 'display_pricelist', in_array( $display_pricelist, array( 'yes', 'no' ), true ) ? $display_pricelist : '' );
		}

		if ( isset( $_POST[ 'display_pricelist_only' ] ) ) {
			$display_pricelist_only = sanitize_key( $post_value( 'display_pricelist_only' ) );
			update_term_meta( $term_id, 'display_pricelist_only', in_array( $display_pricelist_only, array( 'yes', 'no' ), true ) ? $display_pricelist_only : '' );
		}

	}

	add_action( 'created_support-category', 'baspa_supports_admin_category_fields_save' );
	add_action( 'edited_support-category', 'baspa_supports_admin_category_fields_save' );

}
