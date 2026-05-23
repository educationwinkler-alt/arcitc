<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_type_partner_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_type_partner_register(): void {

		register_post_type( 'partner', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Partners', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Partners', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Partner', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Partner', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Partners', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Partner', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Partner', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Partner', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Partner', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Partner', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Partners', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Partners', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Partners', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Partners Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Partner Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Partner Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Partner published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Partner updated.', 'type', 'baspa' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'query_var'           => true,
			'rewrite'             => array(
				'slug'       => _x( 'partner', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 37,
			'menu_icon'           => 'dashicons-businessman',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_type_partner_register' );

}

if ( !function_exists( 'baspa_type_partner_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_type_partner_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'partner' ) {
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

	add_filter( 'allowed_block_types_all', 'baspa_type_partner_blocks', 10 );

	// Backward compatibility before WP 5.8
	if ( !function_exists( 'get_allowed_block_types' ) ) {
		add_filter( 'allowed_block_types', 'baspa_type_partner_blocks', 10 );
	}

}

if ( !function_exists( 'baspa_type_partner_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_type_partner_columns(): array {

		return array(
			'cb'          => "<input type=\"checkbox\">",
			'thumbnail'   => '',
			'title'       => esc_html__( 'Partner', 'baspa' ),
			'url'         => esc_html__( 'URL', 'baspa' ),
			'description' => esc_html__( 'Description', 'baspa' ),
			'order'       => esc_html__( 'Order', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-partner_columns', 'baspa_type_partner_columns' );

}

if ( !function_exists( 'baspa_type_partner_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_type_partner_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'thumbnail':

				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 30 ) ) . '</a>';
				} else {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 30px; background-color: white"></div></a>';
				}

				break;

			case 'url':

				$url = get_post_meta( $post->ID, 'partner_url', true );

				if ( !empty( $url ) ) {
					echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_url( $url ) . '</a>';
				}

				break;

			case 'description':

				$description = get_post_meta( $post->ID, 'partner_description', true );

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

	add_action( 'manage_partner_posts_custom_column', 'baspa_type_partner_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_type_partner_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_type_partner_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'partner' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_type_partner_admin_list' );

}
