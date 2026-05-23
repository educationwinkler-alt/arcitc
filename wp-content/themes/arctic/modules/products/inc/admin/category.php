<?php

/**
 * Admin Category
 */

if ( !function_exists( 'baspa_products_admin_category_fields' ) ) {

	/**
	 * Term Fields
	 *
	 * @param $term
	 * @param $taxonomy
	 *
	 * @return void
	 */
	function baspa_products_admin_category_fields( $term, $taxonomy ): void {

		$category_description_short = get_term_meta( $term->term_id, 'category_description_short', true );
		$category_type              = get_term_meta( $term->term_id, 'category_type', true );
		$category_image             = get_term_meta( $term->term_id, 'category_image', true );
		?>

		<tr class="form-field">
			<th>
				<label for="category_description_short">
					<?php echo esc_html_x( 'Short Description', 'admin', 'baspa' ); ?>
				</label>
			</th>
			<td>
				<input type="text"
				       id="category_description_short"
				       name="category_description_short"
				       value="<?php echo esc_attr( $category_description_short ); ?>"
				       maxlength="100">
				<p class="description">
					<?php echo esc_html_x( 'Short description of the category', 'admin', 'baspa' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th>
				<label for="category_type">
					<?php echo esc_html_x( 'Type', 'admin', 'baspa' ); ?>
				</label>
			</th>
			<td>
				<select id="category_type" name="category_type">
					<option value=""></option>
					<option value="accessories" <?php echo selected( 'accessories', $category_type ) ?>><?php echo esc_html_x( 'Accessories', 'admin', 'baspa' ); ?></option>
				</select>
				<p class="description">
					<?php echo esc_html_x( 'Select a type of a category.', 'admin', 'baspa' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th>
				<label for="category_image">
					<?php echo esc_html_x( 'Image', 'admin', 'baspa' ); ?>
				</label>
			</th>
			<td>
				<?php
				$image = wp_get_attachment_image_url( $category_image, 'medium' );

				//				do_action( 'qm/debug', $category_image );

				if ( $image ) { ?>
					<figure class="f-term__image--preview js-term__image--preview">
						<img src="<?php echo esc_url( $image ) ?>" alt="">
					</figure>
					<a href="#" class="f-term__image--change button js-term__image--upload">
						<?php echo esc_html_x( 'Change Image', 'admin', 'baspa' ); ?>
					</a>
					<a href="#" class="f-term__image--remove button js-term__image--remove">
						<?php echo esc_html_x( 'Remove Image', 'admin', 'baspa' ); ?>
					</a>
					<input type="hidden"
					       name="category_image"
					       class="js-term__image--id"
					       value="<?php echo absint( $category_image ); ?>">
				<?php } else { ?>
					<figure class="f-term__image--preview js-term__image--preview"></figure>
					<a href="#" class="button js-term__image--upload">
						<?php echo esc_html_x( 'Add Image', 'admin', 'baspa' ); ?>
					</a>
					<a href="#" class="f-term__image--remove button js-term__image--remove" style="display:none">
						<?php echo esc_html_x( 'Remove Image', 'admin', 'baspa' ); ?>
					</a>
					<input type="hidden" name="category_image" class="js-term__image--id" value="">
				<?php } ?>
			</td>
		</tr>
	<?php }

	add_action( 'product-category_edit_form_fields', 'baspa_products_admin_category_fields', 10, 2 );

}

if ( !function_exists( 'baspa_products_admin_category_fields_save' ) ) {

	/**
	 * Term Fields - Save
	 *
	 * @param $term_id
	 *
	 * @return void
	 */
	function baspa_products_admin_category_fields_save( $term_id ): void {

		if ( isset( $_POST[ 'category_description_short' ] ) ) {
			update_term_meta( $term_id, 'category_description_short', sanitize_text_field( $_POST[ 'category_description_short' ] ) );
		}
		if ( isset( $_POST[ 'category_type' ] ) ) {
			update_term_meta( $term_id, 'category_type', sanitize_text_field( $_POST[ 'category_type' ] ) );
		}
		if ( isset( $_POST[ 'category_image' ] ) ) {
			update_term_meta( $term_id, 'category_image', absint( $_POST[ 'category_image' ] ) );
		}

	}

	add_action( 'created_product-category', 'baspa_products_admin_category_fields_save' );
	add_action( 'edited_product-category', 'baspa_products_admin_category_fields_save' );

}
