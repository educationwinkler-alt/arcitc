<?php

/**
 * Setup
 */

if ( !function_exists( 'baspa_setup' ) ) {

	function baspa_setup(): void {

		// Add Semantic Markup Support
		add_theme_support( 'html5', array(
			'navigation-widgets',
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		) );

		// Add Custom Header Support
		add_theme_support( 'custom-header', array(
			'width'       => '1920',
			'height'      => '1080',
			'flex-width'  => true,
			'flex-height' => true,
			'header-text' => false,
		) );

		// Add Wide Align Support
		add_theme_support( 'align-wide' );

		// Add Responsive Embeds Support
		add_theme_support( 'responsive-embeds' );

		// Add Title Tag Support
		add_theme_support( 'title-tag' );

		// Add Post Thumbnails Support
		add_theme_support( 'post-thumbnails' );

		// Add Automatic Feed Links Support
		add_theme_support( 'automatic-feed-links' );

		// Add Translation Support
		load_theme_textdomain( get_template(), trailingslashit( get_template_directory() ) . 'languages' );
		// Keep legacy Baspa translations alive until text domains are migrated.
		load_theme_textdomain( 'baspa', trailingslashit( get_template_directory() ) . 'languages' );

		// Set Post Thumbnail Size
		set_post_thumbnail_size( 800, 600, array(
			'center',
			'center',
		) );

		/**
		 * Editor
		 */
		// Add Block Styles Support
		add_theme_support( 'wp-block-styles' );

		// Remove Block Templates Support
		// @url https://make.wordpress.org/core/2021/06/16/introducing-the-template-editor-in-wordpress-5-8/
		//remove_theme_support( 'block-templates' ); // Automatically enabled by using the 'theme.json' file

		// Remove Core Block Patterns Support
		remove_theme_support( 'core-block-patterns' );

		// Add Block Template Parts Support
		add_theme_support( 'block-template-parts' );

		// Add Appearance Tools Support
		add_theme_support( 'appearance-tools' );

		// Add Editor Styles Support
		add_theme_support( 'editor-styles' );

		// Add Editor Style
		add_editor_style( 'dist/css/editor.css' );

	}

	add_action( 'after_setup_theme', 'baspa_setup', 1 );

}
