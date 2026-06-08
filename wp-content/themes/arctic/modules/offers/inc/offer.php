<?php

/**
 * Offer
 */

if (!function_exists('baspa_offers_has_featured')) {

	/**
	 * Check If Has Featured Offers
	 *
	 * @return bool
	 */
	function baspa_offers_has_featured(): bool {

		$offers_query_args = array(
			'post_type'   => 'offer',
			'post_status' => 'publish',
			'meta_query'  => array(
				array(
					'key'   => 'offer_featured',
					'value' => 1,
				),
			),
		);

		$offers_query = new WP_Query( $offers_query_args );

		return $offers_query->have_posts();

	}

}

if ( !function_exists( 'baspa_offers_archive_page' ) ) {

	/**
	 * Get the editable offers landing page.
	 *
	 * @return array{id:int,title:string,permalink:string}
	 */
	function baspa_offers_archive_page(): array {
		if ( function_exists( 'forqy_get_page_by_template' ) ) {
			$page = forqy_get_page_by_template( 'template-offers.php' );

			if ( !empty( $page[ 'id' ] ) && !empty( $page[ 'permalink' ] ) ) {
				return $page;
			}
		}

		$page = get_page_by_path( 'akcni-nabidky' );
		if ( $page instanceof WP_Post ) {
			return array(
				'id'        => (int) $page->ID,
				'title'     => get_the_title( $page ),
				'permalink' => get_permalink( $page ),
			);
		}

		return array(
			'id'        => 0,
			'title'     => __( 'Akční nabídky', 'baspa' ),
			'permalink' => home_url( '/akcni-nabidky/' ),
		);
	}

}

if ( !function_exists( 'baspa_offers_archive_url' ) ) {

	function baspa_offers_archive_url(): string {
		$page = baspa_offers_archive_page();

		return !empty( $page[ 'permalink' ] ) ? (string) $page[ 'permalink' ] : home_url( '/akcni-nabidky/' );
	}

}

if ( !function_exists( 'baspa_offers_query' ) ) {

	function baspa_offers_query( array $args = array() ): WP_Query {
		$defaults = array(
			'post_type'      => 'offer',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
		);

		return new WP_Query( array_merge( $defaults, $args ) );
	}

}

if ( !function_exists( 'baspa_offer_type_label' ) ) {

	function baspa_offer_type_label( int $post_id ): string {
		$type_custom = trim( (string) get_post_meta( $post_id, 'offer_type_custom', true ) );
		if ( '' !== $type_custom ) {
			return $type_custom;
		}

		$type = (string) get_post_meta( $post_id, 'offer_type', true );
		$map  = array(
			'spring' => __( 'Jarní nabídka', 'baspa' ),
			'summer' => __( 'Letní nabídka', 'baspa' ),
			'autumn' => __( 'Podzimní nabídka', 'baspa' ),
			'winter' => __( 'Zimní nabídka', 'baspa' ),
		);

		return $map[ $type ] ?? '';
	}

}

if ( !function_exists( 'baspa_offer_card_data' ) ) {

	/**
	 * Normalize one offer for archive cards and navigation promos.
	 *
	 * @param int|WP_Post|null $post
	 *
	 * @return array<string,mixed>
	 */
	function baspa_offer_card_data( int|WP_Post|null $post = null ): array {
		$post = get_post( $post );

		if ( !( $post instanceof WP_Post ) ) {
			return array();
		}

		$post_id     = (int) $post->ID;
		$title       = trim( (string) get_post_meta( $post_id, 'offer_title', true ) );
		$short_title = trim( (string) get_post_meta( $post_id, 'offer_title_short', true ) );
		$description = trim( (string) get_post_meta( $post_id, 'offer_description', true ) );
		$image_id    = (int) get_post_thumbnail_id( $post_id );
		$promo_image_ids = function_exists( 'arctic_meta_attachment_ids' ) ? arctic_meta_attachment_ids( $post_id, 'offer_promo_image_id' ) : array();
		$promo_image_id  = !empty( $promo_image_ids[0] ) ? (int) $promo_image_ids[0] : $image_id;

		return array(
			'id'          => $post_id,
			'title'       => '' !== $title ? $title : get_the_title( $post ),
			'short_title' => '' !== $short_title ? $short_title : get_the_title( $post ),
			'description' => $description,
			'type'        => (string) get_post_meta( $post_id, 'offer_type', true ),
			'label'       => baspa_offer_type_label( $post_id ),
			'price'       => trim( (string) get_post_meta( $post_id, 'offer_price', true ) ),
			'old_price'   => trim( (string) get_post_meta( $post_id, 'offer_old_price', true ) ),
			'discount'    => trim( (string) get_post_meta( $post_id, 'offer_discount', true ) ),
			'status'      => trim( (string) get_post_meta( $post_id, 'offer_status', true ) ),
			'valid_until' => trim( (string) get_post_meta( $post_id, 'offer_valid_until', true ) ),
			'image_id'    => $image_id,
			'promo_image_id' => $promo_image_id,
			'permalink'   => get_permalink( $post ),
			'button_text' => trim( (string) get_post_meta( $post_id, 'offer_button_text', true ) ),
			'button_url'  => trim( (string) get_post_meta( $post_id, 'offer_button_url', true ) ),
			'contact_member_id' => absint( get_post_meta( $post_id, 'offer_contact_member_id', true ) ),
		);
	}

}

if ( !function_exists( 'baspa_offers_promo_data' ) ) {

	/**
	 * One editable offer is used as source for small promo cards when available.
	 *
	 * @return array<string,mixed>
	 */
	function baspa_offers_promo_data(): array {
		$query = baspa_offers_query( array(
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'   => 'offer_featured',
					'value' => 1,
				),
			),
		) );

		if ( !$query->have_posts() ) {
			$query = baspa_offers_query( array(
				'posts_per_page' => 1,
			) );
		}

		$data = array();
		if ( $query->have_posts() ) {
			$query->the_post();
			$data = baspa_offer_card_data( get_the_ID() );
			wp_reset_postdata();
		}

		if ( empty( $data ) ) {
			$data = array(
				'id'          => 0,
				'title'       => __( 'Akční nabídky', 'baspa' ),
				'short_title' => __( 'Akční nabídky', 'baspa' ),
				'image_id'    => 0,
			);
		}

		$data[ 'archive_url' ] = baspa_offers_archive_url();
		$data[ 'button_text' ] = __( 'Zobrazit nabídku', 'baspa' );

		return $data;
	}

}
