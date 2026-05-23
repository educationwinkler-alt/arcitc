<?php

/**
 * Shortcodes
 */

if ( !function_exists( 'baspa_contacts_shortcode_form_catalog' ) ) {

	/**
	 * Form - Catalog
	 * [formular-katalog]
	 *
	 * @return bool|string
	 */
	function baspa_contacts_shortcode_form_catalog(): bool|string {

		ob_start();

		get_template_part( 'modules/contacts/templates/form', 'catalog' );

		return ob_get_clean();

	}

	add_shortcode( 'formular-katalog', 'baspa_contacts_shortcode_form_catalog' );

}
