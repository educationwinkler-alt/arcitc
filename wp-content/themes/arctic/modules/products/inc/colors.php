<?php

/**
 * Product color catalog helpers.
 */

if ( !function_exists( 'arctic_product_color_types' ) ) {

	/**
	 * Color catalog types.
	 *
	 * @return array
	 */
	function arctic_product_color_types(): array {

		return array(
			'shell'   => esc_html_x( 'Shell color', 'admin', 'baspa' ),
			'cabinet' => esc_html_x( 'Cabinet color', 'admin', 'baspa' ),
		);

	}

}

if ( !function_exists( 'arctic_product_color_type_label' ) ) {

	/**
	 * Get color type label.
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	function arctic_product_color_type_label( string $type ): string {

		$types = arctic_product_color_types();

		return (string) ( $types[ $type ] ?? $type );

	}

}

if ( !function_exists( 'arctic_product_color_ids_from_meta' ) ) {

	/**
	 * Normalize Meta Box post field values.
	 *
	 * @param int    $product_id
	 * @param string $key
	 *
	 * @return array
	 */
	function arctic_product_color_ids_from_meta( int $product_id, string $key ): array {

		$raw_values = get_post_meta( $product_id, $key, false );
		$ids        = array();

		$flatten = static function ( mixed $value ) use ( &$flatten, &$ids ): void {
			if ( is_array( $value ) ) {
				foreach ( $value as $item ) {
					$flatten( $item );
				}
				return;
			}

			$id = absint( $value );
			if ( $id ) {
				$ids[] = $id;
			}
		};

		foreach ( $raw_values as $raw_value ) {
			$flatten( maybe_unserialize( $raw_value ) );
		}

		return array_values( array_unique( $ids ) );

	}

}

if ( !function_exists( 'arctic_product_color_query' ) ) {

	/**
	 * Query catalog colors for admin fields.
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	function arctic_product_color_query( string $type = '' ): array {

		$args = array(
			'post_type'      => 'spa_color',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		);

		if ( '' !== $type ) {
			$args['meta_query'] = array(
				array(
					'key'   => 'spa_color_type',
					'value' => $type,
				),
			);
		}

		return get_posts( $args );

	}

}

if ( !function_exists( 'arctic_product_color_data' ) ) {

	/**
	 * Normalize one catalog color post for frontend rendering.
	 *
	 * @param int|WP_Post $color
	 * @param string      $expected_type
	 *
	 * @return array
	 */
	function arctic_product_color_data( int|WP_Post $color, string $expected_type = '' ): array {

		$post = $color instanceof WP_Post ? $color : get_post( $color );

		if ( !$post || 'spa_color' !== $post->post_type || 'publish' !== get_post_status( $post ) ) {
			return array();
		}

		$color_id = (int) $post->ID;
		$type     = (string) get_post_meta( $color_id, 'spa_color_type', true );

		if ( '' !== $expected_type && $type !== $expected_type ) {
			return array();
		}

		$image_id  = (int) get_post_thumbnail_id( $color_id );
		$image_url = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
		$hex       = (string) get_post_meta( $color_id, 'spa_color_hex', true );

		return array(
			'id'           => $color_id,
			'name'         => get_the_title( $color_id ),
			'type'         => $type,
			'image'        => $image_id,
			'image_url'    => $image_url,
			'color_hex'    => $hex,
			'asset_source' => $image_id ? 'media-library' : '',
			'asset_status' => $image_id ? 'admin-product-color' : ( '' !== $hex ? 'admin-product-color-hex' : 'WAITING_ON_OWNER' ),
			'source'       => 'spa_color',
		);

	}

}

if ( !function_exists( 'arctic_product_color_fallback_asset' ) ) {

	/**
	 * Resolve legacy/Figma fallback swatches while old imports are being migrated.
	 *
	 * @param string $type
	 * @param string $name
	 *
	 * @return array
	 */
	function arctic_product_color_fallback_asset( string $type, string $name ): array {

		$slug = sanitize_title( remove_accents( $name ) );

		$maps = array(
			'shell' => array(
				'dakota' => array(
					array( 'path' => 'owner-swatches/acrylic-dakota.jpg', 'status' => 'owner-supplied' ),
					array( 'path' => 'figma/color-dakota.png', 'status' => 'figma-fallback' ),
				),
				'espresso' => array(
					array( 'path' => 'owner-swatches/acrylic-espresso.jpg', 'status' => 'owner-supplied' ),
					array( 'path' => 'figma/color-espresso.png', 'status' => 'figma-fallback' ),
				),
				'kalahari' => array(
					array( 'path' => 'owner-swatches/acrylic-kalahari.jpg', 'status' => 'owner-supplied' ),
					array( 'path' => 'figma/color-kalahari.png', 'status' => 'figma-fallback' ),
				),
				'odyssey' => array(
					array( 'path' => 'owner-swatches/acrylic-odyssey.jpg', 'status' => 'owner-supplied' ),
					array( 'path' => 'figma/color-odyssey.png', 'status' => 'figma-fallback' ),
				),
				'platinum-swirl' => array(
					array( 'path' => 'figma/color-platinum-swirl.png', 'status' => 'figma-fallback' ),
				),
			),
			'cabinet' => array(
				'cedrovy-kabinet-standardni' => array(
					array( 'path' => 'figma/cabinet-cedar.png', 'status' => 'figma-fallback' ),
				),
				'cedar' => array(
					array( 'path' => 'figma/cabinet-cedar.png', 'status' => 'figma-fallback' ),
				),
				'bezudrzbovy-kabinet-volitelny' => array(
					array( 'path' => 'figma/cabinet-maintenance-free.png', 'status' => 'figma-fallback' ),
				),
				'maintenance-free' => array(
					array( 'path' => 'figma/cabinet-maintenance-free.png', 'status' => 'figma-fallback' ),
				),
			),
		);

		foreach ( $maps[ $type ][ $slug ] ?? array() as $candidate ) {
			$relative = trim( (string) ( $candidate['path'] ?? '' ), '/\\' );

			if ( '' === $relative ) {
				continue;
			}

			if ( file_exists( WP_CONTENT_DIR . '/uploads/import/' . $relative ) ) {
				return array(
					'image_url'    => content_url( 'uploads/import/' . $relative ),
					'asset_source' => 'uploads/import/' . $relative,
					'asset_status' => (string) ( $candidate['status'] ?? 'available' ),
				);
			}
		}

		return array(
			'image_url'    => '',
			'asset_source' => '',
			'asset_status' => 'WAITING_ON_OWNER',
		);

	}

}

if ( !function_exists( 'arctic_product_legacy_color_options' ) ) {

	/**
	 * Normalize old per-product color fields as a fallback.
	 *
	 * @param int    $product_id
	 * @param string $type
	 *
	 * @return array
	 */
	function arctic_product_legacy_color_options( int $product_id, string $type ): array {

		$name_key   = 'shell' === $type ? 'product_acrylic_colors' : 'product_cabinet_colors';
		$option_key = 'shell' === $type ? 'product_acrylic_color_options' : 'product_cabinet_color_options';
		$names      = array_values( array_filter( array_map( 'strval', get_post_meta( $product_id, $name_key ) ) ) );
		$options    = array();

		if ( empty( $names ) && 'shell' === $type && !has_term( 'dalsi-sortiment', 'product-category', $product_id ) ) {
			$names = array( 'Dakota', 'Kalahari', 'Odyssey', 'Platinum Swirl', 'Espresso' );
		}

		foreach ( get_post_meta( $product_id, $option_key ) as $option ) {
			if ( !is_array( $option ) ) {
				continue;
			}

			$name     = trim( (string) ( $option['name'] ?? '' ) );
			$image_id = isset( $option['image'] ) ? absint( $option['image'] ) : 0;

			if ( '' === $name ) {
				continue;
			}

			$asset = arctic_product_color_fallback_asset( $type, $name );
			if ( $image_id && wp_attachment_is_image( $image_id ) ) {
				$asset = array(
					'image_url'    => (string) wp_get_attachment_image_url( $image_id, 'thumbnail' ),
					'asset_source' => 'media-library',
					'asset_status' => 'admin-product-color-legacy',
				);
			}

			$options[ sanitize_title( remove_accents( $name ) ) ] = array(
				'id'           => 0,
				'name'         => $name,
				'type'         => $type,
				'image'        => $image_id,
				'image_url'    => $asset['image_url'] ?? '',
				'asset_source' => $asset['asset_source'] ?? '',
				'asset_status' => $asset['asset_status'] ?? 'WAITING_ON_OWNER',
				'source'       => 'legacy-product-meta',
			);
		}

		$merged = array();
		foreach ( $names as $name ) {
			$name = trim( (string) $name );
			$slug = sanitize_title( remove_accents( $name ) );

			if ( '' === $slug ) {
				continue;
			}

			if ( isset( $options[ $slug ] ) ) {
				$merged[] = $options[ $slug ];
				unset( $options[ $slug ] );
				continue;
			}

			$asset    = arctic_product_color_fallback_asset( $type, $name );
			$merged[] = array(
				'id'           => 0,
				'name'         => $name,
				'type'         => $type,
				'image'        => 0,
				'image_url'    => $asset['image_url'] ?? '',
				'asset_source' => $asset['asset_source'] ?? '',
				'asset_status' => $asset['asset_status'] ?? 'WAITING_ON_OWNER',
				'source'       => 'legacy-product-meta',
			);
		}

		return array_values( array_merge( $merged, $options ) );

	}

}

if ( !function_exists( 'arctic_product_get_color_options' ) ) {

	/**
	 * Get product color options from global catalog, with legacy fallback.
	 *
	 * @param int    $product_id
	 * @param string $type
	 *
	 * @return array
	 */
	function arctic_product_get_color_options( int $product_id, string $type ): array {

		$key     = 'shell' === $type ? 'product_shell_color_ids' : 'product_cabinet_color_ids';
		$options = array();

		foreach ( arctic_product_color_ids_from_meta( $product_id, $key ) as $color_id ) {
			$color = arctic_product_color_data( $color_id, $type );

			if ( !empty( $color ) ) {
				$options[] = $color;
			}
		}

		if ( !empty( $options ) ) {
			return $options;
		}

		if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() ) {
			return array();
		}

		return arctic_product_legacy_color_options( $product_id, $type );

	}

}

if ( !function_exists( 'arctic_product_has_color_options' ) ) {

	/**
	 * Whether a product has any color section content.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	function arctic_product_has_color_options( int $product_id ): bool {

		return !empty( arctic_product_get_color_options( $product_id, 'shell' ) )
			|| !empty( arctic_product_get_color_options( $product_id, 'cabinet' ) );

	}

}
