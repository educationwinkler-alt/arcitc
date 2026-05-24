<?php

/**
 * Product Benefits
 */

$benefits = array(
	'Samonosná kompozitní skořepina',
	'Izolace Heatlock',
	'Cedrový kabinet',
	'Podlaha vířivky',
	'Termokryt',
	'Servisní přístup',
	'Variabilita sedadel',
	'Automatická úprava vody',
	'Ovládání Gecko',
	'Filtrace',
	'Masážní trysky',
	'LED osvětlení',
	'Aromaterapie',
	'Hudební systém',
	'Wi-Fi ovládání',
	'Záruka na skořepinu',
	'Nerezové trysky',
	'Celoroční provoz',
);
?>

<section id="vyhody" class="f-section f-section--product-benefits js-links__section">
	<div class="f-section__container a-container">
		<header class="f-section__header">
			<h2><?php echo esc_html__( 'Výhody vířivek Arctic Spas - série Classic', 'baspa' ); ?></h2>
			<p><?php echo esc_html__( 'Vířivky Arctic Spas série Classic jsou standardně dodávány s řadou funkcí. Kliknutím na některou z níže uvedených funkcí se o nich dozvíte více.', 'baspa' ); ?></p>
		</header>

		<div class="f-product-benefits">
			<?php foreach ( $benefits as $benefit ) { ?>
				<article class="f-product-benefit">
					<span class="f-product-benefit__media" aria-hidden="true"></span>
					<h3><?php echo esc_html( $benefit ); ?></h3>
					<p><?php echo esc_html__( 'Návrh vychází z požadavků na provoz v chladném klimatu, jednoduchou údržbu a stabilní výkon po mnoho sezon.', 'baspa' ); ?></p>
					<span class="f-product-benefit__more" aria-hidden="true">+</span>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
