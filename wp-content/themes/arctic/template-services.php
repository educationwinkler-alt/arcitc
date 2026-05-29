<?php
/**
 * Template Name: Figma Služby
 */

$service_fallback_photos = array(
	'consultation'    => content_url( 'uploads/import/legacy-services/service-consultation.jpg' ),
	'meeting'         => content_url( 'uploads/import/legacy-services/service-meeting.jpg' ),
	'catalog'         => content_url( 'uploads/import/legacy-services/service-catalog.jpg' ),
	'showroom'        => content_url( 'uploads/import/legacy-services/service-showroom.jpg' ),
	'delivery'        => content_url( 'uploads/import/legacy-services/service-delivery.jpg' ),
	'service-support' => content_url( 'uploads/import/legacy-services/service-support.jpg' ),
);
$default_service_image = $service_fallback_photos['consultation'];
$services      = array(
	array(
		'slug'        => 'consultation',
		'title'       => 'Konzultace',
		'description' => 'Pokud si ještě nejste jisti, že skutečně chcete koupit vířivku, využijte naši nabídku nezávazné konzultace. Obrátit se na nás můžete telefonicky či e-mailem. Obecná doporučení, jak postupovat při výběru vířivky, jistě oceníte, ať už se rozhodnete jakkoliv.',
		'image'       => '',
		'fallback_photo' => $service_fallback_photos['consultation'],
		'image_alt'   => 'Konzultace služeb Arctic Spas',
	),
	array(
		'slug'        => 'meeting',
		'title'       => 'Osobní schůzka',
		'description' => 'Současná situace na trhu vířivek se může jevit jako nepřehledná. Nejvíce informací můžete získat při osobním jednání. Po dohodě se rádi přijedeme nezávazně podívat na místo plánované instalace vířivky a poradíme vám s jejím umístěním a technickými podmínkami připojení.',
		'image'       => '',
		'fallback_photo' => $service_fallback_photos['meeting'],
		'image_alt'   => 'Osobní schůzka a posouzení místa instalace',
	),
	array(
		'slug'        => 'catalog',
		'title'       => 'Katalog a cenová nabídka',
		'description' => 'Na prodejně máme k dispozici tištěný katalog a spolu s ceníkem si z naší prodejny odnesete také veškeré potřebné informace. A pokud vás v naší nabídce osloví konkrétní model vířivky, zpracujeme vám individuální cenovou nabídku.',
		'image'       => '',
		'fallback_photo' => $service_fallback_photos['catalog'],
		'image_alt'   => 'Katalog a cenová nabídka vířivek',
	),
	array(
		'slug'        => 'showroom',
		'title'       => 'Vzorková prodejna',
		'description' => 'Zázemí naší vzorkové prodejny umožňuje nejen prohlídku, ale také vyzkoušení vybraných vířivek. Doufáme, že vás snadná dostupnost z D1 a příjemné prostředí nalákají k návštěvě, určitě to má při vážném zájmu smysl.',
		'image'       => '',
		'fallback_photo' => $service_fallback_photos['showroom'],
		'image_alt'   => 'Vzorková prodejna a showroom Arctic Spas',
	),
	array(
		'slug'        => 'delivery',
		'title'       => 'Dovoz a instalace',
		'description' => 'Férovým jednáním při prodeji naše nadstandardní služby nekončí. Objednanou vířivku vám v dohodnutém termínu zdarma přivezeme, zajistíme přesun z veřejné komunikace na místo určení, zapojíme a zaškolíme.',
		'image'       => '',
		'fallback_photo' => $service_fallback_photos['delivery'],
		'image_alt'   => 'Dovoz, instalace a zaškolení vířivky',
	),
	array(
		'slug'        => 'service-support',
		'title'       => 'Záruční a pozáruční servis',
		'description' => 'Považujeme za samozřejmost postarat se o naše zákazníky také v rámci záručního a pozáručního servisu kdekoliv v ČR nebo na Slovensku. Jsme tu pro vás již více než 14 let.',
		'image'       => '',
		'fallback_photo' => $service_fallback_photos['service-support'],
		'image_alt'   => 'Záruční a pozáruční servis Arctic Spas',
	),
);

foreach ( $services as $index => $service ) {
	$primary_image = $service['image'] ?? '';
	$photo_fallback = $service['fallback_photo'] ?? '';

	$resolved_image = $primary_image;
	if ( empty( $resolved_image ) ) {
		$resolved_image = $photo_fallback;
	}
	if ( empty( $resolved_image ) ) {
		$resolved_image = $default_service_image;
	}

	$services[ $index ]['resolved_image'] = $resolved_image;
}

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--services">
	<section class="f-section f-section--figma-services">
		<div class="f-section__container a-container">
			<div class="f-service-grid">
				<?php foreach ( $services as $service ) { ?>
					<article class="f-service-card">
						<img src="<?php echo esc_url( $service['resolved_image'] ); ?>" alt="<?php echo esc_attr( $service['image_alt'] ?? '' ); ?>" loading="lazy" decoding="async">
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
