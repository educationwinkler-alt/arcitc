<?php

/**
 * Timberwolf Figma detail body.
 */

$product_id             = get_the_ID();
$configurator_image_url = content_url( 'uploads/import/figma/category-configurator.png' );
$model_name             = function_exists( 'arctic_jucra_get_product_model_name' ) ? arctic_jucra_get_product_model_name( $product_id ) : '';
$builder_url            = function_exists( 'arctic_jucra_get_builder_url' ) ? arctic_jucra_get_builder_url( $model_name ) : home_url( '/konfigurator/' );
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
			<div class="f-configurator-cta f-configurator-cta--shared f-configurator-cta--product">
				<div class="f-configurator-cta__content">
					<h2 id="product-configurator-title"><?php echo esc_html__( 'Nakonfigurujte si vlastní víøivku', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Vyberte si konfiguraci, barvu skoøepiny, kabinet a další výbavu podle vlastních pøedstav.', 'baspa' ); ?></p>
					<a class="f-button a-button a-button--accent" href="<?php echo esc_url( $builder_url ); ?>">
						<?php echo esc_html__( 'Konfigurovat', 'baspa' ); ?>
					</a>
				</div>

				<div class="f-configurator-cta__visual" style="--configurator-image: url('<?php echo esc_url( $configurator_image_url ); ?>');" aria-hidden="true">
					<img class="f-configurator-cta__image" src="<?php echo esc_url( $configurator_image_url ); ?>" alt="">
				</div>
			</div>
		</section>
	</div>
</section>
