<?php

/**
 * Privacy
 *
 * @package    forqys/function
 * @since      1.0.6
 */

if ( !function_exists( 'forqy_privacy_page_exists' ) ) {

	/**
	 * Check If Privacy Page Exists
	 *
	 * @return bool
	 */
	function forqy_privacy_page_exists(): bool {
		$privacy_page_id     = get_option( 'wp_page_for_privacy_policy', 0 );
		$privacy_page_status = get_post_status( $privacy_page_id );

		if ( !empty( $privacy_page_id ) ) {
			if ( $privacy_page_status === 'publish' ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
