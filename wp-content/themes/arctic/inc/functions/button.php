<?php

/**
 * Button
 */

if ( !function_exists( 'baspa_button_next' ) ) {

	/**
	 * Button - Next
	 *
	 * @param string $url
	 * @param string $text
	 * @param bool $outline
	 *
	 * @return string
	 */
	function baspa_button_next( string $text, string $url, bool $outline = true ): string {

		$button_class = array(
			'f-button',
			'f-button--next',
			'a-button',
		);

		if ( $outline ) {
			$button_class[] = 'a-button--outline';
		}

		// Construct button
		$button = '<a href="' . esc_url( $url ) . '" class="' . join( ' ', $button_class ) . '">';
		$button .= esc_html( $text );
		$button .= '<span class="f-next__icon" aria-hidden="true"></span>';
		$button .= '</a>';

		return $button;

	}

}

if ( !function_exists( 'baspa_button_block_markup' ) ) {

	/**
	 * Change Markup of the 'core/button' Block
	 *
	 * @param $block_content
	 * @param $block
	 *
	 * @return array|mixed|string|string[]
	 */
	function baspa_button_block_markup( $block_content, $block ): mixed {

		if ( 'core/button' === $block[ 'blockName' ] && isset($block[ 'attrs' ][ 'className' ]) && str_contains( $block[ 'attrs' ][ 'className' ], 'is-style-next' ) ) {
//			do_action( 'qm/debug', $block_content );
			$block_content = str_replace( '</a>', '<span class="f-next__icon" aria-hidden="true"></span></a>', $block_content );
		}

		return $block_content;
	}

	add_filter( 'render_block', 'baspa_button_block_markup', 10, 2 );

}
