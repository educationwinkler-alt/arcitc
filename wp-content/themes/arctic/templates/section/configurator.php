<?php

/**
 * Arctic Configurator CTA
 */

$hot_tubs_url = get_term_link( 'virivky', 'product-category' );
$hot_tubs_url = is_wp_error( $hot_tubs_url ) ? home_url( '/catalog/virivky/' ) : $hot_tubs_url;

$configurator_image = get_posts( array(
	'post_type'      => 'attachment',
	'post_status'    => 'inherit',
	'posts_per_page' => 1,
	'fields'         => 'ids',
	'meta_key'       => '_arctic_seed_key',
	'meta_value'     => 'figma-node-1-409-category-configurator',
) );
?>

<section class="f-section f-section--configurator">
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
				<?php if ( !empty( $configurator_image ) ) { ?>
					<?php echo wp_get_attachment_image( (int) $configurator_image[0], 'large', false, array(
						'class' => 'f-configurator-cta__image',
					) ); ?>
				<?php } else { ?>
					<div class="f-configurator-cta__screen"></div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
