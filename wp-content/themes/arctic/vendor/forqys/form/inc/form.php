<?php

/**
 * Form
 *
 * @package forqys/form
 * @since   1.0.0
 */

if ( !function_exists( 'forqy_form_processing' ) ) {

	/**
	 * AJAX Form Processing
	 *
	 * @return void
	 */
	function forqy_form_processing(): void {

		// Template Path
		$form_processing_template_path = $_POST[ 'f-form-processing-path' ] ?? apply_filters(
			'forqy_form_processing_template_path',
			'modules/contacts/templates/form/processing'
		);

		get_template_part( $form_processing_template_path );

		exit();

	}

	add_action( 'wp_ajax_form_processing', 'forqy_form_processing' );
	add_action( 'wp_ajax_nopriv_form_processing', 'forqy_form_processing' );

}
