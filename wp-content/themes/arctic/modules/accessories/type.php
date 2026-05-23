<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_type_accessory_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_type_accessory_register(): void {

		register_post_type( 'accessory', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Accessories', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Accessories', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Accessory', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Accessory', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Accessories', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Accessory', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Accessory', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Accessory', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Accessory', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Accessory', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Accessories', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Accessories', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Accessories', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Accessories Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Accessory Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Accessory Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Accessory published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Accessory updated.', 'type', 'baspa' ),
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
				'slug'       => _x( 'accessory', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 37,
			'menu_icon'           => 'dashicons-admin-plugins',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_type_accessory_register' );

}

if ( !function_exists( 'baspa_type_accessory_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_type_accessory_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'accessory' ) {
			$allowed_blocks = array(
				'core/heading',
				'core/paragraph',
				'core/list',
				'core/list-item',
				'core/columns',
				'core/column',
				'core/buttons',
				'core/button',
				'core/image',
				'core/post-featured-image',
				'core/cover',
				'core/group',
			);
		}

		return $allowed_blocks;

	}

	add_filter( 'allowed_block_types_all', 'baspa_type_accessory_blocks', 10 );

	// Backward compatibility before WP 5.8
	if ( !function_exists( 'get_allowed_block_types' ) ) {
		add_filter( 'allowed_block_types', 'baspa_type_accessory_blocks', 10 );
	}

}

if ( !function_exists( 'baspa_type_accessory_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_type_accessory_columns(): array {

		return array(
			'cb'                   => "<input type=\"checkbox\">",
			'thumbnail'            => '',
			'title'                => esc_html__( 'Accessory', 'baspa' ),
			'accessory-categories' => esc_html__( 'Categories', 'baspa' ),
			'url'                  => esc_html__( 'URL', 'baspa' ),
			'order'                => esc_html__( 'Order', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-accessory_columns', 'baspa_type_accessory_columns' );

}

if ( !function_exists( 'baspa_type_accessory_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_type_accessory_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'thumbnail':

				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 60 ) ) . '</a>';
				} else {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 60px; background-color: white"></div></a>';
				}

				break;

			case 'accessory-categories':

				$terms = get_the_terms( $post->ID, 'accessory-category' );

				if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
					$terms = wp_list_sort( $terms, 'parent', 'ASC' );

					$term_names = array_map( function ( $term ) {
						return $term->name;
					}, $terms );

					echo implode( ', ', $term_names );
				}

				break;

			case 'url':

				$url = get_post_meta( $post->ID, 'accessory_url', true );

				if ( !empty( $url ) ) {
					echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $url ) . '</a>';
				}

				break;

			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_accessory_posts_custom_column', 'baspa_type_accessory_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_type_accessory_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_type_accessory_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'accessory' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_type_accessory_admin_list' );

}
