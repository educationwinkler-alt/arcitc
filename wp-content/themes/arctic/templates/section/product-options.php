<?php

/**
 * Product Optional Equipment
 */

$product_id = get_the_ID();
$section    = function_exists( 'arctic_product_get_option_section' )
	? arctic_product_get_option_section( $product_id )
	: array(
		'heading' => __( 'Volitelná výbava', 'baspa' ),
		'text'    => __( 'Doplňky a technologie se vybírají podle modelu, konfigurace a způsobu používání vířivky.', 'baspa' ),
		'cards'   => array(),
	);
$options    = $section['cards'] ?? array();
?>

<?php if ( !empty( $options ) ) { ?>
<section id="volitelna-vybava" class="f-section f-section--product-options js-links__section" data-content-source="<?php echo esc_attr( (string) ( $options[0]['source'] ?? 'static-fallback' ) ); ?>">
	<div class="f-section__container a-container">
		<header class="f-section__header">
			<h2><?php echo esc_html( (string) $section['heading'] ); ?></h2>
			<p><?php echo esc_html( (string) $section['text'] ); ?></p>
		</header>

		<div class="f-product-benefits f-product-benefits--options">
			<?php foreach ( $options as $index => $option ) {
				$icon         = (string) ( $option['icon'] ?? 'option' );
				$media_status = (string) ( $option['media_status'] ?? 'WAITING_ON_FIGMA_EXPORT' );
				$media_url    = (string) ( $option['media_url'] ?? '' );
				?>
				<article class="f-product-benefit f-product-benefit--static f-product-benefit--media-<?php echo esc_attr( $icon ); ?>" data-content-source="<?php echo esc_attr( (string) ( $option['source'] ?? 'static-fallback' ) ); ?>">
					<?php if ( '' !== $media_url ) { ?>
						<span class="f-product-benefit__media f-product-benefit__media--<?php echo esc_attr( $icon ); ?>"
							data-asset-status="<?php echo esc_attr( $media_status ); ?>"
							data-asset-source="<?php echo esc_attr( (string) ( $option['media_source'] ?? 'product-media' ) ); ?>"
							data-figma-node="<?php echo esc_attr( (string) ( $option['figma_node'] ?? '1:' . ( 1500 + ( $index * 10 ) ) ) ); ?>"
							aria-hidden="true">
							<img src="<?php echo esc_url( $media_url ); ?>" alt="" loading="lazy" decoding="async">
						</span>
					<?php } ?>
					<h3><?php echo esc_html( (string) ( $option['title'] ?? '' ) ); ?></h3>
					<p><?php echo esc_html( (string) ( $option['summary'] ?? '' ) ); ?></p>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
<?php } ?>
