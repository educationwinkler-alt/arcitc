<?php

/**
 * Product Configurations
 */

if ( !function_exists( 'baspa_products_configuration_meta_key' ) ) {

	/**
	 * Structured configuration meta key.
	 *
	 * @return string
	 */
	function baspa_products_configuration_meta_key(): string {

		return 'product_configuration_items';

	}

}

if ( !function_exists( 'baspa_products_legacy_configuration_meta_key' ) ) {

	/**
	 * Legacy repeated fieldset meta key.
	 *
	 * @return string
	 */
	function baspa_products_legacy_configuration_meta_key(): string {

		return 'product_configurations';

	}

}

if ( !function_exists( 'baspa_products_configuration_text_value' ) ) {

	/**
	 * Normalize scalar configuration values into safe text.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	function baspa_products_configuration_text_value( $value ): string {

		if ( is_array( $value ) || is_object( $value ) ) {
			return '';
		}

		return sanitize_text_field( wp_unslash( (string) $value ) );

	}

}

if ( !function_exists( 'baspa_products_configuration_bool_value' ) ) {

	/**
	 * Cast admin checkbox values while keeping legacy rows active by default.
	 *
	 * @param mixed $value
	 * @param bool  $default
	 *
	 * @return bool
	 */
	function baspa_products_configuration_bool_value( $value, bool $default = true ): bool {

		if ( $value === null || $value === '' ) {
			return $default;
		}

		if ( is_bool( $value ) ) {
			return $value;
		}

		return in_array( strtolower( (string) $value ), array( '1', 'true', 'yes', 'on' ), true );

	}

}

if ( !function_exists( 'baspa_products_normalize_configuration' ) ) {

	/**
	 * Normalize one configuration row from either the new schema or legacy fieldset.
	 *
	 * @param array $configuration
	 * @param int   $index
	 *
	 * @return array
	 */
	function baspa_products_normalize_configuration( array $configuration, int $index = 0 ): array {

		$has_active_flag = array_key_exists( 'active', $configuration );
		$active          = baspa_products_configuration_bool_value( $configuration['active'] ?? null, !$has_active_flag );
		$sort_order      = isset( $configuration['sort_order'] ) ? absint( $configuration['sort_order'] ) : 0;

		if ( $sort_order <= 0 ) {
			$sort_order = ( $index + 1 ) * 10;
		}

		$name        = baspa_products_configuration_text_value( $configuration['name'] ?? '' );
		$price       = baspa_products_configuration_text_value( $configuration['price'] ?? '' );
		$price_text  = baspa_products_configuration_text_value( $configuration['price_text'] ?? '' );
		$seats       = baspa_products_configuration_text_value( $configuration['seats'] ?? '' );
		$jets        = baspa_products_configuration_text_value( $configuration['jets'] ?? '' );
		$pumps       = baspa_products_configuration_text_value( $configuration['pumps'] ?? '' );
		$dimensions  = baspa_products_configuration_text_value( $configuration['dimensions'] ?? '' );
		$notes       = baspa_products_configuration_text_value( $configuration['notes'] ?? ( $configuration['description'] ?? '' ) );
		$image_id    = isset( $configuration['image_id'] ) ? absint( $configuration['image_id'] ) : 0;

		if ( $image_id <= 0 && isset( $configuration['image'] ) ) {
			$image_id = absint( $configuration['image'] );
		}

		if ( $price_text === '' && $price !== '' ) {
			$price_text = $price;
		}

		$has_content = array_filter( array(
			$name,
			$price,
			$price_text,
			$seats,
			$jets,
			$pumps,
			$dimensions,
			$notes,
			$image_id,
		) );

		if ( empty( $has_content ) ) {
			return array();
		}

		return array(
			'active'      => $active ? 1 : 0,
			'sort_order'  => $sort_order,
			'name'        => $name,
			'price'       => $price,
			'price_text'  => $price_text,
			'seats'       => $seats,
			'jets'        => $jets,
			'pumps'       => $pumps,
			'dimensions'  => $dimensions,
			'notes'       => $notes,
			'image_id'    => $image_id,
		);

	}

}

if ( !function_exists( 'baspa_products_normalize_configurations' ) ) {

	/**
	 * Normalize a list of configuration rows.
	 *
	 * @param array $configurations
	 *
	 * @return array
	 */
	function baspa_products_normalize_configurations( array $configurations ): array {

		$items = array();

		foreach ( array_values( $configurations ) as $index => $configuration ) {
			if ( !is_array( $configuration ) ) {
				continue;
			}

			$item = baspa_products_normalize_configuration( $configuration, $index );

			if ( !empty( $item ) ) {
				$items[] = $item;
			}
		}

		usort( $items, static function ( array $a, array $b ): int {
			if ( $a['sort_order'] === $b['sort_order'] ) {
				return strcmp( $a['name'], $b['name'] );
			}

			return $a['sort_order'] <=> $b['sort_order'];
		} );

		return array_values( $items );

	}

}

if ( !function_exists( 'baspa_products_get_structured_configurations' ) ) {

	/**
	 * Get normalized configurations from the new structured meta key.
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	function baspa_products_get_structured_configurations( int $product_id ): array {

		$raw = get_post_meta( $product_id, baspa_products_configuration_meta_key(), true );

		if ( !is_array( $raw ) ) {
			return array();
		}

		if ( isset( $raw['name'] ) || isset( $raw['price'] ) || isset( $raw['price_text'] ) ) {
			$raw = array( $raw );
		}

		return baspa_products_normalize_configurations( $raw );

	}

}

if ( !function_exists( 'baspa_products_get_legacy_configurations' ) ) {

	/**
	 * Get normalized configurations from the old repeated fieldset meta key.
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	function baspa_products_get_legacy_configurations( int $product_id ): array {

		$legacy = get_post_meta( $product_id, baspa_products_legacy_configuration_meta_key() );

		if ( !is_array( $legacy ) ) {
			return array();
		}

		return baspa_products_normalize_configurations( $legacy );

	}

}

if ( !function_exists( 'baspa_products_get_configurations' ) ) {

	/**
	 * Get product configurations. New structured values win, legacy values remain fallback.
	 *
	 * @param int  $product_id
	 * @param bool $include_inactive
	 *
	 * @return array
	 */
	function baspa_products_get_configurations( int $product_id, bool $include_inactive = false ): array {

		$configurations = baspa_products_get_structured_configurations( $product_id );

		if ( empty( $configurations ) ) {
			$configurations = baspa_products_get_legacy_configurations( $product_id );
		}

		if ( !$include_inactive ) {
			$configurations = array_filter( $configurations, static function ( array $configuration ): bool {
				return !empty( $configuration['active'] );
			} );
		}

		return array_values( $configurations );

	}

}

if ( !function_exists( 'baspa_products_product_has_configurations' ) ) {

	/**
	 * Check if product has at least one active configuration.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	function baspa_products_product_has_configurations( int $product_id ): bool {

		return !empty( baspa_products_get_configurations( $product_id ) );

	}

}

if ( !function_exists( 'baspa_products_update_configurations' ) ) {

	/**
	 * Store structured configurations for a product.
	 *
	 * @param int   $product_id
	 * @param array $configurations
	 *
	 * @return void
	 */
	function baspa_products_update_configurations( int $product_id, array $configurations ): void {

		$configurations = baspa_products_normalize_configurations( $configurations );

		if ( empty( $configurations ) ) {
			delete_post_meta( $product_id, baspa_products_configuration_meta_key() );
			return;
		}

		update_post_meta( $product_id, baspa_products_configuration_meta_key(), $configurations );

	}

}

if ( !function_exists( 'baspa_products_configurations_metabox_add' ) ) {

	/**
	 * Register native structured configurations metabox.
	 *
	 * @return void
	 */
	function baspa_products_configurations_metabox_add(): void {

		add_meta_box(
			'baspa-product-configurations-structured',
			esc_html_x( 'Product Configurations', 'admin', 'baspa' ),
			'baspa_products_configurations_metabox_render',
			'product',
			'normal',
			'default'
		);

	}

	add_action( 'add_meta_boxes_product', 'baspa_products_configurations_metabox_add' );

}

if ( !function_exists( 'baspa_products_configurations_metabox_field_name' ) ) {

	/**
	 * Build configuration input name.
	 *
	 * @param string $index
	 * @param string $field
	 *
	 * @return string
	 */
	function baspa_products_configurations_metabox_field_name( string $index, string $field ): string {

		return 'product_configuration_items[' . $index . '][' . $field . ']';

	}

}

if ( !function_exists( 'baspa_products_configurations_metabox_render_row' ) ) {

	/**
	 * Render one configuration row in admin.
	 *
	 * @param string $index
	 * @param array  $configuration
	 *
	 * @return void
	 */
	function baspa_products_configurations_metabox_render_row( string $index, array $configuration ): void {

		$active      = !empty( $configuration['active'] );
		$sort_order  = isset( $configuration['sort_order'] ) ? absint( $configuration['sort_order'] ) : 10;
		$name        = $configuration['name'] ?? '';
		$price       = $configuration['price'] ?? '';
		$price_text  = $configuration['price_text'] ?? '';
		$seats       = $configuration['seats'] ?? '';
		$jets        = $configuration['jets'] ?? '';
		$pumps       = $configuration['pumps'] ?? '';
		$dimensions  = $configuration['dimensions'] ?? '';
		$notes       = $configuration['notes'] ?? '';
		$image_id    = isset( $configuration['image_id'] ) ? absint( $configuration['image_id'] ) : 0;
		?>
		<div class="postbox baspa-product-configuration-row" data-configuration-row>
			<div class="inside">
				<p>
					<label>
						<input type="hidden" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'active' ) ); ?>" value="0">
						<input type="checkbox" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'active' ) ); ?>" value="1" <?php checked( $active ); ?>>
						<?php echo esc_html_x( 'Active', 'admin', 'baspa' ); ?>
					</label>
					<button type="button" class="button-link-delete alignright" data-configuration-remove>
						<?php echo esc_html_x( 'Remove', 'admin', 'baspa' ); ?>
					</button>
				</p>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><label><?php echo esc_html_x( 'Sort order', 'admin', 'baspa' ); ?></label></th>
							<td><input class="small-text" type="number" min="0" step="1" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'sort_order' ) ); ?>" value="<?php echo esc_attr( $sort_order ); ?>"></td>
							<th scope="row"><label><?php echo esc_html_x( 'Name', 'admin', 'baspa' ); ?></label></th>
							<td><input class="regular-text" type="text" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'name' ) ); ?>" value="<?php echo esc_attr( $name ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label><?php echo esc_html_x( 'Price text', 'admin', 'baspa' ); ?></label></th>
							<td><input class="regular-text" type="text" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'price_text' ) ); ?>" value="<?php echo esc_attr( $price_text ); ?>"></td>
							<th scope="row"><label><?php echo esc_html_x( 'Raw price', 'admin', 'baspa' ); ?></label></th>
							<td><input class="regular-text" type="text" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'price' ) ); ?>" value="<?php echo esc_attr( $price ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label><?php echo esc_html_x( 'Seats', 'admin', 'baspa' ); ?></label></th>
							<td><input class="regular-text" type="text" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'seats' ) ); ?>" value="<?php echo esc_attr( $seats ); ?>"></td>
							<th scope="row"><label><?php echo esc_html_x( 'Jets', 'admin', 'baspa' ); ?></label></th>
							<td><input class="regular-text" type="text" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'jets' ) ); ?>" value="<?php echo esc_attr( $jets ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label><?php echo esc_html_x( 'Pumps', 'admin', 'baspa' ); ?></label></th>
							<td><input class="regular-text" type="text" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'pumps' ) ); ?>" value="<?php echo esc_attr( $pumps ); ?>"></td>
							<th scope="row"><label><?php echo esc_html_x( 'Dimensions', 'admin', 'baspa' ); ?></label></th>
							<td><input class="regular-text" type="text" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'dimensions' ) ); ?>" value="<?php echo esc_attr( $dimensions ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label><?php echo esc_html_x( 'Image', 'admin', 'baspa' ); ?></label></th>
							<td colspan="3">
								<input class="small-text" type="number" min="0" step="1" data-configuration-image-id name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'image_id' ) ); ?>" value="<?php echo esc_attr( $image_id ); ?>">
								<button type="button" class="button" data-configuration-select-image><?php echo esc_html_x( 'Select image', 'admin', 'baspa' ); ?></button>
								<button type="button" class="button-link-delete" data-configuration-clear-image><?php echo esc_html_x( 'Clear image', 'admin', 'baspa' ); ?></button>
								<div data-configuration-image-preview style="margin-top: 8px;">
									<?php
									if ( $image_id > 0 ) {
										echo wp_get_attachment_image( $image_id, 'thumbnail' );
									}
									?>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><label><?php echo esc_html_x( 'Notes', 'admin', 'baspa' ); ?></label></th>
							<td colspan="3"><textarea class="large-text" rows="3" name="<?php echo esc_attr( baspa_products_configurations_metabox_field_name( $index, 'notes' ) ); ?>"><?php echo esc_textarea( $notes ); ?></textarea></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php

	}

}

if ( !function_exists( 'baspa_products_configurations_metabox_render' ) ) {

	/**
	 * Render structured configurations metabox.
	 *
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	function baspa_products_configurations_metabox_render( WP_Post $post ): void {

		wp_enqueue_media();
		wp_nonce_field( 'baspa_product_configurations_save', 'baspa_product_configurations_nonce' );

		$configurations = baspa_products_get_configurations( $post->ID, true );

		if ( empty( $configurations ) ) {
			$configurations = array(
				array(
					'active'     => 1,
					'sort_order' => 10,
				),
			);
		}
		?>
		<div class="baspa-product-configurations" data-product-configurations data-next-index="<?php echo esc_attr( count( $configurations ) ); ?>">
			<p class="description">
				<?php echo esc_html_x( 'Structured configuration data. Legacy product_configurations values are used as fallback until this box is saved.', 'admin', 'baspa' ); ?>
			</p>

			<div data-configuration-rows>
				<?php
				foreach ( array_values( $configurations ) as $index => $configuration ) {
					baspa_products_configurations_metabox_render_row( (string) $index, $configuration );
				}
				?>
			</div>

			<p>
				<button type="button" class="button button-secondary" data-configuration-add>
					<?php echo esc_html_x( '+ Add Configuration', 'admin', 'baspa' ); ?>
				</button>
			</p>

			<template data-configuration-template>
				<?php
				baspa_products_configurations_metabox_render_row( '__INDEX__', array(
					'active'     => 1,
					'sort_order' => 10,
				) );
				?>
			</template>
		</div>

		<script>
			(function () {
				const root = document.querySelector('[data-product-configurations]');

				if (!root) {
					return;
				}

				const rows = root.querySelector('[data-configuration-rows]');
				const template = root.querySelector('[data-configuration-template]');
				const addButton = root.querySelector('[data-configuration-add]');
				let nextIndex = Number(root.dataset.nextIndex || rows.children.length);

				addButton.addEventListener('click', function () {
					rows.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, String(nextIndex)));
					const row = rows.lastElementChild;
					const sortInput = row ? row.querySelector('input[name$="[sort_order]"]') : null;

					if (sortInput) {
						sortInput.value = String((nextIndex + 1) * 10);
					}

					nextIndex += 1;
				});

				root.addEventListener('click', function (event) {
					const removeButton = event.target.closest('[data-configuration-remove]');

					if (removeButton) {
						const row = removeButton.closest('[data-configuration-row]');
						if (row && root.querySelectorAll('[data-configuration-row]').length > 1) {
							row.remove();
						}
						return;
					}

					const clearButton = event.target.closest('[data-configuration-clear-image]');

					if (clearButton) {
						const row = clearButton.closest('[data-configuration-row]');
						row.querySelector('[data-configuration-image-id]').value = '';
						row.querySelector('[data-configuration-image-preview]').innerHTML = '';
						return;
					}

					const selectButton = event.target.closest('[data-configuration-select-image]');

					if (!selectButton || !window.wp || !wp.media) {
						return;
					}

					const row = selectButton.closest('[data-configuration-row]');
					const input = row.querySelector('[data-configuration-image-id]');
					const preview = row.querySelector('[data-configuration-image-preview]');
					const frame = wp.media({
						title: '<?php echo esc_js( esc_html_x( 'Select configuration image', 'admin', 'baspa' ) ); ?>',
						button: {
							text: '<?php echo esc_js( esc_html_x( 'Use image', 'admin', 'baspa' ) ); ?>'
						},
						multiple: false
					});

					frame.on('select', function () {
						const attachment = frame.state().get('selection').first().toJSON();
						const thumbnail = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

						input.value = attachment.id;
						preview.innerHTML = thumbnail ? '<img src="' + thumbnail + '" alt="" style="max-width: 120px; height: auto;">' : '';
					});

					frame.open();
				});
			}());
		</script>
		<?php

	}

}

if ( !function_exists( 'baspa_products_configurations_metabox_save' ) ) {

	/**
	 * Save structured configurations.
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	function baspa_products_configurations_metabox_save( int $post_id, WP_Post $post ): void {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( $post->post_type !== 'product' ) {
			return;
		}

		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if (
			!isset( $_POST['baspa_product_configurations_nonce'] ) ||
			!wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['baspa_product_configurations_nonce'] ) ), 'baspa_product_configurations_save' )
		) {
			return;
		}

		$raw_configurations = isset( $_POST['product_configuration_items'] ) ? wp_unslash( $_POST['product_configuration_items'] ) : array();

		if ( !is_array( $raw_configurations ) ) {
			delete_post_meta( $post_id, baspa_products_configuration_meta_key() );
			return;
		}

		baspa_products_update_configurations( $post_id, $raw_configurations );

	}

	add_action( 'save_post_product', 'baspa_products_configurations_metabox_save', 10, 2 );

}
