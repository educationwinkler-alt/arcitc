<?php

/**
 * Features Type
 */

if ( !function_exists( 'arctic_type_feature_register' ) ) {

	/**
	 * Register editable feature cards used by the features page.
	 */
	function arctic_type_feature_register(): void {

		register_post_type( 'feature', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Features', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Features', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Feature', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Feature', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Features', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Feature', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Feature', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Feature', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Feature', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Feature', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Features', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Features', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Features', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Features Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Feature Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Feature Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Feature published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Feature updated.', 'type', 'baspa' ),
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
			'menu_icon'           => 'dashicons-admin-tools',
			'supports'            => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'arctic_type_feature_register' );

}

if ( !function_exists( 'arctic_type_feature_columns' ) ) {

	/**
	 * Admin columns.
	 *
	 * @return array
	 */
	function arctic_type_feature_columns(): array {

		return array(
			'cb'        => '<input type="checkbox">',
			'thumbnail' => '',
			'title'     => esc_html_x( 'Feature', 'type', 'baspa' ),
			'detail'    => esc_html_x( 'Detail Page', 'type', 'baspa' ),
			'order'     => esc_html_x( 'Order', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-feature_columns', 'arctic_type_feature_columns' );

}

if ( !function_exists( 'arctic_type_feature_columns_content' ) ) {

	/**
	 * Admin columns content.
	 *
	 * @param string $column
	 */
	function arctic_type_feature_columns_content( string $column ): void {
		global $post;

		switch ( $column ) {
			case 'thumbnail':
				if ( has_post_thumbnail() ) {
					echo '<a href="' . esc_url( get_edit_post_link() ) . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 45 ) ) . '</a>';
				} else {
					echo '<a href="' . esc_url( get_edit_post_link() ) . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 45px; background-color: white"></div></a>';
				}
				break;

			case 'detail':
				$detail_page_id = (int) get_post_meta( get_the_ID(), 'feature_detail_page_id', true );
				if ( $detail_page_id ) {
					echo '<a href="' . esc_url( get_edit_post_link( $detail_page_id ) ) . '">' . esc_html( get_the_title( $detail_page_id ) ) . '</a>';
				}
				break;

			case 'order':
				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}
				break;
		}

	}

	add_action( 'manage_feature_posts_custom_column', 'arctic_type_feature_columns_content', 10, 1 );

}

if ( !function_exists( 'arctic_type_feature_admin_list' ) ) {

	/**
	 * Keep admin listing in the same order as the frontend cards.
	 *
	 * @param WP_Query $wp_query
	 */
	function arctic_type_feature_admin_list( WP_Query $wp_query ): void {

		if ( is_admin() && isset( $wp_query->query['post_type'] ) && 'feature' === $wp_query->query['post_type'] && !isset( $_GET['order'] ) ) {
			$wp_query->set( 'orderby', array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			) );
		}

	}

	add_filter( 'pre_get_posts', 'arctic_type_feature_admin_list' );

}
