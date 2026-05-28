<?php

/**
 * Site Icon
 */

if ( !function_exists( 'arctic_site_icon_asset_path' ) ) {
	function arctic_site_icon_asset_path(): string {
		return get_theme_file_path( 'images/icon.png' );
	}
}

if ( !function_exists( 'arctic_site_icon_asset_url' ) ) {
	function arctic_site_icon_asset_url(): string {
		return get_theme_file_uri( 'images/icon.png' );
	}
}

if ( !function_exists( 'arctic_find_site_icon_attachment' ) ) {
	function arctic_find_site_icon_attachment(): int {
		$queries = array(
			array(
				'meta_key'   => '_arctic_site_icon_asset',
				'meta_value' => '1',
			),
			array(
				'meta_key'   => '_arctic_seed_key',
				'meta_value' => 'arctic-site-icon',
			),
		);

		foreach ( $queries as $query ) {
			$ids = get_posts( array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => $query['meta_key'],
				'meta_value'     => $query['meta_value'],
			) );

			if ( !empty( $ids ) ) {
				$attachment_id = (int) $ids[0];
				update_post_meta( $attachment_id, '_arctic_site_icon_asset', '1' );
				update_post_meta( $attachment_id, '_arctic_seed_key', 'arctic-site-icon' );
				return $attachment_id;
			}
		}

		return 0;
	}
}

if ( !function_exists( 'arctic_create_site_icon_attachment' ) ) {
	function arctic_create_site_icon_attachment(): int {
		$icon_path = arctic_site_icon_asset_path();
		if ( !file_exists( $icon_path ) || !is_readable( $icon_path ) ) {
			return 0;
		}

		$bits = file_get_contents( $icon_path );
		if ( false === $bits ) {
			return 0;
		}

		$upload = wp_upload_bits( 'arctic-site-icon.png', null, $bits );
		if ( !empty( $upload['error'] ) || empty( $upload['file'] ) ) {
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = wp_insert_attachment( array(
			'post_title'     => 'Arctic Site Icon',
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image/png',
		), $upload['file'], 0, true );

		if ( is_wp_error( $attachment_id ) ) {
			return 0;
		}

		$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		if ( !is_wp_error( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		update_post_meta( $attachment_id, '_arctic_site_icon_asset', '1' );
		update_post_meta( $attachment_id, '_arctic_seed_key', 'arctic-site-icon' );
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Arctic Spas favicon' );

		return (int) $attachment_id;
	}
}

if ( !function_exists( 'arctic_resolve_site_icon_attachment' ) ) {
	function arctic_resolve_site_icon_attachment(): int {
		$attachment_id = arctic_find_site_icon_attachment();
		if ( $attachment_id > 0 ) {
			return $attachment_id;
		}

		return arctic_create_site_icon_attachment();
	}
}

if ( !function_exists( 'arctic_enforce_site_icon_option' ) ) {
	/**
	 * Keep site_icon deterministic after DB imports (local/stage/prod).
	 */
	function arctic_enforce_site_icon_option(): void {
		if ( wp_installing() ) {
			return;
		}

		if ( defined( 'ARCTIC_DISABLE_SITE_ICON_ENFORCE' ) && ARCTIC_DISABLE_SITE_ICON_ENFORCE ) {
			return;
		}

		$icon_path = arctic_site_icon_asset_path();
		if ( !file_exists( $icon_path ) || !is_readable( $icon_path ) ) {
			return;
		}

		$target_id = arctic_resolve_site_icon_attachment();
		if ( $target_id <= 0 ) {
			return;
		}

		if ( (int) get_option( 'site_icon', 0 ) !== $target_id ) {
			update_option( 'site_icon', $target_id, false );
		}
	}

	add_action( 'after_setup_theme', 'arctic_enforce_site_icon_option', 20 );
}

if ( !function_exists( 'arctic_site_icon_url_fallback' ) ) {
	/**
	 * Final fallback for local runs where browser cache can hold legacy icon URL.
	 */
	function arctic_site_icon_url_fallback( string $url, int $size = 512, int $blog_id = 0 ): string {
		$icon_path = arctic_site_icon_asset_path();
		if ( !file_exists( $icon_path ) ) {
			return $url;
		}

		if ( function_exists( 'wp_get_environment_type' ) && 'local' === wp_get_environment_type() ) {
			return arctic_site_icon_asset_url();
		}

		return $url;
	}

	add_filter( 'get_site_icon_url', 'arctic_site_icon_url_fallback', 30, 3 );
}
