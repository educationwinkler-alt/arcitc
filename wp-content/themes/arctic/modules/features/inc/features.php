<?php

/**
 * Feature helpers.
 */

if ( !function_exists( 'arctic_feature_fallback_image_url' ) ) {

	/**
	 * Default Figma image used until a feature gets its own approved card image.
	 *
	 * @return string
	 */
	function arctic_feature_fallback_image_url(): string {

		return content_url( 'uploads/import/figma/category-hero-virivky.jpg' );

	}

}

if ( !function_exists( 'arctic_features_get_page_options' ) ) {

	/**
	 * Get page options for feature detail links.
	 *
	 * @return array
	 */
	function arctic_features_get_page_options(): array {

		$options = array();
		$pages   = get_pages( array(
			'post_status' => array( 'publish', 'draft', 'private' ),
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
		) );

		foreach ( $pages as $page ) {
			$options[ (int) $page->ID ] = get_the_title( $page );
		}

		return $options;

	}

}

if ( !function_exists( 'arctic_feature_detail_url' ) ) {

	/**
	 * Get frontend URL for a feature card.
	 *
	 * @param int $feature_id
	 *
	 * @return string
	 */
	function arctic_feature_detail_url( int $feature_id ): string {

		$custom_url = trim( (string) get_post_meta( $feature_id, 'feature_custom_url', true ) );

		if ( '' !== $custom_url ) {
			return $custom_url;
		}

		$detail_page_id = (int) get_post_meta( $feature_id, 'feature_detail_page_id', true );
		$detail_page    = $detail_page_id ? get_post( $detail_page_id ) : null;

		if ( $detail_page && 'trash' !== get_post_status( $detail_page ) ) {
			return get_permalink( $detail_page_id );
		}

		$anchor = sanitize_title( get_post_meta( $feature_id, 'feature_card_anchor', true ) ?: get_the_title( $feature_id ) );

		return home_url( '/vlastnosti/#' . $anchor );

	}

}

if ( !function_exists( 'arctic_feature_data' ) ) {

	/**
	 * Normalize a feature post for templates.
	 *
	 * @param int|WP_Post $feature
	 *
	 * @return array
	 */
	function arctic_feature_data( int|WP_Post $feature ): array {

		$post = $feature instanceof WP_Post ? $feature : get_post( $feature );

		if ( !$post || 'feature' !== $post->post_type || 'publish' !== get_post_status( $post ) ) {
			return array();
		}

		$feature_id   = (int) $post->ID;
		$image_id     = (int) get_post_thumbnail_id( $feature_id );
		$allow_seed   = function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks();
		$image_url    = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'full' ) : ( $allow_seed ? arctic_feature_fallback_image_url() : '' );
		$description  = trim( get_the_excerpt( $feature_id ) );
		$detail_page  = (int) get_post_meta( $feature_id, 'feature_detail_page_id', true );
		$custom_url   = trim( (string) get_post_meta( $feature_id, 'feature_custom_url', true ) );
		$card_anchor  = sanitize_title( get_post_meta( $feature_id, 'feature_card_anchor', true ) ?: get_the_title( $feature_id ) );

		if ( '' === $description ) {
			$description = wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 18, '...' );
		}

		return array(
			'id'             => $feature_id,
			'title'          => get_the_title( $feature_id ),
			'description'    => $description,
			'url'            => arctic_feature_detail_url( $feature_id ),
			'anchor'         => $card_anchor,
			'detail_page_id' => $detail_page,
			'custom_url'     => $custom_url,
			'image_id'       => $image_id,
			'image_url'      => $image_url,
			'image_alt'      => $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '',
			'asset_status'   => $image_id ? 'admin-feature' : ( $allow_seed ? 'figma-export' : 'admin-empty' ),
			'source'         => 'feature-cpt',
		);

	}

}

if ( !function_exists( 'arctic_features_get_items' ) ) {

	/**
	 * Get features ordered for listing pages.
	 *
	 * @return array
	 */
	function arctic_features_get_items(): array {

		$posts = get_posts( array(
			'post_type'      => 'feature',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
		) );

		$features = array();

		foreach ( $posts as $post ) {
			$feature = arctic_feature_data( $post );

			if ( !empty( $feature ) ) {
				$features[] = $feature;
			}
		}

		return $features;

	}

}

if ( !function_exists( 'arctic_feature_get_by_detail_page' ) ) {

	/**
	 * Get the feature linked to a detail page.
	 *
	 * @param int $page_id
	 *
	 * @return array
	 */
	function arctic_feature_get_by_detail_page( int $page_id ): array {

		$posts = get_posts( array(
			'post_type'      => 'feature',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => 'feature_detail_page_id',
			'meta_value'     => $page_id,
		) );

		if ( empty( $posts ) ) {
			return array();
		}

		return arctic_feature_data( $posts[0] );

	}

}
