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
		$form_processing_template_path = apply_filters(
			'forqy_form_processing_template_path',
			'modules/contacts/templates/form/processing'
		);
		$allowed_template_paths        = apply_filters(
			'forqy_form_processing_allowed_template_paths',
			array(
				'modules/contacts/templates/form/processing',
			)
		);

		if ( !in_array( $form_processing_template_path, $allowed_template_paths, true ) ) {
			wp_die( esc_html__( 'Invalid form processing template.', 'baspa' ), '', array( 'response' => 400 ) );
		}

		get_template_part( $form_processing_template_path );

		wp_die();

	}

	add_action( 'wp_ajax_form_processing', 'forqy_form_processing' );
	add_action( 'wp_ajax_nopriv_form_processing', 'forqy_form_processing' );

}
