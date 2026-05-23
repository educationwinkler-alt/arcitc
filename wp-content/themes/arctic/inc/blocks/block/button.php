<?php

/**
 * Button Block
 */

if ( !function_exists( 'baspa_block_button_modify' ) ) {

	/**
	 * Modify 'core/button' Block to Off Trigger
	 *
	 * @param $block_content
	 * @param $block
	 *
	 * @return array|mixed|string|string[]
	 */
	function baspa_block_button_modify( $block_content, $block ): mixed {

		$page_contact = !function_exists( 'forqy_get_page_by_template' ) ?: ( ( !empty( forqy_get_page_by_template( 'template-contact.php' ) ) ) ? forqy_get_page_by_template( 'template-contact.php' ) : null );

		// Check if button
		if ( 'core/button' === $block[ 'blockName' ] ) {

			// Check for class
			if ( isset( $block[ 'attrs' ][ 'className' ] ) ) {

				/**
				 * Contact Button
				 */
				if (
					str_contains( $block[ 'attrs' ][ 'className' ], 'is-style-contact' )
				) {

					// Add data attributes
					$block_content = str_replace(
						'<a ',
						'<a data-off="contact" aria-expanded="false" aria-controls="' . sanitize_title( esc_attr_x( 'contact-us', 'anchor', 'baspa' ) ) . '" ',
						$block_content );

				}

				/**
				 * Service Button
				 */
				if (
					str_contains( $block[ 'attrs' ][ 'className' ], 'is-style-service' )
				) {

					// Add data attributes
					$block_content = str_replace(
						'<a ',
						'<a data-off="service" aria-expanded="false" aria-controls="' . sanitize_title( esc_attr_x( 'service', 'anchor', 'baspa' ) ) . '" ',
						$block_content );

				}

				/**
				 * Contact + Service Button
				 */
				if (
					str_contains( $block[ 'attrs' ][ 'className' ], 'is-style-contact' ) ||
					str_contains( $block[ 'attrs' ][ 'className' ], 'is-style-service' )
				) {

					// Add class
					$block_content = str_replace(
						'class="',
						'class="f-off__trigger js-off__trigger ',
						$block_content );

					// Add href
					if ( !empty( $page_contact ) && isset( $page_contact[ 'permalink' ] ) ) {
						$block_content = str_replace(
							'href="#"',
							'href="' . esc_url( $page_contact[ 'permalink' ] ) . '"',
							$block_content );
					}
				}
			}
		}

		return $block_content;
	}

	add_filter( 'render_block', 'baspa_block_button_modify', 10, 2 );

}
