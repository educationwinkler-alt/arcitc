<?php

/**
 * Display
 *
 * @since 1.0.0
 */

if ( !function_exists( 'forqy_hours' ) ) {

	/**
	 * Display Hours
	 *
	 * @param array<array<string>> $hours
	 * @param bool $short
	 * @param int $short_length
	 *
	 * @return string
	 */
	function forqy_hours( array $hours, bool $short = false, int $short_length = 2 ): string {
		$result   = [];
		$previous = null;
		$group    = [];

		foreach ( $hours as $day => $data ) {
			$label  = $data[ 'label' ];
			$parsed = implode( ', ', function_exists( 'forqy_hours_parse' ) ? forqy_hours_parse( $data[ 'value' ] ?? '' ) : '' );

			if ( $previous && $previous[ 'value' ] === $parsed ) {
				$group[] = $short ? mb_substr( $label, 0, $short_length ) : $label;
			} else {
				if ( $group ) {
					$result[] = function_exists( 'forqy_hours_format_day_group' ) ? forqy_hours_format_day_group( $group, $previous[ 'value' ], $short_length, $short ) : '';
				}
				$group = [ $short ? mb_substr( $label, 0, $short_length ) : $label ];
			}
			$previous = [ 'value' => $parsed, 'label' => $label ];
		}

		if ( $group && !empty( $previous[ 'value' ] ) ) {
			$result[] = function_exists( 'forqy_hours_format_day_group' ) ? forqy_hours_format_day_group( $group, $previous[ 'value' ], $short_length, $short ) : '';
		}

//		do_action( 'qm/debug', $result );

		return implode( ', ', $result );
	}

}
