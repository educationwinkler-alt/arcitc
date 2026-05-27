<?php

/**
 * Category Intro
 */

$is_swimspa = is_tax( 'product-category', 'swimspa' );

if ( $is_swimspa ) {
	$blocks = array(
		array(
			'title'       => __( 'Výhody celoročních bazénů Arctic', 'baspa' ),
			'text'        => __( 'Rodinný bazén na zahradě je snem řady domácností. Swimspa Arctic přivezeme kompletní, včetně filtrace, automatické dezinfekce, elektroohřevu, obvodové izolace FreeHeat™ a bezpečného termokrytu. Stačí ji postavit na rovnou plochu, připojit k elektřině a napustit vodou.', 'baspa' ),
			'button_text' => __( 'Více o vlastnostech', 'baspa' ),
			'url'         => home_url( '/vlastnosti/' ),
			'image'       => content_url( 'uploads/import/figma-category-celorocni-bazeny.jpg' ),
			'alt'         => __( 'Celoroční bazén Arctic Spas', 'baspa' ),
		),
		array(
			'title'       => __( 'Celoroční provoz bez stavebních prací', 'baspa' ),
			'text'        => __( 'Celoroční bazén Arctic vám přinese zábavu, relaxaci i sportovní vyžití bez výkopů a složitých stavebních prací. Díky kvalitní konstrukci, izolaci a termokrytu je připravený pro pohodlné používání po celý rok.', 'baspa' ),
			'button_text' => __( 'Více o záruce', 'baspa' ),
			'url'         => home_url( '/zaruka/' ),
			'image'       => content_url( 'uploads/import/legacy-categories/swimspa.jpg' ),
			'alt'         => __( 'Venkovní swimspa Arctic Spas', 'baspa' ),
		),
	);
} else {
	$blocks = array(
		array(
			'title'       => __( 'Vlastnosti vířivek', 'baspa' ),
			'text'        => __( 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie. Unikátní technická řešení, jako obvodová izolace FreeHeat™, sklolaminátová podlaha Forever Floor™, servisní přístupy či termokryt Mylovac™, dělají z venkovních vířivek Arctic Spas tu nejlepší volbu, pokud vám jsou blízké hodnoty jako kvalita a úspornost.', 'baspa' ),
			'button_text' => __( 'Více o vlastnostech', 'baspa' ),
			'url'         => home_url( '/vlastnosti/' ),
			'image'       => content_url( 'uploads/import/figma/category-vlastnosti.jpg' ),
			'alt'         => __( 'Vlastnosti vířivek Arctic Spas podle grafiky', 'baspa' ),
		),
		array(
			'title'       => __( 'Záruka', 'baspa' ),
			'text'        => __( 'Na rozdíl od jiných výrobců nejsou pro nás výše uvedená tvrzení jenom líbivé fráze. Za kvalitou našich výrobků si stojíme, což jasně dokládá unikátní doživotní záruka Arctic Spas na vodotěsnost skořepiny a pětiletá záruka na většinu komponentů, včetně ohřevu.', 'baspa' ),
			'button_text' => __( 'Více o záruce', 'baspa' ),
			'url'         => home_url( '/zaruka/' ),
			'image'       => content_url( 'uploads/import/figma/category-zaruka.jpg' ),
			'alt'         => __( 'Záruka Arctic Spas podle grafiky', 'baspa' ),
		),
	);
}
?>

<section class="f-section f-section--category-intro">
	<div class="f-section__container a-container">
		<div class="f-category-intro f-category-intro--split">
			<div class="f-category-intro__content">
				<h2><?php echo esc_html( $blocks[0]['title'] ); ?></h2>
				<p><?php echo esc_html( $blocks[0]['text'] ); ?></p>
				<a class="f-button a-button a-button--accent" href="<?php echo esc_url( $blocks[0]['url'] ); ?>">
					<?php echo esc_html( $blocks[0]['button_text'] ); ?>
				</a>
			</div>
			<figure class="f-category-intro__image">
				<img src="<?php echo esc_url( $blocks[0]['image'] ); ?>" width="674" height="424" alt="<?php echo esc_attr( $blocks[0]['alt'] ); ?>" loading="lazy" decoding="async">
			</figure>
		</div>

		<div class="f-category-intro f-category-intro--reverse">
			<figure class="f-category-intro__image">
				<img src="<?php echo esc_url( $blocks[1]['image'] ); ?>" width="674" height="424" alt="<?php echo esc_attr( $blocks[1]['alt'] ); ?>" loading="lazy" decoding="async">
			</figure>
			<div class="f-category-intro__content">
				<h2><?php echo esc_html( $blocks[1]['title'] ); ?></h2>
				<p><?php echo esc_html( $blocks[1]['text'] ); ?></p>
				<a class="f-button a-button a-button--accent" href="<?php echo esc_url( $blocks[1]['url'] ); ?>">
					<?php echo esc_html( $blocks[1]['button_text'] ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
