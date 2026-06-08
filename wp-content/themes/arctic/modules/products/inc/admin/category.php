<?php

/**
 * Admin Category
 */

if ( !function_exists( 'baspa_products_admin_category_image_field' ) ) {

	function baspa_products_admin_category_image_field( string $key, string $label, $image_id, string $description = '' ): void {

		$image_id = absint( $image_id );
		$image    = wp_get_attachment_image_url( $image_id, 'medium' );
		?>
		<tr class="form-field">
			<th>
				<label for="<?php echo esc_attr( $key ); ?>">
					<?php echo esc_html( $label ); ?>
				</label>
			</th>
			<td data-term-image-field="<?php echo esc_attr( $key ); ?>" data-term-media-field="<?php echo esc_attr( $key ); ?>" data-term-media-type="image">
				<figure class="f-term__image--preview js-term__image--preview">
					<?php if ( $image ) { ?>
						<img src="<?php echo esc_url( $image ); ?>" alt="">
					<?php } ?>
				</figure>
				<a href="#" class="button js-term__image--upload js-term__media--upload">
					<?php echo esc_html_x( $image ? 'Change Image' : 'Add Image', 'admin', 'baspa' ); ?>
				</a>
				<a href="#"
				   class="f-term__image--remove button js-term__image--remove js-term__media--remove"
				   <?php echo $image ? '' : 'style="display:none"'; ?>>
					<?php echo esc_html_x( 'Remove Image', 'admin', 'baspa' ); ?>
				</a>
				<input type="hidden"
				       id="<?php echo esc_attr( $key ); ?>"
				       name="<?php echo esc_attr( $key ); ?>"
				       class="js-term__image--id"
				       value="<?php echo esc_attr( $image_id ); ?>">
				<?php if ( $description ) { ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php } ?>
			</td>
		</tr>
		<?php
	}

}

if ( !function_exists( 'baspa_products_admin_category_media_field' ) ) {

	function baspa_products_admin_category_media_field( string $key, string $label, $attachment_id, string $description = '', string $media_type = 'image' ): void {

		$attachment_id = absint( $attachment_id );
		$media_type    = 'video' === $media_type ? 'video' : 'image';
		$attachment    = $attachment_id ? get_post( $attachment_id ) : null;
		$media_url     = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
		$is_video      = 'video' === $media_type;
		?>
		<tr class="form-field">
			<th>
				<label for="<?php echo esc_attr( $key ); ?>">
					<?php echo esc_html( $label ); ?>
				</label>
			</th>
			<td data-term-media-field="<?php echo esc_attr( $key ); ?>" data-term-media-type="<?php echo esc_attr( $media_type ); ?>">
				<figure class="f-term__image--preview js-term__image--preview">
					<?php if ( $media_url ) { ?>
						<?php if ( $is_video ) { ?>
							<video src="<?php echo esc_url( $media_url ); ?>" controls muted preload="metadata"></video>
						<?php } else { ?>
							<?php echo wp_get_attachment_image( $attachment_id, 'medium', false, array( 'alt' => '' ) ); ?>
						<?php } ?>
					<?php } ?>
				</figure>
				<a href="#" class="button js-term__media--upload">
					<?php echo esc_html_x( $media_url ? 'Change Media' : 'Add Media', 'admin', 'baspa' ); ?>
				</a>
				<a href="#"
				   class="f-term__image--remove button js-term__media--remove"
				   <?php echo $media_url ? '' : 'style="display:none"'; ?>>
					<?php echo esc_html_x( 'Remove Media', 'admin', 'baspa' ); ?>
				</a>
				<input type="hidden"
				       id="<?php echo esc_attr( $key ); ?>"
				       name="<?php echo esc_attr( $key ); ?>"
				       class="js-term__image--id"
				       value="<?php echo esc_attr( $attachment_id ); ?>">
				<?php if ( $attachment instanceof WP_Post ) { ?>
					<p class="description"><?php echo esc_html( $attachment->post_title ); ?></p>
				<?php } ?>
				<?php if ( $description ) { ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php } ?>
			</td>
		</tr>
		<?php
	}

}

if ( !function_exists( 'baspa_products_admin_category_select_field' ) ) {

	function baspa_products_admin_category_select_field( string $key, string $label, string $value, array $options, string $description = '' ): void {
		?>
		<tr class="form-field">
			<th>
				<label for="<?php echo esc_attr( $key ); ?>">
					<?php echo esc_html( $label ); ?>
				</label>
			</th>
			<td>
				<select id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>">
					<?php foreach ( $options as $option_value => $option_label ) { ?>
						<option value="<?php echo esc_attr( (string) $option_value ); ?>" <?php selected( $value, (string) $option_value ); ?>>
							<?php echo esc_html( (string) $option_label ); ?>
						</option>
					<?php } ?>
				</select>
				<?php if ( $description ) { ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php } ?>
			</td>
		</tr>
		<?php
	}

}

if ( !function_exists( 'baspa_products_admin_category_text_field' ) ) {

	function baspa_products_admin_category_text_field( string $key, string $label, string $value, string $description = '' ): void {
		?>
		<tr class="form-field">
			<th>
				<label for="<?php echo esc_attr( $key ); ?>">
					<?php echo esc_html( $label ); ?>
				</label>
			</th>
			<td>
				<input type="text"
				       id="<?php echo esc_attr( $key ); ?>"
				       name="<?php echo esc_attr( $key ); ?>"
				       value="<?php echo esc_attr( $value ); ?>">
				<?php if ( $description ) { ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php } ?>
			</td>
		</tr>
		<?php
	}

}

if ( !function_exists( 'baspa_products_admin_category_textarea_field' ) ) {

	function baspa_products_admin_category_textarea_field( string $key, string $label, string $value, string $description = '' ): void {
		?>
		<tr class="form-field">
			<th>
				<label for="<?php echo esc_attr( $key ); ?>">
					<?php echo esc_html( $label ); ?>
				</label>
			</th>
			<td>
				<textarea id="<?php echo esc_attr( $key ); ?>"
				          name="<?php echo esc_attr( $key ); ?>"
				          rows="5"><?php echo esc_textarea( $value ); ?></textarea>
				<?php if ( $description ) { ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php } ?>
			</td>
		</tr>
		<?php
	}

}

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
		$category_heading_image     = get_term_meta( $term->term_id, 'category_heading_image', true );
		$category_heading_media_type = arctic_hero_media_clean_type( (string) get_term_meta( $term->term_id, 'category_heading_media_type', true ) );
		$category_heading_video     = get_term_meta( $term->term_id, 'category_heading_video', true );
		$category_heading_poster    = get_term_meta( $term->term_id, 'category_heading_poster_image', true );
		$category_heading_title     = get_term_meta( $term->term_id, 'category_heading_title', true );
		$category_heading_text      = get_term_meta( $term->term_id, 'category_heading_text', true );
		$category_heading_cta_text  = get_term_meta( $term->term_id, 'category_heading_cta_text', true );
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
				       maxlength="255">
				<p class="description">
					<?php echo esc_html_x( 'Short description for category cards and menus.', 'admin', 'baspa' ); ?>
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
					<option value="accessories" <?php echo selected( 'accessories', $category_type ); ?>><?php echo esc_html_x( 'Accessories', 'admin', 'baspa' ); ?></option>
				</select>
				<p class="description">
					<?php echo esc_html_x( 'Select a type of a category.', 'admin', 'baspa' ); ?>
				</p>
			</td>
		</tr>
		<?php
		baspa_products_admin_category_image_field(
			'category_image',
			esc_html_x( 'Category card image', 'admin', 'baspa' ),
			$category_image,
			esc_html_x( 'Used as a fallback category image and in category cards.', 'admin', 'baspa' )
		);
		?>

		<tr class="form-field">
			<th colspan="2">
				<h2><?php echo esc_html_x( 'Arctic category hero', 'admin', 'baspa' ); ?></h2>
			</th>
		</tr>
		<?php
		baspa_products_admin_category_select_field(
			'category_heading_media_type',
			esc_html_x( 'Hero media type', 'admin', 'baspa' ),
			$category_heading_media_type,
			function_exists( 'arctic_hero_media_type_options' ) ? arctic_hero_media_type_options() : array( 'image' => 'Image', 'video' => 'Video' ),
			esc_html_x( 'Image uses the hero image below. Video uses a self-hosted MP4/WebM and keeps the hero/poster image as fallback.', 'admin', 'baspa' )
		);
		baspa_products_admin_category_text_field(
			'category_heading_title',
			esc_html_x( 'Hero title', 'admin', 'baspa' ),
			(string) $category_heading_title
		);
		baspa_products_admin_category_textarea_field(
			'category_heading_text',
			esc_html_x( 'Hero text', 'admin', 'baspa' ),
			(string) $category_heading_text
		);
		baspa_products_admin_category_text_field(
			'category_heading_cta_text',
			esc_html_x( 'Hero button text', 'admin', 'baspa' ),
			(string) $category_heading_cta_text
		);
		baspa_products_admin_category_image_field(
			'category_heading_image',
			esc_html_x( 'Hero image', 'admin', 'baspa' ),
			$category_heading_image,
			esc_html_x( 'This image is used in the category header.', 'admin', 'baspa' )
		);
		baspa_products_admin_category_media_field(
			'category_heading_video',
			esc_html_x( 'Hero video', 'admin', 'baspa' ),
			$category_heading_video,
			esc_html_x( 'Use a short muted MP4/WebM from the Media Library.', 'admin', 'baspa' ),
			'video'
		);
		baspa_products_admin_category_image_field(
			'category_heading_poster_image',
			esc_html_x( 'Video poster / fallback image', 'admin', 'baspa' ),
			$category_heading_poster,
			esc_html_x( 'Used before the video loads and on reduced-motion devices. If empty, the hero image is used.', 'admin', 'baspa' )
		);

		for ( $index = 1; $index <= 2; $index++ ) {
			$prefix = 'category_intro_' . $index;
			?>
			<tr class="form-field">
				<th colspan="2">
					<h2><?php echo esc_html( sprintf( esc_html_x( 'Figma intro card %d', 'admin', 'baspa' ), $index ) ); ?></h2>
				</th>
			</tr>
			<?php
			baspa_products_admin_category_text_field(
				$prefix . '_title',
				esc_html_x( 'Title', 'admin', 'baspa' ),
				(string) get_term_meta( $term->term_id, $prefix . '_title', true )
			);
			baspa_products_admin_category_textarea_field(
				$prefix . '_text',
				esc_html_x( 'Text', 'admin', 'baspa' ),
				(string) get_term_meta( $term->term_id, $prefix . '_text', true )
			);
			baspa_products_admin_category_text_field(
				$prefix . '_button_text',
				esc_html_x( 'Button text', 'admin', 'baspa' ),
				(string) get_term_meta( $term->term_id, $prefix . '_button_text', true )
			);
			baspa_products_admin_category_text_field(
				$prefix . '_button_url',
				esc_html_x( 'Button URL', 'admin', 'baspa' ),
				(string) get_term_meta( $term->term_id, $prefix . '_button_url', true ),
				esc_html_x( 'Use an internal path like /vlastnosti/ or a full URL.', 'admin', 'baspa' )
			);
			baspa_products_admin_category_image_field(
				$prefix . '_image',
				esc_html_x( 'Image', 'admin', 'baspa' ),
				get_term_meta( $term->term_id, $prefix . '_image', true )
			);
			baspa_products_admin_category_text_field(
				$prefix . '_alt',
				esc_html_x( 'Image alt text', 'admin', 'baspa' ),
				(string) get_term_meta( $term->term_id, $prefix . '_alt', true )
			);
		}
	}

	add_action( 'product-category_edit_form_fields', 'baspa_products_admin_category_fields', 10, 2 );

}

if ( !function_exists( 'baspa_products_admin_series_fields' ) ) {

	function baspa_products_admin_series_fields( $term, $taxonomy ): void {

		$series_heading_media_type = arctic_hero_media_clean_type( (string) get_term_meta( $term->term_id, 'series_heading_media_type', true ) );
		$series_heading_image      = get_term_meta( $term->term_id, 'series_heading_image', true );
		$series_heading_video      = get_term_meta( $term->term_id, 'series_heading_video', true );
		$series_heading_poster     = get_term_meta( $term->term_id, 'series_heading_poster_image', true );
		$series_heading_title      = get_term_meta( $term->term_id, 'series_heading_title', true );
		$series_heading_text       = get_term_meta( $term->term_id, 'series_heading_text', true );
		$series_heading_cta_text   = get_term_meta( $term->term_id, 'series_heading_cta_text', true );
		?>
		<tr class="form-field">
			<th colspan="2">
				<h2><?php echo esc_html_x( 'Arctic series hero', 'admin', 'baspa' ); ?></h2>
			</th>
		</tr>
		<?php
		baspa_products_admin_category_select_field(
			'series_heading_media_type',
			esc_html_x( 'Hero media type', 'admin', 'baspa' ),
			$series_heading_media_type,
			function_exists( 'arctic_hero_media_type_options' ) ? arctic_hero_media_type_options() : array( 'image' => 'Image', 'video' => 'Video' ),
			esc_html_x( 'Image uses the hero image below. Video uses a self-hosted MP4/WebM and keeps the hero/poster image as fallback.', 'admin', 'baspa' )
		);
		baspa_products_admin_category_text_field(
			'series_heading_title',
			esc_html_x( 'Hero title', 'admin', 'baspa' ),
			(string) $series_heading_title
		);
		baspa_products_admin_category_textarea_field(
			'series_heading_text',
			esc_html_x( 'Hero text', 'admin', 'baspa' ),
			(string) $series_heading_text
		);
		baspa_products_admin_category_text_field(
			'series_heading_cta_text',
			esc_html_x( 'Hero button text', 'admin', 'baspa' ),
			(string) $series_heading_cta_text
		);
		baspa_products_admin_category_image_field(
			'series_heading_image',
			esc_html_x( 'Hero image', 'admin', 'baspa' ),
			$series_heading_image,
			esc_html_x( 'Used as the series header image and as fallback for video.', 'admin', 'baspa' )
		);
		baspa_products_admin_category_media_field(
			'series_heading_video',
			esc_html_x( 'Hero video', 'admin', 'baspa' ),
			$series_heading_video,
			esc_html_x( 'Use a short muted MP4/WebM from the Media Library.', 'admin', 'baspa' ),
			'video'
		);
		baspa_products_admin_category_image_field(
			'series_heading_poster_image',
			esc_html_x( 'Video poster / fallback image', 'admin', 'baspa' ),
			$series_heading_poster,
			esc_html_x( 'Used before the video loads and on reduced-motion devices. If empty, the hero image is used.', 'admin', 'baspa' )
		);
	}

	add_action( 'product-series_edit_form_fields', 'baspa_products_admin_series_fields', 10, 2 );

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

		$term = get_term( $term_id );
		$tax  = $term && !is_wp_error( $term ) ? get_taxonomy( $term->taxonomy ) : null;
		if ( !$tax || !current_user_can( $tax->cap->edit_terms ) ) {
			return;
		}

		$post_value = static function ( string $key, string $default = '' ): string {
			return isset( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : $default;
		};

		$text_fields = array(
			'category_description_short',
			'category_heading_title',
			'category_heading_cta_text',
		);
		$textarea_fields = array(
			'category_heading_text',
		);
		$image_fields = array(
			'category_image',
			'category_heading_image',
			'category_heading_video',
			'category_heading_poster_image',
		);

		for ( $index = 1; $index <= 2; $index++ ) {
			$prefix            = 'category_intro_' . $index;
			$text_fields[]     = $prefix . '_title';
			$text_fields[]     = $prefix . '_button_text';
			$text_fields[]     = $prefix . '_button_url';
			$text_fields[]     = $prefix . '_alt';
			$textarea_fields[] = $prefix . '_text';
			$image_fields[]    = $prefix . '_image';
		}

		foreach ( $text_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_term_meta( $term_id, $field, sanitize_text_field( $post_value( $field ) ) );
			}
		}

		foreach ( $textarea_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_term_meta( $term_id, $field, sanitize_textarea_field( $post_value( $field ) ) );
			}
		}

		foreach ( $image_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_term_meta( $term_id, $field, absint( $post_value( $field ) ) );
			}
		}

		if ( isset( $_POST['category_heading_media_type'] ) ) {
			update_term_meta( $term_id, 'category_heading_media_type', arctic_hero_media_clean_type( $post_value( 'category_heading_media_type' ) ) );
		}

		if ( isset( $_POST[ 'category_type' ] ) ) {
			$category_type = sanitize_key( $post_value( 'category_type' ) );
			update_term_meta( $term_id, 'category_type', in_array( $category_type, array( 'accessories' ), true ) ? $category_type : '' );
		}

	}

	add_action( 'created_product-category', 'baspa_products_admin_category_fields_save' );
	add_action( 'edited_product-category', 'baspa_products_admin_category_fields_save' );

}

if ( !function_exists( 'baspa_products_admin_series_fields_save' ) ) {

	function baspa_products_admin_series_fields_save( $term_id ): void {

		$term = get_term( $term_id );
		$tax  = $term && !is_wp_error( $term ) ? get_taxonomy( $term->taxonomy ) : null;
		if ( !$tax || !current_user_can( $tax->cap->edit_terms ) ) {
			return;
		}

		$post_value = static function ( string $key, string $default = '' ): string {
			return isset( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : $default;
		};

		foreach ( array( 'series_heading_title', 'series_heading_cta_text' ) as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_term_meta( $term_id, $field, sanitize_text_field( $post_value( $field ) ) );
			}
		}

		if ( isset( $_POST['series_heading_text'] ) ) {
			update_term_meta( $term_id, 'series_heading_text', sanitize_textarea_field( $post_value( 'series_heading_text' ) ) );
		}

		foreach ( array( 'series_heading_image', 'series_heading_video', 'series_heading_poster_image' ) as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_term_meta( $term_id, $field, absint( $post_value( $field ) ) );
			}
		}

		if ( isset( $_POST['series_heading_media_type'] ) ) {
			update_term_meta( $term_id, 'series_heading_media_type', arctic_hero_media_clean_type( $post_value( 'series_heading_media_type' ) ) );
		}
	}

	add_action( 'created_product-series', 'baspa_products_admin_series_fields_save' );
	add_action( 'edited_product-series', 'baspa_products_admin_series_fields_save' );

}
