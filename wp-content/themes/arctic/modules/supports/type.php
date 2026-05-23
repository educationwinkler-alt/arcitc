<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_type_support_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_type_support_register(): void {

		register_post_type( 'support', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Support', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Support', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Support', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Support', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Supports', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Support', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Support', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Support', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Support', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Support', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Supports', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Supports', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Supports', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Supports Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Support Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Support Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Support published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Support updated.', 'type', 'baspa' ),
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
			'publicly_queryable'  => false,
			'query_var'           => true,
			'rewrite'             => array(
				'slug'       => _x( 'support', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 38,
			'menu_icon'           => 'dashicons-sos',
			'supports'            => array(
				'title',
				'editor',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_type_support_register' );

}

if ( !function_exists( 'baspa_type_support_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_type_support_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'support' ) {
			$allowed_blocks = array(
				'core/heading',
				'core/paragraph',
				'core/list',
				'core/image',
				'core/cover',
				'core/columns',
				'core/column',
				'core/buttons',
				'core/button',
				'core/group',
			);
		}

		return $allowed_blocks;

	}

	add_filter( 'allowed_block_types_all', 'baspa_type_support_blocks', 10 );

}

if ( !function_exists( 'baspa_type_support_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_type_support_columns(): array {

		return array(
			'cb'                     => "<input type=\"checkbox\">",
			'title'                  => esc_html_x( 'Support', 'type', 'baspa' ),
			'support-categories'     => esc_html_x( 'Categories', 'type', 'baspa' ),
			'display-pricelist'      => esc_html_x( 'Also in pricelist?', 'type', 'baspa' ),
			'display-pricelist-only' => esc_html_x( 'Only in pricelist?', 'type', 'baspa' ),
			'order'                  => esc_html_x( 'Order', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-support_columns', 'baspa_type_support_columns' );

}

if ( !function_exists( 'baspa_type_support_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_type_support_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'support-categories':

				$terms = get_the_terms( $post->ID, 'support-category' );

				if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
					$terms = wp_list_sort( $terms, 'parent', 'ASC' );

					$term_names = array_map( function ( $term ) {
						return $term->name;
					}, $terms );

					echo implode( ', ', $term_names );
				}

				break;

			case 'display-pricelist':

				$display_pricelist = get_post_meta( $post->ID, 'support_display_pricelist', true );

				do_action('qm/debug', $display_pricelist);

				if ( $display_pricelist ) {
					echo esc_html_x( 'Yes', 'admin', 'baspa' );
				}

				break;

			case 'display-pricelist-only':

				$display_pricelist_only = get_post_meta( $post->ID, 'support_display_pricelist_only', true );

				if ( $display_pricelist_only ) {
					echo esc_html_x( 'Yes', 'admin', 'baspa' );
				}

				break;

			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_support_posts_custom_column', 'baspa_type_support_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_type_support_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_type_support_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'support' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_type_support_admin_list' );

}
