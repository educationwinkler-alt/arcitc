<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_pages_type_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_pages_type_columns(): array {

		return array(
			'cb'               => "<input type=\"checkbox\">",
			'thumbnail'        => '',
			'title'            => esc_html__( 'Title', 'baspa' ),
			'product-category' => esc_html__( 'Product Category', 'baspa' ),
			'order'            => esc_html__( 'Order', 'baspa' ),
			'author'           => esc_html__( 'Author', 'baspa' ),
			'date'             => esc_html__( 'Date', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-page_columns', 'baspa_pages_type_columns' );

}

if ( !function_exists( 'baspa_pages_type_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_pages_type_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'thumbnail':

				if ( has_post_thumbnail() ) {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block">' . get_the_post_thumbnail( get_the_ID(), array( 60, 45 ) ) . '</a>';
				} else {
					echo '<a href="' . get_edit_post_link() . '" class="post-thumbnail" style="display: inline-block"><div style="display: block; width: 60px; height: 45px; background-color: white"></div></a>';
				}

				break;

			case 'product-category':

				$product_category_id = get_post_meta( $post->ID, 'page_product_category', true );
				$term                = get_term( $product_category_id, 'product-category' );

				if ( !empty( $term ) && !is_wp_error($term) ) {
					echo '<a href="' . esc_url( get_edit_term_link( $term->term_id ) ) . '">';
					echo esc_html( $term->name ) . ' [' . esc_html( $product_category_id ) . ']';
					echo '</a>';
				}

				break;

			case 'page-price':

				$price = get_post_meta( $post->ID, 'page_price', true );
				$price_text = get_post_meta( $post->ID, 'page_price_text', true );

				if ( !empty( $price ) ) { ?>
					<div class="f-price">
						<ins style="text-decoration: none;"><?php echo esc_html( $price ); ?></ins>
					</div>
				<?php } else if ( !empty( $price_text ) ) { ?>
					<div class="f-price f-price--text">
						<?php echo esc_html( $price_text ); ?>
					</div>
				<?php }

				break;

			case 'page-categories':

				$categories = get_the_terms( $post->ID, 'page-category' );

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

	add_action( 'manage_page_posts_custom_column', 'baspa_pages_type_columns_content', 10, 1 );

}