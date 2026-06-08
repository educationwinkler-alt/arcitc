<?php

/**
 * Catalog Request Section
 */

$environment = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';

if ( 'local' !== $environment ) {
	return;
}

$image_url = content_url( 'uploads/import/legacy-services/service-catalog.jpg' );
$title     = html_entity_decode( 'Kompletn&#237; katalog s cen&#237;kem produkt&#367;', ENT_QUOTES, 'UTF-8' );
$text      = html_entity_decode( 'Zadejte e-mail a po&#353;leme v&#225;m odkaz na aktu&#225;ln&#237; katalog a orienta&#269;n&#237; ceny. U konkr&#233;tn&#237;ho modelu v&#225;m potom p&#345;iprav&#237;me p&#345;esnou nab&#237;dku podle konfigurace.', ENT_QUOTES, 'UTF-8' );
?>

<section id="<?php echo sanitize_title( esc_attr_x( 'catalog-price-list', 'anchor', 'baspa' ) ); ?>"
         class="f-section f-section--catalog f-section--catalog-request">
	<div class="f-section__container a-container">
		<div class="f-catalog-request">
			<div class="f-catalog-request__content">
				<header class="f-section__header">
					<h2><?php echo esc_html( $title ); ?></h2>
					<p><?php echo esc_html( $text ); ?></p>
				</header>

				<div class="f-section__form">
					<?php get_template_part( 'modules/contacts/templates/form', 'catalog' ); ?>
				</div>
			</div>

			<div class="f-catalog-request__media" aria-hidden="true">
				<figure>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="">
				</figure>
			</div>
		</div>
	</div>
</section>
