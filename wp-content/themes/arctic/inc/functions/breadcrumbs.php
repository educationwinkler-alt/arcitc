<?php

/**
 * Breadcrumbs
 */

if ( !function_exists( 'baspa_breadcrumbs' ) ) {

	/**
	 * Breadcrumbs Settings
	 *
	 * @return array
	 */
	function baspa_breadcrumbs(): array {

		return array(
			'types'     => array(
				'product' => 'product-category',
			),
			'templates' => array(
				'post'      => 'template-blog.php',
				'service'   => 'template-services.php',
				'reference' => 'template-references.php',
				'faq'       => 'template-faq.php',
			),
			'separator' => '&mdash;',
			'label'     => _x( 'Breadcrumbs', 'breadcrumbs', 'baspa' ),
			'home'      => '<svg class="icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 2L14 7V8H13V14H10V12C10 10.8954 9.10457 10 8 10C6.89543 10 6 10.8954 6 12V14H3V8H2V7L8 2Z" fill="black"/></svg>',
			'404'       => _x( 'Page not found', 'breadcrumbs', 'baspa' ),
			'search'    => _x( 'Search results for "%s"', 'breadcrumbs', 'baspa' ),
		);

	}

	add_filter( 'forqy_breadcrumbs', 'baspa_breadcrumbs' );

}
