<?php

/**
 * Form
 */

if ( !function_exists( 'baspa_contacts_form_interest_options' ) ) {

	/**
	 * Set Interest Options
	 *
	 * @return array
	 */
	function baspa_contacts_form_interest_options(): array {

		return array(
			'pool'    => __( 'Swimspa', 'baspa' ),
			'jacuzzi' => __( 'Vířivka', 'baspa' ),
			'service' => __( 'Servis', 'baspa' ),
		);

	}

}

if ( !function_exists( 'baspa_contacts_get_interest_title' ) ) {

	/**
	 * Get Interest Title
	 *
	 * @param $key
	 *
	 * @return string
	 */
	function baspa_contacts_get_interest_title( $key ): string {

		$interests = baspa_contacts_form_interest_options();

		return $interests[ $key ] ?: '';

	}

}
