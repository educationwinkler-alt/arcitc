<?php

/**
 * Figma hero promo banner.
 */

if ( !function_exists( 'arctic_is_root_homepage_request' ) || !arctic_is_root_homepage_request() ) {
	return;
}

?>

<aside class="f-hero-promo" aria-label="<?php echo esc_attr__( 'Akcni nabidka skladovych virivek', 'baspa' ); ?>">
	<img class="f-hero-promo__image"
		src="<?php echo esc_url( content_url( 'uploads/import/figma/hp-fixed-banner-product.png' ) ); ?>"
		alt="" loading="eager" decoding="async">
	<span class="f-hero-promo__bubble" aria-hidden="true"></span>
	<strong><?php echo esc_html__( 'Akcni nabidka skladovych virivek', 'baspa' ); ?></strong>
	<a class="f-hero-promo__button" href="<?php echo esc_url( home_url( '/virivky/' ) ); ?>">
		<?php echo esc_html__( 'Zobrazit nabidku', 'baspa' ); ?>
	</a>
</aside>
