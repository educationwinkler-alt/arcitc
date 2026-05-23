<?php

/**
 * Hours
 *
 * @package     forqys/function
 * @since       1.0.0
 */

if ( !function_exists( 'forqy_hours_status' ) ) {

	/**
	 * Check If is Open or Closed
	 *
	 * @param string $from
	 * @param string $to
	 *
	 * @return string
	 */
	function forqy_hours_status( string $from, string $to ): string {

		$status = 'closed';

		$business_days = array( 1, 2, 3, 4, 5 );

		$current_day  = date( 'w', strtotime( current_datetime()->format( 'w' ) ) );
		$current_time = date( 'H:i', strtotime( current_datetime()->format( 'H:i' ) ) );

		if ( in_array( $current_day, $business_days ) ) {
			if ( strtotime( $current_time ) >= strtotime( $from ) && strtotime( $current_time ) < strtotime( $to ) ) {
				$status = 'open';
			}
		}

//		do_action( 'qm/debug', $current_day );
//		do_action( 'qm/debug', $current_time );
//		do_action( 'qm/debug', $status );

		return $status;
	}

}
