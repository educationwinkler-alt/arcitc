<?php

/**
 * Timberwolf Figma detail body.
 */

$product_id             = get_the_ID();
$configurator_image_url = content_url( 'uploads/import/figma/category-configurator.png' );
$model_name             = function_exists( 'arctic_jucra_get_product_model_name' ) ? arctic_jucra_get_product_model_name( $product_id ) : '';
$show_viewer            = function_exists( 'arctic_jucra_can_render_viewer' ) && arctic_jucra_can_render_viewer( $model_name );
$shortcode              = $show_viewer && function_exists( 'arctic_jucra_build_shortcode' ) ? arctic_jucra_build_shortcode( $model_name ) : '';
$pricing_url            = function_exists( 'arctic_jucra_get_pricing_url' ) ? arctic_jucra_get_pricing_url( '/kontakt/' ) : home_url( '/kontakt/' );
?>

<section class="f-section f-section--single f-section--description f-section--product-detail-config">
	<div class="f-section__container a-container">
		<div class="f-product-detail-config__layout">
			<div class="f-product-detail-config__main">
				<?php get_template_part( 'modules/products/templates/post/single/configurations' ); ?>
			</div>

			<div class="f-product-detail-config__sidebar">
				<?php get_template_part( 'modules/products/templates/post/single/sidebar' ); ?>
			</div>
		</div>

		<section class="f-product-detail-configurator" aria-labelledby="product-configurator-title">
			<div class="f-configurator-cta">
				<div class="f-configurator-cta__content">
					<h2 id="product-configurator-title"><?php echo esc_html__( 'Nakonfigurujte si vlastní vířivku', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Vyberte si konfiguraci, barvu skořepiny, kabinet a další výbavu podle vlastních představ.', 'baspa' ); ?></p>
					<a class="f-button a-button a-button--accent" href="<?php echo esc_url( $pricing_url ); ?>">
						<?php echo esc_html__( 'Konfigurovat', 'baspa' ); ?>
					</a>
				</div>

				<div class="f-configurator-cta__visual"<?php echo $show_viewer ? '' : ' aria-hidden="true"'; ?>>
					<?php if ( $show_viewer && $shortcode !== '' ) { ?>
						<div class="f-configurator-cta__viewer" data-jucra-model="<?php echo esc_attr( $model_name ); ?>">
							<?php echo do_shortcode( $shortcode ); ?>
						</div>
					<?php } else { ?>
						<img class="f-configurator-cta__image" src="<?php echo esc_url( $configurator_image_url ); ?>" alt="">
					<?php } ?>
				</div>
			</div>
		</section>
	</div>
</section>
