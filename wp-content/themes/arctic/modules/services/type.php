<?php

/**
 * Services Type
 */

if ( !function_exists( 'arctic_type_service_register' ) ) {

	/**
	 * Register editable service cards used by the services page.
	 */
	function arctic_type_service_register(): void {

		register_post_type( 'service', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Services', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Services', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Service', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Service', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Services', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Service', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Service', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Service', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Service', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Service', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Services', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Services', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Services', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Services Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Service Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Service Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Service published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Service updated.', 'type', 'baspa' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'query_var'           => true,
			'menu_position'       => 37,
			'menu_icon'           => 'dashicons-hammer',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'arctic_type_service_register' );

}

if ( !function_exists( 'arctic_type_service_blocks' ) ) {

	/**
	 * Limit editor blocks to content that renders safely in service cards.
	 *
	 * @param bool|array $allowed_blocks
	 *
	 * @return bool|array
	 */
	function arctic_type_service_blocks( bool|array $allowed_blocks ): bool|array {

		if ( get_post_type( get_the_ID() ) === 'service' ) {
			$allowed_blocks = array(
				'core/paragraph',
				'core/list',
				'core/list-item',
				'core/buttons',
				'core/button',
			);
		}

		return $allowed_blocks;

	}

	add_filter( 'allowed_block_types_all', 'arctic_type_service_blocks', 10 );

}

if ( !function_exists( 'arctic_type_service_columns' ) ) {

	/**
	 * Admin columns.
	 *
	 * @return array
	 */
	function arctic_type_service_columns(): array {

		return array(
			'cb'        => '<input type="checkbox">',
			'thumbnail' => '',
			'title'     => esc_html_x( 'Service', 'type', 'baspa' ),
			'order'     => esc_html_x( 'Order', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-service_columns', 'arctic_type_service_columns' );

}

if ( !function_exists( 'arctic_type_service_columns_content' ) ) {

	/**
	 * Admin columns content.
	 *
	 * @param string $column
	 */
	function arctic_type_service_columns_content( string $column ): void {
		global $post;

		switch ( $column ) {
			case 'thumbnail':
				if ( has_post_thumbnail() ) {
					echo '<a href="' . esc_url( get_edit_post_link() ) . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 45 ) ) . '</a>';
				} else {
					echo '<a href="' . esc_url( get_edit_post_link() ) . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 45px; background-color: white"></div></a>';
				}
				break;

			case 'order':
				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}
				break;
		}

	}

	add_action( 'manage_service_posts_custom_column', 'arctic_type_service_columns_content', 10, 1 );

}

if ( !function_exists( 'arctic_type_service_admin_list' ) ) {

	/**
	 * Keep admin listing in the same order as the frontend cards.
	 *
	 * @param WP_Query $wp_query
	 */
	function arctic_type_service_admin_list( WP_Query $wp_query ): void {

		if ( is_admin() && isset( $wp_query->query['post_type'] ) && 'service' === $wp_query->query['post_type'] && !isset( $_GET['order'] ) ) {
			$wp_query->set( 'orderby', array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			) );
		}

	}

	add_filter( 'pre_get_posts', 'arctic_type_service_admin_list' );

}
