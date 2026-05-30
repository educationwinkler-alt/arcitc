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
		$weekday_hours = '8:00-17:00';

		return array(
			'monday'    => array(
				'label' => __( 'Pondělí', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_monday', $weekday_hours ),
			),
			'tuesday'   => array(
				'label' => __( 'Úterý', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_tuesday', $weekday_hours ),
			),
			'wednesday' => array(
				'label' => __( 'Středa', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_wednesday', $weekday_hours ),
			),
			'thursday'  => array(
				'label' => __( 'Čtvrtek', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_thursday', $weekday_hours ),
			),
			'friday'    => array(
				'label' => __( 'Pátek', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_friday', $weekday_hours ),
			),
			'saturday'  => array(
				'label' => __( 'Sobota', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_saturday', '' ),
			),
			'sunday'    => array(
				'label' => __( 'Neděle', 'baspa' ),
				'value' => get_theme_mod( 'baspa_hours_sunday', '' ),
			),
		);

	}

	add_filter( 'forqy_hours', 'baspa_hours' );

}

if ( !function_exists( 'baspa_hours_bar_label' ) ) {

	/**
	 * Header copy matches the Figma top contact while the status class stays dynamic.
	 *
	 * @param array<string, array<string, mixed>> $hours
	 *
	 * @return string
	 */
	function baspa_hours_bar_label( array $hours ): string {
		$weekdays = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday' );
		$parsed   = array();

		foreach ( $weekdays as $day ) {
			$value    = isset( $hours[ $day ]['value'] ) ? (string) $hours[ $day ]['value'] : '';
			$parsed[] = implode( ', ', function_exists( 'forqy_hours_parse' ) ? forqy_hours_parse( $value ) : array() );
		}

		$weekday_value = $parsed[0] ?? '';
		$same_weekdays = !empty( $weekday_value ) && 1 === count( array_unique( $parsed ) );
		$weekend_empty = empty( $hours['saturday']['value'] ) && empty( $hours['sunday']['value'] );

		if ( $same_weekdays && $weekend_empty ) {
			$weekday_value = preg_replace( '/\b0(\d:)/', '$1', $weekday_value );

			return sprintf( 'Po - Pá %s h', $weekday_value );
		}

		return function_exists( 'forqy_hours' ) ? forqy_hours( $hours, true, 2 ) : '';
	}

}
