<?php

/**
 * Breadcrumbs
 *
 * @package     forqys/function
 * @since       1.0.1
 */

if ( !function_exists( 'forqy_breadcrumbs' ) ) {

	/**
	 * Breadcrumbs
	 *
	 * @param string $text
	 * @param string $url
	 *
	 * @return void
	 */
	function forqy_breadcrumbs( string $text = '', string $url = '' ): void {

		ob_start();

		$current = get_queried_object();

		$kses_allowed_svg_tags = array(
			'svg' => array(
				'xmlns' => true,
				'viewBox' => true,
				'width' => true,
				'height' => true,
				'fill' => true,
				'class' => true,
			),
			'path' => array(
				'd' => true,
				'fill' => true,
				'stroke' => true,
			),
		);

		// Defaults
		$defaults = array_replace( array(
			'types'     => array(
				'product' => 'product-category', // type + taxonomy
			),
			'templates' => array(
				'post' => 'template-blog.php',
			),
			'separator' => '&mdash;',
			'label'     => _x( 'Breadcrumbs', 'breadcrumbs' ),
			'home'      => !empty( get_option( 'page_on_front' ) ) ? get_the_title( get_option( 'page_on_front' ) ) : _x( 'Home', 'breadcrumbs' ),
			'404'       => _x( 'Page not found', 'breadcrumbs' ),
			'search'    => _x( 'Search results for "%s"', 'breadcrumbs' ),
		), apply_filters( 'forqy_breadcrumbs', array() ) );

		// Separator
		$separator = ( !is_front_page() || !is_home() ) ? '<span class="f-breadcrumbs__separator" aria-hidden="true">' . wp_kses( $defaults[ 'separator' ], $kses_allowed_svg_tags ) . '</span>' : '';

		/**
		 * Home
		 */
		echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . wp_kses( $defaults[ 'home' ], $kses_allowed_svg_tags ) . '</a>' . $separator . '</li>';

		if ( !is_front_page() || !is_home() ) {
			if ( !empty( $text ) && !empty( $url ) ) {
				/**
				 * Custom
				 */
				echo '<li><a href="' . esc_url( $url ) . '" aria-current="page">' . wp_kses( $text, $kses_allowed_svg_tags ) . '</a></li>';
			} else {
				if ( is_page() ) {
					/**
					 * Page
					 */
					$page = get_post( get_the_ID() );
					// Page Parent
					if ( $page->post_parent ) {
						forqy_breadcrumbs_get_post_hierarchy( $page->post_parent );

						echo '<li><a href="' . get_the_permalink( $page->post_parent ) . '">' . get_the_title( $page->post_parent ) . '</a>' . $separator . '</li>';
					}

					echo '<li><a href="' . get_the_permalink() . '" aria-current="page">' . get_the_title() . '</a></li>';
				} else if ( is_singular( 'post' ) ) {
					/**
					 * Single Post
					 */
					if ( isset( $defaults[ 'templates' ][ 'post' ] ) ) {
						forqy_breadcrumbs_get_page_template( $defaults[ 'templates' ][ 'post' ], $separator );
					}

					$categories = get_the_category();
					if ( $categories ) {
						foreach ( $categories as $category ) {
							echo forqy_breadcrumbs_get_term_hierarchy( $category->term_id, 'category', $separator ) . '<li><a href="' . get_category_link( $category->term_id ) . '">' . esc_html( $category->name ) . '</a>' . $separator . '</li>';
						}
					}

					echo '<li><a href="' . get_the_permalink() . '" aria-current="page">' . get_the_title() . '</a></li>';

				} else if ( is_singular() ) {
					/**
					 * Single Post Type
					 */
					$post = get_post( get_the_ID() );

					if ( isset( $defaults[ 'templates' ] ) ) {
						foreach ( $defaults[ 'templates' ] as $type => $template ) {

							/** Post Type with Page Template on Single */
							if ( post_type_exists( $type ) && is_singular( $type ) ) {
								if ( !empty( $template ) && function_exists( 'forqy_get_page_by_template' ) ) {
									$page = forqy_get_page_by_template( $template );

									if ( isset( $page[ 'permalink' ] ) && $page[ 'title' ] ) {
										echo '<li><a href="' . esc_url( $page[ 'permalink' ] ) . '">' . esc_html( $page[ 'title' ] ) . '</a>' . $separator . '</li>';
									}
								}
							}
						}
					}

					if ( isset( $defaults[ 'types' ] ) ) {
						foreach ( $defaults[ 'types' ] as $type => $taxonomy ) {

							/** Post Type with Taxonomies on Single */
							if ( post_type_exists( $type ) && is_singular( $type ) ) {
								if ( !empty( $taxonomy ) && taxonomy_exists( $taxonomy ) ) {
									$terms = get_the_terms( $current, $taxonomy );

									if ( $terms ) {
										$term = reset( $terms ); // Get the first term

										if ( $term ) {
											forqy_breadcrumbs_get_term_hierarchy( $term->term_id, $taxonomy, $separator );
											echo '<li><a href="' . get_term_link( $term ) . '">' . $term->name . '</a>' . $separator . '</li>';
										}
									}
								}
							}
						}
					}

					// Type Parent
					if ( $post->post_parent ) {
						forqy_breadcrumbs_get_post_hierarchy( $post->post_parent );

						echo '<li><a href="' . get_the_permalink( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . '</a>' . $separator . '</li>';
					}

					echo '<li><a href="' . get_the_permalink() . '" aria-current="page">' . get_the_title() . '</a></li>';

				} else if ( is_category() || is_tag() ) {
					/**
					 * Category, Tag, Taxonomy
					 */
					if ( isset( $defaults[ 'templates' ][ 'post' ] ) ) {
						forqy_breadcrumbs_get_page_template( $defaults[ 'templates' ][ 'post' ], $separator );
					}

					forqy_breadcrumbs_get_term_hierarchy( $current->term_id, $current->taxonomy, $separator );

					echo '<li><a href="' . get_term_link( $current ) . '" aria-current="page">' . single_term_title( '', false ) . '</a></li>';
				} else if ( is_tax() ) {
					/**
					 * Tax
					 */
					$current_post_type = reset( get_taxonomy( $current->taxonomy )->object_type ); // reset - get only first item of array
					if ( isset( $defaults[ 'templates' ][ $current_post_type ] ) ) {
						forqy_breadcrumbs_get_page_template( $defaults[ 'templates' ][ $current_post_type ], $separator );
					}

					forqy_breadcrumbs_get_term_hierarchy( $current->term_id, $current->taxonomy, $separator );

					echo '<li><a href="' . get_term_link( $current->term_id ) . '" aria-current="page">' . single_term_title( '', false ) . '</a></li>';
				} else if ( is_home() ) {
					/**
					 * Blog - Home
					 */
					echo '<li><a href="' . get_the_permalink( get_option( 'page_for_posts', true ) ) . '" aria-current="page">' . get_the_title( get_option( 'page_for_posts', true ) ) . '</a></li>';
				} else if ( is_search() ) {
					/**
					 * Search
					 */
					echo '<li>' . sprintf( esc_html( $defaults[ 'search' ] ), get_search_query() ) . '</li>';
				} else if ( is_404() ) {
					/**
					 * 404
					 */
					echo '<li>' . esc_html( $defaults[ '404' ] ) . '</li>';
				} else {
					echo '<li><a href="' . get_the_permalink( $current ) . '" aria-current="page">' . esc_html( $current->post_title ) . '</a></li>';
				}
			}
		}

		$breadcrumbs = ob_get_clean();

		echo '<nav class="f-breadcrumbs" aria-label="' . esc_attr( $defaults[ 'label' ] ) . '"><ol>' . $breadcrumbs . '</ol></nav>';
	}

}

if ( !function_exists( 'forqy_breadcrumbs_get_page_template' ) ) {

	/**
	 * Get Page Template
	 *
	 * @param string $template
	 * @param string $separator
	 *
	 * @return void
	 */
	function forqy_breadcrumbs_get_page_template( string $template, string $separator = '&mdash;' ): void {

		if ( function_exists( 'forqy_get_page_by_template' ) && !empty( forqy_get_page_by_template( $template ) ) ) {
			$template_page = forqy_get_page_by_template( $template );
			if ( isset( $template_page[ 'id' ] ) ) {
				echo '<li><a href="' . get_the_permalink( $template_page[ 'id' ] ) . '">' . get_the_title( $template_page[ 'id' ] ) . '</a>' . $separator . '</li>';
			}
		}
	}

}

if ( !function_exists( 'forqy_breadcrumbs_get_post_hierarchy' ) ) {

	/**
	 * Get Type Hierarchy
	 *
	 * @param $post_id
	 * @param string $separator
	 *
	 * @return void
	 */
	function forqy_breadcrumbs_get_post_hierarchy( $post_id, string $separator = '&mdash;' ): void {

		$post_hierarchy = get_post_ancestors( $post_id );
		$post_hierarchy = array_reverse( $post_hierarchy );

		foreach ( $post_hierarchy as $parent_id ) {
			$parent_post = get_post( $parent_id );
			echo '<li><a href="' . get_the_permalink( $parent_id ) . '">' . get_the_title( $parent_id ) . '</a>' . $separator . '</li>';
		}
	}

}

if ( !function_exists( 'forqy_breadcrumbs_get_term_hierarchy' ) ) {

	/**
	 * Get Term Hierarchy
	 *
	 * @param $term_id
	 * @param $taxonomy
	 * @param string $separator
	 *
	 * @return void
	 */
	function forqy_breadcrumbs_get_term_hierarchy( $term_id, $taxonomy, string $separator = '&mdash;' ): void {

		$term           = get_term( $term_id, $taxonomy );
		$term_hierarchy = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
		$term_hierarchy = array_reverse( $term_hierarchy );

		foreach ( $term_hierarchy as $parent_id ) {
			$parent_term = get_term( $parent_id, $taxonomy );
			echo '<li><a href="' . get_term_link( $parent_term ) . '">' . $parent_term->name . '</a>' . $separator . '</li>';
		}
	}

}
