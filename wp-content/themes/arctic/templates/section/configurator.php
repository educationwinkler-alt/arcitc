<?php

/**
 * Arctic Configurator CTA
 */

$hot_tubs_url = get_term_link( 'virivky', 'product-category' );
$hot_tubs_url = is_wp_error( $hot_tubs_url ) ? home_url( '/virivky/' ) : $hot_tubs_url;

$fallback_url_setting = (string) get_theme_mod( 'arctic_configurator_fallback_url', '/virivky/' );
$fallback_url         = function_exists( 'arctic_sections_url' ) ? arctic_sections_url( $fallback_url_setting, '/virivky/' ) : $hot_tubs_url;
$pricing_url = function_exists( 'arctic_jucra_get_pricing_url' ) ? arctic_jucra_get_pricing_url( '/kontakt/' ) : home_url( '/kontakt/' );
$model_name  = function_exists( 'arctic_jucra_get_default_model' ) ? arctic_jucra_get_default_model() : '';
$show_viewer = function_exists( 'arctic_jucra_can_render_viewer' ) && arctic_jucra_can_render_viewer( $model_name );
$shortcode   = $show_viewer && function_exists( 'arctic_jucra_build_shortcode' ) ? arctic_jucra_build_shortcode( $model_name ) : '';

$title       = get_theme_mod( 'arctic_configurator_title', 'Nakonfigurujte si vlastni virivku' );
$text        = get_theme_mod( 'arctic_configurator_text', 'Vyberte si model, vybavu a barvy. Pripravime vam konkretni doporuceni i cenovou nabidku.' );
$button_text = get_theme_mod( 'arctic_configurator_button_text', 'Konfigurovat' );
$button_url  = $show_viewer ? $pricing_url : $fallback_url;

$configurator_image = content_url( 'uploads/import/figma/category-configurator.png' );
?>

<section id="konfigurator" class="f-section f-section--configurator">
	<div class="f-section__container a-container">
		<div class="f-configurator-cta">
			<div class="f-configurator-cta__content">
				<h2><?php echo esc_html( $title ); ?></h2>
				<p><?php echo wp_kses_post( $text ); ?></p>
				<a class="f-button a-button a-button--outline" href="<?php echo esc_url( $button_url ); ?>">
					<?php echo esc_html( $button_text ); ?>
				</a>
			</div>

			<div class="f-configurator-cta__visual"<?php echo $show_viewer ? '' : ' aria-hidden="true"'; ?>>
				<?php if ( $show_viewer && $shortcode !== '' ) { ?>
					<div class="f-configurator-cta__viewer" data-jucra-model="<?php echo esc_attr( $model_name ); ?>">
						<?php echo do_shortcode( $shortcode ); ?>
					</div>
				<?php } else { ?>
					<img class="f-configurator-cta__image" src="<?php echo esc_url( $configurator_image ); ?>" width="667" height="312" alt="" loading="lazy" decoding="async">
				<?php } ?>
			</div>
		</div>
	</div>
</section>
