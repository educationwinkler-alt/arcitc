<?php

/**
 * Blocks
 */

if ( !function_exists( 'baspa_blocks_register_styles' ) ) {

	/**
	 * Register Block Styles
	 *
	 * @return void
	 */
	function baspa_blocks_register_styles(): void {

		// Paragraph
		register_block_style( 'core/paragraph', array(
			'name'  => 'balance',
			'label' => esc_html_x( 'Balance', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/paragraph', array(
			'name'  => 'pretty',
			'label' => esc_html_x( 'Pretty', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/paragraph', array(
			'name'  => 'subheading',
			'label' => esc_html_x( 'Subheading', 'block style', 'baspa' ),
		) );

		// Group
		register_block_style( 'core/group', array(
			'name'  => 'feature',
			'label' => esc_html_x( 'Feature', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/group', array(
			'name'  => 'feature-shadow',
			'label' => esc_html_x( 'Feature - Shadow', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/group', array(
			'name'  => 'service',
			'label' => esc_html_x( 'Service', 'block style', 'baspa' ),
		) );

		// Columns
		register_block_style( 'core/columns', array(
			'name'  => 'features-separated',
			'label' => esc_html_x( 'Features - Separated', 'block style', 'baspa' ),
		) );

		// Button
		register_block_style( 'core/button', array(
			'name'  => 'contact',
			'label' => esc_html_x( 'Contact', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/button', array(
			'name'  => 'service',
			'label' => esc_html_x( 'Service', 'block style', 'baspa' ),
		) );

		// Cover
		register_block_style( 'core/cover', array(
			'name'  => 'container',
			'label' => esc_html_x( 'Container', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/cover', array(
			'name'  => 'container--75',
			'label' => esc_html_x( 'Container - 75%', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/cover', array(
			'name'  => 'container--50',
			'label' => esc_html_x( 'Container - 50%', 'block style', 'baspa' ),
		) );

		// Media & Text
		register_block_style( 'core/media-text', array(
			'name'  => 'container',
			'label' => esc_html_x( 'Container', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/media-text', array(
			'name'  => 'media-to-edge',
			'label' => esc_html_x( 'Media to Edge', 'block style', 'baspa' ),
		) );

		// Details
		register_block_style( 'core/details', array(
			'name'  => 'icon',
			'label' => esc_html_x( 'Icon', 'block style', 'baspa' ),
		) );

		// List
		register_block_style( 'core/list', array(
			'name'  => 'featured',
			'label' => esc_html_x( 'Featured', 'block style', 'baspa' ),
		) );
		register_block_style( 'core/list', array(
			'name'  => 'pages',
			'label' => esc_html_x( 'Pages', 'block style', 'baspa' ),
		) );

		// Query
		register_block_style( 'core/query', array(
			'name'  => 'pages',
			'label' => esc_html_x( 'Pages', 'block style', 'baspa' ),
		) );

	}

	add_action( 'init', 'baspa_blocks_register_styles' );

}
