<?php

/**
 * Object
 *
 * @package     forqys/function
 * @since       1.1.0
 */

if ( !function_exists( 'forqy_get_current_object' ) ) {

	/**
	 * Get Current Object
	 *
	 * @return object|null
	 */
	function forqy_get_current_object(): ?object {
		global $wp_query;

		/*
		 * Direct access via $wp_query->queried_object is the universal solution
		 * to get object of any type of query - posts, pages, archives, taxonomies, etc.
		 */

		return $wp_query->queried_object ?? null;
	}

}

if ( !function_exists( 'forqy_get_current_object_title' ) ) {

	/**
	 * Get Current Object Title
	 *
	 * @return string|null
	 */
	function forqy_get_current_object_title(): ?string {
		$queried_object = forqy_get_current_object();

		if ( !$queried_object ) {
			return null;
		}

		// Posts, pages and CPTs
		if ( isset( $queried_object->post_title ) ) {
			return $queried_object->post_title;
		}

		// Taxonomy terms
		if ( isset( $queried_object->name ) ) {
			return $queried_object->name;
		}

		// Archives of CPTs
		if ( isset( $queried_object->labels->name ) ) {
			return $queried_object->labels->name;
		}

		// Authors
		if ( isset( $queried_object->display_name ) ) {
			return $queried_object->display_name;
		}

		// Date archives
		if ( is_date() ) {
			return get_the_archive_title();
		}

		return null;
	}

}

if ( !function_exists( 'forqy_get_current_object_url' ) ) {

	/**
	 * Get Current Object URL
	 *
	 * @return string|null
	 */
	function forqy_get_current_object_url(): ?string {
		$queried_object = forqy_get_current_object();

		if ( !$queried_object ) {
			return null;
		}

		// Posts, pages and CPTs
		if ( isset( $queried_object->ID ) ) {
			return get_permalink( $queried_object->ID );
		}

		// Taxonomy terms
		if ( isset( $queried_object->term_id ) ) {
			return get_term_link( $queried_object );
		}

		// Archives of CPTs
		if ( isset( $queried_object->name ) && isset( $queried_object->has_archive ) ) {
			return get_post_type_archive_link( $queried_object->name );
		}

		// Authors
		if ( isset( $queried_object->ID ) && isset( $queried_object->user_nicename ) ) {
			return get_author_posts_url( $queried_object->ID, $queried_object->user_nicename );
		}

		return null;
	}

}
