<?php

/**
 * Downloads Type
 */

if ( !function_exists( 'arctic_downloads_type_register' ) ) {

	function arctic_downloads_type_register(): void {

		register_post_type( 'download', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Downloads', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Downloads', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Download', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Downloads', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Download', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Download', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Download', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Download', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Download', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Downloads', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Downloads', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Downloads Found in Trash', 'type', 'baspa' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => array(
				'slug'       => _x( 'ke-stazeni', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 39,
			'menu_icon'           => 'dashicons-download',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'arctic_downloads_type_register' );

}

if ( !function_exists( 'arctic_downloads_type_columns' ) ) {

	function arctic_downloads_type_columns(): array {

		return array(
			'cb'                  => '<input type="checkbox">',
			'title'               => esc_html_x( 'Download', 'type', 'baspa' ),
			'download-categories' => esc_html_x( 'Categories', 'type', 'baspa' ),
			'download-file'       => esc_html_x( 'File', 'type', 'baspa' ),
			'order'               => esc_html_x( 'Order', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-download_columns', 'arctic_downloads_type_columns' );

}

if ( !function_exists( 'arctic_downloads_type_columns_content' ) ) {

	function arctic_downloads_type_columns_content( string $column ): void {
		global $post;

		switch ( $column ) {
			case 'download-categories':
				$terms = get_the_terms( $post->ID, 'download-category' );
				if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
					echo esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) );
				}
				break;

			case 'download-file':
				$file = get_post_meta( $post->ID, 'download_file_url', true );
				if ( !empty( $file ) ) {
					echo esc_html( basename( $file ) );
				}
				break;

			case 'order':
				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}
				break;
		}
	}

	add_action( 'manage_download_posts_custom_column', 'arctic_downloads_type_columns_content', 10, 1 );

}
