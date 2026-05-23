<?php

/**
 * Affiliate Product List
 */

/**
 * Customize Main Query in An Admin List
 */
add_action( 'pre_get_posts', function ( $query ) {

	if ( is_admin() && $query->is_main_query() && $query->get( 'post_type' ) === 'product' ) {

		if ( isset( $_GET[ 'affiliate' ] ) && $_GET[ 'affiliate' ] == '1' ) {

			$query->set( 'meta_query', array(
				array(
					'key'   => 'product_type',
					'value' => 'affiliate',
				),
			) );
		} else {

			$query->set( 'meta_query', array(
				'relation' => 'OR',
				array(
					'key'   => 'product_type',
					'value' => array( 'standard' ),
				),
				array(
					'key'     => 'product_type',
					'compare' => 'NOT EXISTS',
				),
			) );
		}
	}
} );

/**
 * Add Links Above Table on an Admin List
 */
add_filter( 'views_edit-product', function ( $views ) {
	global $pagenow;

	/**
	 * Current Page
	 */
	$standard_current  = (
		$pagenow === 'edit.php' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'product' &&
		count( $_GET ) === 1
	) ? 'current' : '';
	$affiliate_current = (
		$pagenow === 'edit.php' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'product' &&
		isset( $_GET[ 'affiliate' ] ) && $_GET[ 'affiliate' ] === '1'
	) ? 'current' : '';
	$published_current = (
		$pagenow === 'edit.php' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'product' &&
		isset( $_GET[ 'post_status' ] ) && $_GET[ 'post_status' ] === 'publish'
	) ? 'current' : '';

	/**
	 * Counters
	 */
	// Count 'standard' products
	$standard_count = ( new WP_Query( array(
		'post_type'      => 'product',
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'   => 'product_type',
				'value' => array( 'standard' ),
			),
			array(
				'key'     => 'product_type',
				'compare' => 'NOT EXISTS',
			),
		),
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) ) )->found_posts;

	// Count 'affiliate' products
	$affiliate_count = ( new WP_Query( array(
		'post_type'      => 'product',
		'meta_query'     => array(
			array(
				'key'   => 'product_type',
				'value' => 'affiliate',
			),
		),
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) ) )->found_posts;

	// Count 'published' products
	$published_count = ( new WP_Query( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) ) )->found_posts;

	/**
	 * Links
	 */
	$standard_view = sprintf(
		'<a href="%s" class="%s">' . esc_html_x( 'Standard', 'admin', 'baspa' ) . ' <span class="count">(%d)</span></a>',
		admin_url( 'edit.php?post_type=product' ),
		$standard_current,
		$standard_count,
	);
	// Affiliate link
	$affiliate_view = sprintf(
		'<a href="%s" class="%s">' . esc_html_x( 'Affiliate', 'admin', 'baspa' ) . ' <span class="count">(%d)</span></a>',
		admin_url( 'edit.php?post_type=product&affiliate=1' ),
		$affiliate_current,
		$affiliate_count,
	);
	// Published link
	$published_view = sprintf(
		'<a href="%s" class="%s">' . esc_html_x( 'Published', 'admin', 'baspa' ) . ' <span class="count">(%d)</span></a>',
		admin_url( 'edit.php?post_type=product&post_status=publish' ),
		$published_current,
		$published_count,
	);

	$all_view = $views[ 'all' ];
	unset( $views[ 'all' ] );
	unset( $views[ 'publish' ] );

	return array(
			'standard'  => $standard_view,
			'affiliate' => $affiliate_view,
			'published' => $published_view,
		) + $views;
} );
