<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_type_reference_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_type_reference_register(): void {

		register_post_type( 'reference', array(
			'labels'              => array(
				'name'               => esc_html_x( 'References', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'References', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Reference', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Reference', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All References', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Reference', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Reference', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Reference', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Reference', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Reference', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View References', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search References', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No References', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No References Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Reference Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Reference Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Reference published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Reference updated.', 'type', 'baspa' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => array(
				'slug'       => _x( 'project', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 37,
			'menu_icon'           => 'dashicons-portfolio',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_type_reference_register' );

}

if ( !function_exists( 'baspa_type_reference_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_type_reference_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'reference' ) {
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

	add_filter( 'allowed_block_types_all', 'baspa_type_reference_blocks', 10 );

	// Backward compatibility before WP 5.8
	if ( !function_exists( 'get_allowed_block_types' ) ) {
		add_filter( 'allowed_block_types', 'baspa_type_reference_blocks', 10 );
	}

}

if ( !function_exists( 'baspa_type_reference_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_type_reference_columns(): array {

		return array(
			'cb'                   => "<input type=\"checkbox\">",
			'title'                => esc_html__( 'Reference', 'baspa' ),
//			'featured'             => esc_html__( 'Featured?', 'baspa' ),
			'single'               => esc_html__( 'Single', 'baspa' ),
			'reference_categories' => esc_html__( 'Categories', 'baspa' ),
			'description'          => esc_html__( 'Description', 'baspa' ),
			'order'                => esc_html__( 'Order', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-reference_columns', 'baspa_type_reference_columns' );

}

if ( !function_exists( 'baspa_type_reference_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_type_reference_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'featured':

				$featured = get_post_meta( $post->ID, 'reference_featured', true );

				if ( !empty( $featured ) ) {
					echo '&check;';
				}

				break;

			case 'single':

				$single = get_post_meta( $post->ID, 'reference_single', true );

				if ( !empty( $single ) ) {
					echo '&check;';
				}

				break;

			case 'reference_categories':

				$categories = get_the_term_list( $post->ID, 'reference-category', '', ', ', '' );

				if ( !empty( $categories ) ) {
					echo strip_tags( $categories );
				}

				break;

			case 'description':

				$description = get_post_meta( $post->ID, 'reference_description', true );

				if ( !empty( $description ) ) {
					echo wp_kses_post( strip_tags( $description, "<strong><em><br>" ) );
				}

				break;

			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_reference_posts_custom_column', 'baspa_type_reference_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_type_reference_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_type_reference_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'reference' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_type_reference_admin_list' );

}
