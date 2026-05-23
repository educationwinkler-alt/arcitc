<?php

if ( !function_exists( 'forqy_hours_parse' ) ) {

	/**
	 * Parse Hours
	 *
	 * @param string $value
	 *
	 * @return array<string>
	 */
	function forqy_hours_parse( string $value ): array {
		$intervals = explode( ',', $value );
		$parsed    = [];

		foreach ( $intervals as $interval ) {
			$interval = trim( $interval );

			// Replace "hod.", space of "am/pm", all lowercase
			$interval = strtolower( str_replace( [ 'hod.', 'am', 'pm' ], [ '', ' am', ' pm' ], $interval ) );
			$interval = preg_replace( '/\s+/', '', $interval );

			// Get time
			if ( preg_match( '/(\d{1,2})(?::(\d{2}))?([ap]m)?-(\d{1,2})(?::(\d{2}))?([ap]m)?/', $interval, $matches ) ) {
				$from     = forqy_hours_convert_to_24h( $matches[ 1 ], $matches[ 2 ] ?? '00', $matches[ 3 ] ?? '' );
				$to       = forqy_hours_convert_to_24h( $matches[ 4 ], $matches[ 5 ] ?? '00', $matches[ 6 ] ?? '' );
				$parsed[] = "$from-$to";
			}
		}

		return $parsed;

	}

}

if ( !function_exists( 'forqy_hours_convert_to_24h' ) ) {

	/**
	 * @param string $h
	 * @param string $m
	 * @param string $ampm
	 *
	 * @return string
	 */
	function forqy_hours_convert_to_24h( string $h, string $m, string $ampm = '' ): string {
		$h = (int)$h;
		$m = str_pad( $m, 2, '0', STR_PAD_LEFT );

		if ( $ampm === 'pm' && $h < 12 ) $h += 12;
		if ( $ampm === 'am' && $h === 12 ) $h = 0;

		return str_pad( $h, 2, '0', STR_PAD_LEFT ) . ':' . $m;

	}

}

if ( !function_exists( 'forqy_hours_format_day_group' ) ) {

	/**
	 * Format Day Group
	 *
	 * @param array<string> $group
	 * @param string $value
	 * @param int $short_length
	 * @param bool $short
	 *
	 * @return string
	 */
	function forqy_hours_format_day_group( array $group, string $value, int $short_length, bool $short ): string {
		$days  = $group;
		$label = count( $days ) > 1 ? "{$days[0]}–" . end( $days ) : $days[ 0 ];

		return "$label: $value";
	}

}
