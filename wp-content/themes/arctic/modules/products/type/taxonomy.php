<?php

/**
 * Taxonomy
 */

if ( !function_exists( 'baspa_products_taxonomy_register' ) ) {

	/**
	 * Register Taxonomy
	 *
	 * @return void
	 */
	function baspa_products_taxonomy_register(): void {

		/**
		 * Category
		 */
		register_taxonomy( 'product-category', 'product', array(
			'labels'       => array(
				'name'                       => esc_html_x( 'Product Categories', 'taxonomy', 'baspa' ),
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
			'label'        => esc_html_x( 'Product Categories', 'taxonomy', 'baspa' ),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'has_archive'  => true,
			'rewrite'      => array(
				'slug'         => esc_html_x( 'catalog', 'taxonomy', 'baspa' ),
				'hierarchical' => true,
				'with_front'   => false,
			),
		) );

		/**
		 * Manufacturer
		 */
		register_taxonomy( 'product-manufacturer', 'product', array(
			'labels'       => array(
				'name'                       => esc_html_x( 'Manufacturers', 'taxonomy', 'baspa' ),
				'singular_name'              => esc_html_x( 'Manufacturer', 'taxonomy', 'baspa' ),
				'search_items'               => esc_html_x( 'Search Manufacturers', 'taxonomy', 'baspa' ),
				'popular_items'              => esc_html_x( 'Popular Manufacturers', 'taxonomy', 'baspa' ),
				'all_items'                  => esc_html_x( 'All Manufacturers', 'taxonomy', 'baspa' ),
				'edit_item'                  => esc_html_x( 'Edit Manufacturer', 'taxonomy', 'baspa' ),
				'view_item'                  => esc_html_x( 'View Manufacturer', 'taxonomy', 'baspa' ),
				'update_item'                => esc_html_x( 'Update Manufacturer', 'taxonomy', 'baspa' ),
				'add_new_item'               => esc_html_x( 'Add New Manufacturer', 'taxonomy', 'baspa' ),
				'new_item_name'              => esc_html_x( 'New Manufacturer Name', 'taxonomy', 'baspa' ),
				'not_found'                  => esc_html_x( 'No manufacturers found.', 'taxonomy', 'baspa' ),
				'add_or_remove_items'        => esc_html_x( 'Add or remove manufacturers', 'taxonomy', 'baspa' ),
				'separate_items_with_commas' => esc_html_x( 'Separate manufacturers with commas', 'taxonomy', 'baspa' ),
				'back_to_items'              => esc_html_x( 'Back to manufacturers', 'taxonomy', 'baspa' ),
			),
			'label'        => esc_html_x( 'Manufacturers', 'taxonomy', 'baspa' ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'has_archive'  => false,
			'rewrite'      => array(
				'slug'         => esc_html_x( 'manufacturer', 'taxonomy', 'baspa' ),
				'hierarchical' => false,
				'with_front'   => false,
			),
		) );

		/**
		 * Product Kind
		 */
		register_taxonomy( 'product-kind', 'product', array(
			'labels'       => array(
				'name'          => esc_html_x( 'Product Types', 'taxonomy', 'baspa' ),
				'singular_name' => esc_html_x( 'Product Type', 'taxonomy', 'baspa' ),
				'all_items'     => esc_html_x( 'All Product Types', 'taxonomy', 'baspa' ),
				'edit_item'     => esc_html_x( 'Edit Product Type', 'taxonomy', 'baspa' ),
				'add_new_item'  => esc_html_x( 'Add Product Type', 'taxonomy', 'baspa' ),
				'new_item_name' => esc_html_x( 'New Product Type', 'taxonomy', 'baspa' ),
			),
			'label'        => esc_html_x( 'Product Types', 'taxonomy', 'baspa' ),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'has_archive'  => true,
			'rewrite'      => array(
				'slug'         => esc_html_x( 'sortiment', 'taxonomy', 'baspa' ),
				'hierarchical' => true,
				'with_front'   => false,
			),
		) );

		/**
		 * Product Series
		 */
		register_taxonomy( 'product-series', 'product', array(
			'labels'       => array(
				'name'          => esc_html_x( 'Product Series', 'taxonomy', 'baspa' ),
				'singular_name' => esc_html_x( 'Product Series', 'taxonomy', 'baspa' ),
				'all_items'     => esc_html_x( 'All Product Series', 'taxonomy', 'baspa' ),
				'edit_item'     => esc_html_x( 'Edit Product Series', 'taxonomy', 'baspa' ),
				'add_new_item'  => esc_html_x( 'Add Product Series', 'taxonomy', 'baspa' ),
				'new_item_name' => esc_html_x( 'New Product Series', 'taxonomy', 'baspa' ),
			),
			'label'        => esc_html_x( 'Product Series', 'taxonomy', 'baspa' ),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'has_archive'  => true,
			'rewrite'      => array(
				'slug'         => esc_html_x( 'rada', 'taxonomy', 'baspa' ),
				'hierarchical' => true,
				'with_front'   => false,
			),
		) );

	}

	add_action( 'init', 'baspa_products_taxonomy_register' );

}
