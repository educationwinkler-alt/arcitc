<?php

/**
 * Shortcodes
 *
 * @package    	forqys/function
 * @since      	1.0.8
 */

/**
 * Turn on Shortcode Processing for Patterns Using 'block_template_part' Function
 *
 * @url https://github.com/WordPress/gutenberg/issues/58727
 */
add_filter( 'render_block_core/shortcode', function ( string $block_content ): string {
	return do_shortcode( shortcode_unautop( $block_content ) );
} );
