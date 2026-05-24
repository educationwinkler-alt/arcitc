<?php

/**
 * Category Intro
 */

$features_image = content_url( 'uploads/import/figma/category-vlastnosti.jpg' );
$warranty_image = content_url( 'uploads/import/figma/category-zaruka.jpg' );
?>

<section class="f-section f-section--category-intro">
	<div class="f-section__container a-container">
		<div class="f-category-intro f-category-intro--split">
			<div class="f-category-intro__content">
				<h2><?php echo esc_html__( 'Vlastnosti vířivek', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie. Unikátní technická řešení, jako obvodová izolace FreeHeat™, sklolaminátová podlaha Forever Floor™, servisní přístupy či termokryt Mylovac™, dělají z venkovních vířivek Arctic Spas tu nejlepší volbu, pokud vám jsou blízké hodnoty jako kvalita a úspornost.', 'baspa' ); ?></p>
				<a class="f-button a-button a-button--accent" href="<?php echo esc_url( home_url( '/vlastnosti/' ) ); ?>">
					<?php echo esc_html__( 'Více o vlastnostech', 'baspa' ); ?>
				</a>
			</div>
			<figure class="f-category-intro__image">
				<img src="<?php echo esc_url( $features_image ); ?>" width="674" height="424" alt="<?php echo esc_attr__( 'Vlastnosti vířivek Arctic Spas podle grafiky', 'baspa' ); ?>" loading="lazy" decoding="async">
			</figure>
		</div>

		<div class="f-category-intro f-category-intro--reverse">
			<figure class="f-category-intro__image">
				<img src="<?php echo esc_url( $warranty_image ); ?>" width="674" height="424" alt="<?php echo esc_attr__( 'Záruka Arctic Spas podle grafiky', 'baspa' ); ?>" loading="lazy" decoding="async">
			</figure>
			<div class="f-category-intro__content">
				<h2><?php echo esc_html__( 'Záruka', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Na rozdíl od jiných výrobců nejsou pro nás výše uvedená tvrzení jenom líbivé fráze. Za kvalitou našich výrobků si stojímě, což jasně dokládá unikátní doživotní záruka Arctic Spas na vodotěsnost skořepiny a pětiletá záruka na většinu komponentů, včetně ohřevu.', 'baspa' ); ?></p>
				<a class="f-button a-button a-button--accent" href="<?php echo esc_url( home_url( '/zaruka/' ) ); ?>">
					<?php echo esc_html__( 'Více o záruce', 'baspa' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
