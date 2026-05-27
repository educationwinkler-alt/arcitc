<?php

/**
 * Figma hero promo banner.
 */

$promo_enabled = (bool) get_theme_mod( 'arctic_home_promo_enabled', 1 );

if ( !$promo_enabled || !function_exists( 'arctic_is_root_homepage_request' ) || !arctic_is_root_homepage_request() ) {
	return;
}

$promo_title       = get_theme_mod( 'arctic_home_promo_title', 'Akční nabídka skladových vířivek' );
$promo_button_text = get_theme_mod( 'arctic_home_promo_button_text', 'Zobrazit nabídku' );
$promo_button_url  = function_exists( 'arctic_sections_url' ) ? arctic_sections_url( (string) get_theme_mod( 'arctic_home_promo_url', '/virivky/' ), '/virivky/' ) : home_url( '/virivky/' );

?>

<aside class="f-hero-promo" aria-label="<?php echo esc_attr( $promo_title ); ?>">
	<img class="f-hero-promo__image"
		src="<?php echo esc_url( content_url( 'uploads/import/figma/hp-fixed-banner-product.png' ) ); ?>"
		alt="" loading="eager" decoding="async">
	<span class="f-hero-promo__bubble" aria-hidden="true"></span>
	<strong><?php echo esc_html( $promo_title ); ?></strong>
	<a class="f-hero-promo__button" href="<?php echo esc_url( $promo_button_url ); ?>">
		<?php echo esc_html( $promo_button_text ); ?>
	</a>
</aside>
