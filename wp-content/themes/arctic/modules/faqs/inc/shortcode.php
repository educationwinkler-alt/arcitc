<?php

/**
 * Shortcode
 */

if ( !function_exists( 'baspa_faqs_shortcode' ) ) {

	/**
	 * FAQ
	 * [faq]
	 *
	 * @return bool|string
	 */
	function baspa_faqs_shortcode(): bool|string {

		ob_start();

		get_template_part( 'modules/faqs/templates/loop' );

		return ob_get_clean();

	}

	add_shortcode( 'faq', 'baspa_faqs_shortcode' );

}

if ( !function_exists( 'baspa_faqs_shortcode_section' ) ) {

	/**
	 * FAQs
	 * [faq-section]
	 *
	 * @return bool|string
	 */
	function baspa_faqs_shortcode_section(): bool|string {

		ob_start();

		get_template_part( 'modules/faqs/templates/section' );

		return ob_get_clean();

	}

	add_shortcode( 'faq-section', 'baspa_faqs_shortcode_section' );

}
