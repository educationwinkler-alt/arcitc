<?php

/**
 * Figma hero promo banner.
 */

$promo_enabled = (bool) get_theme_mod( 'arctic_home_promo_enabled', 1 );

if ( !$promo_enabled || !function_exists( 'arctic_is_root_homepage_request' ) || !arctic_is_root_homepage_request() ) {
	return;
}

$promo_offer       = function_exists( 'baspa_offers_promo_data' ) ? baspa_offers_promo_data() : array();
$promo_offer_id    = !empty( $promo_offer[ 'id' ] ) ? (int) $promo_offer[ 'id' ] : 0;
$promo_image_id    = !empty( $promo_offer[ 'promo_image_id' ] ) ? (int) $promo_offer[ 'promo_image_id' ] : 0;
$promo_title       = !empty( $promo_offer[ 'short_title' ] )
	? (string) $promo_offer[ 'short_title' ]
	: ( function_exists( 'arctic_sections_get_theme_mod' )
	? arctic_sections_get_theme_mod( 'arctic_home_promo_title', 'Akční nabídky' )
	: get_theme_mod( 'arctic_home_promo_title', 'Akční nabídky' ) );
$promo_default_url = function_exists( 'baspa_offers_archive_url' ) ? baspa_offers_archive_url() : home_url( '/akcni-nabidky/' );
$promo_button_text = !empty( $promo_offer[ 'button_text' ] ) ? (string) $promo_offer[ 'button_text' ] : get_theme_mod( 'arctic_home_promo_button_text', 'Zobrazit nabídku' );
$promo_button_url  = !empty( $promo_offer[ 'archive_url' ] )
	? (string) $promo_offer[ 'archive_url' ]
	: ( function_exists( 'arctic_sections_url' ) ? arctic_sections_url( (string) get_theme_mod( 'arctic_home_promo_url', '/akcni-nabidky/' ), $promo_default_url ) : $promo_default_url );
$promo_mark = trim( wp_strip_all_tags( $promo_title ) );
$promo_mark = '' !== $promo_mark ? ( function_exists( 'mb_substr' ) ? mb_substr( $promo_mark, 0, 1 ) : substr( $promo_mark, 0, 1 ) ) : '';
$promo_mark = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $promo_mark ) : strtoupper( $promo_mark );

?>

<aside class="f-hero-promo"
       aria-label="<?php echo esc_attr( $promo_title ); ?>"
       data-content-source="<?php echo esc_attr( $promo_offer_id > 0 ? 'offer-cpt' : 'theme-mod' ); ?>"
       data-offer-id="<?php echo esc_attr( (string) $promo_offer_id ); ?>">
	<?php if ( $promo_image_id > 0 ) {
		echo wp_get_attachment_image( $promo_image_id, 'medium', false, array(
			'class'             => 'f-hero-promo__image',
			'alt'               => '',
			'loading'           => 'eager',
			'decoding'          => 'async',
			'data-asset-status' => 'admin-offer-promo',
		) );
	} else { ?>
		<span class="f-hero-promo__image f-hero-promo__placeholder" data-asset-status="admin-empty" aria-hidden="true">
			<?php echo esc_html( $promo_mark ); ?>
		</span>
	<?php } ?>
	<span class="f-hero-promo__bubble" aria-hidden="true"></span>
	<strong><?php echo esc_html( $promo_title ); ?></strong>
	<a class="f-hero-promo__button" href="<?php echo esc_url( $promo_button_url ); ?>">
		<?php echo esc_html( $promo_button_text ); ?>
	</a>
</aside>
