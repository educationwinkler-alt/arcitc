<?php

/**
 * Type
 */

if ( ! function_exists( 'baspa_type_job_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_type_job_register(): void {

		register_post_type( 'job', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Pracovní pozice', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Pracovní pozice', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Pracovní pozice', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Přidat pracovní pozici', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'Všechny pracovní pozice', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Přidat pozici', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Přidat pracovní pozici', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Upravit pracovní pozici', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'Nová pracovní pozice', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'Zobrazit pracovní pozici', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'Zobrazit pracovní pozice', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Hledat pracovní pozice', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'Žádné pracovní pozice', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'V koši nejsou žádné pracovní pozice', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Archiv pracovních pozic', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Atributy pracovní pozice', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Pracovní pozice byla publikována.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Pracovní pozice byla aktualizována.', 'type', 'baspa' ),
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

if ( ! function_exists( 'baspa_jobs_type_content_default' ) ) {

	/**
	 * Default Content
	 *
	 * @param $content
	 * @param $post
	 *
	 * @return mixed|string
	 */
	function baspa_jobs_type_content_default( $content, $post ): mixed {

		if ( $post->post_type === 'job' ) {
			$content = '<!-- wp:paragraph -->
<p>Hledáme člověka, který chce pracovat s prémiovým wellness sortimentem Arctic Spas a umí být spolehlivým partnerem pro zákazníky od první konzultace až po hotovou realizaci.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading">Požadujeme</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>spolehlivost, samostatnost a profesionální vystupování</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>chuť učit se produktové detaily vířivek, swimspa a dalšího sortimentu</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>pečlivou komunikaci se zákazníky i interním týmem</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>řidičský průkaz skupiny B a ochotu jezdit za realizacemi</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>technické nebo obchodní zkušenosti jsou výhodou</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading">Nabízíme</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>zázemí showroomu Arctic Spas v Moravanech u Brna</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>práci s kvalitní značkou a jasným produktovým portfoliem</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>zaškolení v produktech, montážích a péči o zákazníky</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>dlouhodobou spolupráci v menším specializovaném týmu</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>férové podmínky podle zkušeností a domluvené role</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:lukas.dusek@arctic-spas.cz">Ozvěte se nám</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->';
		}

		return $content;
	}

	add_filter( 'default_content', 'baspa_jobs_type_content_default', 10, 2 );

}

if ( ! function_exists( 'baspa_type_job_blocks' ) ) {

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

if ( ! function_exists( 'baspa_type_job_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array
	 */
	function baspa_type_job_columns(): array {

		return array(
			'cb'    => '<input type="checkbox">',
			'title' => esc_html_x( 'Pracovní pozice', 'type', 'baspa' ),
			'order' => esc_html_x( 'Pořadí', 'type', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-job_columns', 'baspa_type_job_columns' );

}

if ( ! function_exists( 'baspa_type_job_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param $column
	 */
	function baspa_type_job_columns_content( $column ): void {
		global $post;

		switch ( $column ) {

			case 'order':

				if ( ! empty( $post->menu_order ) ) {
					echo esc_html( $post->menu_order );
				}

				break;

		}

	}

	add_action( 'manage_job_posts_custom_column', 'baspa_type_job_columns_content', 10, 1 );

}

if ( ! function_exists( 'baspa_type_job_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param $wp_query
	 */
	function baspa_type_job_admin_list( $wp_query ): void {

		if ( is_admin() && isset( $wp_query->query['post_type'] ) ) {

			if ( $wp_query->query['post_type'] === 'job' && ! isset( $_GET['order'] ) ) {
				$wp_query->set( 'orderby', array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_type_job_admin_list' );

}