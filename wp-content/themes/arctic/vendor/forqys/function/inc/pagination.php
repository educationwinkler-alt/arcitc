<?php

/**
 * Pagination
 *
 * @package    forqys/function
 * @since      1.0.0
 */

if ( !function_exists( 'forqy_pagination' ) ) {

	/**
	 * Pagination
	 *
	 * @param array<string> $arguments
	 * @param int|null $pages
	 * @param int|null $paged
	 * @param int|null $page_range
	 *
	 * @return void
	 */
	function forqy_pagination( array $arguments = array(), int $pages = null, int $paged = null, int $page_range = null ): void {
		global $paged;

		if ( $pages == '' ) {
			global $wp_query;

			$pages = $wp_query->max_num_pages;

			if ( !$pages ) {
				$pages = 1;
			}
		}

		if ( empty( $paged ) ) {
			$current = 1;
		} else {
			$current = $paged;
		}

		if ( empty( $page_range ) ) {
			$page_range = 2;
		}

		// Defaults
		$defaults = array(
			'total'    => $pages,
			'current'  => $current,
			'mid_size' => $page_range,
		);

		// Settings
		$settings = wp_parse_args( $defaults, $arguments );

		if ( $pages > 1 ) {
			echo paginate_links( $settings );
		}

	}

}

if ( !function_exists( 'forqy_pagination_post' ) ) {

	/**
	 * Pagination for Post
	 *
	 * @param array<string> $arguments
	 *
	 * @return array<string>
	 */
	function forqy_pagination_post( array $arguments ): array {

		return wp_parse_args( array(
			'before'   => '',
			'after'    => '',
			'pagelink' => '<span>%</span>',
		), $arguments );

	}

	add_filter( 'wp_link_pages_args', 'forqy_pagination_post' );

}
