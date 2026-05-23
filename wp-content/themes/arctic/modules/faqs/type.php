<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_type_faq_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_type_faq_register(): void {

		register_post_type( 'faq', array(
			'labels'              => array(
				'name'               => esc_html_x( 'FAQs', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'FAQs', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'FAQ', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add FAQ', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All FAQs', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add FAQ', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add FAQ', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit FAQ', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New FAQ', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View FAQ', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View FAQs', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search FAQs', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No FAQs', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No FAQs Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'FAQ Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'FAQ Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'FAQ published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'FAQ updated.', 'type', 'baspa' ),
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
				'slug'       => _x( 'faqs', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 38,
			'menu_icon'           => 'dashicons-format-chat',
			'supports'            => array(
				'title',
				'editor',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_type_faq_register' );

}

if ( !function_exists( 'baspa_type_faq_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_type_faq_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'faq' ) {
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

	add_filter( 'allowed_block_types_all', 'baspa_type_faq_blocks', 10 );

}

if ( !function_exists( 'baspa_type_faq_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_type_faq_columns(): array {

		return array(
			'cb'    => "<input type=\"checkbox\">",
			'title' => esc_html_x( 'FAQ', 'type', 'baspa' ),
			'faq-categories' => esc_html_x( 'Categories', 'type', 'baspa' ),
			'order' => esc_html_x( 'Order', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-faq_columns', 'baspa_type_faq_columns' );

}

if ( !function_exists( 'baspa_type_faq_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_type_faq_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'faq-categories':

				$categories = get_the_terms( $post->ID, 'faq-category' );

				if ( !empty( $categories ) ) {
					foreach ( $categories as $category ) { ?>
						<a href="<?php echo get_edit_term_link( $category->term_id ); ?>">
							<?php echo esc_html( $category->name ); ?>
						</a>
					<?php }
				}

				break;

			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_faq_posts_custom_column', 'baspa_type_faq_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_type_faq_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_type_faq_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'faq' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_type_faq_admin_list' );

}
