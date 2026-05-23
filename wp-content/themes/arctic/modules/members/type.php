<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_type_member_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_type_member_register(): void {

		register_post_type( 'member', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Members', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Members', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Member', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Member', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Members', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Member', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Member', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Member', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Member', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Member', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Members', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Members', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Members', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Members Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Member Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Member Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Member published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Member updated.', 'type', 'baspa' ),
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
				'slug'       => _x( 'member', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 37,
			'menu_icon'           => 'dashicons-id-alt',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_type_member_register' );

}

if ( !function_exists( 'baspa_type_member_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_type_member_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'member' ) {
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

	add_filter( 'allowed_block_types_all', 'baspa_type_member_blocks', 10 );

	// Backward compatibility before WP 5.8
	if ( !function_exists( 'get_allowed_block_types' ) ) {
		add_filter( 'allowed_block_types', 'baspa_type_member_blocks', 10 );
	}

}

if ( !function_exists( 'baspa_type_member_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_type_member_columns(): array {

		return array(
			'cb'        => "<input type=\"checkbox\">",
			'thumbnail' => '',
			'title'     => esc_html__( 'Member', 'baspa' ),
			'contacts'  => esc_html__( 'In contacts?', 'baspa' ),
			'position'  => esc_html__( 'Position', 'baspa' ),
			'scope'     => esc_html__( 'Scope', 'baspa' ),
			'email'     => esc_html__( 'Email', 'baspa' ),
			'phone'     => esc_html__( 'Phone', 'baspa' ),
			'order'     => esc_html__( 'Order', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-member_columns', 'baspa_type_member_columns' );

}

if ( !function_exists( 'baspa_type_member_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_type_member_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'thumbnail':

				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 60 ) ) . '</a>';
				} else {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 60px; background-color: white"></div></a>';
				}

				break;

			case 'contacts':

				$contacts = get_post_meta( $post->ID, 'member_contacts', true );

				if ( $contacts == 1 ) {
					echo '&starf;';
				}

				break;

			case 'position':

				$position = get_post_meta( $post->ID, 'member_position', true );

				if ( !empty( $position ) ) {
					echo esc_html( $position );
				}

				break;

			case 'scope':

				$scope = get_post_meta( $post->ID, 'member_scope', true );

				if ( !empty( $scope ) ) {
					echo esc_html( $scope );
				}

				break;

			case 'email':

				$email = get_post_meta( $post->ID, 'member_email', true );

				if ( !empty( $email ) ) {
					echo esc_html( $email );
				}

				break;

			case 'phone':

				$phone = get_post_meta( $post->ID, 'member_phone', true );

				if ( !empty( $phone ) ) {
					echo esc_html( $phone );
				}

				break;

			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_member_posts_custom_column', 'baspa_type_member_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_type_member_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_type_member_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'member' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_type_member_admin_list' );

}
