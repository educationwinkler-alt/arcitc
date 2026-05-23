<?php

/**
 * Patterns
 */

if ( ! function_exists( 'baspa_patterns' ) ) {

	/**
	 * Register Patterns
	 *
	 * @return void
	 */
	function baspa_patterns(): void {

		register_block_pattern(
			'baspa/pattern',
			array(
				'title'   => esc_html_x( 'Pattern', 'admin', 'baspa' ),
				'content' => '',
			)
		);

	}

//	add_action( 'init', 'baspa_patterns' );

}
