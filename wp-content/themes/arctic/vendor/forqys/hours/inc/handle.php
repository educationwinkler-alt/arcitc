<?php

/**
 * Handle Hours
 *
 * @since 1.0.0
 */

if ( !function_exists( 'forqy_hours_ajax_is_open' ) ) {

	/**
	 * AJAX to get open status
	 *
	 * @throws Exception
	 */
	function forqy_hours_ajax_is_open(): void {
		$hours_raw = isset( $_POST[ 'hours' ] ) ? json_decode( stripslashes( $_POST[ 'hours' ] ), true ) : [];

		if ( !is_array( $hours_raw ) ) {
			wp_send_json_error( [ 'message' => 'Invalid hours data' ] );
		}

		$now = new DateTime( 'now', wp_timezone() );
		$day = strtolower( $now->format( 'l' ) );

		if ( empty( $hours_raw[ $day ][ 'value' ] ) ) {
			wp_send_json( [ 'open' => false ] );
		}

		$intervals   = forqy_hours_parse( $hours_raw[ $day ][ 'value' ] );
		$now_minutes = $now->format( 'H' ) * 60 + $now->format( 'i' );

		foreach ( $intervals as $interval ) {
			[ $from, $to ] = explode( '-', $interval );
			[ $fh, $fm ] = explode( ':', $from );
			[ $th, $tm ] = explode( ':', $to );

			$from_minutes = $fh * 60 + $fm;
			$to_minutes   = $th * 60 + $tm;

			if ( $now_minutes >= $from_minutes && $now_minutes <= $to_minutes ) {
				wp_send_json( [ 'open' => true ] );
			}
		}

		wp_send_json( [ 'open' => false ] );
	}

	add_action( 'wp_ajax_hours_is_open', 'forqy_hours_ajax_is_open' );
	add_action( 'wp_ajax_nopriv_hours_is_open', 'forqy_hours_ajax_is_open' );

}
