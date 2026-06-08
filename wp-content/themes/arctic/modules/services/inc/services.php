<?php

/**
 * Services helpers.
 */

if ( !function_exists( 'arctic_service_data' ) ) {

	/**
	 * Normalize a service post for templates.
	 *
	 * @param int|WP_Post $service
	 *
	 * @return array
	 */
	function arctic_service_data( int|WP_Post $service ): array {

		$post = $service instanceof WP_Post ? $service : get_post( $service );

		if ( !$post || 'service' !== $post->post_type || 'publish' !== get_post_status( $post ) ) {
			return array();
		}

		$service_id = (int) $post->ID;
		$image_id   = (int) get_post_thumbnail_id( $service_id );

		return array(
			'id'           => $service_id,
			'title'        => get_the_title( $service_id ),
			'content'      => trim( apply_filters( 'the_content', $post->post_content ) ),
			'image_id'     => $image_id,
			'image_alt'    => (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
			'asset_status' => $image_id ? 'admin-service' : 'WAITING_ON_OWNER',
			'source'       => 'service-cpt',
		);

	}

}

if ( !function_exists( 'arctic_services_get_items' ) ) {

	/**
	 * Get services ordered for the services page.
	 *
	 * @return array
	 */
	function arctic_services_get_items(): array {

		$posts = get_posts( array(
			'post_type'      => 'service',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
		) );

		$services = array();

		foreach ( $posts as $post ) {
			$service = arctic_service_data( $post );

			if ( !empty( $service ) ) {
				$services[] = $service;
			}
		}

		return $services;

	}

}
