<?php

/**
 * Product Benefits
 */

$benefits = array(
	'Samonosná kompozitní skořepina',
	'Tepelně izolovaný kabinet',
	'Efektivní celoroční provoz',
	'Pohodlný servisní přístup',
	'Volitelná výbava',
	'Automatická úprava vody',
);
?>

<section id="vyhody" class="f-section f-section--product-benefits js-links__section">
	<div class="f-section__container a-container">
		<header class="f-section__header">
			<h2><?php echo esc_html__( 'Výhody vířivek Arctic Spas', 'baspa' ); ?></h2>
			<p><?php echo esc_html__( 'Technologie, izolace a konstrukce jsou navržené pro dlouhou životnost a nízké náklady v celoročním provozu.', 'baspa' ); ?></p>
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
