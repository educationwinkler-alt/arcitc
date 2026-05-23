<?php

/**
 * Price
 *
 * @package    forqys/function
 * @since      1.0.9
 */

if ( !function_exists( 'forqy_price_czk' ) ) {

	/**
	 * Format Price to CZK
	 *
	 * @param $value
	 * @param int $decimals
	 * @param string $prefix
	 * @param string $suffix
	 *
	 * @return string
	 */
	function forqy_price_czk( $value, int $decimals = 2, string $prefix = '', string $suffix = 'Kč' ): string {

		$value = number_format( $value, $decimals, ',', ' ' );

		$prefix_el = !empty( $prefix ) ? '<span class="prefix">' . esc_html( $prefix ) . '</span>&nbsp;' : '';
		$suffix_el = !empty( $suffix ) ? '&nbsp;<span class="suffix">' . esc_html( $suffix ) . '</span>' : '';

		return $prefix_el . $value . $suffix_el;

	}

}