<?php

/**
 * Page
 *
 * @package    forqys/function
 * @since      1.0.0
 */

if ( !function_exists( 'forqy_get_page_by_template' ) ) {

	/**
	 * Get Page by Page Template
	 *
	 * @param $template
	 *
	 * @return array
	 */
	function forqy_get_page_by_template( $template ): array {

		$pages = get_posts( array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'meta_query'  => array(
				array(
					'key'     => '_wp_page_template',
					'value'   => $template, // template.php
					'compare' => '=',
				)
			)
		) );

		if ( !empty( $pages ) ) {

			foreach ( $pages as $page ) {

				$data[ 'id' ]        = $page->ID;
				$data[ 'title' ]     = get_the_title( $page->ID );
				$data[ 'permalink' ] = get_permalink( $page->ID );

				return $data;

			}

		}

		return array();

	}

}
