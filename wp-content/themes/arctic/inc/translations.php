<?php

/**
 * Translations
 */

if ( !function_exists( 'baspa_translations' ) ) {

	/**
	 * Translation Strings
	 *
	 * @return array<string>
	 */
	function baspa_translations(): array {

		return array(
			'pswp_close'     => _x( 'Close', 'lightbox', 'baspa' ),
			'pswp_prev'      => _x( 'Previous', 'lightbox', 'baspa' ),
			'pswp_next'      => _x( 'Next', 'lightbox', 'baspa' ),
			'pswp_zoom'      => _x( 'Zoom', 'lightbox', 'baspa' ),
			'pswp_error'     => _x( 'Unfortunately, the item could not be loaded.', 'lightbox', 'baspa' ),
			'pswp_separator' => _x( ' / ', 'lightbox', 'baspa' ),
		);

	}

	add_filter( 'forqy_translations', 'baspa_translations' );

}
