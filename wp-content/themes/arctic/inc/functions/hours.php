<?php

/**
 * Hours
 */

if ( !function_exists( 'baspa_hours' ) ) {

	/**
	 * Set Hours
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function baspa_hours(): array {

		return array(
			'monday'    => array(
				'label' => __( 'Monday', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_monday' ),
			),
			'tuesday'   => array(
				'label' => __( 'Tuesday', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_tuesday' ),
			),
			'wednesday' => array(
				'label' => __( 'Wednesday', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_wednesday' ),
			),
			'thursday'  => array(
				'label' => __( 'Thursday', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_thursday' ),
			),
			'friday'    => array(
				'label' => __( 'Friday', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_friday' ),
			),
			'saturday'  => array(
				'label' => __( 'Saturday', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_saturday' ),
			),
			'sunday'    => array(
				'label' => __( 'Sunday', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_sunday' ),
			),
		);

	}

	add_filter( 'forqy_hours', 'baspa_hours' );

}
