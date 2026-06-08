<?php

/**
 * Members
 */

if ( !function_exists( 'baspa_members_exists' ) ) {

	/**
	 * Check If Product Has Variations
	 *
	 * @return bool
	 */
	function baspa_members_exists(): bool {

		$products_query_args = array(
			'post_type'      => 'member',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$products_query = new WP_Query( $products_query_args );

		return (bool)$products_query->found_posts;

	}

}

if ( !function_exists( 'baspa_members_contact_settings' ) ) {

	/**
	 * Admin-selectable member slots used across the site.
	 *
	 * @return array
	 */
	function baspa_members_contact_settings(): array {

		return array(
			'contact_cta'     => array(
				'option'      => 'baspa_member_contact_cta_id',
				'label'       => __( 'Contact CTA member', 'baspa' ),
				'description' => __( 'Used in shared contact blocks below category/product content.', 'baspa' ),
			),
			'product_sidebar' => array(
				'option'      => 'baspa_member_product_sidebar_id',
				'label'       => __( 'Product sidebar member', 'baspa' ),
				'description' => __( 'Used in the contact card on product detail pages.', 'baspa' ),
			),
			'offer_sidebar'   => array(
				'option'      => 'baspa_member_offer_sidebar_id',
				'label'       => __( 'Offer/sidebar member', 'baspa' ),
				'description' => __( 'Used in shared quick-contact cards on offer and sidebar pages.', 'baspa' ),
			),
			'footer_quick'    => array(
				'option'      => 'baspa_member_footer_quick_id',
				'label'       => __( 'Footer quick contact member', 'baspa' ),
				'description' => __( 'Used in the quick contact panel in the footer.', 'baspa' ),
			),
			'showroom_contact' => array(
				'option'      => 'baspa_member_showroom_contact_id',
				'label'       => __( 'Showroom contact member', 'baspa' ),
				'description' => __( 'Used in the contact box on the showroom page.', 'baspa' ),
			),
			'support_help'    => array(
				'option'      => 'baspa_member_support_help_id',
				'label'       => __( 'Support help member', 'baspa' ),
				'description' => __( 'Used in the help card on the support page.', 'baspa' ),
			),
		);
	}

}

if ( !function_exists( 'baspa_member_initials' ) ) {

	/**
	 * Build readable initials for avatar fallbacks.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	function baspa_member_initials( string $name ): string {

		$name  = trim( wp_strip_all_tags( $name ) );
		$words = preg_split( '/\s+/u', $name );

		if ( empty( $words ) ) {
			return '';
		}

		$lower = static function ( string $value ): string {
			return function_exists( 'mb_strtolower' ) ? mb_strtolower( $value ) : strtolower( $value );
		};
		$upper = static function ( string $value ): string {
			return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $value ) : strtoupper( $value );
		};
		$first = static function ( string $value ): string {
			return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 1 ) : substr( $value, 0, 1 );
		};
		$length = static function ( string $value ): int {
			return function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
		};

		$initials = '';
		foreach ( $words as $word ) {
			$word = trim( $word, " \t\n\r\0\x0B.,;:-" );

			if ( '' === $word || in_array( $lower( $word ), array( 'ing', 'bc', 'mgr' ), true ) ) {
				continue;
			}

			$initials .= $upper( $first( $word ) );

			if ( $length( $initials ) >= 2 ) {
				break;
			}
		}

		return $initials ?: $upper( $first( $name ) );
	}

}

if ( !function_exists( 'baspa_member_phone_href' ) ) {

	/**
	 * Normalize a human readable phone number for tel: links.
	 *
	 * @param string $phone
	 *
	 * @return string
	 */
	function baspa_member_phone_href( string $phone ): string {

		return preg_replace( '/[^0-9+]/', '', $phone ) ?: '';
	}

}

if ( !function_exists( 'baspa_member_data' ) ) {

	/**
	 * Normalize member post data for templates.
	 *
	 * @param int|WP_Post $member
	 * @param string      $image_size
	 *
	 * @return array
	 */
	function baspa_member_data( $member, string $image_size = 'thumbnail' ): array {

		$post = $member instanceof WP_Post ? $member : get_post( (int) $member );

		if ( !$post || 'member' !== $post->post_type || 'publish' !== get_post_status( $post ) ) {
			return array();
		}

		$member_id = (int) $post->ID;
		$name      = get_the_title( $member_id );
		$image_id  = get_post_thumbnail_id( $member_id );
		$avatar_ids = array_values( array_filter( array_map( 'absint', get_post_meta( $member_id, 'member_avatar' ) ) ) );
		$avatar_id  = !empty( $avatar_ids ) ? (int) $avatar_ids[0] : 0;
		$image     = $image_id ? wp_get_attachment_image_url( $image_id, $image_size ) : '';
		$avatar    = $avatar_id ? wp_get_attachment_image_url( $avatar_id, $image_size ) : '';

		return array(
			'id'           => $member_id,
			'name'         => $name,
			'position'     => (string) get_post_meta( $member_id, 'member_position', true ),
			'scope'        => (string) get_post_meta( $member_id, 'member_scope', true ),
			'email'        => (string) get_post_meta( $member_id, 'member_email', true ),
			'phone'        => (string) get_post_meta( $member_id, 'member_phone', true ),
			'initials'     => baspa_member_initials( $name ),
			'image_id'     => (int) $image_id,
			'image'        => (string) $image,
			'avatar_id'    => (int) $avatar_id,
			'avatar'       => (string) $avatar,
			'asset_status' => ( $avatar_id || $image_id ) ? 'admin-member' : 'WAITING_ON_OWNER',
			'source'       => 'admin-member',
		);
	}

}

if ( !function_exists( 'baspa_members_get_selected_contact' ) ) {

	/**
	 * Read a selected member for a site contact slot, falling back to contact members.
	 *
	 * @param string $context
	 * @param string $image_size
	 *
	 * @return array
	 */
	function baspa_members_get_selected_contact( string $context = 'contact_cta', string $image_size = 'thumbnail' ): array {

		$settings  = baspa_members_contact_settings();
		$option    = $settings[ $context ]['option'] ?? '';
		$member_id = $option ? (int) get_option( $option, 0 ) : 0;

		if ( $member_id ) {
			$member = baspa_member_data( $member_id, $image_size );

			if ( !empty( $member ) ) {
				return $member;
			}
		}

		$queries = array();
		if ( function_exists( 'baspa_members_query_contacts' ) ) {
			$queries[] = baspa_members_query_contacts();
		}
		if ( function_exists( 'baspa_members_query' ) ) {
			$queries[] = baspa_members_query();
		}

		foreach ( $queries as $query ) {
			if ( $query instanceof WP_Query && !empty( $query->posts ) ) {
				$member = baspa_member_data( $query->posts[0], $image_size );

				if ( !empty( $member ) ) {
					return $member;
				}
			}
		}

		return array();
	}

}

if ( !function_exists( 'baspa_member_avatar_html' ) ) {

	/**
	 * Render the shared member avatar/initials wrapper.
	 *
	 * @param array  $member
	 * @param string $class
	 * @param string $image_size
	 * @param string $loading
	 *
	 * @return string
	 */
	function baspa_member_avatar_html( array $member, string $class, string $image_size = 'thumbnail', string $loading = 'lazy' ): string {

		$classes = array( $class );
		$status  = !empty( $member['asset_status'] ) ? $member['asset_status'] : 'WAITING_ON_OWNER';
		$content = '';

		$image_id = !empty( $member['avatar_id'] ) ? (int) $member['avatar_id'] : (int) ( $member['image_id'] ?? 0 );

		if ( !empty( $image_id ) ) {
			$content = wp_get_attachment_image( $image_id, $image_size, false, array(
				'alt'      => '',
				'loading'  => $loading,
				'decoding' => 'async',
			) );
		}

		if ( empty( $content ) ) {
			$classes[] = $class . '--waiting';
			$content   = esc_html( $member['initials'] ?? '' );
		}

		return sprintf(
			'<span class="%1$s" data-asset-status="%2$s" aria-hidden="true">%3$s</span>',
			esc_attr( implode( ' ', array_filter( $classes ) ) ),
			esc_attr( $status ),
			$content
		);
	}

}
