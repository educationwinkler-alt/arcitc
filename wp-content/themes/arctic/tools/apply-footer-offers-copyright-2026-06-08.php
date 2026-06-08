<?php
/**
 * Apply customer-requested footer/offers/copyright data fixes.
 *
 * Run after a database backup:
 * wp eval-file wp-content/themes/arctic/tools/apply-footer-offers-copyright-2026-06-08.php
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

$summary = array(
	'theme_mods' => array(),
	'options' => array(),
	'menu_items' => array(),
	'offers' => array(),
);

$contains = static function ( string $haystack, string $needle ): bool {
	if ( '' === $needle ) {
		return true;
	}

	if ( function_exists( 'mb_stripos' ) ) {
		return false !== mb_stripos( $haystack, $needle );
	}

	return false !== stripos( $haystack, $needle );
};

$year = function_exists( 'date_i18n' ) ? date_i18n( 'Y' ) : gmdate( 'Y' );

$copyright = sprintf( 'Copyright © %s BASPA s.r.o. Všechna práva vyhrazena.', $year );
set_theme_mod( 'baspa_copyright', $copyright );
set_theme_mod( 'arctic_home_promo_title', 'Akční nabídky' );

$summary['theme_mods']['baspa_copyright'] = $copyright;
$summary['theme_mods']['arctic_home_promo_title'] = 'Akční nabídky';

$interest_option_lines = preg_split( '/\r\n|\r|\n/', (string) get_option( 'baspa_contacts_interest_options', '' ) );
$interest_options = array();

foreach ( $interest_option_lines ?: array() as $line ) {
	$line = trim( (string) $line );

	if ( '' === $line || false === strpos( $line, '|' ) ) {
		continue;
	}

	list( $value, $label ) = array_map( 'trim', explode( '|', $line, 2 ) );

	if ( '' !== $value && '' !== $label ) {
		$interest_options[ $value ] = $label;
	}
}

$interest_options['offer'] = 'Akční nabídka';

$interest_option_value = implode( "\n", array_map(
	static fn( string $value, string $label ): string => $value . '|' . $label,
	array_keys( $interest_options ),
	$interest_options
) );

update_option( 'baspa_contacts_interest_options', $interest_option_value );
$summary['options']['baspa_contacts_interest_options'] = $interest_option_value;

$locations = get_nav_menu_locations();
$footer_menu_id = !empty( $locations['navigation_footer'] ) ? (int) $locations['navigation_footer'] : 0;

if ( $footer_menu_id > 0 ) {
	$items = wp_get_nav_menu_items( $footer_menu_id, array(
		'update_post_term_cache' => false,
	) );

	$info_parent_id = 0;
	$service_position = 0;

	foreach ( $items ?: array() as $item ) {
		if ( 0 === (int) $item->menu_item_parent && 'Další informace' === (string) $item->title ) {
			$info_parent_id = (int) $item->ID;
		}

		if ( 'Servis' === (string) $item->title ) {
			$service_position = (int) $item->menu_order;
		}
	}

	foreach ( $items ?: array() as $item ) {
		$title = (string) $item->title;
		$url = (string) $item->url;
		$is_old_stock_link = $contains( $title, 'Skladové vířivky' );
		$is_offers_link = false !== strpos( $url, '/akcni-nabidky/' );

		if ( !$is_old_stock_link && !$is_offers_link ) {
			continue;
		}

		$args = array(
			'menu-item-title' => 'Akční nabídky',
			'menu-item-url' => home_url( '/akcni-nabidky/' ),
			'menu-item-status' => 'publish',
			'menu-item-type' => 'custom',
			'menu-item-parent-id' => $info_parent_id > 0 ? $info_parent_id : (int) $item->menu_item_parent,
		);

		if ( $service_position > 0 ) {
			$args['menu-item-position'] = max( 1, $service_position - 1 );
		}

		$updated_id = wp_update_nav_menu_item( $footer_menu_id, (int) $item->ID, $args );

		if ( is_wp_error( $updated_id ) ) {
			throw new RuntimeException( $updated_id->get_error_message() );
		}

		$summary['menu_items'][] = array(
			'id' => (int) $updated_id,
			'title' => 'Akční nabídky',
			'parent_id' => $args['menu-item-parent-id'],
		);
	}
}

$offers = get_posts( array(
	'post_type' => 'offer',
	'post_status' => array( 'publish', 'draft', 'private' ),
	'posts_per_page' => -1,
	'fields' => 'ids',
) );

foreach ( $offers as $offer_id ) {
	$offer_id = (int) $offer_id;
	$seed_key = (string) get_post_meta( $offer_id, '_arctic_seed_key', true );
	$title = get_the_title( $offer_id );
	$short_title = (string) get_post_meta( $offer_id, 'offer_title_short', true );
	$offer_title = (string) get_post_meta( $offer_id, 'offer_title', true );

	$should_update = 'offer-stock-hot-tubs' === $seed_key
		|| $contains( $title, 'Výprodej skladových' )
		|| $contains( $short_title, 'Výprodej skladových' )
		|| $contains( $offer_title, 'Výprodej skladových' );

	if ( !$should_update ) {
		continue;
	}

	$result = wp_update_post( array(
		'ID' => $offer_id,
		'post_title' => 'Akční nabídky Arctic Spas',
		'post_name' => 'akcni-nabidky-arctic-spas',
	), true );

	if ( is_wp_error( $result ) ) {
		throw new RuntimeException( $result->get_error_message() );
	}

	update_post_meta( $offer_id, 'offer_type_custom', 'Akční nabídky' );
	update_post_meta( $offer_id, 'offer_title_short', 'Akční nabídky' );
	update_post_meta( $offer_id, 'offer_title', 'Akční nabídky Arctic Spas' );

	$summary['offers'][] = array(
		'id' => $offer_id,
		'title' => 'Akční nabídky Arctic Spas',
		'short_title' => 'Akční nabídky',
	);
}

if ( function_exists( 'flush_rewrite_rules' ) ) {
	flush_rewrite_rules( false );
}

echo wp_json_encode( $summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . PHP_EOL;
