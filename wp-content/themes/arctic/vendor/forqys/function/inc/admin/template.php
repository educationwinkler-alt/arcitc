<?php

/**
 * Template
 *
 * @package     forqys/function
 * @since       1.0.0
 */

if ( !function_exists( 'forqy_admin_template_add_class' ) ) {

	/**
	 * Add Page Template Class to Body
	 *
	 * @param $classes
	 *
	 * @return mixed|string
	 */
	function forqy_admin_template_add_class( $classes ) {

		$template = get_page_template_slug();

		if ( $template ) {
			$class   = preg_replace( '/\.php$/', '', $template );
			$classes .= ' f-page--' . sanitize_html_class( $class );
		}

		return $classes;
	}

	add_filter( 'admin_body_class', 'forqy_admin_template_add_class' );

}
