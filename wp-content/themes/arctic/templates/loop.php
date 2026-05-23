<?php

/**
 * Loop
 */

/*
get_template_part( 'templates/loop', '', array(
	'query_module'          => 'posts',
	'query_args'            => array(),
	'query_class'           => array(),
	'query_pagination'      => true,
	'query_posts_per_page'  => 10,
	'query_empty'           => 'templates/loop/empty',
) );
*/

// Container
$container_class = array( 'f-listings', 'a-grid', 'a-gap--xs' );
if ( isset( $args[ 'query_class' ] ) && is_array( $args[ 'query_class' ] ) ) {
	$container_class = $args[ 'query_class' ];
}
if ( isset( $args[ 'query_module' ] ) ) {
	$container_class[] = 'f-listings--' . $args[ 'query_module' ];
}

// Post
$post_number = 1;

if ( isset( $args[ 'query_args' ] ) ) {

	/**
	 * Query Arguments
	 */
	$query_args = is_array( $args[ 'query_args' ] ) ? $args[ 'query_args' ] : array();

	// Paged
	if ( isset( $args[ 'query_pagination' ] ) && $args[ 'query_pagination' ] ) {

		if ( get_query_var( 'paged' ) ) {
			$query_args[ 'paged' ] = get_query_var( 'paged' );
		} else if ( get_query_var( 'page' ) ) {
			$query_args[ 'paged' ] = get_query_var( 'page' );
		} else {
			$query_args[ 'paged' ] = 1;
		}
	} else {
		$query_args[ 'paged' ] = 1;
	}

	// Posts per Page
	$query_args[ 'posts_per_page' ] = $args[ 'query_posts_per_page' ] ?? get_option( 'posts_per_page' );

	// Order by
	if ( !array_key_exists( 'orderby', $query_args ) ) {
		$query_args[ 'orderby' ] = array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		);
	}

	/**
	 * Query
	 */
	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) { ?>

		<div <?php forqy_class( $container_class ); ?>>

			<?php while ( $query->have_posts() ) {
				$query->the_post();

				if ( isset( $args[ 'query_module' ] ) ) {
					get_template_part( 'modules/' . esc_attr( $args[ 'query_module' ] ) . '/templates/post/listing', $args[ 'query_listing' ] ?? '', array(
						'post_number' => $post_number,
					) );
				} else {
					get_template_part( 'modules/posts/templates/post/listing', $args[ 'query_listing' ] ?? '', array(
						'post_number' => $post_number,
					) );
				}
				$post_number++;
			} ?>

		</div>

		<?php
		if ( isset( $args[ 'query_pagination' ] ) && $args[ 'query_pagination' ] && $query_args[ 'paged' ] && function_exists( 'baspa_pagination' ) ) {
			baspa_pagination( $query->max_num_pages );
		}

		wp_reset_query();
	} else {
		if ( isset( $args[ 'query_empty' ] ) ) {
			get_template_part( esc_attr( $args[ 'query_empty' ] ) );
		} else {
			get_template_part( 'templates/loop/empty' );
		}
	}
} else {

	/**
	 * Default Query
	 */
	if ( have_posts() ) { ?>

		<div <?php forqy_class( $container_class ); ?>>

			<?php while ( have_posts() ) {
				the_post();

				if ( isset( $args[ 'query_module' ] ) ) {
					get_template_part( 'modules/' . esc_attr( $args[ 'query_module' ] ) . '/templates/post/listing', $args[ 'query_listing' ] ?? '', array(
						'post_number' => $post_number,
					) );
				} else {
					get_template_part( 'modules/posts/templates/post/listing', $args[ 'query_listing' ] ?? '', array(
						'post_number' => $post_number,
					) );
				}
				$post_number++;
			} ?>

		</div>

		<?php
		if ( isset( $args[ 'query_pagination' ] ) && $args[ 'query_pagination' ] && function_exists( 'baspa_pagination' ) ) {
			baspa_pagination();
		}

		wp_reset_query();
	} else {
		if ( isset( $args[ 'query_empty' ] ) ) {
			get_template_part( esc_attr( $args[ 'query_empty' ] ) );
		} else {
			get_template_part( 'templates/loop/empty' );
		}
	}

}
