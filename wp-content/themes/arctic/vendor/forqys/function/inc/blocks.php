<?php

/**
 * Blocks
 *
 * @package     forqys/function
 * @since       1.0.7
 */

if ( !function_exists( 'forqy_get_reusable_block' ) ) {

	/**
	 * Get Reusable Block by ID or Title
	 *
	 * @param $id_or_title
	 *
	 * @return string
	 */
	function forqy_get_reusable_block( $id_or_title ): string {

		if ( is_int( $id_or_title ) ) {
			$reusable_block = get_post( $id_or_title );
		} else {
			$query = new WP_Query(array(
				'post_type'   => 'wp_block',
				'title'       => $id_or_title,
				'post_status' => 'publish',
				'posts_per_page' => 1,
			));

			$reusable_block = $query->have_posts() ? $query->posts[0] : null;
		}

		if ( $reusable_block && 'wp_block' === $reusable_block->post_type ) {
			return do_blocks( $reusable_block->post_content );
		}

		return '';
	}

}
