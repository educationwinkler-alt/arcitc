<?php

/**
 * Arctic Configurator CTA
 */

$hot_tubs_url = get_term_link( 'virivky', 'product-category' );
$hot_tubs_url = is_wp_error( $hot_tubs_url ) ? home_url( '/catalog/virivky/' ) : $hot_tubs_url;

$configurator_image = content_url( 'uploads/import/figma/category-configurator.png' );
?>

<section id="konfigurator" class="f-section f-section--configurator">
	<div class="f-section__container a-container">
		<div class="f-configurator-cta">
			<div class="f-configurator-cta__content">
				<h2><?php echo esc_html__( 'Nakonfigurujte si vlastní vířivku', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Vyberte si model, výbavu a barvy. Připravíme vám konkrétní doporučení i cenovou nabídku.', 'baspa' ); ?></p>
				<a class="f-button a-button a-button--outline" href="<?php echo esc_url( $hot_tubs_url ); ?>">
					<?php echo esc_html__( 'Konfigurovat', 'baspa' ); ?>
				</a>
			</div>
			<div class="f-configurator-cta__visual" aria-hidden="true">
				<img class="f-configurator-cta__image" src="<?php echo esc_url( $configurator_image ); ?>" width="667" height="312" alt="" loading="lazy" decoding="async">
			</div>
		</div>
	</div>
</section>
