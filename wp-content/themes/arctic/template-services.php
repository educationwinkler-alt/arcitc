<?php
/**
 * Template Name: Figma Sluzby
 */

$service_asset = static function ( string $filename ): string {
	return content_url( 'uploads/import/legacy-services/' . ltrim( $filename, '/' ) );
};

$fallback_services = array(
	array(
		'title'          => __( 'Konzultace', 'baspa' ),
		'content'        => wpautop( __( 'Pokud si jeste nejste jisti, ze skutecne chcete koupit virivku, vyuzijte nasi nabidku nezavazne konzultace. Obratit se na nas muzete telefonicky ci e-mailem. Obecna doporuceni, jak postupovat pri vyberu virivky, jiste ocenite, at uz se rozhodnete jakkoliv.', 'baspa' ) ),
		'fallback_image' => $service_asset( 'service-consultation.jpg' ),
		'image_alt'      => __( 'Konzultace sluzeb Arctic Spas', 'baspa' ),
	),
	array(
		'title'          => __( 'Osobni schuzka', 'baspa' ),
		'content'        => wpautop( __( 'Soucasna situace na trhu virivek se muze jevit jako neprehledna. Nejvice informaci muzete ziskat pri osobnim jednani. Po dohode se radi prijedeme nezavazne podivat na misto planovane instalace virivky a poradime vam s jejim umistenim a technickymi podminkami pripojeni.', 'baspa' ) ),
		'fallback_image' => $service_asset( 'service-meeting.jpg' ),
		'image_alt'      => __( 'Osobni schuzka a posouzeni mista instalace', 'baspa' ),
	),
	array(
		'title'          => __( 'Katalog a cenova nabidka', 'baspa' ),
		'content'        => wpautop( __( 'Na prodejne mame k dispozici tisteny katalog a spolu s cenikem si z nasi prodejny odnesete take veskere potrebne informace. A pokud vas v nasi nabidce oslovi konkretni model virivky, zpracujeme vam individualni cenovou nabidku.', 'baspa' ) ),
		'fallback_image' => $service_asset( 'service-catalog.jpg' ),
		'image_alt'      => __( 'Katalog a cenova nabidka virivek', 'baspa' ),
	),
	array(
		'title'          => __( 'Vzorkova prodejna', 'baspa' ),
		'content'        => wpautop( __( 'Zazemi nasi vzorkove prodejny umoznuje nejen prohlidku, ale take vyzkouseni vybranych virivek. Doufame, ze vas snadna dostupnost z D1 a prijemne prostredi nalakaji k navsteve, urcite to ma pri vaznem zajmu smysl.', 'baspa' ) ),
		'fallback_image' => $service_asset( 'service-showroom.jpg' ),
		'image_alt'      => __( 'Vzorkova prodejna a showroom Arctic Spas', 'baspa' ),
	),
	array(
		'title'          => __( 'Dovoz a instalace', 'baspa' ),
		'content'        => wpautop( __( 'Ferovym jednanim pri prodeji nase nadstandardni sluzby nekonci. Objednanou virivku vam v dohodnutem terminu zdarma privezeme, zajistime presun z verejne komunikace na misto urceni, zapojime a zaskolime.', 'baspa' ) ),
		'fallback_image' => $service_asset( 'service-delivery.jpg' ),
		'image_alt'      => __( 'Dovoz, instalace a zaskoleni virivky', 'baspa' ),
	),
	array(
		'title'          => __( 'Zarucni a pozarucni servis', 'baspa' ),
		'content'        => wpautop( __( 'Povazujeme za samozrejmost postarat se o nase zakazniky take v ramci zarucniho a pozarucniho servisu kdekoliv v CR nebo na Slovensku. Jsme tu pro vas jiz vice nez 14 let.', 'baspa' ) ),
		'fallback_image' => $service_asset( 'service-support.jpg' ),
		'image_alt'      => __( 'Zarucni a pozarucni servis Arctic Spas', 'baspa' ),
	),
);

$services = function_exists( 'arctic_services_get_items' ) ? arctic_services_get_items() : array();

if ( empty( $services ) && function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ) {
	foreach ( $fallback_services as $index => $fallback_service ) {
		$fallback_services[ $index ]['id']           = 0;
		$fallback_services[ $index ]['image_id']     = 0;
		$fallback_services[ $index ]['asset_status'] = 'static-fallback';
		$fallback_services[ $index ]['source']       = 'static-fallback';
	}

	$services = $fallback_services;
}

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--services">
	<section class="f-section f-section--figma-services">
		<div class="f-section__container a-container">
			<h2 class="screen-reader-text"><?php echo esc_html( get_the_title() ); ?></h2>
			<div class="f-service-grid">
				<?php foreach ( $services as $service ) {
					$service_id   = (int) ( $service['id'] ?? 0 );
					$source       = (string) ( $service['source'] ?? 'static-fallback' );
					$asset_status = (string) ( $service['asset_status'] ?? 'WAITING_ON_OWNER' );
					$image_id     = (int) ( $service['image_id'] ?? 0 );
					$title        = (string) ( $service['title'] ?? '' );
					$content      = (string) ( $service['content'] ?? '' );
					$image_alt    = (string) ( $service['image_alt'] ?? $title );
					?>
					<article class="f-service-card" data-content-source="<?php echo esc_attr( $source ); ?>" data-service-id="<?php echo esc_attr( (string) $service_id ); ?>">
						<?php
						if ( $image_id ) {
							echo wp_get_attachment_image( $image_id, 'full', false, array(
								'alt'               => $image_alt ?: $title,
								'loading'           => 'lazy',
								'decoding'          => 'async',
								'data-asset-status' => $asset_status,
							) );
						} elseif ( !empty( $service['fallback_image'] ) ) {
							?>
							<img src="<?php echo esc_url( (string) $service['fallback_image'] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" decoding="async" data-asset-status="<?php echo esc_attr( $asset_status ); ?>">
						<?php } ?>
						<h2><?php echo esc_html( $title ); ?></h2>
						<?php echo wp_kses_post( $content ); ?>
					</article>
				<?php } ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
