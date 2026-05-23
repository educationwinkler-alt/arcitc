<?php

/**
 * Admin List
 */

if ( !function_exists( 'baspa_products_admin_filter_fields' ) ) {

	/**
	 * Admin Filter Fields
	 */
	function baspa_products_admin_filter_fields(): void {
		global $typenow;

		if ( $typenow !== 'product' ) {
			return;
		}

		$product_types = function_exists( 'baspa_products_types' ) ? baspa_products_types() : array();

		if ( empty( $product_types ) ) {
			return;
		}

		// Current
		$selected = isset( $_GET[ 'product_type_filter' ] ) ? esc_attr( $_GET[ 'product_type_filter' ] ) : '';
		?>
		<label for="filter-by-type"
		       class="screen-reader-text"><?php echo esc_html__( 'Filter by Type', 'baspa' ); ?></label>
		<select id="filter-by-type" name="product_type">
			<option value=""><?php echo esc_html__( 'All Types', 'baspa' ); ?></option>
			<?php foreach ( $product_types as $key => $product_type ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $key ),
					selected( $product_type, $selected, false ),
					esc_html( $product_type )
				);
			} ?>
		</select>
		<?php
	}

//	add_action( 'restrict_manage_posts', 'baspa_products_admin_filter_fields' );

}

if ( !function_exists( 'baspa_products_admin_filter' ) ) {

	/**
	 * Admin Filter
	 *
	 * @param WP_Query $query
	 */
	function baspa_products_admin_filter( WP_Query $query ): void {
		global $pagenow, $typenow;

		if ( $pagenow === 'edit.php' && $typenow === 'product' && !empty( $_GET[ 'product_type' ] ) ) {
			$query->set( 'meta_query', array(
				array(
					'key'     => 'product_type',
					'value'   => esc_attr( $_GET[ 'product_type' ] ),
					'compare' => '=',
				),
			) );
		}
	}

//	add_action( 'pre_get_posts', 'baspa_products_admin_filter' );

}

if ( !function_exists( 'baspa_products_admin_product_post_state' ) ) {

	/**
	 * Add Post State to Product
	 *
	 * @param $states
	 * @param $post
	 *
	 * @return mixed
	 */
	function baspa_products_admin_product_post_state( $states, $post ): mixed {

		if ( 'product' == get_post_type( $post->ID ) ) {
			$product_type = get_post_meta( $post->ID, 'product_type', true );

			if ( $product_type == 'affiliate' ) {
				$states[] = esc_html_x( 'Affiliate Product', 'admin', 'baspa' );
			}

		}

		return $states;
	}

	add_filter( 'display_post_states', 'baspa_products_admin_product_post_state', 10, 2 );

}
