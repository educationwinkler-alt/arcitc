<?php

/**
 * Figma hero promo banner.
 */

?>

<aside class="f-hero-promo" aria-label="<?php echo esc_attr__( 'Výprodej skladových vířivek', 'baspa' ); ?>">
	<img class="f-hero-promo__image"
	     src="<?php echo esc_url( content_url( 'uploads/import/figma/hp-fixed-banner-product.png' ) ); ?>"
	     alt="" loading="eager" decoding="async">
	<span class="f-hero-promo__bubble" aria-hidden="true"></span>
	<strong><?php echo esc_html__( 'Výprodej skladových vířivek', 'baspa' ); ?></strong>
	<a class="f-hero-promo__button" href="<?php echo esc_url( home_url( '/catalog/virivky/' ) ); ?>">
		<?php echo esc_html__( 'Zobrazit nabídku', 'baspa' ); ?>
	</a>
</aside>
