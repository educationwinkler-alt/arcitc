<?php

/**
 * Downloads Taxonomy
 */

if ( !function_exists( 'arctic_downloads_taxonomy_register' ) ) {

	function arctic_downloads_taxonomy_register(): void {

		register_taxonomy( 'download-category', 'download', array(
			'labels'       => array(
				'name'          => esc_html_x( 'Download Categories', 'taxonomy', 'baspa' ),
				'singular_name' => esc_html_x( 'Download Category', 'taxonomy', 'baspa' ),
				'all_items'     => esc_html_x( 'All Download Categories', 'taxonomy', 'baspa' ),
				'edit_item'     => esc_html_x( 'Edit Download Category', 'taxonomy', 'baspa' ),
				'add_new_item'  => esc_html_x( 'Add Download Category', 'taxonomy', 'baspa' ),
				'new_item_name' => esc_html_x( 'New Download Category', 'taxonomy', 'baspa' ),
			),
			'label'        => esc_html_x( 'Download Categories', 'taxonomy', 'baspa' ),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'has_archive'  => false,
			'rewrite'      => array(
				'slug'         => esc_html_x( 'ke-stazeni/kategorie', 'taxonomy', 'baspa' ),
				'hierarchical' => true,
				'with_front'   => false,
			),
		) );

	}

	add_action( 'init', 'arctic_downloads_taxonomy_register' );

}
