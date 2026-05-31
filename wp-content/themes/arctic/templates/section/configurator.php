<?php

/**
 * Arctic Configurator CTA
 */

if ( is_tax( 'product-category' ) && !is_tax( 'product-category', 'virivky' ) ) {
	return;
}

$builder_url = function_exists( 'arctic_jucra_get_builder_url' ) ? arctic_jucra_get_builder_url() : home_url( '/konfigurator/' );

$title = function_exists( 'arctic_sections_get_theme_mod' )
	? arctic_sections_get_theme_mod( 'arctic_configurator_title', __( 'Nakonfigurujte si vlastní vířivku', 'baspa' ) )
	: get_theme_mod( 'arctic_configurator_title', __( 'Nakonfigurujte si vlastní vířivku', 'baspa' ) );
$text = function_exists( 'arctic_sections_get_theme_mod' )
	? arctic_sections_get_theme_mod( 'arctic_configurator_text', __( 'Vyberte si model, výbavu a barvy. Připravíme vám konkrétní doporučení i cenovou nabídku.', 'baspa' ) )
	: get_theme_mod( 'arctic_configurator_text', __( 'Vyberte si model, výbavu a barvy. Připravíme vám konkrétní doporučení i cenovou nabídku.', 'baspa' ) );
$button_text = get_theme_mod( 'arctic_configurator_button_text', __( 'Konfigurovat', 'baspa' ) );

$configurator_image = content_url( 'uploads/import/figma/category-configurator.png' );
?>

<section id="konfigurator" class="f-section f-section--configurator" data-configurator-scope="hot-tub">
	<div class="f-section__container a-container">
		<div class="f-configurator-cta f-configurator-cta--shared f-configurator-cta--hot-tub">
			<div class="f-configurator-cta__content">
				<h2><?php echo esc_html( $title ); ?></h2>
				<p><?php echo wp_kses_post( $text ); ?></p>
				<a class="f-button a-button a-button--outline" href="<?php echo esc_url( $builder_url ); ?>">
					<?php echo esc_html( $button_text ); ?>
				</a>
			</div>

			<div class="f-configurator-cta__visual" style="--configurator-image: url('<?php echo esc_url( $configurator_image ); ?>');" aria-hidden="true">
				<img class="f-configurator-cta__image" src="<?php echo esc_url( $configurator_image ); ?>" width="667" height="312" alt="" loading="eager" fetchpriority="high" decoding="async">
			</div>
		</div>
	</div>
</section>
