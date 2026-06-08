<?php

/**
 * Product Color Type
 */

if ( !function_exists( 'arctic_product_color_type_register' ) ) {

	/**
	 * Register global product color catalog.
	 */
	function arctic_product_color_type_register(): void {

		register_post_type( 'spa_color', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Product Colors', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Product Colors', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Product Color', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Product Color', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Product Colors', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Product Color', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Product Color', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Product Color', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Product Color', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Product Color', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Product Colors', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Product Colors', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Product Colors', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Product Colors Found in Trash', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Product Color Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Product color published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Product color updated.', 'type', 'baspa' ),
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
			'menu_position'       => 38,
			'menu_icon'           => 'dashicons-art',
			'supports'            => array(
				'title',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'arctic_product_color_type_register' );

}

if ( !function_exists( 'arctic_product_color_metabox_register' ) ) {

	/**
	 * Register color catalog metabox.
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function arctic_product_color_metabox_register( array $meta_boxes ): array {

		$meta_boxes[] = array(
			'id'         => 'arctic-metabox--spa-color',
			'title'      => esc_html_x( 'Product Color', 'admin', 'baspa' ),
			'post_types' => array( 'spa_color' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name'    => esc_html_x( 'Color Type', 'admin', 'baspa' ),
					'id'      => 'spa_color_type',
					'type'    => 'select',
					'options' => function_exists( 'arctic_product_color_types' ) ? arctic_product_color_types() : array(
						'shell'   => esc_html_x( 'Shell color', 'admin', 'baspa' ),
						'cabinet' => esc_html_x( 'Cabinet color', 'admin', 'baspa' ),
					),
				),
				array(
					'name' => esc_html_x( 'Fallback HEX Color', 'admin', 'baspa' ),
					'desc' => esc_html_x( 'Used only when no swatch image is uploaded.', 'admin', 'baspa' ),
					'id'   => 'spa_color_hex',
					'type' => 'color',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'arctic_product_color_metabox_register' );

}

if ( !function_exists( 'arctic_product_color_type_columns' ) ) {

	/**
	 * Admin columns.
	 *
	 * @return array
	 */
	function arctic_product_color_type_columns(): array {

		return array(
			'cb'        => '<input type="checkbox">',
			'thumbnail' => '',
			'title'     => esc_html_x( 'Product Color', 'type', 'baspa' ),
			'type'      => esc_html_x( 'Type', 'type', 'baspa' ),
			'order'     => esc_html_x( 'Order', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-spa_color_columns', 'arctic_product_color_type_columns' );

}

if ( !function_exists( 'arctic_product_color_type_columns_content' ) ) {

	/**
	 * Admin columns content.
	 *
	 * @param string $column
	 */
	function arctic_product_color_type_columns_content( string $column ): void {
		global $post;

		switch ( $column ) {
			case 'thumbnail':
				if ( has_post_thumbnail() ) {
					echo '<a href="' . esc_url( get_edit_post_link() ) . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 60 ) ) . '</a>';
				} else {
					$hex = (string) get_post_meta( get_the_ID(), 'spa_color_hex', true );
					echo '<a href="' . esc_url( get_edit_post_link() ) . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 60px; background-color: ' . esc_attr( $hex ?: '#ffffff' ) . '"></div></a>';
				}
				break;

			case 'type':
				if ( function_exists( 'arctic_product_color_type_label' ) ) {
					echo esc_html( arctic_product_color_type_label( (string) get_post_meta( get_the_ID(), 'spa_color_type', true ) ) );
				}
				break;

			case 'order':
				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}
				break;
		}

	}

	add_action( 'manage_spa_color_posts_custom_column', 'arctic_product_color_type_columns_content', 10, 1 );

}

if ( !function_exists( 'arctic_product_color_type_admin_list' ) ) {

	/**
	 * Keep admin listing grouped by type and ordered like the frontend.
	 *
	 * @param WP_Query $wp_query
	 */
	function arctic_product_color_type_admin_list( WP_Query $wp_query ): void {

		if ( is_admin() && isset( $wp_query->query['post_type'] ) && 'spa_color' === $wp_query->query['post_type'] && !isset( $_GET['order'] ) ) {
			$wp_query->set( 'orderby', array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			) );
		}

	}

	add_filter( 'pre_get_posts', 'arctic_product_color_type_admin_list' );

}
