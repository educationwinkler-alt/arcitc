<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_products_type_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_products_type_register(): void {

		register_post_type( 'product', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Products', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Products', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Product', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Product', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Products', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Product', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Product', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Product', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Product', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Product', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Products', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Products', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Products', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Products Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Product Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Product Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Product published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Product updated.', 'type', 'baspa' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'capability_type'     => 'page',
			'hierarchical'        => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => array(
				'slug'         => _x( 'product', 'type', 'baspa' ),
				'hierarchical' => true,
				'with_front'   => false,
			),
			'menu_position'       => 36,
			'menu_icon'           => 'dashicons-archive',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
			'template'            => '',
		) );

	}

	add_action( 'init', 'baspa_products_type_register' );

}

if ( !function_exists( 'baspa_products_type_content_default' ) ) {

	/**
	 * Default Content
	 *
	 * @param $content
	 * @param $post
	 *
	 * @return mixed|string
	 */
	function baspa_products_type_content_default( $content, $post ): mixed {

		if ( $post->post_type == 'product' ) {
			$content = '';
		}

		return $content;
	}

	add_filter( 'default_content', 'baspa_products_type_content_default', 10, 2 );

}

if ( !function_exists( 'baspa_products_type_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_products_type_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'product' ) {
			$allowed_blocks = array(
				'core/heading',
				'core/paragraph',
				'core/list',
				'core/list-item',
				'core/table',
				'core/image',
			);
		}

		return $allowed_blocks;

	}

//	add_filter( 'allowed_block_types_all', 'baspa_products_type_blocks', 10 );

	// Backward compatibility before WP 5.8
//	if ( !function_exists( 'get_allowed_block_types' ) ) {
//		add_filter( 'allowed_block_types', 'baspa_products_type_blocks', 10 );
//	}

}

if ( !function_exists( 'baspa_products_type_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_products_type_columns(): array {

		return array(
			'cb'                    => "<input type=\"checkbox\">",
			'thumbnail'             => '',
			'title'                 => esc_html__( 'Product', 'baspa' ),
			'product-price'         => esc_html__( 'Price', 'baspa' ),
			'product-categories'    => esc_html__( 'Categories', 'baspa' ),
			'product-manufacturers' => esc_html__( 'Manufacturer', 'baspa' ),
			'order'                 => esc_html__( 'Order', 'baspa' ),
			'author'                => esc_html__( 'Author', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-product_columns', 'baspa_products_type_columns' );

}

if ( !function_exists( 'baspa_products_type_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_products_type_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'thumbnail':

				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 45 ) ) . '</a>';
				} else {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 45px; background-color: white"></div></a>';
				}

				break;

			case 'product-price':

				$price = get_post_meta( $post->ID, 'product_price', true );
				$price_text = get_post_meta( $post->ID, 'product_price_text', true );
				$price_suffix = get_post_meta( $post->ID, 'product_price_suffix', true );

				if ( !empty( $price ) ) { ?>
					<div class="f-price">
						<ins style="text-decoration: none">
							<?php echo forqy_price_czk( $price, 0 );
							if ( !empty( $price_suffix ) ) { ?>
								<span class="f-price__suffix"><?php echo esc_html( $price_suffix ); ?></span>
							<?php } ?>
						</ins>
					</div>
				<?php } else if ( !empty( $price_text ) ) { ?>
					<div class="f-price f-price--text">
						<?php
						echo esc_html( $price_text );
						if ( !empty( $price_suffix ) ) {
							echo '&nbsp;' ?>
							<span class="f-price__suffix"><?php echo esc_html( $price_suffix ); ?></span>
						<?php } ?>
					</div>
				<?php }

				break;

			case 'product-categories':

				$terms = get_the_terms( $post->ID, 'product-category' );

				if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
					$terms = wp_list_sort( $terms, 'parent', 'ASC' );

					$term_names = array_map( function ( $term ) {
						return $term->name;
					}, $terms );

					echo implode( ', ', $term_names );
				}

				break;

			case 'product-manufacturers':

				$manufacturers = get_the_term_list( $post->ID, 'product-manufacturer', '', ', ', '' );

				if ( !empty( $manufacturers ) ) {
					echo strip_tags( $manufacturers );
				}

				break;

			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_product_posts_custom_column', 'baspa_products_type_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_products_type_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_products_type_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'product' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_products_type_admin_list' );

}
