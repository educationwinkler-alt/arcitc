<?php

/**
 * Gallery Shortcodes
 */

if ( !function_exists( 'baspa_shortcode_divider' ) ) {

	/**
	 * Divider
	 * [divider]
	 *
	 * @param $atts
	 *
	 * @return bool|string
	 */
	function baspa_shortcode_divider( $atts ): bool|string {

		$default_atts = array(
			'anchor' => '',
		);
		$atts         = shortcode_atts( $default_atts, $atts, 'divider' );

		ob_start();
		if ( isset( $atts[ 'anchor' ] ) ) { ?>
			<a href="<?php echo esc_attr( $atts[ 'anchor' ] ); ?>" class="f-divider f-divider--link">
				<span class="f-divider__icon f-icon"><?php get_template_part( 'images/icon/arrow', 'down' ); ?></span>
			</a>
		<?php } else { ?>
			<div class="f-divider">
				<span class="f-divider__icon f-icon"><?php get_template_part( 'images/icon/arrow', 'down' ); ?></span>
			</div>
		<?php }

		return ob_get_clean();

	}

	add_shortcode( 'divider', 'baspa_shortcode_divider' );

}
