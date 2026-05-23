<?php

/**
 * Font API
 *
 * @package forqys/font
 * @since   1.0.0
 */

if ( !function_exists( 'forqy_fonts_config' ) ) {

	/**
	 * Get Configuration
	 *
	 * @return array
	 */
	function forqy_fonts_config(): array {

		// Set defaults and merge with 'forqy_fonts' config if available
		$fonts = array_replace( array(
			'font_api' => 'google',
		), apply_filters( 'forqy_fonts', array() ) );

		return array_replace( $fonts, apply_filters( 'forqy_theme', array() ) );

	}

}

if ( !function_exists( 'forqy_fonts_url' ) ) {

	/**
	 * Construct Font API URL
	 *
	 * @url https://fonts.google.com/
	 * @url https://fontshare.com/
	 *
	 * @return false|string
	 */
	function forqy_fonts_url() {

		$config = forqy_fonts_config();

		if ( empty( $config ) || !empty( $config[ 'font_api' ] ) != 'local' || empty( $config[ 'font_primary' ] ) || empty( $config[ 'font_secondary' ] ) ) {

			return false;

		} else {

			/**
			 * API
			 */
			// Google Fonts API - Default
			$font_api_url          = 'https://fonts.googleapis.com/css2';
			$font_api_param_family = 'family';
			$font_api_param_weight = ':wght@';
			$font_api_family_case  = 'capitalize';
			$font_api_family_sep   = '+';
			$font_api_style_sep    = ';';

			// Fontshare API
			if ( $config[ 'font_api' ] === 'fontshare' ) {
				$font_api_url          = 'https://api.fontshare.com/v2/css'; // Fontshare API
				$font_api_param_family = 'f[]';
				$font_api_param_weight = '@';
				$font_api_family_case  = 'lowercase';
				$font_api_family_sep   = '-';
				$font_api_style_sep    = ',';
			}

			/**
			 * Primary Font
			 */
			$font_primary = esc_attr( $config[ 'font_primary' ] );
			if ( $font_api_family_case === 'capitalize' ) {
				$font_primary = ucwords( $font_primary );
			}
			if ( $font_api_family_case === 'lowercase' ) {
				$font_primary = strtolower( $font_primary );
			}
			$font_primary = str_replace( ' ', $font_api_family_sep, $font_primary );

			// Styles
			$font_primary_styles = $config[ 'font_primary_styles' ] ? esc_attr( $config[ 'font_primary_styles' ] ) : '400,700';
			$font_primary_styles = str_replace( ' ', '', $font_primary_styles );
			$font_primary_styles = str_replace( ',', $font_api_style_sep, $font_primary_styles );
			$font_primary_styles = $font_api_param_weight . $font_primary_styles;

			/**
			 * Secondary Font
			 */
			$font_secondary = esc_attr( $config[ 'font_secondary' ] );
			if ( $font_api_family_case === 'capitalize' ) {
				$font_secondary = ucwords( $font_secondary );
			}
			if ( $font_api_family_case === 'lowercase' ) {
				$font_secondary = strtolower( $font_secondary );
			}
			$font_secondary = str_replace( ' ', $font_api_family_sep, $font_secondary );

			// Styles
			$font_secondary_styles = $config[ 'font_secondary_styles' ] ? esc_attr( $config[ 'font_secondary_styles' ] ) : '400,700';
			$font_secondary_styles = str_replace( ' ', '', $font_secondary_styles );
			$font_secondary_styles = str_replace( ',', $font_api_style_sep, $font_secondary_styles );
			$font_secondary_styles = $font_api_param_weight . $font_secondary_styles;

			/**
			 * Display
			 */
			$font_display = $config[ 'font_display' ] ? esc_attr( $config[ 'font_display' ] ) : 'swap';

			/**
			 * Construct Fonts URL API v2
			 */
			return esc_url_raw( $font_api_url . '?' . $font_api_param_family . '=' . $font_primary . $font_primary_styles . '&amp;' . $font_api_param_family . '=' . $font_secondary . $font_secondary_styles . '&amp;display=' . $font_display );

		}

	}

	add_filter( 'forqy_fonts_url', 'forqy_fonts_url' );

}

if ( !function_exists( 'forqy_fonts_enqueue' ) ) {

	/**
	 * Enqueue Fonts
	 */
	function forqy_fonts_enqueue() {

		$config = forqy_fonts_config();

		if ( apply_filters( 'forqy_fonts_url', false ) ) {

			/**
			 * Enqueue Fonts by API
			 */
			if ( $config[ 'font_api' ] == 'google'
				&& apply_filters( 'forqy_fonts_google_enqueue', true ) ) {
				wp_enqueue_style( 'google-fonts', apply_filters( 'forqy_fonts_url', false ), array(), null );
			}
			if ( $config[ 'font_api' ] == 'fontshare'
				&& apply_filters( 'forqy_fonts_fontshare_enqueue', true ) ) {
				wp_enqueue_style( 'fontshare-fonts', apply_filters( 'forqy_fonts_url', false ), array(), null );
			}
		}

	}

	add_action( 'wp_enqueue_scripts', 'forqy_fonts_enqueue', 1 );
	add_action( 'enqueue_block_editor_assets', 'forqy_fonts_enqueue', 1 );

}

if ( !function_exists( 'forqy_fonts_enqueue_crossorigin' ) ) {

	/**
	 * Add 'crossorigin' Attribute to <link>
	 *
	 * @param $html
	 * @param $handle
	 *
	 * @return string|string[]
	 */
	function forqy_fonts_enqueue_crossorigin( $html, $handle ) {

		if ( apply_filters( 'forqy_fonts_url', false ) ) {

			if ( $handle === 'google-fonts' || $handle === 'fontshare-fonts' ) {
				return str_replace( "media='all'", "media='all' crossorigin", $html );
			}
		}

		return $html;
	}

	add_filter( 'style_loader_tag', 'forqy_fonts_enqueue_crossorigin', 10, 2 );

}

if ( !function_exists( 'forqy_fonts_pre' ) ) {

	/**
	 * Preconnect & Preload Fonts in <head>
	 *
	 * @param $url
	 */
	function forqy_fonts_pre( $url ) {

		$config = forqy_fonts_config();

		// Google Fonts API
		if ( $config[ 'font_api' ] === 'google' && apply_filters( 'forqy_fonts_url', false ) ) {

			if ( apply_filters( 'forqy_fonts_google_preconnect', true ) ) { ?>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<?php }
			if ( apply_filters( 'forqy_fonts_google_preload', true ) ) { ?>
<link rel="preload" href="<?php echo apply_filters( 'forqy_fonts_url', $url ); ?>" as="style" crossorigin>
			<?php }
		}

		// Fontshare API
		if ( $config[ 'font_api' ] === 'fontshare' && apply_filters( 'forqy_fonts_url', false ) ) {

			if ( apply_filters( 'forqy_fonts_fontshare_preconnect', true ) ) { ?>
<link rel="preconnect" href="https://api.fontshare.com" crossorigin>
<link rel="preconnect" href="https://cdn.fontshare.com" crossorigin>
			<?php }
			if ( apply_filters( 'forqy_fonts_fontshare_preload', true ) ) { ?>
<link rel="preload" href="<?php echo apply_filters( 'forqy_fonts_url', $url ); ?>" as="style">
			<?php }
		}

	}

	if ( function_exists( 'forqy_action_attach' ) ) {
		forqy_action_attach( 'forqy_head_pre', 'forqy_fonts_pre', 'wp_head', 5 );
	} else {
		add_action( 'wp_head', 'forqy_fonts_pre', 1 );
	}
	add_action( 'admin_head', 'forqy_fonts_pre', 1 );

}
