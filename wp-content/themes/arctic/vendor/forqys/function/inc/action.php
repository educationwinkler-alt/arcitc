<?php

/**
 * Action
 *
 * @package     forqys/function
 * @since       1.1.1
 */

if ( !function_exists( 'forqy_action_attach' ) ) {

	/**
	 * Attach Function to the Action Hook with Fallback if Action doesn't exists
	 *
	 * @param string $hook				action name
	 * @param callable $callback		function name
	 * @param string $fallback_hook		fallback action name
	 * @param int $priority
	 *
	 * @return void
	 */
	function forqy_action_attach( string $hook, callable $callback, string $fallback_hook, int $priority = 10 ): void {
		add_action( $hook, $callback, $priority );

		add_action( $fallback_hook, function () use ( $hook, $callback ) {
			if ( did_action( $hook ) === 0 ) {
				call_user_func( $callback );
			}
		}, 999 );
	}

}
