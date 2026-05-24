<?php
/**
 * Template Name: Figma Služby
 */

$service_image = content_url( 'uploads/import/figma/category-hero-virivky.jpg' );
$services      = array(
	array(
		'title'       => 'Konzultace',
		'description' => 'Pokud si ještě nejste jisti, že skutečně chcete koupit vířivku, využijte naši nabídku nezávazné konzultace. Obrátit se na nás můžete telefonicky či e-mailem. Obecná doporučení, jak postupovat při výběru vířivky, jistě oceníte, ať už se rozhodnete jakkoliv.',
	),
	array(
		'title'       => 'Osobní schůzka',
		'description' => 'Současná situace na trhu vířivek se může jevit jako nepřehledná. Nejvíce informací můžete získat při osobním jednání. Po dohodě se rádi přijedeme nezávazně podívat na místo plánované instalace vířivky a poradíme vám s jejím umístěním a technickými podmínkami připojení.',
	),
	array(
		'title'       => 'Katalog a cenová nabídka',
		'description' => 'Na prodejně máme k dispozici tištěný katalog a spolu s ceníkem si z naší prodejny odnesete také veškeré potřebné informace. A pokud vás v naší nabídce osloví konkrétní model vířivky, zpracujeme vám individuální cenovou nabídku.',
	),
	array(
		'title'       => 'Vzorková prodejna',
		'description' => 'Zázemí naší vzorkové prodejny umožňuje nejen prohlídku, ale také vyzkoušení vybraných vířivek. Doufáme, že vás snadná dostupnost z D1 a příjemné prostředí nalákají k návštěvě, určitě to má při vážném zájmu smysl.',
	),
	array(
		'title'       => 'Dovoz a instalace',
		'description' => 'Férovým jednáním při prodeji naše nadstandardní služby nekončí. Objednanou vířivku vám v dohodnutém termínu zdarma přivezeme, zajistíme přesun z veřejné komunikace na místo určení, zapojíme a zaškolíme.',
	),
	array(
		'title'       => 'Záruční a pozáruční servis',
		'description' => 'Považujeme za samozřejmost postarat se o naše zákazníky také v rámci záručního a pozáručního servisu kdekoliv v ČR nebo na Slovensku. Jsme tu pro vás již více než 14 let.',
	),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--services">
	<section class="f-section f-section--figma-services">
		<div class="f-section__container a-container">
			<div class="f-service-grid">
				<?php foreach ( $services as $service ) { ?>
					<article class="f-service-card">
						<img src="<?php echo esc_url( $service_image ); ?>" alt="" loading="lazy" decoding="async">
						<h2><?php echo esc_html( $service['title'] ); ?></h2>
						<p><?php echo esc_html( $service['description'] ); ?></p>
					</article>
				<?php } ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
