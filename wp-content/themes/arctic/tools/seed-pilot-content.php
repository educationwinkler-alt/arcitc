<?php
/**
 * Seed pilot Arctic content for local/staging development.
 *
 * Run with:
 * wp eval-file wp-content/themes/arctic/tools/seed-pilot-content.php
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function arctic_seed_term( string $taxonomy, string $name, string $slug, int $parent = 0 ): int {
	$term = term_exists( $slug, $taxonomy );
	if ( $term && !is_wp_error( $term ) ) {
		return (int) $term['term_id'];
	}

	$created = wp_insert_term( $name, $taxonomy, array(
		'slug'   => $slug,
		'parent' => $parent,
	) );

	if ( is_wp_error( $created ) ) {
		throw new RuntimeException( $created->get_error_message() );
	}

	return (int) $created['term_id'];
}

function arctic_seed_attachment( string $seed_key, string $relative_path, string $title, string $alt = '' ): int {
	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_arctic_seed_key',
		'meta_value'     => $seed_key,
	) );

	if ( !empty( $existing ) ) {
		return (int) $existing[0];
	}

	$source = WP_CONTENT_DIR . '/' . ltrim( $relative_path, '/' );
	if ( !file_exists( $source ) ) {
		throw new RuntimeException( 'Missing seed asset: ' . $source );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( basename( $source ) );
	copy( $source, $tmp );

	$file_array = array(
		'name'     => basename( $source ),
		'tmp_name' => $tmp,
	);

	$attachment_id = media_handle_sideload( $file_array, 0, $title );
	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $tmp );
		throw new RuntimeException( $attachment_id->get_error_message() );
	}

	update_post_meta( $attachment_id, '_arctic_seed_key', $seed_key );

	if ( !empty( $alt ) ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
	}

	return (int) $attachment_id;
}

function arctic_seed_post_by_meta( string $post_type, string $meta_key, string $meta_value, array $post_args ): int {
	$existing = get_posts( array(
		'post_type'      => $post_type,
		'post_status'    => array( 'publish', 'draft', 'private' ),
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => $meta_key,
		'meta_value'     => $meta_value,
	) );

	$post_args['post_type'] = $post_type;

	if ( !empty( $existing ) ) {
		$post_args['ID'] = (int) $existing[0];
		$post_id         = wp_update_post( $post_args, true );
	} else {
		$post_id = wp_insert_post( $post_args, true );
	}

	if ( is_wp_error( $post_id ) ) {
		throw new RuntimeException( $post_id->get_error_message() );
	}

	update_post_meta( $post_id, $meta_key, $meta_value );

	return (int) $post_id;
}

function arctic_seed_set_multi_meta( int $post_id, string $key, array $values ): void {
	delete_post_meta( $post_id, $key );
	foreach ( $values as $value ) {
		if ( is_array( $value ) || '' !== trim( (string) $value ) ) {
			add_post_meta( $post_id, $key, $value );
		}
	}
}

function arctic_seed_page( string $slug, string $title, string $content, string $template = '' ): int {
	$page = get_page_by_path( $slug );
	$args = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
	);

	if ( $page ) {
		$args['ID'] = $page->ID;
		$page_id    = wp_update_post( $args, true );
	} else {
		$page_id = wp_insert_post( $args, true );
	}

	if ( is_wp_error( $page_id ) ) {
		throw new RuntimeException( $page_id->get_error_message() );
	}

	if ( !empty( $template ) ) {
		update_post_meta( (int) $page_id, '_wp_page_template', $template );
	}

	return (int) $page_id;
}

function arctic_seed_menu( string $name, string $location, array $items ): int {
	$menu = wp_get_nav_menu_object( $name );

	if ( !$menu ) {
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			throw new RuntimeException( $menu_id->get_error_message() );
		}
	} else {
		$menu_id       = (int) $menu->term_id;
		$current_items = wp_get_nav_menu_items( $menu_id );
		if ( !empty( $current_items ) ) {
			foreach ( $current_items as $item ) {
				wp_delete_post( $item->ID, true );
			}
		}
	}

	foreach ( $items as $item ) {
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'  => $item['title'],
			'menu-item-url'    => $item['url'],
			'menu-item-status' => 'publish',
			'menu-item-type'   => 'custom',
		) );
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	return $menu_id;
}

function arctic_seed_remove_default_content(): void {
	$default_posts = get_posts( array(
		'post_type'      => 'post',
		'post_status'    => array( 'publish', 'draft', 'private', 'trash' ),
		'posts_per_page' => -1,
		'name'           => 'hello-world',
		'fields'         => 'ids',
	) );

	foreach ( $default_posts as $post_id ) {
		if ( get_the_title( $post_id ) === 'Hello world!' ) {
			wp_delete_post( (int) $post_id, true );
		}
	}

	$sample_page = get_page_by_path( 'sample-page' );
	if ( $sample_page && $sample_page->post_title === 'Sample Page' ) {
		wp_delete_post( (int) $sample_page->ID, true );
	}
}

arctic_seed_remove_default_content();

$kind_hot_tubs = arctic_seed_term( 'product-kind', 'Virivky', 'virivky' );
$kind_swimspa  = arctic_seed_term( 'product-kind', 'Swimspa', 'swimspa' );
$kind_covers   = arctic_seed_term( 'product-kind', 'Automaticke kryty', 'automaticke-kryty' );
$kind_saunas   = arctic_seed_term( 'product-kind', 'Sauny', 'sauny' );
$kind_tubs     = arctic_seed_term( 'product-kind', 'Koupaci sudy', 'koupaci-sudy' );
$kind_furniture = arctic_seed_term( 'product-kind', 'Venkovni nabytek', 'venkovni-nabytek' );
$kind_accessories = arctic_seed_term( 'product-kind', 'Prislusenstvi', 'prislusenstvi' );
$kind_cold_plunge = arctic_seed_term( 'product-kind', 'Ochlazovaci bazenky', 'ochlazovaci-bazenky' );

$series_core   = arctic_seed_term( 'product-series', 'Core', 'core' );
$series_classic = arctic_seed_term( 'product-series', 'Classic', 'classic' );
$series_custom = arctic_seed_term( 'product-series', 'Custom', 'custom' );
$series_swimspa = arctic_seed_term( 'product-series', 'Swimspa', 'swimspa' );
$series_covana = arctic_seed_term( 'product-series', 'Covana', 'covana' );

$category_hot_tubs = arctic_seed_term( 'product-category', 'Virivky', 'virivky' );
$category_swimspa  = arctic_seed_term( 'product-category', 'Swimspa', 'swimspa' );
$category_covers   = arctic_seed_term( 'product-category', 'Dalsi sortiment', 'dalsi-sortiment' );

wp_update_term( $category_hot_tubs, 'product-category', array( 'name' => 'Vířivky' ) );
wp_update_term( $category_swimspa, 'product-category', array( 'name' => 'Celoroční bazény' ) );
wp_update_term( $category_covers, 'product-category', array( 'name' => 'Další sortiment' ) );

$download_preparation = arctic_seed_term( 'download-category', 'Stavebni pripravenost', 'stavebni-pripravenost' );
$download_manuals     = arctic_seed_term( 'download-category', 'Navody', 'navody' );
$download_dimensions  = arctic_seed_term( 'download-category', 'Rozmery', 'rozmery' );
$download_warranty    = arctic_seed_term( 'download-category', 'Zaruky', 'zaruky' );
$download_catalogs    = arctic_seed_term( 'download-category', 'Katalogy', 'katalogy' );
$download_water       = arctic_seed_term( 'download-category', 'Uprava vody', 'uprava-vody' );
$download_technical   = arctic_seed_term( 'download-category', 'Technicke dokumenty', 'technicke-dokumenty' );

$lunar_main   = arctic_seed_attachment( 'lunar-main', 'uploads/import/lunar-main.jpg', 'Lunar - Platinum Swirl', 'Virivka Arctic Spas Lunar v barve Platinum Swirl' );
$lunar_corner = arctic_seed_attachment( 'lunar-corner', 'uploads/import/lunar-corner.png', 'Lunar - rohova fotografie', 'Detail virivky Arctic Spas Lunar' );
$lunar_cover  = arctic_seed_attachment( 'lunar-cover-black', 'uploads/import/lunar-cover-black.png', 'Lunar - cerny termokryt', 'Virivka Lunar s cernym termokrytem' );
$timberwolf_signature = arctic_seed_attachment( 'timberwolf-signature', 'uploads/import/timberwolf-signature.jpg', 'Timberwolf - Signature', 'Virivka Arctic Spas Timberwolf Signature' );
$timberwolf_prestige  = arctic_seed_attachment( 'timberwolf-prestige', 'uploads/import/timberwolf-prestige.jpg', 'Timberwolf - Prestige', 'Virivka Arctic Spas Timberwolf Prestige' );
$timberwolf_side      = arctic_seed_attachment( 'timberwolf-side', 'uploads/import/timberwolf-side.jpg', 'Timberwolf - bocni pohled', 'Bocni pohled na virivku Timberwolf' );
$orion_main   = arctic_seed_attachment( 'orion-main', 'uploads/import/orion-main.jpg', 'Orion - Platinum Swirl', 'Virivka Arctic Spas Orion v barve Platinum Swirl' );
$orion_life   = arctic_seed_attachment( 'orion-lifestyle', 'uploads/import/orion-lifestyle.jpg', 'Orion - lifestyle', 'Virivka Orion v exterieru' );
$figma_hero   = arctic_seed_attachment( 'figma-node-1-15-hp-hero', 'uploads/import/figma/hp-hero-arctic-spas-07.jpg', 'Figma HP hero - Arctic Spas 07', 'Vířivka Arctic Spas v podzimní krajině' );
$figma_category_hero_virivky = arctic_seed_attachment( 'figma-node-1-263-category-hero-virivky', 'uploads/import/figma/category-hero-virivky.jpg', 'Figma kategorie - hero vířivky', 'Hero fotografie kategorie vířivek Arctic Spas' );
$category_hot_tubs_life = arctic_seed_attachment( 'figma-node-1-33-hp-category-virivky', 'uploads/import/figma/hp-category-virivky.jpg', 'Figma HP karta - venkovní vířivky', 'Venkovní vířivka Arctic Spas v zahradě' );
$category_swimspa_life = arctic_seed_attachment( 'figma-node-1-34-hp-category-celorocni-bazeny', 'uploads/import/figma/hp-category-celorocni-bazeny.png', 'Figma HP karta - celoroční bazény', 'Celoroční bazén Arctic v zahradě' );
$figma_category_vlastnosti = arctic_seed_attachment( 'figma-node-1-273-category-vlastnosti', 'uploads/import/figma/category-vlastnosti.jpg', 'Figma kategorie - vlastnosti', 'Fotografie k sekci vlastnosti vířivek Arctic Spas' );
$figma_category_zaruka = arctic_seed_attachment( 'figma-node-1-274-category-zaruka', 'uploads/import/figma/category-zaruka.jpg', 'Figma kategorie - záruka', 'Fotografie k sekci záruka Arctic Spas' );
$figma_configurator = arctic_seed_attachment( 'figma-node-1-409-category-configurator', 'uploads/import/figma/category-configurator.png', 'Figma konfigurátor', 'Konfigurátor vířivky Arctic Spas' );
$reference_hot_tubs_life = arctic_seed_attachment( 'figma-node-1-179-realizace-1', 'uploads/import/figma/realizace-1.jpg', 'Figma realizace 1', 'Realizace vířivky Arctic Spas' );
$figma_realizace_2 = arctic_seed_attachment( 'figma-node-1-187-realizace-2', 'uploads/import/figma/realizace-2.jpg', 'Figma realizace 2', 'Realizace vířivky Arctic Spas' );
$figma_realizace_3 = arctic_seed_attachment( 'figma-node-1-195-realizace-3', 'uploads/import/figma/realizace-3.jpg', 'Figma realizace 3', 'Realizace vířivky Arctic Spas' );
$showroom = arctic_seed_attachment( 'figma-node-1-123-showroom-1', 'uploads/import/figma/showroom-1.png', 'Figma showroom 1', 'Showroom Arctic Spas podle grafiky' );
$showroom_2 = arctic_seed_attachment( 'figma-node-1-124-showroom-2', 'uploads/import/figma/showroom-2.png', 'Figma showroom 2', 'Showroom Arctic Spas podle grafiky' );
$figma_showroom_3 = arctic_seed_attachment( 'figma-node-1-125-showroom-3', 'uploads/import/figma/showroom-3.png', 'Figma showroom 3', 'Showroom Arctic Spas podle grafiky' );
$figma_footer_map = arctic_seed_attachment( 'figma-node-1-242-footer-map', 'uploads/import/figma/footer-map.png', 'Figma footer mapa', 'Showroom Arctic Spas ve footeru' );
$figma_contact_map = arctic_seed_attachment( 'figma-node-1-1069-contact-map-showroom', 'uploads/import/figma/contact-map-showroom.png', 'Figma kontakt mapa/showroom', 'Kontaktní mapa a showroom podle grafiky' );
$figma_timberwolf_hero = arctic_seed_attachment( 'figma-node-1-1462-detail-timberwolf-hero', 'uploads/import/figma/detail-timberwolf-hero.jpg', 'Figma Timberwolf hero', 'Hero fotografie detailu vířivky Timberwolf' );
$figma_timberwolf_prestige = arctic_seed_attachment( 'figma-node-1-1472-detail-timberwolf-prestige', 'uploads/import/figma/detail-timberwolf-prestige.png', 'Figma Timberwolf Prestige', 'Konfigurace Timberwolf Prestige podle Figmy' );
$figma_timberwolf_signature = arctic_seed_attachment( 'figma-node-1-1474-detail-timberwolf-signature', 'uploads/import/figma/detail-timberwolf-signature.png', 'Figma Timberwolf Signature', 'Konfigurace Timberwolf Signature podle Figmy' );
$figma_color_dakota = arctic_seed_attachment( 'figma-node-1-1476-color-dakota', 'uploads/import/figma/color-dakota.png', 'Figma barva Dakota', 'Vzorek barvy Dakota podle Figmy' );
$figma_color_kalahari = arctic_seed_attachment( 'figma-node-1-1479-color-kalahari', 'uploads/import/figma/color-kalahari.png', 'Figma barva Kalahari', 'Vzorek barvy Kalahari podle Figmy' );
$figma_color_odyssey = arctic_seed_attachment( 'figma-node-1-1482-color-odyssey', 'uploads/import/figma/color-odyssey.png', 'Figma barva Odyssey', 'Vzorek barvy Odyssey podle Figmy' );
$figma_color_platinum = arctic_seed_attachment( 'figma-node-1-1485-color-platinum-swirl', 'uploads/import/figma/color-platinum-swirl.png', 'Figma barva Platinum Swirl', 'Vzorek barvy Platinum Swirl podle Figmy' );
$figma_color_espresso = arctic_seed_attachment( 'figma-node-1-1488-color-espresso', 'uploads/import/figma/color-espresso.png', 'Figma barva Espresso', 'Vzorek barvy Espresso podle Figmy' );
$figma_cabinet_cedar = arctic_seed_attachment( 'figma-node-1-1492-cabinet-cedar', 'uploads/import/figma/cabinet-cedar.png', 'Figma kabinet cedr', 'Vzorek cedrového kabinetu podle Figmy' );
$figma_cabinet_maintenance_free = arctic_seed_attachment( 'figma-node-1-1495-cabinet-maintenance-free', 'uploads/import/figma/cabinet-maintenance-free.png', 'Figma kabinet bezúdržbový', 'Vzorek bezúdržbového kabinetu podle Figmy' );
$covana_main  = arctic_seed_attachment( 'covana-slide-1', 'uploads/import/covana-slide-1.jpg', 'Covana automaticky kryt', 'Automaticky kryt virivky Covana' );
$sauna_main   = arctic_seed_attachment( 'other-sauna-luxus-1', 'uploads/import/other-sortiment/sauna-luxus-1.jpg', 'Luxusni sauna', 'Luxusni sauna na miru' );
$kirami_main  = arctic_seed_attachment( 'other-koupaci-sud-s1', 'uploads/import/other-sortiment/koupaci-sud-s1.jpg', 'Koupaci sud Kirami', 'Koupaci sud Kirami' );
$ikono_main   = arctic_seed_attachment( 'other-venkovni-nabytek-ikono', 'uploads/import/other-sortiment/venkovni-nabytek-ikono.jpg', 'Venkovni nabytek IKONO', 'Venkovni nabytek IKONO' );
$accessory_main = arctic_seed_attachment( 'other-doplnky-cedr', 'uploads/import/other-sortiment/doplnky-cedr.jpg', 'Cedrove doplnky', 'Cedrove doplnky k virivkam' );
$cold_plunge_main = arctic_seed_attachment( 'other-ochlazovaci-bazenek', 'uploads/import/other-sortiment/ochlazovaci-bazenek.jpg', 'Ochlazovaci bazenek', 'Ochlazovaci bazenek pro domaci wellness' );
$prep_pdf     = arctic_seed_attachment( 'stavebni-pripravenost-pdf', 'uploads/import/stavebni-pripravenost.pdf', 'Stavebni pripravenost Arctic Spas' );

foreach ( array(
	$sauna_main       => 'media-other-sauna-luxus-1',
	$kirami_main      => 'media-other-koupaci-sud-s1',
	$ikono_main       => 'media-other-venkovni-nabytek-ikono',
	$accessory_main   => 'media-other-doplnky-cedr',
	$cold_plunge_main => 'media-other-ochlazovaci-bazenek',
) as $attachment_id => $media_slug ) {
	wp_update_post( array(
		'ID'        => (int) $attachment_id,
		'post_name' => $media_slug,
	) );
}

update_term_meta( $category_hot_tubs, 'category_image', $category_hot_tubs_life );
update_term_meta( $category_hot_tubs, 'category_heading_image', $figma_category_hero_virivky );
update_term_meta( $category_hot_tubs, 'category_description_short', 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily.' );
update_term_meta( $category_hot_tubs, 'category_heading_title', 'Venkovní vířivky Arctic Spas' );
update_term_meta( $category_hot_tubs, 'category_heading_text', 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie.' );
update_term_meta( $category_swimspa, 'category_image', $category_swimspa_life );
update_term_meta( $category_swimspa, 'category_heading_image', $figma_hero );
update_term_meta( $category_swimspa, 'category_description_short', 'Rodinný bazén na zahradě je snem řady domácností. Celoroční bazény Arctic nahrazují klasický bazén a přidávají masážní komfort.' );
update_term_meta( $category_covers, 'category_image', $covana_main );
update_term_meta( $category_covers, 'category_description_short', 'Automaticke kryty, doplnky a navazujici sortiment pro pohodlnejsi provoz virivky.' );

$reference_location_kv = arctic_seed_term( 'reference-category', 'Karlovy Vary', 'karlovy-vary' );
$reference_year_2025   = arctic_seed_term( 'reference-category', '2025', '2025' );

foreach ( array(
	array( 'key' => 'figma-reference-1', 'image' => $reference_hot_tubs_life, 'order' => 10 ),
	array( 'key' => 'figma-reference-2', 'image' => $figma_realizace_2, 'order' => 20 ),
	array( 'key' => 'figma-reference-3', 'image' => $figma_realizace_3, 'order' => 30 ),
) as $reference_item ) {
	$reference_id = arctic_seed_post_by_meta( 'reference', '_arctic_seed_key', $reference_item['key'], array(
		'post_status'  => 'publish',
		'post_title'   => 'Venkovní vířivka Timberwolf',
		'post_name'    => $reference_item['key'],
		'post_content' => '',
		'menu_order'   => $reference_item['order'],
	) );

	set_post_thumbnail( $reference_id, $reference_item['image'] );
	update_post_meta( $reference_id, 'reference_single', 0 );
	update_post_meta( $reference_id, 'reference_location', 'Karlovy Vary' );
	update_post_meta( $reference_id, 'reference_year', '2025' );
	wp_set_post_terms( $reference_id, array( $reference_location_kv, $reference_year_2025 ), 'reference-category' );
}

update_option( 'baspa_references_title', 'Ukázky realizací' );

$lunar_id = arctic_seed_post_by_meta( 'product', 'product_original_url', 'https://www.arctic-spas.cz/virivka-lunar.php', array(
	'post_status'  => 'publish',
	'post_title'   => 'Lunar',
	'post_name'    => 'lunar',
	'post_content' => '<!-- wp:paragraph --><p>Model Lunar z rady Core je nova kanadska virivka pro rok 2025. Kombinuje jedno lehatko, ctyri sedadla a dve konfigurace vybavy pro pohodlnou rodinnou relaxaci.</p><!-- /wp:paragraph -->',
	'menu_order'   => 10,
) );

update_post_meta( $lunar_id, 'product_type', 'standard' );
update_post_meta( $lunar_id, 'product_title_short', 'Lunar' );
update_post_meta( $lunar_id, 'product_price_text', 'od 249 000 Kc' );
update_post_meta( $lunar_id, 'product_price_suffix', 'vc. standardni vybavy' );
update_post_meta( $lunar_id, 'product_description_short', 'Nova virivka Core pro rok 2025 s jednim lehatkem a ctyrmi sedadly.' );
update_post_meta( $lunar_id, 'product_description', 'Lunar je novy model rady Core vyrabeny v Kanade. V konfiguraci Prestige nabizi 20 trysek a jedno cerpadlo, v konfiguraci Signature 40 trysek a dve cerpadla.' );
update_post_meta( $lunar_id, 'product_cta_text', 'Poptat Lunar' );
set_post_thumbnail( $lunar_id, $lunar_main );
arctic_seed_set_multi_meta( $lunar_id, 'product_image', array( $lunar_main ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_images', array( $lunar_main, $lunar_corner, $lunar_cover ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_model', array( 'Core Lunar' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_seats', array( '1 lehatko + 4 sedadla' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_nozzles', array( 'Prestige: 20 trysek / 1 cerpadlo', 'Signature: 40 trysek / 2 cerpadla' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_dimensions_external', array( '212 × 213 cm, výška: 99 cm' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_acrylic_colors', array( 'Platinum Swirl', 'Espresso', 'Kalahari', 'Dakota' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_configurations', array(
	array(
		'name'        => 'Prestige',
		'price'       => '249 000 Kc',
		'seats'       => '1 lehatko + 4 sedadla',
		'jets'        => '20',
		'pumps'       => '1',
		'dimensions'  => '212 x 213 x 99 cm',
		'description' => 'Zakladni konfigurace rady Core.',
	),
	array(
		'name'        => 'Signature',
		'price'       => '279 000 Kc',
		'seats'       => '1 lehatko + 4 sedadla',
		'jets'        => '40',
		'pumps'       => '2',
		'dimensions'  => '212 x 213 x 99 cm',
		'description' => 'Silnejsi masazni konfigurace s dvojnasobnym poctem trysek.',
	),
) );
wp_set_object_terms( $lunar_id, array( $kind_hot_tubs ), 'product-kind' );
wp_set_object_terms( $lunar_id, array( $series_core ), 'product-series' );
wp_set_object_terms( $lunar_id, array( $category_hot_tubs ), 'product-category' );

$orion_id = arctic_seed_post_by_meta( 'product', 'product_original_url', 'https://www.arctic-spas.cz/virivka-orion.php', array(
	'post_status'  => 'publish',
	'post_title'   => 'Orion',
	'post_name'    => 'orion',
	'post_content' => '<!-- wp:paragraph --><p>Orion je novy model rady Core pro sest osob. Sdili technicky zaklad s modelem Lunar, ale misto lehatka nabizi sedaci dispozici.</p><!-- /wp:paragraph -->',
	'menu_order'   => 20,
) );

update_post_meta( $orion_id, 'product_type', 'standard' );
update_post_meta( $orion_id, 'product_title_short', 'Orion' );
update_post_meta( $orion_id, 'product_price_text', 'od 249 000 Kc' );
update_post_meta( $orion_id, 'product_description_short', 'Nova virivka Core pro sest osob.' );
update_post_meta( $orion_id, 'product_description', 'Orion je novy kanadsky model pro rok 2025. Nabizi sest sedadel a konfigurace Prestige nebo Signature.' );
set_post_thumbnail( $orion_id, $orion_main );
arctic_seed_set_multi_meta( $orion_id, 'product_image', array( $orion_main ) );
arctic_seed_set_multi_meta( $orion_id, 'product_images', array( $orion_main, $orion_life ) );
arctic_seed_set_multi_meta( $orion_id, 'product_model', array( 'Core Orion' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_seats', array( '6 sedadel' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_nozzles', array( 'Prestige: 20 trysek / 1 cerpadlo', 'Signature: 40 trysek / 2 cerpadla' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_dimensions_external', array( '212 × 213 cm, výška: 99 cm' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_acrylic_colors', array( 'Platinum Swirl', 'Espresso', 'Kalahari', 'Dakota' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_configurations', array(
	array(
		'name'        => 'Prestige',
		'price'       => '249 000 Kc',
		'seats'       => '6 sedadel',
		'jets'        => '20',
		'pumps'       => '1',
		'dimensions'  => '212 x 213 x 99 cm',
		'description' => 'Zakladni konfigurace rady Core pro sest osob.',
	),
	array(
		'name'        => 'Signature',
		'price'       => '279 000 Kc',
		'seats'       => '6 sedadel',
		'jets'        => '40',
		'pumps'       => '2',
		'dimensions'  => '212 x 213 x 99 cm',
		'description' => 'Silnejsi masazni konfigurace se 40 tryskami.',
	),
) );
wp_set_object_terms( $orion_id, array( $kind_hot_tubs ), 'product-kind' );
wp_set_object_terms( $orion_id, array( $series_core ), 'product-series' );
wp_set_object_terms( $orion_id, array( $category_hot_tubs ), 'product-category' );

$covana_id = arctic_seed_post_by_meta( 'product', 'product_original_url', 'https://www.arctic-spas.cz/covana.php', array(
	'post_status'  => 'publish',
	'post_title'   => 'Covana',
	'post_name'    => 'covana',
	'post_content' => '<!-- wp:paragraph --><p>Covana je automaticky kryt virivky, ktery kombinuje funkci termokrytu, zvedaku a elegantniho pristresku.</p><!-- /wp:paragraph -->',
	'menu_order'   => 100,
) );

update_post_meta( $covana_id, 'product_type', 'landing_section' );
update_post_meta( $covana_id, 'product_title_short', 'Covana' );
update_post_meta( $covana_id, 'product_description_short', 'Automaticky kryt, zvedak a altanek pro virivku v jednom.' );
update_post_meta( $covana_id, 'product_description', 'Covana chrani virivku, zjednodusuje manipulaci s krytem a vytvari pohodlne zastreseni pro kazdodenni pouzivani.' );
update_post_meta( $covana_id, 'product_cta_text', 'Zjistit vice o Covana' );
set_post_thumbnail( $covana_id, $covana_main );
arctic_seed_set_multi_meta( $covana_id, 'product_image', array( $covana_main ) );
arctic_seed_set_multi_meta( $covana_id, 'product_images', array( $covana_main ) );
wp_set_object_terms( $covana_id, array( $kind_covers ), 'product-kind' );
wp_set_object_terms( $covana_id, array( $series_covana ), 'product-series' );
wp_set_object_terms( $covana_id, array( $category_covers ), 'product-category' );

$other_sortiment_products = array(
	array(
		'original_url' => 'https://www.arctic-spas.cz/sauny.php',
		'slug'         => 'sauny',
		'name'         => 'Luxusni sauny',
		'image'        => $sauna_main,
		'kind'         => $kind_saunas,
		'description'  => 'Luxusni sauny na miru, finske sauny a infrakabiny jako navazujici wellness sortiment.',
	),
	array(
		'original_url' => 'https://www.arctic-spas.cz/koupaci-sudy-kirami.php',
		'slug'         => 'koupaci-sudy-kirami',
		'name'         => 'Koupaci sudy Kirami',
		'image'        => $kirami_main,
		'kind'         => $kind_tubs,
		'description'  => 'Venkovni koupaci sudy od finskeho vyrobce Kirami pro relaxaci s ohrevem drevem.',
	),
	array(
		'original_url' => 'https://www.arctic-spas.cz/prislusenstvi-doplnky.php',
		'slug'         => 'prislusenstvi-a-doplnky',
		'name'         => 'Prislusenstvi a doplnky',
		'image'        => $accessory_main,
		'kind'         => $kind_accessories,
		'description'  => 'Prakticke prislusenstvi, schody, kryty, cedrove doplnky a vybaveni pro pohodlnejsi pouzivani virivky.',
	),
	array(
		'original_url' => 'https://www.nabytek-ikono.cz/',
		'slug'         => 'ikono-nabytek',
		'name'         => 'IKONO nabytek',
		'image'        => $ikono_main,
		'kind'         => $kind_furniture,
		'description'  => 'Venkovni nabytek IKONO jako doplnek showroomu a zahradniho wellness prostoru.',
	),
	array(
		'original_url' => 'https://www.arctic-spas.cz/ochlazovaci-bazenek.php',
		'slug'         => 'ochlazovaci-bazenek',
		'name'         => 'Ochlazovaci bazenek',
		'image'        => $cold_plunge_main,
		'kind'         => $kind_cold_plunge,
		'description'  => 'Ochlazovaci bazenek pro kontrastni terapii a doplneni domaciho wellness.',
	),
);

foreach ( $other_sortiment_products as $index => $product ) {
	$product_id = arctic_seed_post_by_meta( 'product', 'product_original_url', $product['original_url'], array(
		'post_status'  => 'publish',
		'post_title'   => $product['name'],
		'post_name'    => $product['slug'],
		'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $product['description'] ) . '</p><!-- /wp:paragraph -->',
		'menu_order'   => 110 + $index,
	) );
	wp_update_post( array(
		'ID'        => $product_id,
		'post_name' => $product['slug'],
	) );

	update_post_meta( $product_id, 'product_type', 'landing_section' );
	update_post_meta( $product_id, 'product_title_short', $product['name'] );
	update_post_meta( $product_id, 'product_price_text', 'na dotaz' );
	update_post_meta( $product_id, 'product_description_short', $product['description'] );
	update_post_meta( $product_id, 'product_description', $product['description'] );
	update_post_meta( $product_id, 'product_cta_text', 'Poptat ' . $product['name'] );
	set_post_thumbnail( $product_id, $product['image'] );
	arctic_seed_set_multi_meta( $product_id, 'product_image', array( $product['image'] ) );
	arctic_seed_set_multi_meta( $product_id, 'product_images', array( $product['image'] ) );
	wp_set_object_terms( $product_id, array( $product['kind'] ), 'product-kind' );
	wp_set_object_terms( $product_id, array( $category_covers ), 'product-category' );
}

$legacy_products = array(
	array( 'source_slug' => 'virivka-summit-xl', 'slug' => 'summit-xl', 'name' => 'Summit XL', 'type' => 'hot_tub', 'series' => $series_custom, 'dimensions' => '293 × 235 cm, výška: 98 cm' ),
	array( 'source_slug' => 'virivka-summit', 'slug' => 'summit', 'name' => 'Summit', 'type' => 'hot_tub', 'series' => $series_custom, 'dimensions' => '235 × 235 cm, výška: 98 cm', 'badge' => 'TOP' ),
	array( 'source_slug' => 'virivka-tundra', 'slug' => 'tundra', 'name' => 'Tundra', 'type' => 'hot_tub', 'series' => $series_custom, 'dimensions' => '235 × 235 cm, výška: 98 cm', 'badge' => 'TOP' ),
	array( 'source_slug' => 'virivka-kodiak', 'slug' => 'kodiak', 'name' => 'Kodiak', 'type' => 'hot_tub', 'series' => $series_custom, 'dimensions' => '235 × 235 cm, výška: 98 cm' ),
	array( 'source_slug' => 'virivka-klondiker', 'slug' => 'klondiker', 'name' => 'Klondiker', 'type' => 'hot_tub', 'series' => $series_custom, 'dimensions' => '235 × 235 cm, výška: 98 cm' ),
	array( 'source_slug' => 'virivka-yukon', 'slug' => 'yukon', 'name' => 'Yukon', 'type' => 'hot_tub', 'series' => $series_custom, 'dimensions' => '217 × 217 cm, výška: 104 cm', 'badge' => 'TOP' ),
	array( 'source_slug' => 'virivka-cub', 'slug' => 'cub', 'name' => 'Cub', 'type' => 'hot_tub', 'series' => $series_custom, 'dimensions' => '217 × 217 cm, výška: 104 cm', 'badge' => 'TOP' ),
	array( 'source_slug' => 'virivka-fox', 'slug' => 'fox', 'name' => 'Arctic Fox', 'type' => 'hot_tub', 'series' => $series_custom, 'dimensions' => '176 × 217 cm, výška: 98 cm', 'badge' => 'TOP' ),
	array( 'source_slug' => 'virivka-mckinley', 'slug' => 'mckinley', 'name' => 'McKinley', 'type' => 'hot_tub', 'series' => $series_classic, 'dimensions' => '235 × 235 cm, výška: 98 cm' ),
	array( 'source_slug' => 'virivka-mustang', 'slug' => 'mustang', 'name' => 'Mustang', 'type' => 'hot_tub', 'series' => $series_classic, 'dimensions' => '235 × 235 cm, výška: 98 cm' ),
	array( 'source_slug' => 'virivka-totem', 'slug' => 'totem', 'name' => 'Totem', 'type' => 'hot_tub', 'series' => $series_classic, 'dimensions' => '217 × 217 cm, výška: 104 cm' ),
	array( 'source_slug' => 'virivka-eagle', 'slug' => 'eagle', 'name' => 'Eagle', 'type' => 'hot_tub', 'series' => $series_classic, 'dimensions' => '217 × 217 cm, výška: 104 cm' ),
	array( 'source_slug' => 'virivka-timberwolf', 'slug' => 'timberwolf', 'name' => 'Timberwolf', 'type' => 'hot_tub', 'series' => $series_classic, 'dimensions' => '174 × 217 cm, výška: 98 cm' ),
	array( 'source_slug' => 'virivka-husky', 'slug' => 'husky', 'name' => 'Husky', 'type' => 'hot_tub', 'series' => $series_core, 'price' => 'od 209 000 Kc', 'description' => 'Kompaktni virivka rady Core s peti sedadly, 20 tryskami a jednim cerpadlem.', 'dimensions' => '213 × 186 cm, výška: 99 cm' ),
	array( 'source_slug' => 'bazen-athabascan', 'slug' => 'athabascan', 'name' => 'Athabascan', 'type' => 'swimspa', 'series' => $series_swimspa ),
	array( 'source_slug' => 'bazen-hudson', 'slug' => 'hudson', 'name' => 'Hudson', 'type' => 'swimspa', 'series' => $series_swimspa ),
	array( 'source_slug' => 'bazen-kingfisher', 'slug' => 'kingfisher', 'name' => 'Kingfisher', 'type' => 'swimspa', 'series' => $series_swimspa ),
	array( 'source_slug' => 'bazen-ocean', 'slug' => 'ocean', 'name' => 'Arctic Ocean', 'type' => 'swimspa', 'series' => $series_swimspa ),
	array( 'source_slug' => 'bazen-okanagan', 'slug' => 'okanagan', 'name' => 'Okanagan', 'type' => 'swimspa', 'series' => $series_swimspa ),
	array( 'source_slug' => 'bazen-wolverine', 'slug' => 'wolverine', 'name' => 'Wolverine', 'type' => 'swimspa', 'series' => $series_swimspa ),
);

foreach ( $legacy_products as $index => $product ) {
	$is_swimspa      = 'swimspa' === $product['type'];
	$original_url    = 'https://www.arctic-spas.cz/' . $product['source_slug'] . '.php';
	$image_id        = arctic_seed_attachment(
		'legacy-' . $product['source_slug'],
		'uploads/import/legacy-products/' . $product['source_slug'] . '.jpg',
		$product['name'],
		( $is_swimspa ? 'Swimspa ' : 'Virivka ' ) . $product['name']
	);
	wp_update_post( array(
		'ID'        => $image_id,
		'post_name' => 'media-' . $product['source_slug'],
	) );

	$default_content = $product['description'] ?? ( $is_swimspa
		? 'Swimspa ' . $product['name'] . ' bude doplnena podle aktualniho obsahu live webu a klientskych podkladu.'
		: 'Virivka ' . $product['name'] . ' bude doplnena podle aktualniho obsahu live webu a klientskych podkladu.' );
	$order_offset    = $is_swimspa ? 200 : 40;

	$product_id = arctic_seed_post_by_meta( 'product', 'product_original_url', $original_url, array(
		'post_status'  => 'publish',
		'post_title'   => $product['name'],
		'post_name'    => $product['slug'],
		'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $default_content ) . '</p><!-- /wp:paragraph -->',
		'menu_order'   => $order_offset + $index,
	) );
	wp_update_post( array(
		'ID'        => $product_id,
		'post_name' => $product['slug'],
	) );

	update_post_meta( $product_id, 'product_type', 'standard' );
	update_post_meta( $product_id, 'product_title_short', $product['name'] );
	update_post_meta( $product_id, 'product_price_text', $product['price'] ?? '' );
	update_post_meta( $product_id, 'product_description_short', $default_content );
	update_post_meta( $product_id, 'product_description', $default_content );
	update_post_meta( $product_id, 'product_cta_text', 'Poptat ' . $product['name'] );
	set_post_thumbnail( $product_id, $image_id );
	arctic_seed_set_multi_meta( $product_id, 'product_image', array( $image_id ) );
	arctic_seed_set_multi_meta( $product_id, 'product_images', array( $image_id ) );
	arctic_seed_set_multi_meta( $product_id, 'product_dimensions_external', array( $product['dimensions'] ?? '' ) );
	if ( !empty( $product['badge'] ) ) {
		update_post_meta( $product_id, 'product_badge', $product['badge'] );
	} else {
		delete_post_meta( $product_id, 'product_badge' );
	}

	if ( $is_swimspa ) {
		wp_set_object_terms( $product_id, array( $kind_swimspa ), 'product-kind' );
		wp_set_object_terms( $product_id, array( $series_swimspa ), 'product-series' );
		wp_set_object_terms( $product_id, array( $category_swimspa ), 'product-category' );
	} else {
		wp_set_object_terms( $product_id, array( $kind_hot_tubs ), 'product-kind' );
		wp_set_object_terms( $product_id, array( $product['series'] ), 'product-series' );
		wp_set_object_terms( $product_id, array( $category_hot_tubs ), 'product-category' );
	}
}

$timberwolf_description = 'Maximalni terapii naleznete ve dvou pohodlnych lehatkach, navic je zde misto i pro tretiho. Maximalni komfort na minimalnim prostoru.';
$timberwolf_id          = arctic_seed_post_by_meta( 'product', 'product_original_url', 'https://www.arctic-spas.cz/virivka-timberwolf.php', array(
	'post_status'  => 'publish',
	'post_title'   => 'Timberwolf',
	'post_name'    => 'timberwolf',
	'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $timberwolf_description ) . '</p><!-- /wp:paragraph -->',
	'menu_order'   => 50,
) );

update_post_meta( $timberwolf_id, 'product_type', 'standard' );
update_post_meta( $timberwolf_id, 'product_title_short', 'Timberwolf' );
update_post_meta( $timberwolf_id, 'product_price_text', 'od 246 700 Kc' );
update_post_meta( $timberwolf_id, 'product_price_suffix', 'vc. montaze' );
update_post_meta( $timberwolf_id, 'product_description_short', 'Kompaktni virivka rady Classic se dvema pohodlnymi lehatky a mistem pro treti osobu.' );
update_post_meta( $timberwolf_id, 'product_description', $timberwolf_description );
update_post_meta( $timberwolf_id, 'product_cta_text', 'Poptat Timberwolf' );
set_post_thumbnail( $timberwolf_id, $timberwolf_signature );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_image', array( $timberwolf_signature ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_images', array( $figma_timberwolf_hero, $figma_timberwolf_prestige, $figma_timberwolf_signature, $timberwolf_signature, $timberwolf_side, $timberwolf_prestige ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_model', array( 'Classic Timberwolf' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_seats', array( '3 osoby' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_nozzles', array( 'Prestige 15/1', 'Signature 30/2' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_dimensions_external', array( '174 × 217 cm, výška: 98 cm' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_water_volume', array( '884 litru' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_acrylic_colors', array( 'Dakota', 'Kalahari', 'Odyssey', 'Platinum Swirl', 'Espresso' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_cabinet_colors', array( 'Cedrovy kabinet standardni', 'Bezudrzbovy kabinet volitelny' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_acrylic_color_options', array(
	array( 'name' => 'Dakota', 'image' => $figma_color_dakota ),
	array( 'name' => 'Kalahari', 'image' => $figma_color_kalahari ),
	array( 'name' => 'Odyssey', 'image' => $figma_color_odyssey ),
	array( 'name' => 'Platinum Swirl', 'image' => $figma_color_platinum ),
	array( 'name' => 'Espresso', 'image' => $figma_color_espresso ),
) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_cabinet_color_options', array(
	array( 'name' => 'Cedrový kabinet', 'image' => $figma_cabinet_cedar ),
	array( 'name' => 'Bezúdržbový kabinet', 'image' => $figma_cabinet_maintenance_free ),
) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_configurations', array(
	array(
		'name'        => 'Prestige 15/1',
		'image'       => $figma_timberwolf_prestige,
		'price'       => 'od 246 700 Kc',
		'seats'       => '3 osoby',
		'jets'        => '10 x 3" + 5 x 5"',
		'pumps'       => '1 dvourychlostni',
		'dimensions'  => '217 x 174 x 98 cm',
		'description' => 'Zakladni konfigurace s 15 tryskami a jednim dvourychlostnim cerpadlem.',
	),
	array(
		'name'        => 'Signature 30/2',
		'image'       => $figma_timberwolf_signature,
		'price'       => 'na dotaz',
		'seats'       => '3 osoby',
		'jets'        => '25 x 3" + 5 x 5"',
		'pumps'       => '1 dvourychlostni + 1 jednorychlostni',
		'dimensions'  => '217 x 174 x 98 cm',
		'description' => 'Silnejsi konfigurace podle puvodniho webu Arctic Spas s 30 tryskami a dvema cerpadly.',
	),
) );
wp_set_object_terms( $timberwolf_id, array( $kind_hot_tubs ), 'product-kind' );
wp_set_object_terms( $timberwolf_id, array( $series_classic ), 'product-series' );
wp_set_object_terms( $timberwolf_id, array( $category_hot_tubs ), 'product-category' );

$download_id = arctic_seed_post_by_meta( 'download', 'download_original_url', 'https://www.arctic-spas.cz/content/download/stavebni-pripravenost.pdf', array(
	'post_status'  => 'publish',
	'post_title'   => 'Stavebni pripravenost',
	'post_name'    => 'stavebni-pripravenost',
	'post_content' => '<!-- wp:paragraph --><p>Technicky dokument pro pripravu mista pred instalaci virivky Arctic Spas.</p><!-- /wp:paragraph -->',
	'menu_order'   => 10,
) );

update_post_meta( $download_id, 'download_file_url', wp_get_attachment_url( $prep_pdf ) );
update_post_meta( $download_id, 'download_document_type', 'preparation' );
wp_set_object_terms( $download_id, array( $download_preparation ), 'download-category' );

$download_definitions = array(
	array( 'path' => 'content/download/2020/navod-arctic-classic-2020.pdf', 'file' => 'download__2020__navod-arctic-classic-2020.pdf', 'title' => 'Navod Arctic Classic 2020', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/2020/navod-arctic-core-2020.pdf', 'file' => 'download__2020__navod-arctic-core-2020.pdf', 'title' => 'Navod Arctic Core 2020', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/2020/navod-arctic-custom-2020.pdf', 'file' => 'download__2020__navod-arctic-custom-2020.pdf', 'title' => 'Navod Arctic Custom 2020', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/AS-Brochure-2022_cz.pdf', 'file' => 'download__AS-Brochure-2022_cz.pdf', 'title' => 'Arctic Spas katalog 2022', 'term' => $download_catalogs, 'type' => 'catalog' ),
	array( 'path' => 'content/download/economy-mode.pdf', 'file' => 'download__economy-mode.pdf', 'title' => 'Economy mode', 'term' => $download_technical, 'type' => 'technical' ),
	array( 'path' => 'content/download/equipment-location.pdf', 'file' => 'download__equipment-location.pdf', 'title' => 'Equipment location', 'term' => $download_technical, 'type' => 'technical' ),
	array( 'path' => 'content/download/navod-arctic-2010.pdf', 'file' => 'download__navod-arctic-2010.pdf', 'title' => 'Navod Arctic 2010', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-arctic-2015.pdf', 'file' => 'download__navod-arctic-2015.pdf', 'title' => 'Navod Arctic 2015', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-arctic-2018.pdf', 'file' => 'download__navod-arctic-2018.pdf', 'title' => 'Navod Arctic 2018', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-arctic-2020-en.pdf', 'file' => 'download__navod-arctic-2020-en.pdf', 'title' => 'Arctic manual 2020 EN', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-coyote.pdf', 'file' => 'download__navod-coyote.pdf', 'title' => 'Navod Coyote', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-onzen.pdf', 'file' => 'download__navod-onzen.pdf', 'title' => 'Navod Onzen', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-spaboy-2017.pdf', 'file' => 'download__navod-spaboy-2017.pdf', 'title' => 'Navod Spa Boy 2017', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-veslarsky-trenazer.pdf', 'file' => 'download__navod-veslarsky-trenazer.pdf', 'title' => 'Navod veslarsky trenazer', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/rozmery/rozmery-7-in.pdf', 'file' => 'download__rozmery__rozmery-7-in.pdf', 'title' => 'Rozmery 7 ft', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-8-in.pdf', 'file' => 'download__rozmery__rozmery-8-in.pdf', 'title' => 'Rozmery 8 ft', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-fox-in.pdf', 'file' => 'download__rozmery__rozmery-fox-in.pdf', 'title' => 'Rozmery Arctic Fox', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-frontier-in.pdf', 'file' => 'download__rozmery__rozmery-frontier-in.pdf', 'title' => 'Rozmery Frontier', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-ocean-in.pdf', 'file' => 'download__rozmery__rozmery-ocean-in.pdf', 'title' => 'Rozmery Arctic Ocean', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-summitxl-in.pdf', 'file' => 'download__rozmery__rozmery-summitxl-in.pdf', 'title' => 'Rozmery Summit XL', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-timberwolf-in.pdf', 'file' => 'download__rozmery__rozmery-timberwolf-in.pdf', 'title' => 'Rozmery Timberwolf', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/stavebni-pripravenost.pdf', 'file' => 'download__stavebni-pripravenost.pdf', 'title' => 'Stavebni pripravenost', 'term' => $download_preparation, 'type' => 'preparation' ),
	array( 'path' => 'content/download/zaklady-upravy-vody.pdf', 'file' => 'download__zaklady-upravy-vody.pdf', 'title' => 'Zaklady upravy vody', 'term' => $download_water, 'type' => 'water' ),
	array( 'path' => 'content/download/zaruky/zaruky-arctic.pdf', 'file' => 'download__zaruky__zaruky-arctic.pdf', 'title' => 'Zaruky Arctic Spas', 'term' => $download_warranty, 'type' => 'warranty' ),
	array( 'path' => 'content/download/zaruky/zaruky-bear.pdf', 'file' => 'download__zaruky__zaruky-bear.pdf', 'title' => 'Zaruky Bear', 'term' => $download_warranty, 'type' => 'warranty' ),
	array( 'path' => 'content/img/vybava/onspa/moznosti-pripojeni-do-site.pdf', 'file' => 'img__vybava__onspa__moznosti-pripojeni-do-site.pdf', 'title' => 'Moznosti pripojeni do site', 'term' => $download_technical, 'type' => 'technical' ),
);

foreach ( $download_definitions as $index => $download ) {
	$attachment_id = arctic_seed_attachment(
		'download-' . sanitize_title( $download['file'] ),
		'uploads/import/downloads/' . $download['file'],
		$download['title']
	);
	wp_update_post( array(
		'ID'        => $attachment_id,
		'post_name' => 'media-' . sanitize_title( $download['file'] ),
	) );

	$download_post_id = arctic_seed_post_by_meta( 'download', 'download_original_url', 'https://www.arctic-spas.cz/' . $download['path'], array(
		'post_status'  => 'publish',
		'post_title'   => $download['title'],
		'post_name'    => sanitize_title( $download['title'] ),
		'post_content' => '<!-- wp:paragraph --><p>Dokument importovany z puvodniho webu Arctic Spas.</p><!-- /wp:paragraph -->',
		'menu_order'   => 20 + $index,
	) );
	wp_update_post( array(
		'ID'        => $download_post_id,
		'post_name' => sanitize_title( $download['title'] ),
	) );
	update_post_meta( $download_post_id, 'download_file_url', wp_get_attachment_url( $attachment_id ) );
	update_post_meta( $download_post_id, 'download_document_type', $download['type'] );
	wp_set_object_terms( $download_post_id, array( $download['term'] ), 'download-category' );
}

$slide_hero_id = arctic_seed_post_by_meta( 'slide', '_arctic_seed_key', 'home-hero-arctic', array(
	'post_status'  => 'publish',
	'post_title'   => 'Kanadské luxusní vířivky',
	'post_content' => '<!-- wp:paragraph --><p>Vířivky Arctic jsou vyrobeny tak, aby efektivně pracovaly v extrémních severských podmínkách a přinesly vám tak garanci nejnižších nákladů, dlouhé životnosti a pohodovou relaxaci, ať se nacházíte kdekoliv.</p><!-- /wp:paragraph -->',
	'menu_order'   => 10,
) );
set_post_thumbnail( $slide_hero_id, $figma_hero );
delete_post_meta( $slide_hero_id, 'button_text' );
delete_post_meta( $slide_hero_id, 'button_url_category' );
delete_post_meta( $slide_hero_id, 'button_url_post' );
delete_post_meta( $slide_hero_id, 'button_url' );

$slide_lunar_id = arctic_seed_post_by_meta( 'slide', '_arctic_seed_key', 'home-hero-lunar', array(
	'post_status'  => 'publish',
	'post_title'   => 'Lunar a Orion 2025',
	'post_content' => '<!-- wp:paragraph --><p>Nove modely rady Core s konfiguraci Prestige nebo Signature a cenou od 249 000 Kc.</p><!-- /wp:paragraph -->',
	'menu_order'   => 20,
) );
set_post_thumbnail( $slide_lunar_id, $lunar_cover );
update_post_meta( $slide_lunar_id, 'button_text', 'Prohlednout Lunar' );
update_post_meta( $slide_lunar_id, 'button_url_post', $lunar_id );

$slide_showroom_id = arctic_seed_post_by_meta( 'slide', '_arctic_seed_key', 'home-hero-showroom', array(
	'post_status'  => 'publish',
	'post_title'   => 'Showroom u Brna',
	'post_content' => '<!-- wp:paragraph --><p>Prijedte si virivku osahat, porovnat vybavu a projit technickou pripravu s konzultantem.</p><!-- /wp:paragraph -->',
	'menu_order'   => 30,
) );
set_post_thumbnail( $slide_showroom_id, $showroom );
update_post_meta( $slide_showroom_id, 'button_text', 'Navstivit showroom' );

$hot_tubs_url = get_term_link( $category_hot_tubs, 'product-category' );
$covers_url   = get_term_link( $category_covers, 'product-category' );
$swimspa_url  = get_term_link( $category_swimspa, 'product-category' );
$hot_tubs_url = is_wp_error( $hot_tubs_url ) ? home_url( '/catalog/virivky/' ) : $hot_tubs_url;
$covers_url   = is_wp_error( $covers_url ) ? home_url( '/catalog/dalsi-sortiment/' ) : $covers_url;
$swimspa_url  = is_wp_error( $swimspa_url ) ? home_url( '/catalog/swimspa/' ) : $swimspa_url;

$home_id = arctic_seed_page(
	'uvod',
	'Uvod',
	'<!-- wp:heading {"textAlign":"center"} --><h2 class="wp-block-heading has-text-align-center">Jsme výhradní prodejce</h2><!-- /wp:heading -->'
	. '<!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">Rádi vám pomůžeme při výběru bazénu nebo vířivky. Jsme vám k dispozici, ať už máte přesnou představu nebo se myšlenkami na bazén či vířivku teprve začínáte zabývat.</p><!-- /wp:paragraph -->'
	. '<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="' . esc_url( home_url( '/o-nas/' ) ) . '">Více o nás</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
	'template-homepage.php'
);

$hot_tubs_page_id = arctic_seed_page(
	'virivky',
	'Vířivky Arctic Spas',
	'<!-- wp:paragraph --><p>Výběr kanadských vířivek Arctic Spas je postavený pro celoroční provoz, nízké provozní náklady a pohodlnou údržbu. Aktivní produkty v novém webu zůstávají v katalogu, vyřazené modely se řeší přes redirecty.</p><!-- /wp:paragraph -->'
);
update_post_meta( $hot_tubs_page_id, 'page_product_category', $category_hot_tubs );

$swimspa_id = arctic_seed_page(
	'swimspa',
	'Celoroční bazény',
	'<!-- wp:paragraph --><p>Celoroční bazény a swimspa budou převzaty z aktuálního obsahu Arctic Spas a napojeny na stejný produktový model jako vířivky.</p><!-- /wp:paragraph -->'
);
update_post_meta( $swimspa_id, 'page_product_category', $category_swimspa );

$showroom_id = arctic_seed_page(
	'showroom',
	'Showroom',
	'<!-- wp:paragraph --><p>Vzorkova prodejna slouzi pro osobni vyber virivky, konzultaci technicke pripravy a srovnani vybavy.</p><!-- /wp:paragraph -->'
	. '<!-- wp:image {"id":' . (int) $showroom . ',"sizeSlug":"large"} --><figure class="wp-block-image size-large">' . wp_get_attachment_image( $showroom, 'large' ) . '</figure><!-- /wp:image -->'
	. '<!-- wp:image {"id":' . (int) $showroom_2 . ',"sizeSlug":"large"} --><figure class="wp-block-image size-large">' . wp_get_attachment_image( $showroom_2, 'large' ) . '</figure><!-- /wp:image -->',
	'template-showroom.php'
);
set_post_thumbnail( $showroom_id, $showroom );
update_post_meta( $slide_showroom_id, 'button_url', get_permalink( $showroom_id ) );

$support_id = arctic_seed_page(
	'podpora',
	'Podpora',
	'',
	'template-support.php'
);
update_post_meta( $support_id, 'page_description_text', 'Zde naleznete odpovědi na časté otázky k problematice koupě, plánování, stavby a provozu domácího bazénu či vířivky.' );

$features_id = arctic_seed_page(
	'vlastnosti',
	'Vlastnosti vířivek',
	'<!-- wp:paragraph --><p>Stránka připravená podle Figma navigace pro izolaci, úpravu vody, termo kryt, servisní přístup a další vlastnosti vířivek Arctic Spas.</p><!-- /wp:paragraph -->'
);

$warranty_id = arctic_seed_page(
	'zaruka',
	'Záruka',
	'<!-- wp:paragraph --><p>Arctic Spas používá konstrukci, izolaci a servisovatelnou technologii určenou pro celoroční provoz. Tato stránka bude rozvedená podle Figma sekce záruky a aktuálních podkladů Arctic Spas.</p><!-- /wp:paragraph -->'
);

$info_id = arctic_seed_page(
	'dalsi-informace',
	'Další informace',
	'<!-- wp:paragraph --><p>Rozcestník pro průběh realizace, podporu, reference, často kladené otázky, servis a kontakt.</p><!-- /wp:paragraph -->'
);

$about_id = arctic_seed_page(
	'o-nas',
	'O nás',
	'<!-- wp:paragraph --><p>Arctic Spas CZ je specializovaná produktová prezentace pro kanadské vířivky, celoroční bazény a navazující wellness sortiment.</p><!-- /wp:paragraph -->'
);

$downloads_page_id = arctic_seed_page(
	'ke-stazeni',
	'Ke stazeni',
	'<!-- wp:heading --><h2>Dokumenty ke stazeni</h2><!-- /wp:heading --><!-- wp:shortcode -->[arctic-downloads]<!-- /wp:shortcode -->'
);

$references_page_id = arctic_seed_page(
	'reference',
	'Reference',
	'',
	'template-references.php'
);

$contact_id = arctic_seed_page(
	'kontakt',
	'Kontakt',
	'<!-- wp:paragraph --><p>Arctic Spas CZ, Bohunicka cesta 15, Moravany u Brna. Telefon: +420 777 099 687. E-mail: lukas.dusek@arctic-spas.cz.</p><!-- /wp:paragraph -->',
	'template-contact.php'
);
update_post_meta( $contact_id, 'page_title_text', 'Jsme k dispozici pro vaše dotazy' );

set_theme_mod( 'baspa_name', 'Arctic Spas CZ' );
set_theme_mod( 'baspa_phone', '+420 777 099 687' );
set_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );
set_theme_mod( 'baspa_street', 'Bohunicka cesta 15' );
set_theme_mod( 'baspa_zip', '664 48' );
set_theme_mod( 'baspa_city', 'Moravany u Brna' );
set_theme_mod( 'baspa_map', '' );
set_theme_mod( 'arctic_map_embed', '' );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );

$sample_page = get_page_by_path( 'sample-page' );
if ( $sample_page ) {
	wp_update_post( array(
		'ID'          => $sample_page->ID,
		'post_status' => 'draft',
	) );
}

arctic_seed_menu( 'Arctic hlavni navigace', 'navigation', array(
	array( 'title' => 'Vířivky', 'url' => $hot_tubs_url ),
	array( 'title' => 'Celoroční bazény', 'url' => $swimspa_url ),
	array( 'title' => 'Vlastnosti', 'url' => get_permalink( $features_id ) ),
	array( 'title' => 'Další informace', 'url' => get_permalink( $info_id ) ),
) );

arctic_seed_menu( 'Arctic horni lista', 'navigation_bar', array(
	array( 'title' => 'Lunar', 'url' => get_permalink( $lunar_id ) ),
	array( 'title' => 'Orion', 'url' => get_permalink( $orion_id ) ),
	array( 'title' => 'Ke stazeni', 'url' => get_permalink( $downloads_page_id ) ),
) );

arctic_seed_menu( 'Arctic paticka', 'navigation_footer', array(
	array( 'title' => 'Vířivky', 'url' => $hot_tubs_url ),
	array( 'title' => 'Celoroční bazény', 'url' => $swimspa_url ),
	array( 'title' => 'Další sortiment', 'url' => $covers_url ),
	array( 'title' => 'Vlastnosti vířivek', 'url' => get_permalink( $features_id ) ),
	array( 'title' => 'Další informace', 'url' => get_permalink( $info_id ) ),
	array( 'title' => 'Podpora', 'url' => get_permalink( $support_id ) ),
	array( 'title' => 'Showroom', 'url' => get_permalink( $showroom_id ) ),
	array( 'title' => 'Kontakt', 'url' => get_permalink( $contact_id ) ),
) );

flush_rewrite_rules();

if ( function_exists( 'WP_CLI' ) ) {
	WP_CLI::success( 'Seeded Arctic pilot content, shell pages, menus, media, and local settings.' );
}
