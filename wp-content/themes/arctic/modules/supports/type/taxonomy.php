<?php

/**
 * Taxonomy
 */

if ( !function_exists( 'baspa_supports_taxonomy_register' ) ) {

	/**
	 * Register Taxonomy
	 *
	 * @return void
	 */
	function baspa_supports_taxonomy_register(): void {

		/**
		 * Category
		 */
		register_taxonomy( 'support-category', 'support', array(
			'labels'       => array(
				'name'                       => esc_html_x( 'Categories', 'taxonomy', 'baspa' ),
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
			'label'        => esc_html_x( 'Support Categories', 'taxonomy', 'baspa' ),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'has_archive'  => true,
			'rewrite'      => array(
				'slug'         => esc_html_x( 'supports', 'taxonomy', 'baspa' ),
				'hierarchical' => true,
				'with_front'   => false,
			),
		) );

	}

	add_action( 'init', 'baspa_supports_taxonomy_register' );

}

if ( !function_exists( 'baspa_supports_taxonomy_columns' ) ) {

	function baspa_supports_taxonomy_columns( $columns ): array {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			if ( $key === 'posts' ) {
				$new_columns[ 'display_pricelist' ]      = _x( 'Also in pricelist?', 'type', 'baspa' );
				$new_columns[ 'display_pricelist_only' ] = _x( 'Only in pricelist?', 'type', 'baspa' );
			}
			$new_columns[ $key ] = $value;
		}

		return $new_columns;
	}

	add_filter( 'manage_edit-support-category_columns', 'baspa_supports_taxonomy_columns' );

}

if ( !function_exists( 'baspa_supports_taxonomy_columns_content' ) ) {

	function baspa_supports_taxonomy_columns_content( $content, $column_name, $term_id ) {
		if ( $column_name === 'display_pricelist' ) {
			$content = get_term_meta( $term_id, 'display_pricelist', true ) ? esc_html_x( 'Yes', 'admin', 'baspa' ) : '—';
		}
		if ( $column_name === 'display_pricelist_only' ) {
			$content = get_term_meta( $term_id, 'display_pricelist_only', true ) ? esc_html_x( 'Yes', 'admin', 'baspa' ) : '—';
		}

		return $content;
	}

	add_filter( 'manage_support-category_custom_column', 'baspa_supports_taxonomy_columns_content', 10, 3 );

}
