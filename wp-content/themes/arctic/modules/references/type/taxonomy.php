<?php

/**
 * Taxonomy
 */

if ( !function_exists( 'baspa_references_taxonomy_register' ) ) {

	/**
	 * Register Taxonomy
	 *
	 * @return void
	 */
	function baspa_references_taxonomy_register(): void {

		/**
		 * Category
		 */
		register_taxonomy( 'reference-category', 'reference', array(
			'labels'       => array(
				'name'                       => esc_html_x( 'Reference Categories', 'taxonomy', 'baspa' ),
				'menu_name'                  => esc_html_x( 'Categories', 'taxonomy', 'baspa' ),
				'singular_name'              => esc_html_x( 'Category', 'taxonomy', 'baspa' ),
				'search_items'               => esc_html_x( 'Search Categories', 'taxonomy', 'baspa' ),
				'popular_items'              => esc_html_x( 'Popular Categories', 'taxonomy', 'baspa' ),
				'all_items'                  => esc_html_x( 'All Categories', 'taxonomy', 'baspa' ),
				'edit_item'                  => esc_html_x( 'Edit Category', 'taxonomy', 'baspa' ),
				'view_item'                  => esc_html_x( 'View Category', 'taxonomy', 'baspa' ),
				'update_item'                => esc_html_x( 'Update Category', 'taxonomy', 'baspa' ),
				'add_new_item'               => esc_html_x( 'Add New Category', 'taxonomy', 'baspa' ),
				'new_item_name'              => esc_html_x( 'New Category Name', 'taxonomy', 'baspa' ),
				'not_found'                  => esc_html_x( 'No categories found.', 'taxonomy', 'baspa' ),
				'add_or_remove_items'        => esc_html_x( 'Add or remove categories', 'taxonomy', 'baspa' ),
				'separate_items_with_commas' => esc_html_x( 'Separate categories with commas', 'taxonomy', 'baspa' ),
				'back_to_items'              => esc_html_x( 'Back to categories', 'taxonomy', 'baspa' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'has_archive'  => false,
			'rewrite'      => array(
				'slug'       => esc_attr_x( 'references', 'taxonomy', 'baspa' ),
				'with_front' => false,
			),
		) );

	}

	add_action( 'init', 'baspa_references_taxonomy_register' );

}
