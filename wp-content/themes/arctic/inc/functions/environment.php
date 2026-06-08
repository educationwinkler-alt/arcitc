<?php

/**
 * Environment helpers.
 */

if ( !function_exists( 'arctic_allow_seed_fallbacks' ) ) {
	function arctic_allow_seed_fallbacks(): bool {
		if ( defined( 'ARCTIC_ALLOW_SEED_FALLBACKS' ) ) {
			return (bool) ARCTIC_ALLOW_SEED_FALLBACKS;
		}

		$environment = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';
		$allowed     = in_array( $environment, array( 'local', 'development' ), true );

		return (bool) apply_filters( 'arctic_allow_seed_fallbacks', $allowed, $environment );
	}
}

if ( !function_exists( 'arctic_legacy_mojibake_values' ) ) {
	/**
	 * Generate legacy mojibake forms from a canonical UTF-8 string.
	 *
	 * Older seeded option values were sometimes saved after UTF-8 bytes were
	 * interpreted as Windows-1250. Generate those values at runtime so the
	 * source remains clean UTF-8 while old DB values still normalize.
	 *
	 * @param string $value
	 * @param int    $levels
	 *
	 * @return array<int, string>
	 */
	function arctic_legacy_mojibake_values( string $value, int $levels = 2 ): array {
		if ( !function_exists( 'iconv' ) ) {
			return array();
		}

		$values  = array();
		$current = $value;

		for ( $index = 0; $index < $levels; $index++ ) {
			$converted = @iconv( 'Windows-1250', 'UTF-8//IGNORE', $current );

			if ( !is_string( $converted ) || '' === $converted || $converted === $current ) {
				break;
			}

			$values[] = $converted;
			$current  = $converted;
		}

		return array_values( array_unique( $values ) );
	}
}
