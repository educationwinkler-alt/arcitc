<?php
/**
 * Arctic local safety guards.
 *
 * Keeps the Docker WordPress instance from contacting live services while the
 * Baspa fork is being adapted.
 */

if ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'local' === WP_ENVIRONMENT_TYPE ) {
	add_filter( 'pre_http_request', 'arctic_local_block_external_http', 10, 3 );
	add_filter( 'pre_wp_mail', 'arctic_local_block_mail', 10, 2 );
}

function arctic_local_block_external_http( $preempt, array $parsed_args, string $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );

	if ( empty( $host ) ) {
		return $preempt;
	}

	$allowed_hosts = array(
		'localhost',
		'127.0.0.1',
		'::1',
		'wordpress',
		'db',
	);

	if ( in_array( strtolower( $host ), $allowed_hosts, true ) ) {
		return $preempt;
	}

	return new WP_Error(
		'arctic_local_external_http_blocked',
		sprintf( 'Local WordPress blocked an external HTTP request to %s.', esc_html( $host ) )
	);
}

function arctic_local_block_mail( $return, array $atts ) {
	return true;
}
