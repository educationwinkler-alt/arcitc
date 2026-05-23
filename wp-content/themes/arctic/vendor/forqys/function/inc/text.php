<?php

/**
 * Text
 *
 * @package    	forqys/function
 * @since      	1.1.3
 */

if ( !function_exists( 'forqy_text_obfuscate' ) ) {

	/**
	 * Obfuscate Text Marked with {*TEXT*}
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	function forqy_text_obfuscate( string $text ): string {

		return preg_replace_callback( '/{\*(.*?)\*}/', function ( $matches ) {
			return antispambot( $matches[ 1 ] );
		}, $text );
	}
}
