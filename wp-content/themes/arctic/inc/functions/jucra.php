<?php

/**
 * Jucra Configurator helpers.
 */

if ( !function_exists( 'arctic_jucra_sanitize_relative_path' ) ) {

	/**
	 * Sanitize relative path used for pricing form links.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	function arctic_jucra_sanitize_relative_path( $value ): string {

		$path = trim( sanitize_text_field( (string)$value ) );

		if ( $path === '' ) {
			return '';
		}

		if ( preg_match( '#^[a-z][a-z0-9+\-.]*://#i', $path ) || strpos( $path, '//' ) === 0 ) {
			return '';
		}

		if ( strpos( $path, '/' ) !== 0 ) {
			$path = '/' . ltrim( $path, '/' );
		}

		return $path;

	}

}

if ( !function_exists( 'arctic_is_root_homepage_request' ) ) {

	/**
	 * True only for canonical homepage URL "/" request.
	 *
	 * @return bool
	 */
	function arctic_is_root_homepage_request(): bool {

		if ( !is_front_page() ) {
			return false;
		}

		$current_request_path = '/';

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$path_from_request = parse_url( wp_unslash( (string)$_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
			if ( is_string( $path_from_request ) && $path_from_request !== '' ) {
				$current_request_path = $path_from_request;
			}
		}

		return untrailingslashit( $current_request_path ) === '';

	}

}

if ( !function_exists( 'arctic_jucra_is_enabled' ) ) {

	/**
	 * Whether Jucra integration is enabled in theme settings.
	 *
	 * @return bool
	 */
	function arctic_jucra_is_enabled(): bool {
		return (bool)get_theme_mod( 'arctic_jucra_enabled', false );
	}

}

if ( !function_exists( 'arctic_jucra_has_shortcode' ) ) {

	/**
	 * Whether Visao shortcode is available.
	 *
	 * @return bool
	 */
	function arctic_jucra_has_shortcode(): bool {
		return function_exists( 'shortcode_exists' ) && (
			shortcode_exists( 'visao_viewer' ) ||
			shortcode_exists( 'visao_builder' )
		);
	}

}

if ( !function_exists( 'arctic_jucra_get_shortcode_tag' ) ) {

	/**
	 * Resolve the Visao shortcode tag used by the installed plugin build.
	 *
	 * JUCRA KB 4832 documents [visao_viewer], but the supplied v1.26 ZIP
	 * registers [visao_builder]. Support both so the theme follows reality.
	 *
	 * @return string
	 */
	function arctic_jucra_get_shortcode_tag(): string {

		if ( function_exists( 'shortcode_exists' ) && shortcode_exists( 'visao_viewer' ) ) {
			return 'visao_viewer';
		}

		if ( function_exists( 'shortcode_exists' ) && shortcode_exists( 'visao_builder' ) ) {
			return 'visao_builder';
		}

		return 'visao_viewer';

	}

}

if ( !function_exists( 'arctic_jucra_get_default_model' ) ) {

	/**
	 * Resolve default Jucra model from Customizer.
	 *
	 * @return string
	 */
	function arctic_jucra_get_default_model(): string {

		$model = sanitize_text_field( (string)get_theme_mod( 'arctic_jucra_default_model', '' ) );
		$model = str_replace( array( '[', ']', '"', '\'' ), '', $model );

		return trim( $model );

	}

}

if ( !function_exists( 'arctic_jucra_resolve_model_name' ) ) {

	/**
	 * Resolve explicit model name or fall back to global default.
	 *
	 * @param string $model_name
	 *
	 * @return string
	 */
	function arctic_jucra_resolve_model_name( string $model_name = '' ): string {

		$model = sanitize_text_field( $model_name );
		$model = str_replace( array( '[', ']', '"', '\'' ), '', $model );
		$model = trim( $model );

		if ( $model !== '' ) {
			return $model;
		}

		return arctic_jucra_get_default_model();

	}

}

if ( !function_exists( 'arctic_jucra_get_product_model_name' ) ) {

	/**
	 * Resolve product-specific model name used for the viewer.
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	function arctic_jucra_get_product_model_name( int $post_id ): string {

		$model = get_post_meta( $post_id, 'jucra_model_name', true );
		$model = arctic_jucra_resolve_model_name( (string)$model );

		if ( $model !== '' && $model !== arctic_jucra_get_default_model() ) {
			return $model;
		}

		$title_slug = sanitize_title( get_the_title( $post_id ) );
		foreach ( arctic_jucra_model_definitions() as $definition ) {
			if ( sanitize_title( $definition['model_name'] ?? '' ) === $title_slug || sanitize_title( $definition['label'] ?? '' ) === $title_slug ) {
				return $definition['model_name'];
			}
		}

		return $model;

	}

}

if ( !function_exists( 'arctic_jucra_can_render_viewer' ) ) {

	/**
	 * Determine if we can render an inline viewer.
	 *
	 * @param string $model_name
	 *
	 * @return bool
	 */
	function arctic_jucra_can_render_viewer( string $model_name = '' ): bool {

		if ( !arctic_jucra_is_enabled() || !arctic_jucra_has_shortcode() ) {
			return false;
		}

		return arctic_jucra_resolve_model_name( $model_name ) !== '';

	}

}

if ( !function_exists( 'arctic_jucra_build_shortcode' ) ) {

	/**
	 * Build Visao shortcode string for rendering.
	 *
	 * @param string $model_name
	 *
	 * @return string
	 */
	function arctic_jucra_build_shortcode( string $model_name = '' ): string {

		$model = arctic_jucra_resolve_model_name( $model_name );

		if ( $model === '' ) {
			return '';
		}

		return sprintf( '[%s model_name="%s"]', arctic_jucra_get_shortcode_tag(), $model );

	}

}

if ( !function_exists( 'arctic_jucra_localize_shortcode_output' ) ) {

	/**
	 * Apply the Czech site contract around the vendor shortcode output.
	 *
	 * The Visao plugin remains the source of truth for models and option data;
	 * this only removes plugin chrome and translates stable UI labels.
	 *
	 * @param string $html
	 * @param string $model_name
	 *
	 * @return string
	 */
	function arctic_jucra_localize_shortcode_output( string $html, string $model_name = '' ): string {

		if ( $html === '' ) {
			return '';
		}

		$pricing_url = add_query_arg(
			'model_name',
			arctic_jucra_resolve_model_name( $model_name ),
			arctic_jucra_get_pricing_url( arctic_jucra_get_inquiry_relative_path() )
		);

		$html = preg_replace(
			'/<a href="[^"]*"\s+id="pricing-link"/',
			'<a href="' . esc_url( $pricing_url ) . '" id="pricing-link" data-jucra-pricing-handoff="local-inquiry"',
			$html,
			1
		) ?: $html;

		$html = preg_replace(
			'/\s*<small id="variants-title-version"[^>]*>.*?<\/small>/',
			'',
			$html
		) ?: $html;

		$replacements = array(
			'Build Your Spa'                                                                       => 'Sestavte si svou vířivku',
			'Explore a diverse range of shell colors, jets, and cabinet finishes.'                 => 'Vyberte dostupné trysky, barvu skořepiny a kabinetu pro zvolený model.',
			'Loading the Arctic Spas 3D Builder Experience....'                                    => 'Načítáme 3D konfigurátor Arctic Spas...',
			'For the best Build Your Spa experience, please use the latest version of your browser' => 'Pro nejlepší zážitek z 3D konfigurátoru použijte aktuální verzi prohlížeče.',
			'Request Pricing'                                                                      => 'Vyžádat cenovou nabídku',
			'Shell<br>Colour'                                                                      => 'Barva skořepiny',
			'Cabinet<br>Colour'                                                                    => 'Barva kabinetu',
		);

		$html = strtr( $html, $replacements );
		$html = preg_replace( '/(<li class=[\'\"]titles-options[\'\"]>)Jets(<\/li>)/', '$1Trysky$2', $html ) ?: $html;
		$html = preg_replace_callback(
			'/(<div class=[\'\"]option-name-text[\'\"]>)(.*?)(<\/div>)/',
			static function ( array $matches ): string {
				return $matches[1] . esc_html( arctic_jucra_translate_option_label( wp_strip_all_tags( $matches[2] ) ) ) . $matches[3];
			},
			$html
		) ?: $html;

		$compatibility_nodes = array();
		foreach ( array( 'status-label', 'current-step', 'steps-dropdown' ) as $element_id ) {
			if ( strpos( $html, 'id="' . $element_id . '"' ) === false ) {
				$compatibility_nodes[] = '<span id="' . esc_attr( $element_id ) . '"></span>';
			}
		}

		if ( !empty( $compatibility_nodes ) ) {
			$html .= '<div class="f-jucra-builder__vendor-compat" hidden aria-hidden="true">' . implode( '', $compatibility_nodes ) . '</div>';
		}

		return $html;

	}

}

if ( !function_exists( 'arctic_jucra_render_shortcode' ) ) {

	/**
	 * Render Visao shortcode through the local Arctic output contract.
	 *
	 * @param string $shortcode
	 * @param string $model_name
	 *
	 * @return string
	 */
	function arctic_jucra_render_shortcode( string $shortcode, string $model_name = '' ): string {

		if ( $shortcode === '' ) {
			return '';
		}

		$output = do_shortcode( $shortcode );

		if ( function_exists( 'wp_dequeue_style' ) ) {
			wp_dequeue_style( 'visao-css-poppins-font' );
			wp_deregister_style( 'visao-css-poppins-font' );
		}

		return arctic_jucra_localize_shortcode_output( $output, $model_name );

	}

}

if ( !function_exists( 'arctic_jucra_get_inquiry_relative_path' ) ) {

	/**
	 * Dedicated page that receives the configured model/options from Visao.
	 *
	 * @return string
	 */
	function arctic_jucra_get_inquiry_relative_path(): string {
		return '/poptavka-konfigurace/';
	}

}

if ( !function_exists( 'arctic_jucra_get_inquiry_url' ) ) {

	/**
	 * Build the local inquiry URL.
	 *
	 * @return string
	 */
	function arctic_jucra_get_inquiry_url(): string {
		return home_url( arctic_jucra_get_inquiry_relative_path() );
	}

}

if ( !function_exists( 'arctic_jucra_get_pricing_relative_path' ) ) {

	/**
	 * Resolve configured pricing path for Jucra CTA.
	 *
	 * @param string $default_path
	 *
	 * @return string
	 */
	function arctic_jucra_get_pricing_relative_path( string $default_path = '' ): string {

		if ( $default_path === '' ) {
			$default_path = arctic_jucra_get_inquiry_relative_path();
		}

		$configured = arctic_jucra_sanitize_relative_path( get_theme_mod( 'arctic_jucra_pricing_relative_url', $default_path ) );
		$fallback   = arctic_jucra_sanitize_relative_path( $default_path );

		if ( $configured === '/kontakt/' && $fallback === arctic_jucra_get_inquiry_relative_path() ) {
			return $fallback;
		}

		return $configured !== '' ? $configured : ( $fallback !== '' ? $fallback : arctic_jucra_get_inquiry_relative_path() );

	}

}

if ( !function_exists( 'arctic_jucra_get_pricing_url' ) ) {

	/**
	 * Build absolute local pricing URL from configured relative path.
	 *
	 * @param string $default_path
	 *
	 * @return string
	 */
	function arctic_jucra_get_pricing_url( string $default_path = '' ): string {

		return home_url( arctic_jucra_get_pricing_relative_path( $default_path ) );

	}

}

if ( !function_exists( 'arctic_jucra_translate_option_label' ) ) {

	/**
	 * Translate stable Visao option labels without changing HTML ids.
	 *
	 * @param string $label
	 *
	 * @return string
	 */
	function arctic_jucra_translate_option_label( string $label ): string {

		$label = trim( $label );

		return strtr( $label, array(
			'4 Pumps' => '4 čerpadla',
			'3 Pumps' => '3 čerpadla',
			'2 Pumps' => '2 čerpadla',
			'1 Pump'  => '1 čerpadlo',
		) );

	}

}

if ( !function_exists( 'arctic_jucra_normalize_option_id' ) ) {

	/**
	 * Accept old localized query values generated before option ids were
	 * protected from Czech label replacement.
	 *
	 * @param string $option_id
	 *
	 * @return string
	 */
	function arctic_jucra_normalize_option_id( string $option_id ): string {

		return strtr( $option_id, array(
			'Trysky'    => 'Jets',
			'čerpadla' => 'Pumps',
			'čerpadlo' => 'Pump',
			'ÄŤerpadla' => 'Pumps',
			'ÄŤerpadlo' => 'Pump',
		) );

	}

}

if ( !function_exists( 'arctic_jucra_request_value' ) ) {

	/**
	 * Read a scalar request value from GET, POST, or REQUEST.
	 *
	 * @param string $key
	 * @param string $source
	 *
	 * @return string
	 */
	function arctic_jucra_request_value( string $key, string $source = 'request' ): string {

		$bucket = $_REQUEST;

		if ( $source === 'post' ) {
			$bucket = $_POST;
		} else if ( $source === 'get' ) {
			$bucket = $_GET;
		}

		if ( !isset( $bucket[ $key ] ) || is_array( $bucket[ $key ] ) ) {
			return '';
		}

		return sanitize_text_field( wp_unslash( (string)$bucket[ $key ] ) );

	}

}

if ( !function_exists( 'arctic_jucra_fetch_model_payload' ) ) {

	/**
	 * Fetch Visao option metadata for the selected model.
	 *
	 * @param string $model_name
	 *
	 * @return array<string,mixed>
	 */
	function arctic_jucra_fetch_model_payload( string $model_name ): array {

		$model_name = arctic_jucra_resolve_model_name( $model_name );

		if ( $model_name === '' ) {
			return array();
		}

		$cache_key = 'arctic_jucra_model_' . md5( strtolower( $model_name ) );
		$cached    = get_transient( $cache_key );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$url  = 'https://api.arcticspascore.com/live/jsons/visao-3d-viewer.php?model_name=' . rawurlencode( $model_name );
		$body = '';

		$response = wp_remote_get(
			$url,
			array(
				'timeout'     => 8,
				'redirection' => 3,
				'headers'     => array(
					'Accept'     => 'application/json',
					'User-Agent' => 'ArcticSpasCZ/1.0 WordPress',
				),
			)
		);

		if ( !is_wp_error( $response ) ) {
			$response_code = (int)wp_remote_retrieve_response_code( $response );

			if ( $response_code >= 200 && $response_code < 300 ) {
				$body = (string)wp_remote_retrieve_body( $response );
			}
		}

		$data = json_decode( $body, true );

		if ( ( !is_array( $data ) || empty( $data['general_responses']['tub_exists'] ) ) && ini_get( 'allow_url_fopen' ) ) {
			$context = stream_context_create(
				array(
					'http' => array(
						'timeout' => 8,
						'header'  => "Accept: application/json\r\nUser-Agent: ArcticSpasCZ/1.0 WordPress\r\n",
					),
				)
			);

			$fallback_body = @file_get_contents( $url, false, $context );

			if ( is_string( $fallback_body ) && $fallback_body !== '' ) {
				$data = json_decode( $fallback_body, true );
			}
		}

		if ( !is_array( $data ) || empty( $data['general_responses']['tub_exists'] ) ) {
			return array();
		}

		set_transient( $cache_key, $data, 15 * MINUTE_IN_SECONDS );

		return $data;

	}

}

if ( !function_exists( 'arctic_jucra_find_option' ) ) {

	/**
	 * Find one Visao option by id and return a safe summary.
	 *
	 * @param array<int,array<string,mixed>> $options
	 * @param string                         $option_id
	 *
	 * @return array<string,string>
	 */
	function arctic_jucra_find_option( array $options, string $option_id ): array {

		$option_id            = trim( $option_id );
		$normalized_option_id = arctic_jucra_normalize_option_id( $option_id );
		$matched_option       = array();

		foreach ( $options as $option ) {
			if ( !is_array( $option ) ) {
				continue;
			}

			$current_id = isset( $option['option_id'] ) ? (string)$option['option_id'] : '';

			if ( $current_id === $option_id || $current_id === $normalized_option_id ) {
				$matched_option = $option;
				break;
			}
		}

		if ( empty( $matched_option ) && $option_id === '' ) {
			foreach ( $options as $option ) {
				if ( is_array( $option ) && !empty( $option['option_id'] ) ) {
					$matched_option = $option;
					break;
				}
			}
		}

		if ( !empty( $matched_option ) ) {
			$current_id = isset( $matched_option['option_id'] ) ? (string)$matched_option['option_id'] : '';
			$label      = isset( $matched_option['option_name'] ) ? arctic_jucra_translate_option_label( (string)$matched_option['option_name'] ) : '';

			return array(
				'id'       => $current_id,
				'label'    => $label !== '' ? $label : $current_id,
				'icon_url' => isset( $matched_option['icon_url'] ) ? esc_url_raw( (string)$matched_option['icon_url'] ) : '',
			);
		}

		$fallback_label = preg_replace( '/^dd-(Acrylic|Cabinet)?\s*/i', '', $normalized_option_id ) ?: $normalized_option_id;

		return array(
			'id'       => $normalized_option_id,
			'label'    => arctic_jucra_translate_option_label( trim( $fallback_label ) ),
			'icon_url' => '',
		);

	}

}

if ( !function_exists( 'arctic_jucra_get_inquiry_selection' ) ) {

	/**
	 * Build a display/submission payload from Visao query parameters.
	 *
	 * @param string $source
	 *
	 * @return array<string,mixed>
	 */
	function arctic_jucra_get_inquiry_selection( string $source = 'request' ): array {

		$model_name = arctic_jucra_request_value( 'model_name', $source );

		if ( $model_name === '' && $source !== 'get' ) {
			$model_name = arctic_jucra_request_value( 'f-jucra-model', $source );
		}

		$model_name = arctic_jucra_resolve_model_name( $model_name );
		$payload    = arctic_jucra_fetch_model_payload( $model_name );

		$option_ids = array(
			'jets'    => arctic_jucra_request_value( 'option_jets', $source ) ?: arctic_jucra_request_value( 'f-jucra-option-jets', $source ),
			'acrylic' => arctic_jucra_request_value( 'option_acrylic', $source ) ?: arctic_jucra_request_value( 'f-jucra-option-acrylic', $source ),
			'cabinet' => arctic_jucra_request_value( 'option_cabinet', $source ) ?: arctic_jucra_request_value( 'f-jucra-option-cabinet', $source ),
		);

		$options = array(
			'jets'    => array(
				'title' => 'Trysky',
				'value' => !empty( $payload['options_jets'] ) && is_array( $payload['options_jets'] ) ? arctic_jucra_find_option( $payload['options_jets'], $option_ids['jets'] ) : array(),
			),
			'acrylic' => array(
				'title' => 'Barva skořepiny',
				'value' => !empty( $payload['options_acrylics'] ) && is_array( $payload['options_acrylics'] ) ? arctic_jucra_find_option( $payload['options_acrylics'], $option_ids['acrylic'] ) : array(),
			),
			'cabinet' => array(
				'title' => 'Barva kabinetu',
				'value' => !empty( $payload['options_cabinets'] ) && is_array( $payload['options_cabinets'] ) ? arctic_jucra_find_option( $payload['options_cabinets'], $option_ids['cabinet'] ) : array(),
			),
		);

		foreach ( $options as $key => $option ) {
			if ( empty( $option['value'] ) && $option_ids[ $key ] !== '' ) {
				$options[ $key ]['value'] = array(
					'id'       => arctic_jucra_normalize_option_id( $option_ids[ $key ] ),
					'label'    => arctic_jucra_translate_option_label( arctic_jucra_normalize_option_id( $option_ids[ $key ] ) ),
					'icon_url' => '',
				);
			}
		}

		$valid = $model_name !== '' && !empty( $options['jets']['value']['id'] ) && !empty( $options['acrylic']['value']['id'] ) && !empty( $options['cabinet']['value']['id'] );

		return array(
			'valid'       => $valid,
			'model_name'  => $model_name,
			'model_slug'  => $model_name !== '' ? arctic_jucra_model_slug_from_name( $model_name ) : '',
			'options'     => $options,
			'builder_url' => $model_name !== '' ? arctic_jucra_get_builder_url( $model_name ) : arctic_jucra_get_builder_url(),
		);

	}

}

if ( !function_exists( 'arctic_jucra_model_definitions' ) ) {

	/**
	 * Local model map for the Czech builder selector.
	 *
	 * @return array<string,array<string,string>>
	 */
	function arctic_jucra_model_definitions(): array {

		$models = array(
			'summit-xl'   => array( 'label' => 'Summit XL', 'model_name' => 'Summit XL', 'product_slug' => 'summit-xl' ),
			'summit'      => array( 'label' => 'Summit', 'model_name' => 'Summit', 'product_slug' => 'summit' ),
			'tundra'      => array( 'label' => 'Tundra', 'model_name' => 'Tundra', 'product_slug' => 'tundra' ),
			'kodiak'      => array( 'label' => 'Kodiak', 'model_name' => 'Kodiak', 'product_slug' => 'kodiak' ),
			'cub'         => array( 'label' => 'Cub', 'model_name' => 'Cub', 'product_slug' => 'cub' ),
			'arctic-fox'  => array( 'label' => 'Arctic Fox', 'model_name' => 'Arctic Fox', 'product_slug' => 'arctic-fox' ),
			'mustang'     => array( 'label' => 'Mustang', 'model_name' => 'Mustang', 'product_slug' => 'mustang' ),
			'mckinley'    => array( 'label' => 'McKinley', 'model_name' => 'McKinley', 'product_slug' => 'mckinley' ),
			'totem'       => array( 'label' => 'Totem', 'model_name' => 'Totem', 'product_slug' => 'totem' ),
			'eagle'       => array( 'label' => 'Eagle', 'model_name' => 'Eagle', 'product_slug' => 'eagle' ),
			'timberwolf'  => array( 'label' => 'Timberwolf', 'model_name' => 'Timberwolf', 'product_slug' => 'timberwolf' ),
			'lunar'       => array( 'label' => 'Lunar', 'model_name' => 'Lunar', 'product_slug' => 'lunar' ),
			'orion'       => array( 'label' => 'Orion', 'model_name' => 'Orion', 'product_slug' => 'orion' ),
			'husky'       => array( 'label' => 'Husky', 'model_name' => 'Husky', 'product_slug' => 'husky' ),
		);

		return apply_filters( 'arctic_jucra_model_definitions', $models );

	}

}

if ( !function_exists( 'arctic_jucra_model_slug_from_name' ) ) {

	/**
	 * Resolve a local model slug from a Jucra model name.
	 *
	 * @param string $model_name
	 *
	 * @return string
	 */
	function arctic_jucra_model_slug_from_name( string $model_name ): string {

		$normalized = sanitize_title( $model_name );

		foreach ( arctic_jucra_model_definitions() as $slug => $model ) {
			if ( sanitize_title( $model['model_name'] ?? '' ) === $normalized ) {
				return $slug;
			}
		}

		return $normalized;

	}

}

if ( !function_exists( 'arctic_jucra_get_builder_url' ) ) {

	/**
	 * Build local Czech builder URL.
	 *
	 * @param string $model_name
	 *
	 * @return string
	 */
	function arctic_jucra_get_builder_url( string $model_name = '' ): string {

		$model_name = arctic_jucra_resolve_model_name( $model_name );
		$path       = '/konfigurator/';

		if ( $model_name !== '' ) {
			$path .= arctic_jucra_model_slug_from_name( $model_name ) . '/';
		}

		return home_url( $path );

	}

}

if ( !function_exists( 'arctic_jucra_get_selected_model' ) ) {

	/**
	 * Resolve selected builder model from request path/query.
	 *
	 * @return array<string,string>
	 */
	function arctic_jucra_get_selected_model(): array {

		$models = arctic_jucra_model_definitions();
		$slug   = '';

		if ( isset( $_GET['model'] ) ) {
			$slug = sanitize_title( wp_unslash( (string)$_GET['model'] ) );
		}

		if ( $slug === '' && isset( $_SERVER['REQUEST_URI'] ) ) {
			$path     = parse_url( wp_unslash( (string)$_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
			$segments = array_values( array_filter( explode( '/', trim( (string)$path, '/' ) ) ) );

			if ( isset( $segments[0] ) && $segments[0] === 'konfigurator' && isset( $segments[1] ) ) {
				$slug = sanitize_title( $segments[1] );
			}
		}

		if ( $slug === '' || !isset( $models[ $slug ] ) ) {
			$default_model = arctic_jucra_get_default_model();
			$slug          = $default_model !== '' ? arctic_jucra_model_slug_from_name( $default_model ) : 'timberwolf';
		}

		if ( !isset( $models[ $slug ] ) ) {
			$slug = array_key_first( $models );
		}

		$model         = $models[ $slug ] ?? array();
		$model['slug'] = $slug;

		return $model;

	}

}

if ( !function_exists( 'arctic_jucra_is_builder_request' ) ) {

	/**
	 * Detect the virtual Czech builder route.
	 *
	 * @return bool
	 */
	function arctic_jucra_is_builder_request(): bool {

		if ( is_admin() || wp_doing_ajax() ) {
			return false;
		}

		$path = isset( $_SERVER['REQUEST_URI'] ) ? parse_url( wp_unslash( (string)$_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';

		return is_string( $path ) && preg_match( '#^/konfigurator(?:/|$)#', $path ) === 1;

	}

}

if ( !function_exists( 'arctic_jucra_is_inquiry_request' ) ) {

	/**
	 * Detect the virtual Czech configurator inquiry route.
	 *
	 * @return bool
	 */
	function arctic_jucra_is_inquiry_request(): bool {

		if ( is_admin() || wp_doing_ajax() ) {
			return false;
		}

		$path = isset( $_SERVER['REQUEST_URI'] ) ? parse_url( wp_unslash( (string)$_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';

		return is_string( $path ) && preg_match( '#^/poptavka-konfigurace(?:/|$)#', $path ) === 1;

	}

}

if ( !function_exists( 'arctic_jucra_template_redirect' ) ) {

	/**
	 * Serve virtual Jucra pages even when WordPress pages have not been seeded.
	 *
	 * @return void
	 */
	function arctic_jucra_template_redirect(): void {

		$is_builder = arctic_jucra_is_builder_request();
		$is_inquiry = arctic_jucra_is_inquiry_request();

		if ( !$is_builder && !$is_inquiry ) {
			return;
		}

		global $wp_query;

		if ( $wp_query instanceof WP_Query ) {
			$wp_query->is_404      = false;
			$wp_query->is_page     = true;
			$wp_query->is_singular = true;
		}

		status_header( 200 );
		nocache_headers();
		include get_theme_file_path( $is_inquiry ? 'template-jucra-inquiry.php' : 'template-configurator.php' );
		exit;

	}

	add_action( 'template_redirect', 'arctic_jucra_template_redirect', 1 );

}

if ( !function_exists( 'arctic_jucra_document_title' ) ) {

	/**
	 * Use a real title for the virtual builder route instead of the 404 title.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	function arctic_jucra_document_title( string $title ): string {

		if ( arctic_jucra_is_builder_request() ) {
			return trim( sprintf( '3D konfigurator Arctic Spas - %s', get_bloginfo( 'name' ) ) );
		}

		if ( arctic_jucra_is_inquiry_request() ) {
			return trim( sprintf( 'Poptávka konfigurace - %s', get_bloginfo( 'name' ) ) );
		}

		return $title;

	}

	add_filter( 'pre_get_document_title', 'arctic_jucra_document_title', 20 );

}

if ( !function_exists( 'arctic_jucra_filter_sale_menu_items' ) ) {

	/**
	 * Keep the sale menu item homepage-only and rename it to the approved promo label.
	 *
	 * @param array    $sorted_menu_items
	 * @param stdClass $args
	 *
	 * @return array
	 */
	function arctic_jucra_filter_sale_menu_items( array $sorted_menu_items, stdClass $args ): array {

		if ( is_admin() ) {
			return $sorted_menu_items;
		}

		$is_root_homepage = function_exists( 'arctic_is_root_homepage_request' ) && arctic_is_root_homepage_request();

		foreach ( $sorted_menu_items as $index => $item ) {
			$title = isset( $item->title ) ? wp_strip_all_tags( (string)$item->title ) : '';
			$url   = isset( $item->url ) ? (string)$item->url : '';

			if ( preg_match( '#/swimspa/?#i', $url ) ) {
				$url = preg_replace( '/#serie-(core|classic|custom)$/i', '#serie-swimspa', $url );
			}

			if ( preg_match( '#/podpora/?#i', $url ) ) {
				$url = preg_replace( '/#servis$/i', '#servisni-formular', $url );
			}

			if ( $url !== ( isset( $item->url ) ? (string)$item->url : '' ) ) {
				$item->url                 = $url;
				$sorted_menu_items[$index] = $item;
			}

			$is_sale_item = strpos( strtolower( remove_accents( $title ) ), 'vyprodej' ) !== false || stripos( $url, '#vyprodej' ) !== false;
			if ( !$is_sale_item ) {
				continue;
			}

			if ( $is_root_homepage ) {
				$item->title               = 'Akční nabídka';
				$item->url                 = preg_replace( '/#vyprodej.*$/i', '', $url ) ?: home_url( '/virivky/' );
				$sorted_menu_items[$index] = $item;
			} else {
				unset( $sorted_menu_items[$index] );
			}
		}

		return array_values( $sorted_menu_items );

	}

	add_filter( 'wp_nav_menu_objects', 'arctic_jucra_filter_sale_menu_items', 20, 2 );

}
