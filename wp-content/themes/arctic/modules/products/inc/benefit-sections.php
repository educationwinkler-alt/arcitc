<?php

/**
 * Product benefit and optional equipment helpers.
 */

if ( !function_exists( 'arctic_product_fieldset_rows' ) ) {

	/**
	 * Flatten Meta Box fieldset_text rows.
	 *
	 * @param int    $product_id
	 * @param string $meta_key
	 *
	 * @return array
	 */
	function arctic_product_fieldset_rows( int $product_id, string $meta_key ): array {

		$rows = array();

		foreach ( get_post_meta( $product_id, $meta_key ) as $raw_row ) {
			if ( !is_array( $raw_row ) ) {
				continue;
			}

			if ( array_key_exists( 'title', $raw_row ) || array_key_exists( 'name', $raw_row ) ) {
				$rows[] = $raw_row;
				continue;
			}

			foreach ( $raw_row as $nested_row ) {
				if ( is_array( $nested_row ) ) {
					$rows[] = $nested_row;
				}
			}
		}

		return $rows;

	}

}

if ( !function_exists( 'arctic_product_truthy' ) ) {

	/**
	 * Normalize admin text flags.
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	function arctic_product_truthy( mixed $value ): bool {

		$value = strtolower( trim( (string) $value ) );

		return in_array( $value, array( '1', 'yes', 'true', 'ano', 'on' ), true );

	}

}

if ( !function_exists( 'arctic_product_primary_series' ) ) {

	/**
	 * Get the primary product series for copy fallbacks.
	 *
	 * @param int $product_id
	 *
	 * @return WP_Term|null
	 */
	function arctic_product_primary_series( int $product_id ): ?WP_Term {

		$terms = wp_get_post_terms( $product_id, 'product-series' );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return null;
		}

		$order = array( 'custom', 'classic', 'core', 'swimspa-custom', 'swimspa-classic', 'swimspa', 'covana' );

		usort( $terms, static function ( WP_Term $a, WP_Term $b ) use ( $order ): int {
			$a_position = array_search( $a->slug, $order, true );
			$b_position = array_search( $b->slug, $order, true );

			$a_position = false === $a_position ? 999 : $a_position;
			$b_position = false === $b_position ? 999 : $b_position;

			return $a_position <=> $b_position;
		} );

		return $terms[0];

	}

}

if ( !function_exists( 'arctic_product_benefit_fallback_copy' ) ) {

	/**
	 * Build product benefit fallback copy from product taxonomy.
	 *
	 * @param int $product_id
	 *
	 * @return array{heading:string,text:string}
	 */
	function arctic_product_benefit_fallback_copy( int $product_id ): array {

		$series     = arctic_product_primary_series( $product_id );
		$is_swimspa = has_term( 'swimspa', 'product-category', $product_id ) || has_term( array( 'swimspa-classic', 'swimspa-custom', 'swimspa' ), 'product-series', $product_id );

		if ( !$series instanceof WP_Term ) {
			return array(
				'heading' => $is_swimspa
					? __( 'Výhody celoročních bazénů Arctic Spas', 'baspa' )
					: __( 'Výhody vířivek Arctic Spas', 'baspa' ),
				'text'    => $is_swimspa
					? __( 'Celoroční bazény Arctic Spas jsou dodávány s výbavou podle zvolené řady, konfigurace a způsobu používání.', 'baspa' )
					: __( 'Vířivky Arctic Spas jsou dodávány s výbavou podle zvolené řady, konfigurace a způsobu používání.', 'baspa' ),
			);
		}

		$series_name           = $series->name;
		$is_arctic_pool_series = str_starts_with( $series_name, 'Bazény ARCTIC' );

		if ( $is_swimspa ) {
			return array(
				'heading' => $is_arctic_pool_series
					? sprintf( __( 'Výhody celoročních bazénů Arctic Spas - %s', 'baspa' ), $series_name )
					: sprintf( __( 'Výhody celoročních bazénů Arctic Spas - série %s', 'baspa' ), $series_name ),
				'text'    => $is_arctic_pool_series
					? sprintf( __( 'Celoroční bazény Arctic Spas z řady %s jsou dodávány s výbavou podle zvolené konfigurace a způsobu používání.', 'baspa' ), $series_name )
					: sprintf( __( 'Celoroční bazény Arctic Spas série %s jsou dodávány s výbavou podle zvolené konfigurace a způsobu používání.', 'baspa' ), $series_name ),
			);
		}

		return array(
			'heading' => sprintf( __( 'Výhody vířivek Arctic Spas - série %s', 'baspa' ), $series_name ),
			'text'    => sprintf( __( 'Vířivky Arctic Spas série %s jsou dodávány s výbavou podle zvolené konfigurace a způsobu používání.', 'baspa' ), $series_name ),
		);

	}

}

if ( !function_exists( 'arctic_product_default_benefits' ) ) {

	/**
	 * Default Figma benefit cards.
	 *
	 * @return array
	 */
	function arctic_product_default_benefits(): array {

		return array(
			array(
				'title'          => __( 'Samonosná kompozitní skořepina', 'baspa' ),
				'summary'        => __( 'Prémiový akrylát Aristech, ručně nanášený sklolaminát a pevný kompozitní základ bez podpůrné pěny.', 'baspa' ),
				'icon'           => 'shell',
				'interactive'    => true,
				'popup_title'    => __( 'Samonosná kompozitní skořepina s doživotní zárukou', 'baspa' ),
				'popup_content'  => '<p>' . esc_html__( 'Skořepina vířivky Arctic Spas je samotným základem naší konstrukce a proto k její výrobě přistupujeme s vědomím zásadní důležitosti její funkce.', 'baspa' ) . '</p><p>' . esc_html__( 'Začínáme vakuovým formováním vrstvy prémiového akrylátu Aristech s antibakteriální úpravou Bio-Lok™. Poté ručně nanášíme sklolaminátový kompozit vrstvu po vrstvě, aby skořepina získala svou legendární pevnost.', 'baspa' ) . '</p><p>' . esc_html__( 'Používáme nejsilnější vrstvu sklolaminátu v oboru a nehledáme žádné úsporné zkratky. Výsledkem je pevný a přesný základ pro instalaci pokročilých technologií Arctic Spas®.', 'baspa' ) . '</p>',
				'popup_image_url' => content_url( 'uploads/import/figma/popup-shell-detail.png' ),
			),
			array( 'title' => __( 'Izolace Heatlock', 'baspa' ), 'icon' => 'heatlock' ),
			array( 'title' => __( 'Cedrový kabinet', 'baspa' ), 'icon' => 'cabinet' ),
			array( 'title' => __( 'Podlaha vířivky', 'baspa' ), 'icon' => 'floor' ),
			array( 'title' => __( 'Termokryt', 'baspa' ), 'icon' => 'cover' ),
			array( 'title' => __( 'Servisní přístup', 'baspa' ), 'icon' => 'service' ),
			array( 'title' => __( 'Variabilita sedadel', 'baspa' ), 'icon' => 'seats' ),
			array( 'title' => __( 'Automatická úprava vody', 'baspa' ), 'icon' => 'water' ),
			array( 'title' => __( 'Ovládání Gecko', 'baspa' ), 'icon' => 'control' ),
			array( 'title' => __( 'Filtrace', 'baspa' ), 'icon' => 'filter' ),
			array( 'title' => __( 'Masážní trysky', 'baspa' ), 'icon' => 'jets' ),
			array( 'title' => __( 'LED osvětlení', 'baspa' ), 'icon' => 'led' ),
			array( 'title' => __( 'Aromaterapie', 'baspa' ), 'icon' => 'aroma' ),
			array( 'title' => __( 'Hudební systém', 'baspa' ), 'icon' => 'audio' ),
			array( 'title' => __( 'Wi-Fi ovládání', 'baspa' ), 'icon' => 'wifi' ),
			array( 'title' => __( 'Záruka na skořepinu', 'baspa' ), 'icon' => 'warranty' ),
			array( 'title' => __( 'Nerezové trysky', 'baspa' ), 'icon' => 'steel' ),
			array( 'title' => __( 'Celoroční provoz', 'baspa' ), 'icon' => 'winter' ),
		);

	}

}

if ( !function_exists( 'arctic_product_default_options' ) ) {

	/**
	 * Default Figma optional equipment cards.
	 *
	 * @return array
	 */
	function arctic_product_default_options(): array {

		return array(
			array( 'title' => 'Onzen úprava vody', 'icon' => 'onzen' ),
			array( 'title' => 'Spa Boy monitoring', 'icon' => 'spa-boy' ),
			array( 'title' => 'Wi-Fi ovládání', 'icon' => 'wifi' ),
			array( 'title' => 'LED osvětlení', 'icon' => 'led' ),
			array( 'title' => 'Bluetooth audio', 'icon' => 'audio' ),
			array( 'title' => 'Termokryt', 'icon' => 'cover' ),
			array( 'title' => 'Schůdky a madla', 'icon' => 'steps' ),
			array( 'title' => 'Covana kryt', 'icon' => 'covana' ),
		);

	}

}

if ( !function_exists( 'arctic_product_normalize_benefit_cards' ) ) {

	/**
	 * Normalize product admin rows into frontend benefit cards.
	 *
	 * @param int    $product_id
	 * @param string $items_key
	 * @param string $images_key
	 * @param string $popup_images_key
	 * @param array  $fallback_cards
	 * @param string $fallback_summary
	 *
	 * @return array
	 */
	function arctic_product_normalize_benefit_cards( int $product_id, string $items_key, string $images_key, string $popup_images_key, array $fallback_cards, string $fallback_summary ): array {

		$rows          = arctic_product_fieldset_rows( $product_id, $items_key );
		$has_rows      = !empty( $rows );
		$allow_seed    = function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks();
		$image_ids     = array_values( array_filter( array_map( 'absint', get_post_meta( $product_id, $images_key ) ) ) );
		$popup_ids     = array_values( array_filter( array_map( 'absint', get_post_meta( $product_id, $popup_images_key ) ) ) );
		$source        = !$has_rows ? 'static-fallback' : $items_key;
		$cards         = array();
		$base_cards    = !$has_rows ? $fallback_cards : $rows;

		if ( !$has_rows && !$allow_seed ) {
			return array();
		}

		foreach ( array_values( $base_cards ) as $index => $row ) {
			if ( !is_array( $row ) ) {
				continue;
			}

			$title = trim( wp_strip_all_tags( (string) ( $row['title'] ?? $row['name'] ?? '' ) ) );

			if ( '' === $title ) {
				continue;
			}

			$summary        = trim( wp_strip_all_tags( (string) ( $row['summary'] ?? $row['text'] ?? '' ) ) );
			$icon           = sanitize_title( (string) ( $row['icon'] ?? 'feature' ) );
			$interactive    = !empty( $row['interactive'] ) && ( true === $row['interactive'] || arctic_product_truthy( $row['interactive'] ) );
			$image_id       = (int) ( $image_ids[ $index ] ?? 0 );
			$popup_image_id = (int) ( $popup_ids[ $index ] ?? 0 );
			$media_index    = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
			$media_url      = '';
			$media_status   = 'admin-empty';
			$media_source   = '';
			$popup_image    = '';
			$popup_status   = 'admin-empty';

			if ( $image_id ) {
				$media_url    = (string) wp_get_attachment_image_url( $image_id, 'full' );
				$media_status = 'admin-product-benefit';
				$media_source = 'product-media';
			}

			if ( $popup_image_id ) {
				$popup_image  = (string) wp_get_attachment_image_url( $popup_image_id, 'full' );
				$popup_status = 'admin-product-benefit';
			}

			$cards[] = array(
				'title'              => $title,
				'summary'            => '' !== $summary ? $summary : $fallback_summary,
				'icon'               => '' !== $icon ? $icon : 'feature',
				'interactive'        => $interactive,
				'media_url'          => $media_url,
				'media_status'       => $media_status,
				'media_source'       => $media_source,
				'figma_node'         => '1:' . ( 1500 + ( $index * 10 ) ),
				'popup_id'           => 'benefit-' . sanitize_title( (string) ( $row['icon'] ?? $title ) ),
				'popup_title'        => trim( wp_strip_all_tags( (string) ( $row['popup_title'] ?? $title ) ) ),
				'popup_content'      => trim( (string) ( $row['popup_content'] ?? '' ) ),
				'popup_image_url'    => $popup_image,
				'popup_image_status' => $popup_status,
				'source'             => $source,
			);
		}

		return $cards;

	}

}

if ( !function_exists( 'arctic_product_get_benefit_section' ) ) {

	/**
	 * Get product benefits section data.
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	function arctic_product_get_benefit_section( int $product_id ): array {

		$heading = trim( wp_strip_all_tags( (string) get_post_meta( $product_id, 'product_benefits_heading', true ) ) );
		$text    = trim( wp_strip_all_tags( (string) get_post_meta( $product_id, 'product_benefits_description', true ) ) );
		$fallback_copy = arctic_product_benefit_fallback_copy( $product_id );

		return array(
			'heading' => '' !== $heading ? $heading : $fallback_copy['heading'],
			'text'    => '' !== $text ? $text : $fallback_copy['text'],
			'cards'   => arctic_product_normalize_benefit_cards(
				$product_id,
				'product_benefit_items',
				'product_benefit_images',
				'product_benefit_popup_images',
				arctic_product_default_benefits(),
				__( 'Návrh vychází z požadavků na provoz v chladném klimatu, jednoduchou údržbu a stabilní výkon po mnoho sezon.', 'baspa' )
			),
		);

	}

}

if ( !function_exists( 'arctic_product_get_option_section' ) ) {

	/**
	 * Get product options section data.
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	function arctic_product_get_option_section( int $product_id ): array {

		$heading = trim( wp_strip_all_tags( (string) get_post_meta( $product_id, 'product_options_heading', true ) ) );
		$text    = trim( wp_strip_all_tags( (string) get_post_meta( $product_id, 'product_options_description', true ) ) );

		return array(
			'heading' => '' !== $heading ? $heading : __( 'Volitelná výbava', 'baspa' ),
			'text'    => '' !== $text ? $text : __( 'Doplňky a technologie se vybírají podle modelu, konfigurace a způsobu používání vířivky.', 'baspa' ),
			'cards'   => arctic_product_normalize_benefit_cards(
				$product_id,
				'product_option_items',
				'product_option_images',
				'product_option_popup_images',
				arctic_product_default_options(),
				__( 'Konkrétní dostupnost a doporučenou kombinaci potvrdíme v nabídce pro vybraný model.', 'baspa' )
			),
		);

	}

}
