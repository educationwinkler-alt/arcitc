<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_type_job_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_type_job_register(): void {

		register_post_type( 'job', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Jobs', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Jobs', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Job', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Job', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Jobs', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Job', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Job', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Job', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Job', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Job', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Jobs', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Jobs', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Jobs', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Jobs Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Job Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Job Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Job published.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Job updated.', 'type', 'baspa' ),
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
				'slug'       => _x( 'jobs', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 38,
			'menu_icon'           => 'dashicons-index-card',
			'supports'            => array(
				'title',
				'editor',
				'page-attributes',
			),
		) );

	}

	add_action( 'init', 'baspa_type_job_register' );

}

if ( !function_exists( 'baspa_jobs_type_content_default' ) ) {

	/**
	 * Default Content
	 *
	 * @param $content
	 * @param $post
	 *
	 * @return mixed|string
	 */
	function baspa_jobs_type_content_default( $content, $post ): mixed {

		if ( $post->post_type == 'job' ) {
			$content = '<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id es.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading">Požadujeme</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Ut enim ad minim veniam, quis nostrud exercitation ullamco</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>laboris nisi ut aliquip ex ea commodo consequat.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Duis aute irure dolor in reprehenderit in voluptate velit e</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading">Nabízíme</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Ut enim ad minim veniam, quis nostrud exercitation ullamco</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>laboris nisi ut aliquip ex ea commodo consequat.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Duis aute irure dolor in reprehenderit in voluptate velit e</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="mailto:info@arctic-spas.cz">Kontakujte nás</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->';
		}

		return $content;
	}

	add_filter( 'default_content', 'baspa_jobs_type_content_default', 10, 2 );

}

if ( !function_exists( 'baspa_type_job_blocks' ) ) {

	/**
	 * Allowed Blocks
	 *
	 * @param $allowed_blocks
	 *
	 * @return bool|array
	 */
	function baspa_type_job_blocks( $allowed_blocks ): array|bool {

		if ( get_post_type( get_the_ID() ) === 'job' ) {
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

	add_filter( 'allowed_block_types_all', 'baspa_type_job_blocks', 10 );

}

if ( !function_exists( 'baspa_type_job_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_type_job_columns(): array {

		return array(
			'cb'    => "<input type=\"checkbox\">",
			'title' => esc_html_x( 'Job', 'type', 'baspa' ),
			'order' => esc_html_x( 'Order', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-job_columns', 'baspa_type_job_columns' );

}

if ( !function_exists( 'baspa_type_job_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_type_job_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'order':

				if ( !empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_job_posts_custom_column', 'baspa_type_job_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_type_job_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_type_job_admin_list( $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'job' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_type_job_admin_list' );

}
