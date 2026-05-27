<?php

/**
 * Product Benefits
 */

$benefits = array(
	array(
		'title'   => __( 'Samonosná kompozitní skořepina', 'baspa' ),
		'summary' => __( 'Prémiový akrylát Aristech, ručně nanášený sklolaminát a pevný kompozitní základ bez podpůrné pěny.', 'baspa' ),
		'popup'   => true,
	),
	array( 'title' => __( 'Izolace Heatlock', 'baspa' ) ),
	array( 'title' => __( 'Cedrový kabinet', 'baspa' ) ),
	array( 'title' => __( 'Podlaha vířivky', 'baspa' ) ),
	array( 'title' => __( 'Termokryt', 'baspa' ) ),
	array( 'title' => __( 'Servisní přístup', 'baspa' ) ),
	array( 'title' => __( 'Variabilita sedadel', 'baspa' ) ),
	array( 'title' => __( 'Automatická úprava vody', 'baspa' ) ),
	array( 'title' => __( 'Ovládání Gecko', 'baspa' ) ),
	array( 'title' => __( 'Filtrace', 'baspa' ) ),
	array( 'title' => __( 'Masážní trysky', 'baspa' ) ),
	array( 'title' => __( 'LED osvětlení', 'baspa' ) ),
	array( 'title' => __( 'Aromaterapie', 'baspa' ) ),
	array( 'title' => __( 'Hudební systém', 'baspa' ) ),
	array( 'title' => __( 'Wi-Fi ovládání', 'baspa' ) ),
	array( 'title' => __( 'Záruka na skořepinu', 'baspa' ) ),
	array( 'title' => __( 'Nerezové trysky', 'baspa' ) ),
	array( 'title' => __( 'Celoroční provoz', 'baspa' ) ),
);

$popup_id = sanitize_title( esc_attr_x( 'shell-benefit-popup', 'anchor', 'baspa' ) );
?>

<section id="vyhody" class="f-section f-section--product-benefits js-links__section">
	<div class="f-section__container a-container">
		<header class="f-section__header">
			<h2><?php echo esc_html__( 'Výhody vířivek Arctic Spas - série Classic', 'baspa' ); ?></h2>
			<p><?php echo esc_html__( 'Vířivky Arctic Spas série Classic jsou standardně dodávány s řadou funkcí. Kliknutím na vybrané funkce se o nich dozvíte více.', 'baspa' ); ?></p>
		</header>

		<div class="f-product-benefits">
			<?php foreach ( $benefits as $benefit ) {
				$has_popup = !empty( $benefit['popup'] );
				?>
				<article class="f-product-benefit<?php echo $has_popup ? ' f-product-benefit--has-popup' : ''; ?>">
					<span class="f-product-benefit__media" aria-hidden="true"></span>
					<h3><?php echo esc_html( $benefit['title'] ); ?></h3>
					<p><?php echo esc_html( $benefit['summary'] ?? __( 'Návrh vychází z požadavků na provoz v chladném klimatu, jednoduchou údržbu a stabilní výkon po mnoho sezon.', 'baspa' ) ); ?></p>
					<span class="f-product-benefit__more" aria-hidden="true">+</span>

					<?php if ( $has_popup ) { ?>
						<button type="button"
							class="f-product-benefit__trigger f-off__trigger js-off__trigger"
							data-off="benefit-shell"
							aria-controls="<?php echo esc_attr( $popup_id ); ?>">
							<span class="screen-reader-text"><?php echo esc_html__( 'Zobrazit detail skořepiny', 'baspa' ); ?></span>
						</button>
					<?php } ?>
				</article>
			<?php } ?>
		</div>
	</div>
</section>

<aside id="<?php echo esc_attr( $popup_id ); ?>"
	class="f-off f-off--benefit-popup f-off--dialog a-off a-off--dialog js-off"
	data-off="benefit-shell"
	data-off-breakpoint="all"
	data-off-relocate="false"
	aria-labelledby="benefit-shell-title"
	aria-hidden="true">

	<div class="f-benefit-popup f-off__container a-off__container">
		<button class="f-benefit-popup__close f-off__close a-off__close js-off__close"
			data-off="benefit-shell"
			aria-controls="<?php echo esc_attr( $popup_id ); ?>">
			<?php if ( function_exists( 'forqy_get_icon' ) ) {
				forqy_get_icon( 'close' );
			} ?>
			<span class="screen-reader-text"><?php echo esc_html__( 'Zavřít okno', 'baspa' ); ?></span>
		</button>

		<h2 id="benefit-shell-title"><?php echo esc_html__( 'Samonosná kompozitní skořepina s doživotní zárukou', 'baspa' ); ?></h2>

		<figure class="f-benefit-popup__media">
			<img src="<?php echo esc_url( content_url( 'uploads/import/figma/popup-shell-detail.png' ) ); ?>" width="697" height="364" alt="<?php echo esc_attr__( 'Detail skořepiny vířivky Arctic Spas', 'baspa' ); ?>" loading="lazy" decoding="async">
		</figure>

		<div class="f-benefit-popup__content">
			<p><?php echo esc_html__( 'Skořepina vířivky Arctic Spas je samotným základem naší konstrukce a proto k její výrobě přistupujeme s vědomím zásadní důležitosti její funkce.', 'baspa' ); ?></p>
			<p><?php echo esc_html__( 'Začínáme vakuovým formováním vrstvy prémiového akrylátu Aristech s antibakteriální úpravou Bio-Lok™. Poté ručně nanášíme sklolaminátový kompozit vrstvu po vrstvě, aby skořepina získala svou legendární pevnost.', 'baspa' ); ?></p>
			<p><?php echo esc_html__( 'Používáme nejsilnější vrstvu sklolaminátu v oboru a nehledáme žádné úsporné zkratky. Výsledkem je pevný a přesný základ pro instalaci pokročilých technologií Arctic Spas®.', 'baspa' ); ?></p>
		</div>

		<button class="f-benefit-popup__button js-off__close" type="button" data-off="benefit-shell" aria-controls="<?php echo esc_attr( $popup_id ); ?>">
			<?php echo esc_html__( 'Zavřít okno', 'baspa' ); ?>
		</button>
	</div>
</aside>
