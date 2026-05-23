<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_offer_type_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_offer_type_register(): void {

		register_post_type( 'offer', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Offers', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Offers', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Offer', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Offer', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Offers', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Offer', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Offer', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Offer', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Offer', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Offer', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Offers', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Offers', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Offers', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Offers Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Offer Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Offer Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Offer published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Offer updated.', 'type', 'baspa' ),
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
			'query_var'           => false,
			'rewrite'             => array(
				'slug'       => _x( 'offer', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 35,
			'menu_icon'           => 'dashicons-tag',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_offer_type_register' );

}

if ( !function_exists( 'baspa_offer_type_content_default' ) ) {

	/**
	 * Default Content
	 *
	 * @param $content
	 * @param $post
	 *
	 * @return mixed|string
	 */
	function baspa_offer_type_content_default( $content, $post ): mixed {

		if ( $post->post_type == 'offer' ) {
			$content = '';
		}

		return $content;
	}

	add_filter( 'default_content', 'baspa_offer_type_content_default', 10, 2 );

}

if ( !function_exists( 'baspa_offer_type_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_type_offer_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'offer' ) {
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

//	add_filter( 'allowed_block_types_all', 'baspa_type_offer_blocks', 10 );

	// Backward compatibility before WP 5.8
//	if ( !function_exists( 'get_allowed_block_types' ) ) {
//		add_filter( 'allowed_block_types', 'baspa_type_offer_blocks', 10 );
//	}

}

if ( !function_exists( 'baspa_offer_type_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_offer_type_columns(): array {

		return array(
			'cb'        => "<input type=\"checkbox\">",
			'thumbnail' => '',
			'title'     => esc_html__( 'Offer', 'baspa' ),
			'featured'  => esc_html__( 'Featured?', 'baspa' ),
			'order'     => esc_html__( 'Order', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-offer_columns', 'baspa_offer_type_columns' );

}

if ( !function_exists( 'baspa_offer_type_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_offer_type_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'thumbnail':

				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 45 ) ) . '</a>';
				} else {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 45px; background-color: white"></div></a>';
				}

				break;

			case 'featured':

				$featured = get_post_meta($post->ID, 'offer_featured', true);

				if ( $featured ) {
					echo '&check;';
				}

				break;

			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_offer_posts_custom_column', 'baspa_offer_type_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_offer_type_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_offer_type_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'offer' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_offer_type_admin_list' );

}
