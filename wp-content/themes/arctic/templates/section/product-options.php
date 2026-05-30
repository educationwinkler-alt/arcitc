<?php

/**
 * Product Optional Equipment
 */

$options = array(
	'Onzen úprava vody',
	'Spa Boy monitoring',
	'Wi-Fi ovládání',
	'LED osvětlení',
	'Bluetooth audio',
	'Termokryt',
	'Schůdky a madla',
	'Covana kryt',
);

$option_icons = array(
	'onzen',
	'spa-boy',
	'wifi',
	'led',
	'audio',
	'cover',
	'steps',
	'covana',
);
?>

<section id="volitelna-vybava" class="f-section f-section--product-options js-links__section">
	<div class="f-section__container a-container">
		<header class="f-section__header">
			<h2><?php echo esc_html__( 'Volitelná výbava', 'baspa' ); ?></h2>
			<p><?php echo esc_html__( 'Doplňky a technologie se vybírají podle modelu, konfigurace a způsobu používání vířivky.', 'baspa' ); ?></p>
		</header>

		<div class="f-product-benefits f-product-benefits--options">
			<?php foreach ( $options as $index => $option ) {
				$icon = $option_icons[ $index ] ?? 'option';
				?>
				<article class="f-product-benefit f-product-benefit--static f-product-benefit--media-<?php echo esc_attr( $icon ); ?>">
					<span class="f-product-benefit__media f-product-benefit__media--<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
					<h3><?php echo esc_html( $option ); ?></h3>
					<p><?php echo esc_html__( 'Konkrétní dostupnost a doporučenou kombinaci potvrdíme v nabídce pro vybraný model.', 'baspa' ); ?></p>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
