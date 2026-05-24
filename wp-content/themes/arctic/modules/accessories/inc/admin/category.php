<?php

/**
 * Admin Category
 */

if ( !function_exists( 'baspa_accessories_admin_category_fields' ) ) {

	/**
	 * Term Fields
	 *
	 * @param $term
	 * @param $taxonomy
	 *
	 * @return void
	 */
	function baspa_accessories_admin_category_fields( $term, $taxonomy ): void {

		$term_product_category = get_term_meta( $term->term_id, 'product_category', true );
		$product_categories    = get_terms( array(
			'taxonomy'   => 'product-category',
			'parent'     => 0,
			'hide_empty' => false,
		) );
		if ( !empty( $product_categories ) && !is_wp_error( $product_categories ) ) { ?>

			<tr class="form-field">
				<th>
					<label for="product_category">
						<?php echo esc_html_x( 'Product Category', 'admin', 'baspa' ); ?>
					</label>
				</th>
				<td>
					<select id="product_category" name="product_category">
						<option value=""></option>
						<?php foreach ( $product_categories as $product_category ) { ?>
							<option value="<?php echo esc_attr( $product_category->term_id ); ?>" <?php echo selected( $term_product_category, $product_category->term_id ); ?>><?php echo esc_html( $product_category->name ); ?></option>
						<?php } ?>
					</select>
					<p class="description">
						<?php echo esc_html_x( 'Please select the product categories for which it will be displayed.', 'admin', 'baspa' ); ?>
					</p>
				</td>
			</tr>
		<?php }
	}

	add_action( 'accessory-category_edit_form_fields', 'baspa_accessories_admin_category_fields', 10, 2 );

}

if ( !function_exists( 'baspa_accessories_admin_category_fields_save' ) ) {

	/**
	 * Term Fields - Save
	 *
	 * @param $term_id
	 *
	 * @return void
	 */
	function baspa_accessories_admin_category_fields_save( $term_id ): void {

		$term = get_term( $term_id );
		$tax  = $term && !is_wp_error( $term ) ? get_taxonomy( $term->taxonomy ) : null;
		if ( !$tax || !current_user_can( $tax->cap->edit_terms ) ) {
			return;
		}

		$post_value = static function ( string $key, string $default = '' ): string {
			return isset( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : $default;
		};

		if ( isset( $_POST[ 'product_category' ] ) ) {
			update_term_meta( $term_id, 'product_category', absint( $post_value( 'product_category' ) ) );
		}

	}

	add_action( 'created_accessory-category', 'baspa_accessories_admin_category_fields_save' );
	add_action( 'edited_accessory-category', 'baspa_accessories_admin_category_fields_save' );

}
