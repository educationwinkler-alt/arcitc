<?php

/**
 * Template Name: Showroom
 */

$asset = static function ( string $filename ): string {
	return content_url( 'uploads/import/' . ltrim( $filename, '/' ) );
};

$showroom_assets = array(
	'hero'           => $asset( 'owner-showroom/showroom-main-web.jpg' ),
	'pool_detail'    => $asset( 'owner-showroom/showroom-detail-web.jpg' ),
	'hot_tub_detail' => $asset( 'owner-showroom/showroom-covana-interior-web.jpg' ),
);

$contact_email = get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );
$contact_phone = get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$contact_name  = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_contact_name', 'Lukáš Dušek' ) : get_theme_mod( 'arctic_showroom_contact_name', 'Lukáš Dušek' );
$street        = get_theme_mod( 'baspa_street', 'Bohunická cesta 15' );
$city          = get_theme_mod( 'baspa_city', 'Moravany u Brna' );
$hours_title   = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_title', 'Otevírací doba' ) : get_theme_mod( 'arctic_showroom_hours_title', 'Otevírací doba' );
$hours_label   = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_label', 'Úterý - Pátek' ) : get_theme_mod( 'arctic_showroom_hours_label', 'Úterý - Pátek' );
$hours_line_1  = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_line_1', '9:00 - 11:30' ) : get_theme_mod( 'arctic_showroom_hours_line_1', '9:00 - 11:30' );
$hours_line_2  = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_line_2', '12:30 - 16:00' ) : get_theme_mod( 'arctic_showroom_hours_line_2', '12:30 - 16:00' );

$reasons = array(
	array(
		'icon' => 'pool',
		'text' => 'Prohlédnete si naše bazény a vířivky na vlastní oči',
	),
	array(
		'icon' => 'road',
		'text' => 'Najdete nás pouhé 3 km od dálnice D1 (exit 194)',
	),
	array(
		'icon' => 'parking',
		'text' => 'Zaparkujete přímo před prodejnou',
	),
	array(
		'icon' => 'coffee',
		'text' => 'Nabídneme vám občerstvení a můžete využít zázemí',
	),
);

get_header();
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-showroom-page">

		<section class="f-showroom-hero" style="--showroom-hero-image: url('<?php echo esc_url( $showroom_assets['hero'] ); ?>');">
			<div class="f-showroom-hero__container a-container">
				<nav class="f-showroom-breadcrumb" aria-label="<?php echo esc_attr_x( 'Breadcrumbs', 'navigation', 'baspa' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr__( 'Úvod', 'baspa' ); ?>"></a>
					<span><?php echo esc_html__( 'Další informace', 'baspa' ); ?></span>
					<strong><?php echo esc_html__( 'Showroom', 'baspa' ); ?></strong>
				</nav>

				<div class="f-showroom-hero__content">
					<h1><?php echo esc_html__( 'Showroom', 'baspa' ); ?></h1>
					<p><?php echo esc_html__( 'U nás najdete od každého něco a obvykle alespoň dva modely vířivek napuštěné vodou, abyste je mohli podrobit mokré zkoušce.', 'baspa' ); ?></p>
					<a class="f-showroom-gallery-button" href="#fotogalerie">
						<span class="f-showroom-icon f-showroom-icon--camera" aria-hidden="true"></span>
						<?php echo esc_html__( 'Fotogalerie', 'baspa' ); ?>
					</a>
				</div>

				<div class="f-showroom-area-badge" aria-label="<?php echo esc_attr__( '280 metrů čtverečních prezentační plochy', 'baspa' ); ?>">
					<strong>280&nbsp;m²</strong>
					<span><?php echo esc_html__( 'prezentační', 'baspa' ); ?></span>
					<span><?php echo esc_html__( 'plochy', 'baspa' ); ?></span>
				</div>

				<aside class="f-showroom-mini-cta">
					<h2><?php echo esc_html__( 'Přijďte se pobavit o své nové vířivce nebo swimspa', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Na pobočce nebo online s vámi vaše představy rádi probereme. Že žádné nemáte? Tím spíš se zastavte pro inspiraci.', 'baspa' ); ?></p>
					<a class="a-button a-button--outline" href="<?php echo esc_url( home_url( '/kontakt/#formular' ) ); ?>">
						<?php echo esc_html__( 'Domluvit si schůzku', 'baspa' ); ?>
					</a>
				</aside>
			</div>
		</section>

		<section class="f-showroom-info" aria-label="<?php echo esc_attr__( 'Informace o showroomu', 'baspa' ); ?>">
			<div class="f-showroom-info__container a-container">
				<div class="f-showroom-info__item f-showroom-info__item--contact">
					<span class="f-showroom-icon f-showroom-icon--phone" aria-hidden="true"></span>
					<h2><?php echo esc_html__( 'Kontakt', 'baspa' ); ?></h2>
					<strong><?php echo esc_html( $contact_name ); ?></strong>
					<a href="mailto:<?php echo esc_attr( antispambot( $contact_email ) ); ?>"><?php echo esc_html( antispambot( $contact_email ) ); ?></a>
					<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $contact_phone ) ); ?>"><?php echo esc_html( $contact_phone ); ?></a>
				</div>

				<div class="f-showroom-info__item">
					<span class="f-showroom-icon f-showroom-icon--map" aria-hidden="true"></span>
					<h2><?php echo esc_html__( 'Kde nás najdete', 'baspa' ); ?></h2>
					<strong><?php echo esc_html( $city ); ?></strong>
					<span><?php echo esc_html( $street ); ?></span>
					<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php echo esc_html__( 'Zobrazit na mapě.', 'baspa' ); ?></a>
				</div>

				<div class="f-showroom-info__item">
					<span class="f-showroom-icon f-showroom-icon--clock" aria-hidden="true"></span>
					<h2><?php echo esc_html( $hours_title ); ?></h2>
					<strong><?php echo esc_html( $hours_label ); ?></strong>
					<span><?php echo esc_html( $hours_line_1 ); ?></span>
					<span><?php echo esc_html( $hours_line_2 ); ?></span>
				</div>
			</div>
		</section>

		<section class="f-showroom-reasons">
			<div class="f-showroom-reasons__container a-container">
				<h2><?php echo esc_html__( 'Proč navštívit náš showroom?', 'baspa' ); ?></h2>
				<div class="f-showroom-reasons__grid">
					<?php foreach ( $reasons as $reason ) { ?>
						<div class="f-showroom-reason">
							<span class="f-showroom-reason__icon f-showroom-reason__icon--<?php echo esc_attr( $reason['icon'] ); ?>" aria-hidden="true"></span>
							<p><?php echo esc_html( $reason['text'] ); ?></p>
						</div>
					<?php } ?>
				</div>
			</div>
		</section>

		<section id="fotogalerie" class="f-showroom-split f-showroom-split--first">
			<div class="f-showroom-split__container a-container">
				<div class="f-showroom-split__copy">
					<h2><?php echo esc_html__( 'Naše bazény a vířivky si prohlédnete, případně vyzkoušíte', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Naše hlavní vzorková prodejna se nachází v Moravanech u Brna a je velmi dobře dostupná z dálnice D1. Vystavené vířivky, swimspa a vybrané příslušenství si u nás můžete nejen prohlédnout. Minimálně dvě vířivky míváme zprovozněné pro mokrý test, k dispozici je samozřejmě kompletní zázemí.', 'baspa' ); ?></p>
				</div>
				<img src="<?php echo esc_url( $showroom_assets['pool_detail'] ); ?>"
				     alt="<?php echo esc_attr__( 'Showroom Arctic Spas v Moravanech', 'baspa' ); ?>"
				     loading="lazy" decoding="async">
			</div>
		</section>

		<section class="f-showroom-split f-showroom-split--second">
			<div class="f-showroom-split__container a-container">
				<img src="<?php echo esc_url( $showroom_assets['hot_tub_detail'] ); ?>"
				     alt="<?php echo esc_attr__( 'Vířivky Arctic Spas v showroomu', 'baspa' ); ?>"
				     loading="lazy" decoding="async">
				<div class="f-showroom-split__copy">
					<p><?php echo esc_html__( 'U vířivek Arctic Spas nabízíme pro posouzení komfortu a kvality hydromasáže možnost vyzkoušení. Zpravidla dvě vířivky pro vás máme neustále napuštěné, takže si v případě zájmu vezměte plavky. Na místě rádi zodpovíme veškeré vaše dotazy a poradíme s přípravou vašeho projektu.', 'baspa' ); ?></p>
				</div>
			</div>
		</section>

	</main>

<?php
get_footer();
