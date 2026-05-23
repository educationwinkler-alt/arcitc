<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_slides_type_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_slides_type_register(): void {

		register_post_type( 'slide', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Slides', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Slides', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Slide', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Slide', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Slides', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Slide', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Slide', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Slide', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Slide', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Slide', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Slides', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Slides', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Slides', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Slides Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Slide Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Slide Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Slide published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Slide updated.', 'type', 'baspa' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'query_var'           => true,
			'rewrite'             => array(
				'slug'       => _x( 'slide', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 35,
			'menu_icon'           => 'dashicons-align-full-width',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_slides_type_register' );

}

if ( !function_exists( 'baspa_slides_type_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param array<string> $allowed_blocks
	 *
	 * @return bool|array<string>
	 */
	function baspa_slides_type_blocks( array|bool $allowed_blocks ): bool|array {

		if ( get_post_type( get_the_ID() ) === 'slide' ) {
			$allowed_blocks = array(
				'core/heading',
				'core/paragraph',
				'core/list',
				'core/list-item',
				'core/columns',
				'core/column',
				'core/group',
				'core/buttons',
				'core/button',
				'core/image',
			);
		}

		return $allowed_blocks;

	}

	add_filter( 'allowed_block_types_all', 'baspa_slides_type_blocks', 10 );

	// Backward compatibility before WP 5.8
	if ( !function_exists( 'get_allowed_block_types' ) ) {
		add_filter( 'allowed_block_types', 'baspa_slides_type_blocks', 10 );
	}

}

if ( !function_exists( 'baspa_slides_type_content_default' ) ) {

	/**
	 * Default Content
	 *
	 * @param $content
	 * @param $post
	 *
	 * @return mixed|string
	 */
	function baspa_slides_type_content_default( $content, $post ) {

		if ( $post->post_type == 'slide' ) {
			$content = '';
		}

		return $content;
	}

//	add_filter( 'default_content', 'baspa_slides_type_content_default', 10, 2 );

}

if ( !function_exists( 'baspa_slides_type_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_slides_type_columns(): array {

		return array(
			'cb'        => "<input type=\"checkbox\">",
			'thumbnail' => '',
			'title'     => esc_html_x( 'Slide', 'type', 'baspa' ),
			'order'     => esc_html_x( 'Order', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-slide_columns', 'baspa_slides_type_columns' );

}

if ( !function_exists( 'baspa_slides_type_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_slides_type_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'thumbnail':

				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 45 ) ) . '</a>';
				} else {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 45px; background-color: white"></div></a>';
				}

				break;

			// Order
			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_slide_posts_custom_column', 'baspa_slides_type_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_slides_type_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_slides_type_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'slide' && !isset( $_GET[ 'order' ] ) ) {

				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				) );

			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_slides_type_admin_list' );

}
