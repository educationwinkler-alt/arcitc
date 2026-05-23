<?php

/**
 * Post
 */

if ( ! function_exists( 'baspa_excerpt_length' ) ) {

	/**
	 * Excerpt - Length
	 *
	 * @return integer
	 */
	function baspa_excerpt_length(): int {
		return 1; // Number of Words
	}

	add_filter( 'excerpt_length', 'baspa_excerpt_length', 999 );

}

if ( ! function_exists( 'baspa_excerpt_more' ) ) {

	/**
	 * Excerpt - More
	 *
	 * @return string
	 */
	function baspa_excerpt_more(): string {
		return esc_html__( ' ...', 'baspa' );
	}

	add_filter( 'excerpt_more', 'baspa_excerpt_more', 999 );

}
