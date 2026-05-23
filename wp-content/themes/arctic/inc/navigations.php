<?php

/**
 * Navigations
 */

if ( !function_exists( 'baspa_navigations' ) ) {

	/**
	 * Register Navigations
	 */
	function baspa_navigations(): void {

		register_nav_menus( array(
			'navigation'             => esc_html__( 'Navigation', 'baspa' ),
			'navigation_bar'         => esc_html__( 'Navigation', 'baspa' ) . ' &mdash; ' . esc_html__( 'Bar', 'baspa' ),
			'navigation_pools'       => esc_html__( 'Navigation', 'baspa' ) . ' &mdash; ' . esc_html__( 'Pools', 'baspa' ),
			'navigation_jacuzzis'    => esc_html__( 'Navigation', 'baspa' ) . ' &mdash; ' . esc_html__( 'Jacuzzis', 'baspa' ),
			'navigation_information' => esc_html__( 'Navigation', 'baspa' ) . ' &mdash; ' . esc_html__( 'Information', 'baspa' ),
			'navigation_footer'      => esc_html__( 'Navigation', 'baspa' ) . ' &mdash; ' . esc_html__( 'Footer', 'baspa' ),
		) );

	}

	add_action( 'init', 'baspa_navigations' );

}

if ( !function_exists( 'baspa_navigation' ) ) {

	/**
	 * Primary Navigation
	 */
	function baspa_navigation(): void {

		wp_nav_menu( array(
			'theme_location' => 'navigation',
			'container'      => false,
			'menu_class'     => 'f-navigation__list--primary f-navigation__list f-navigation__list--mobile',
			'depth'          => 3,
			'walker'         => new Baspa_Walker_Nav_Menu,
		) );

	}

}

if ( !function_exists( 'baspa_navigation_bar' ) ) {

	/**
	 * Bar Navigation
	 */
	function baspa_navigation_bar(): void {

		wp_nav_menu( array(
			'theme_location' => 'navigation_bar',
			'container'      => false,
			'menu_class'     => 'f-navigation__bar',
			'fallback_cb'    => false,
			'depth'          => 1,
		) );

	}

}

if ( !function_exists( 'baspa_navigation_footer' ) ) {

	/**
	 * Footer Navigation
	 */
	function baspa_navigation_footer(): void {

		wp_nav_menu( array(
			'theme_location' => 'navigation_footer',
			'container'      => false,
			'menu_class'     => 'f-navigation__footer',
			'fallback_cb'    => false,
			'depth'          => 1,
		) );

	}

}

if ( !function_exists( 'baspa_navigation_remove_current_page_item' ) ) {

	/**
	 * Remove 'current_page_item' Class
	 *
	 * @param array<string> $classes
	 *
	 * @return array<string>
	 */
	function baspa_navigation_remove_current_page_item( array $classes ): array {

		$post_type = get_query_var( 'post_type' );

		if ( get_post_type() == $post_type ) {
			$classes = array_filter( $classes, 'baspa_navigation_get_current_value' );
		}

		if ( is_search() ) {
			$classes = array_filter( $classes, 'baspa_navigation_get_current_value' );
		}

		if ( is_tax() ) {
			$classes = array_filter( $classes, 'baspa_navigation_get_current_value' );
		}

		if ( is_search() || is_404() ) {
			$classes = array_filter( $classes, 'baspa_navigation_get_current_value' );
		}

		return $classes;

	}

	/**
	 * Get Current Value
	 *
	 * @param string $element
	 *
	 * @return bool
	 */
	function baspa_navigation_get_current_value( string $element ): bool {
		return ( $element != 'current_page_parent' );
	}

//	add_filter( 'nav_menu_css_class', 'baspa_navigation_remove_current_page_item', 10, 2 );

}

if ( !function_exists( 'baspa_navigation_add_current_menu_item' ) ) {

	/**
	 * Add 'current-menu-item' class for Types
	 *
	 * @param array<string> $classes
	 * @param bool|WP_Post $item
	 *
	 * @return array<string>
	 */
	function baspa_navigation_add_current_menu_item( array $classes = array(), bool|WP_Post $item = false ): array {

		if ( !$item || !isset( $item->object_id ) || !isset( $item->object ) ) {
			return $classes;
		}

		$item_template = get_post_meta( $item->object_id, '_wp_page_template', true );

		// No Item for Search & 404
		if ( is_search() || is_404() ) {
			return $classes;
		}

		// Item for Posts
		if ( $item_template == 'template-blog.php' && (
				is_singular( 'post' ) ||
				is_category() ||
				is_tag()
			) ) {
			$classes[] = 'current-menu-item';
		}
		// Item pro Posts Set as 'page_for_posts'
		if ( $item->object_id == get_option( 'page_for_posts', true ) && (
				is_singular( 'post' ) ||
				is_category() ||
				is_tag()
			) ) {
			$classes[] = 'current-menu-item';
		}

		// Item for Products
		if ( $item_template == 'template-products.php' && (
				is_singular( 'product' ) ||
				is_tax( array( 'product-category' ) )
			) ) {
			$classes[] = 'current-menu-item';
		}
		// Item as Product Category on a Single Product
		if ( is_singular( 'product' ) && $item->object == 'product-category' ) {
			$product_id         = get_queried_object_id();
			$product_categories = get_the_terms( $product_id, 'product-category' );

			if ( is_array( $product_categories ) && (
					in_array( $item->object_id, array_column( $product_categories, 'term_id' ) ) ||
					in_array( $item->object_id, array_column( $product_categories, 'parent' ) ) )
			) {
				$classes[] = 'current-menu-item';
			}
		}

		// Item for References
		if ( $item_template == 'template-references.php' && (
				is_singular( 'reference' ) ||
				is_tax( array( 'reference-category' ) )
			) ) {
			$classes[] = 'current-menu-item';
		}

		return $classes;

	}

	add_filter( 'nav_menu_css_class', 'baspa_navigation_add_current_menu_item', 10, 2 );

}
