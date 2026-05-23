<?php

/**
 * Button Block
 */

if ( !function_exists( 'baspa_block_details_modify' ) ) {

	/**
	 * Modify 'core/details' Block
	 *
	 * @param $block_content
	 * @param $block
	 *
	 * @return array|mixed|string|string[]
	 */
	function baspa_block_details_modify( $block_content, $block ): mixed {

		// Check if details
		if ( 'core/details' === $block[ 'blockName' ] ) {

			// Check for class
			if ( isset( $block[ 'attrs' ][ 'className' ] ) ) {

				/**
				 * Contact Button
				 */
				if (
					str_contains( $block[ 'attrs' ][ 'className' ], 'is-style-icon' )
				) {

					// Add icon
					$block_content = str_replace(
						'<summary>',
						'<summary class="f-summary"><span class="f-summary__icon" aria-hidden="true"></span>',
						$block_content );

					// Add content
//					$block_content = str_replace(
//						'<p>',
//						'<div class="f-details__content f-content"><p>',
//						$block_content );
//					$block_content = str_replace(
//						'</p>',
//						'</p></div>',
//						$block_content );

				}
			}
		}

		return $block_content;
	}

	add_filter( 'render_block', 'baspa_block_details_modify', 10, 2 );

}
