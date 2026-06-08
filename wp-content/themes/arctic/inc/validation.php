<?php

/**
 * Front-end validation helpers.
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function arctic_validation_disable_auto_sizes_css(): void {
	remove_action( 'wp_head', 'wp_print_auto_sizes_contain_css_fix', 1 );
}

function arctic_validation_should_clean_output(): bool {
	$is_robots = function_exists( 'is_robots' ) && is_robots();

	if ( is_admin() || is_feed() || $is_robots || is_trackback() ) {
		return false;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
		return false;
	}

	return true;
}

function arctic_validation_clean_output( string $html ): string {
	if ( '' === $html || false === stripos( $html, '<html' ) ) {
		return $html;
	}

	$cleaned = preg_replace(
		'#\s*<style\s+id=(["\'])wp-img-auto-sizes-contain-inline-css\1[^>]*>.*?</style>#is',
		'',
		$html
	);

	if ( is_string( $cleaned ) ) {
		$html = $cleaned;
	}

	if ( arctic_validation_should_replace_legacy_phone() ) {
		$html = arctic_validation_replace_legacy_phone( $html );
	}

	$blocks = array();
	$html   = preg_replace_callback(
		'#<(script|style)\b[^>]*>.*?</\1>#is',
		static function ( array $matches ) use ( &$blocks ): string {
			$key            = "\0ARCTIC_VALIDATION_BLOCK_" . count( $blocks ) . "\0";
			$blocks[ $key ] = $matches[0];

			return $key;
		},
		$html
	);

	if ( !is_string( $html ) ) {
		return '';
	}

	$void_tags = 'area|base|br|col|embed|hr|img|input|link|meta|source|track|wbr';
	$cleaned   = preg_replace(
		'#<(' . $void_tags . ')\b([^<>]*?)\s*/\s*>#i',
		'<$1$2>',
		$html
	);

	if ( is_string( $cleaned ) ) {
		$html = $cleaned;
	}

	if ( !empty( $blocks ) ) {
		$html = strtr( $html, $blocks );
	}

	return $html;
}

function arctic_validation_should_replace_legacy_phone(): bool {
	if ( function_exists( 'is_page' ) && is_page( array( 'kontakt', 'o-nas' ) ) ) {
		return false;
	}

	$path       = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path       = (string) parse_url( $path, PHP_URL_PATH );
	$normalized = trim( $path, '/' );

	return !in_array( $normalized, array( 'kontakt', 'o-nas' ), true );
}

function arctic_validation_replace_legacy_phone( string $html ): string {
	return strtr( $html, array(
		'+420 777 099 687' => '+420 602 149 106',
		'+420777099687'    => '+420602149106',
		'777 099 687'      => '602 149 106',
		'777099687'        => '602149106',
	) );
}

function arctic_validation_start_output_cleanup(): void {
	if ( !arctic_validation_should_clean_output() ) {
		return;
	}

	ob_start( 'arctic_validation_clean_output' );
}

add_action( 'init', 'arctic_validation_disable_auto_sizes_css' );
add_action( 'template_redirect', 'arctic_validation_start_output_cleanup', 0 );
