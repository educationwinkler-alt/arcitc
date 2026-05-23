<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_posts_type_change' ) ) {

	/**
	 * Change Type
	 *
	 * @return void
	 */
	function baspa_posts_type_change(): void {

		remove_post_type_support( 'post', 'excerpt' );

	}

//	add_filter( 'init', 'baspa_posts_type_change', 10, 2 );

}


if ( !function_exists( 'baspa_posts_type_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_posts_type_columns(): array {

		return array(
			'cb'         => "<input type=\"checkbox\">",
			'thumbnail'  => '',
			'title'      => esc_html__( 'Title' ),
			'categories' => esc_html__( 'Categories' ),
			'tags'       => esc_html__( 'Tags' ),
			'author'     => esc_html__( 'Author' ),
			'date'       => esc_html__( 'Date' ),
		);

	}

	add_filter( 'manage_edit-post_columns', 'baspa_posts_type_columns' );

}

if ( !function_exists( 'baspa_posts_type_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_posts_type_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'thumbnail':

				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 45 ) ) . '</a>';
				} else {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 45px; background-color: white"></div></a>';
				}

				break;

		}

	}

	add_action( 'manage_post_posts_custom_column', 'baspa_posts_type_columns_content', 10, 1 );

}
