<?php

/**
 * Template Name: Showroom
 */

$asset = static function ( string $filename ): string {
	return content_url( 'uploads/import/' . ltrim( $filename, '/' ) );
};

$post_id = get_queried_object_id();

$meta_text = static function ( string $key, string $fallback = '' ) use ( $post_id ): string {
	$value = get_post_meta( $post_id, $key, true );

	if ( is_array( $value ) ) {
		$value = '';
	}

	$value = trim( wp_strip_all_tags( (string) $value ) );

	return '' !== $value ? $value : $fallback;
};

$meta_content = static function ( string $key, string $fallback = '' ) use ( $post_id ): string {
	$value = get_post_meta( $post_id, $key, true );

	if ( is_array( $value ) ) {
		$value = '';
	}

	$value = trim( (string) $value );

	return '' !== $value ? apply_filters( 'the_content', $value ) : apply_filters( 'the_content', $fallback );
};

$image_html = static function ( int $image_id, string $fallback_url, string $alt, string $class = '' ): string {
	$attrs = array(
		'alt'      => $alt,
		'loading'  => 'lazy',
		'decoding' => 'async',
	);

	if ( '' !== $class ) {
		$attrs['class'] = $class;
	}

	if ( $image_id ) {
		return wp_get_attachment_image( $image_id, 'full', false, $attrs );
	}

	$class_attr = '' !== $class ? ' class="' . esc_attr( $class ) . '"' : '';

	return '<img' . $class_attr . ' src="' . esc_url( $fallback_url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">';
};

$gallery_image_ids = array_values( array_filter( array_map( 'absint', get_post_meta( $post_id, 'showroom_gallery_images' ) ) ) );

$showroom_assets = array(
	'hero'            => get_the_post_thumbnail_url( $post_id, 'full' ) ?: $asset( 'owner-showroom/showroom-covana-interior-web.jpg' ),
	'interior_detail' => $gallery_image_ids[0] ?? 0,
	'exterior_detail' => $gallery_image_ids[1] ?? 0,
);

$contact_member = function_exists( 'baspa_members_get_selected_contact' ) ? baspa_members_get_selected_contact( 'showroom_contact', get_template() . '-avatar' ) : array();
$contact_email  = !empty( $contact_member['email'] ) ? $contact_member['email'] : get_theme_mod( 'baspa_email', 'info@arctic-spas.cz' );
$contact_phone  = !empty( $contact_member['phone'] ) ? $contact_member['phone'] : get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$fallback_contact_name = trim( (string) get_theme_mod( 'baspa_name', get_bloginfo( 'name' ) ) );
$contact_name   = !empty( $contact_member['name'] ) ? $contact_member['name'] : ( $fallback_contact_name !== '' ? $fallback_contact_name : __( 'Arctic Spas', 'baspa' ) );
$contact_source = !empty( $contact_member['source'] ) ? $contact_member['source'] : 'customizer-about';
$map_url        = function_exists( 'arctic_get_map_url' ) ? arctic_get_map_url() : get_theme_mod( 'baspa_map', 'https://maps.app.goo.gl/ZsYfoZ2aQGF1JnZG6' );
$street         = get_theme_mod( 'baspa_street', 'Bohunická cesta 15' );
$city           = get_theme_mod( 'baspa_city', 'Moravany u Brna' );
$hours_title    = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_title', 'Otevírací doba' ) : get_theme_mod( 'arctic_showroom_hours_title', 'Otevírací doba' );
$hours_label    = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_label', 'Úterý - Pátek' ) : get_theme_mod( 'arctic_showroom_hours_label', 'Úterý - Pátek' );
$hours_line_1   = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_line_1', '9:00 - 11:30' ) : get_theme_mod( 'arctic_showroom_hours_line_1', '9:00 - 11:30' );
$hours_line_2   = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_line_2', '12:30 - 16:00' ) : get_theme_mod( 'arctic_showroom_hours_line_2', '12:30 - 16:00' );

$hero_title          = $meta_text( 'page_title_text', get_the_title( $post_id ) );
$hero_description    = $meta_text( 'page_description_text', __( 'U nás najdete od každého něco a obvykle alespoň dva modely vířivek napuštěné vodou, abyste je mohli podrobit mokré zkoušce.', 'baspa' ) );
$gallery_button_text = $meta_text( 'showroom_gallery_button_text', __( 'Fotogalerie', 'baspa' ) );
$area_value          = $meta_text( 'showroom_area_value', '280 m²' );
$area_label_1        = $meta_text( 'showroom_area_label_1', __( 'prezentační', 'baspa' ) );
$area_label_2        = $meta_text( 'showroom_area_label_2', __( 'plochy', 'baspa' ) );
$mini_cta_title      = $meta_text( 'showroom_mini_cta_title', __( 'Přijďte se pobavit o své nové vířivce nebo swimspa', 'baspa' ) );
$mini_cta_text       = $meta_text( 'showroom_mini_cta_text', __( 'Na pobočce nebo online s vámi vaše představy rádi probereme. Že žádné nemáte? Tím spíš se zastavte pro inspiraci.', 'baspa' ) );
$mini_cta_button     = $meta_text( 'showroom_mini_cta_button_text', __( 'Domluvit si schůzku', 'baspa' ) );
$mini_cta_url        = get_post_meta( $post_id, 'showroom_mini_cta_button_url', true ) ?: home_url( '/kontakt/#formular' );
$reasons_heading     = $meta_text( 'showroom_reasons_heading', __( 'Proč navštívit náš showroom?', 'baspa' ) );
$primary_title       = $meta_text( 'showroom_primary_title', __( 'Naše bazény a vířivky si prohlédnete, případně vyzkoušíte', 'baspa' ) );
$primary_content     = trim( apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ) );
$secondary_content   = $meta_content( 'showroom_secondary_content', __( 'Showroom najdete v Moravanech u Brna, kousek od dálnice D1. Při návštěvě s vámi projdeme výběr modelu, přípravu místa, možnosti usazení i další kroky realizace. Přijet můžete po domluvě v otevírací době a rovnou probrat další postup.', 'baspa' ) );

if ( '' === $primary_content ) {
	$primary_content = apply_filters( 'the_content', __( 'Naše hlavní vzorková prodejna se nachází v Moravanech u Brna a je velmi dobře dostupná z dálnice D1. Vystavené vířivky, swimspa a vybrané příslušenství si u nás můžete nejen prohlédnout. Minimálně dvě vířivky míváme zprovozněné pro mokrý test, k dispozici je samozřejmě kompletní zázemí.', 'baspa' ) );
}

$default_reasons = array(
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

$reasons = array();
$raw_reason_rows = get_post_meta( $post_id, 'showroom_reasons' );
$reason_rows     = array();

foreach ( $raw_reason_rows as $raw_reason_row ) {
	if ( !is_array( $raw_reason_row ) ) {
		continue;
	}

	if ( array_key_exists( 'text', $raw_reason_row ) || array_key_exists( 'icon', $raw_reason_row ) ) {
		$reason_rows[] = $raw_reason_row;
		continue;
	}

	foreach ( $raw_reason_row as $nested_reason_row ) {
		if ( is_array( $nested_reason_row ) ) {
			$reason_rows[] = $nested_reason_row;
		}
	}
}

foreach ( $reason_rows as $reason_row ) {
	if ( !is_array( $reason_row ) ) {
		continue;
	}

	$text = trim( wp_strip_all_tags( (string) ( $reason_row['text'] ?? '' ) ) );

	if ( '' === $text ) {
		continue;
	}

	$icon = sanitize_title( (string) ( $reason_row['icon'] ?? 'pool' ) );
	if ( !in_array( $icon, array( 'pool', 'road', 'parking', 'coffee' ), true ) ) {
		$icon = 'pool';
	}

	$reasons[] = array(
		'icon' => $icon,
		'text' => $text,
	);
}

if ( empty( $reasons ) ) {
	$reasons = $default_reasons;
}

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
					<h1><?php echo esc_html( $hero_title ); ?></h1>
					<p><?php echo esc_html( $hero_description ); ?></p>
					<a class="f-showroom-gallery-button" href="#fotogalerie">
						<span class="f-showroom-icon f-showroom-icon--camera" aria-hidden="true"></span>
						<?php echo esc_html( $gallery_button_text ); ?>
					</a>
				</div>

				<div class="f-showroom-area-badge" role="group" aria-label="<?php echo esc_attr( trim( $area_value . ' ' . $area_label_1 . ' ' . $area_label_2 ) ); ?>">
					<strong><?php echo esc_html( $area_value ); ?></strong>
					<span><?php echo esc_html( $area_label_1 ); ?></span>
					<span><?php echo esc_html( $area_label_2 ); ?></span>
				</div>

				<aside class="f-showroom-mini-cta" data-content-source="showroom-meta">
					<h2><?php echo esc_html( $mini_cta_title ); ?></h2>
					<p><?php echo esc_html( $mini_cta_text ); ?></p>
					<a class="a-button a-button--outline" href="<?php echo esc_url( $mini_cta_url ); ?>">
						<?php echo esc_html( $mini_cta_button ); ?>
					</a>
				</aside>
			</div>
		</section>

		<section class="f-showroom-info" aria-label="<?php echo esc_attr__( 'Informace o showroomu', 'baspa' ); ?>">
			<div class="f-showroom-info__container a-container">
				<div class="f-showroom-info__item f-showroom-info__item--contact"
				     <?php echo !empty( $contact_member['id'] ) ? 'data-member-id="' . esc_attr( (string) $contact_member['id'] ) . '"' : ''; ?>
				     data-content-source="<?php echo esc_attr( $contact_source ); ?>">
					<span class="f-showroom-icon f-showroom-icon--phone" aria-hidden="true"></span>
					<h2><?php echo esc_html__( 'Kontakt', 'baspa' ); ?></h2>
					<strong><?php echo esc_html( $contact_name ); ?></strong>
					<a href="mailto:<?php echo esc_attr( antispambot( $contact_email ) ); ?>"><?php echo esc_html( antispambot( $contact_email ) ); ?></a>
					<a href="tel:<?php echo esc_attr( function_exists( 'baspa_member_phone_href' ) ? baspa_member_phone_href( $contact_phone ) : str_replace( ' ', '', $contact_phone ) ); ?>"><?php echo esc_html( $contact_phone ); ?></a>
				</div>

				<div class="f-showroom-info__item">
					<span class="f-showroom-icon f-showroom-icon--map" aria-hidden="true"></span>
					<h2><?php echo esc_html__( 'Kde nás najdete', 'baspa' ); ?></h2>
					<strong><?php echo esc_html( $city ); ?></strong>
					<span><?php echo esc_html( $street ); ?></span>
					<a href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html__( 'Zobrazit na mapě.', 'baspa' ); ?></a>
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
				<h2><?php echo esc_html( $reasons_heading ); ?></h2>
				<div class="f-showroom-reasons__grid" data-content-source="showroom-meta">
					<?php foreach ( $reasons as $reason ) { ?>
						<div class="f-showroom-reason">
							<img class="f-showroom-reason__icon"
							     src="<?php echo esc_url( $asset( 'figma/showroom-reason-' . $reason['icon'] . '.svg' ) ); ?>"
							     width="63" height="63" alt="" decoding="async" aria-hidden="true">
							<p><?php echo esc_html( $reason['text'] ); ?></p>
						</div>
					<?php } ?>
				</div>
			</div>
		</section>

		<section id="fotogalerie" class="f-showroom-split f-showroom-split--first">
			<div class="f-showroom-split__container a-container">
				<div class="f-showroom-split__copy" data-content-source="wp-editor">
					<h2><?php echo esc_html( $primary_title ); ?></h2>
					<?php echo wp_kses_post( $primary_content ); ?>
				</div>
				<?php echo $image_html( (int) $showroom_assets['interior_detail'], $asset( 'owner-showroom/showroom-detail-web.jpg' ), esc_attr__( 'Showroom Arctic Spas v Moravanech', 'baspa' ) ); ?>
			</div>
		</section>

		<section class="f-showroom-split f-showroom-split--second">
			<div class="f-showroom-split__container a-container">
				<h2 class="screen-reader-text"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
				<?php echo $image_html( (int) $showroom_assets['exterior_detail'], $asset( 'owner-showroom/showroom-main-web.jpg' ), esc_attr__( 'Vstup do showroomu Arctic Spas v Moravanech', 'baspa' ), 'f-showroom-split__image--exterior' ); ?>
				<div class="f-showroom-split__copy" data-content-source="showroom-meta">
					<?php echo wp_kses_post( $secondary_content ); ?>
				</div>
			</div>
		</section>

	</main>

<?php
get_footer();
