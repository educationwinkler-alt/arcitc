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

function arctic_seed_set_product_configurations( int $post_id, array $values ): void {
	if ( function_exists( 'baspa_products_update_configurations' ) ) {
		baspa_products_update_configurations( $post_id, $values );
	} else {
		update_post_meta( $post_id, 'product_configuration_items', $values );
	}

	// Keep the old repeated meta populated until every environment has migrated.
	arctic_seed_set_multi_meta( $post_id, 'product_configurations', $values );
}

function arctic_seed_legacy_products(): array {
	static $products = null;

	if ( null !== $products ) {
		return $products;
	}

	$products = array();
	$path     = WP_CONTENT_DIR . '/uploads/import/legacy-content/product-data.json';

	if ( file_exists( $path ) ) {
		$data = json_decode( (string) file_get_contents( $path ), true );
		if ( is_array( $data ) ) {
			$products = $data;
		}
	}

	return $products;
}

function arctic_seed_legacy_param( array $legacy, array $labels ): string {
	foreach ( $legacy['parameters'] ?? array() as $parameter ) {
		$label = trim( (string) ( $parameter['label'] ?? '' ) );
		foreach ( $labels as $expected ) {
			if ( 0 === strcasecmp( $label, $expected ) ) {
				return trim( (string) ( $parameter['value'] ?? '' ) );
			}
		}
	}

	return '';
}

function arctic_seed_value_array( string $value ): array {
	$value = trim( $value );
	return '' === $value || 'nemá' === strtolower( $value ) ? array() : array( $value );
}

function arctic_seed_page( string $slug, string $title, string $content, string $template = '' ): int {
	$page = get_page_by_path( $slug );
	if ( !$page ) {
		$pages = get_posts( array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			'name'           => $slug,
		) );
		$page  = !empty( $pages ) ? $pages[0] : null;
	}

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

	$add_item = function( array $item, int $parent_id = 0 ) use ( &$add_item, $menu_id ): int {
		$menu_item_id = wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => $item['title'],
			'menu-item-url'       => $item['url'],
			'menu-item-status'    => 'publish',
			'menu-item-type'      => 'custom',
			'menu-item-parent-id' => $parent_id,
			'menu-item-classes'   => implode( ' ', $item['classes'] ?? array() ),
		) );

		if ( is_wp_error( $menu_item_id ) ) {
			throw new RuntimeException( $menu_item_id->get_error_message() );
		}

		foreach ( $item['children'] ?? array() as $child ) {
			$add_item( $child, (int) $menu_item_id );
		}

		return (int) $menu_item_id;
	};

	foreach ( $items as $item ) {
		$add_item( $item );
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

$kind_hot_tubs = arctic_seed_term( 'product-kind', 'Vířivky', 'virivky' );
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

$category_hot_tubs = arctic_seed_term( 'product-category', 'Vířivky', 'virivky' );
$category_swimspa  = arctic_seed_term( 'product-category', 'Swimspa', 'swimspa' );
$category_covers   = arctic_seed_term( 'product-category', 'Dalsi sortiment', 'dalsi-sortiment' );

wp_update_term( $category_hot_tubs, 'product-category', array( 'name' => 'Vířivky' ) );
wp_update_term( $category_swimspa, 'product-category', array( 'name' => 'Celoroční bazény' ) );
wp_update_term( $category_covers, 'product-category', array( 'name' => 'Další sortiment' ) );

$download_preparation = arctic_seed_term( 'download-category', 'Stavební připravenost', 'stavebni-pripravenost' );
$download_manuals     = arctic_seed_term( 'download-category', 'Návody', 'navody' );
$download_dimensions  = arctic_seed_term( 'download-category', 'Rozměry', 'rozmery' );
$download_warranty    = arctic_seed_term( 'download-category', 'Záruky', 'zaruky' );
$download_catalogs    = arctic_seed_term( 'download-category', 'Katalogy', 'katalogy' );
$download_water       = arctic_seed_term( 'download-category', 'Úprava vody', 'uprava-vody' );
$download_technical   = arctic_seed_term( 'download-category', 'Technické dokumenty', 'technicke-dokumenty' );

$lunar_main   = arctic_seed_attachment( 'lunar-main', 'uploads/import/lunar-main.jpg', 'Lunar - Platinum Swirl', 'Vířivka Arctic Spas Lunar v barvě Platinum Swirl' );
$lunar_corner = arctic_seed_attachment( 'lunar-corner', 'uploads/import/lunar-corner.png', 'Lunar - rohová fotografie', 'Detail vířivky Arctic Spas Lunar' );
$lunar_cover  = arctic_seed_attachment( 'lunar-cover-black', 'uploads/import/lunar-cover-black.png', 'Lunar - černý termokryt', 'Vířivka Lunar s černým termokrytem' );
$timberwolf_signature = arctic_seed_attachment( 'timberwolf-signature', 'uploads/import/timberwolf-signature.jpg', 'Timberwolf - Signature', 'Vířivka Arctic Spas Timberwolf Signature' );
$timberwolf_prestige  = arctic_seed_attachment( 'timberwolf-prestige', 'uploads/import/timberwolf-prestige.jpg', 'Timberwolf - Prestige', 'Vířivka Arctic Spas Timberwolf Prestige' );
$timberwolf_side      = arctic_seed_attachment( 'timberwolf-side', 'uploads/import/timberwolf-side.jpg', 'Timberwolf - boční pohled', 'Boční pohled na vířivku Timberwolf' );
$orion_main   = arctic_seed_attachment( 'orion-main', 'uploads/import/orion-main.jpg', 'Orion - Platinum Swirl', 'Vířivka Arctic Spas Orion v barvě Platinum Swirl' );
$orion_life   = arctic_seed_attachment( 'orion-lifestyle', 'uploads/import/orion-lifestyle.jpg', 'Orion - lifestyle', 'Vířivka Orion v exteriéru' );
$figma_hero   = arctic_seed_attachment( 'figma-node-1-15-hp-hero', 'uploads/import/figma/hp-hero-arctic-spas-07.jpg', 'Figma HP hero - Arctic Spas 07', 'Vířivka Arctic Spas v podzimní krajině' );
$figma_category_hero_virivky = arctic_seed_attachment( 'figma-node-1-263-category-hero-virivky', 'uploads/import/figma/category-hero-virivky.jpg', 'Figma kategorie - hero vířivky', 'Hero fotografie kategorie vířivek Arctic Spas' );
$figma_category_vlastnosti = arctic_seed_attachment( 'figma-node-1-273-category-vlastnosti', 'uploads/import/figma/category-vlastnosti.jpg', 'Figma kategorie - vlastnosti', 'Fotografie k sekci vlastnosti vířivek Arctic Spas' );
$figma_category_zaruka = arctic_seed_attachment( 'figma-node-1-274-category-zaruka', 'uploads/import/figma/category-zaruka.jpg', 'Figma kategorie - záruka', 'Fotografie k sekci záruka Arctic Spas' );
$figma_configurator = arctic_seed_attachment( 'figma-node-1-409-category-configurator', 'uploads/import/figma/category-configurator.png', 'Figma konfigurátor', 'Konfigurátor vířivky Arctic Spas' );
$legacy_category_hot_tubs_life = arctic_seed_attachment( 'legacy-category-virivky', 'uploads/import/legacy-categories/virivky.jpg', 'Arctic Spas - vířivky ze starého webu', 'Obsahová fotografie vířivky ze starého Arctic webu' );
$legacy_category_swimspa_life = arctic_seed_attachment( 'legacy-category-swimspa', 'uploads/import/legacy-categories/swimspa.jpg', 'Arctic Spas - swimspa ze starého webu', 'Obsahová fotografie swimspa ze starého Arctic webu' );
$legacy_reference_fox_life = arctic_seed_attachment( 'legacy-reference-arctic-fox-life', 'uploads/import/legacy-references/arctic-fox-lidi.jpg', 'Reference Arctic Fox', 'Reference zákazníka ze starého Arctic webu' );
$legacy_reference_fox_life_2 = arctic_seed_attachment( 'legacy-reference-arctic-fox-life-2', 'uploads/import/legacy-references/arctic-fox-lidi-2.jpg', 'Reference Arctic Fox 2', 'Reference zákazníka ze starého Arctic webu' );
$legacy_reference_zuz = arctic_seed_attachment( 'legacy-reference-zuz', 'uploads/import/legacy-references/reference-zuz.jpg', 'Reference zákazníka', 'Reference ze starého Arctic webu' );
$legacy_reference_hot_tub_1 = arctic_seed_attachment( 'legacy-reference-virivka-arctic-g1', 'uploads/import/legacy-references/virivka-arctic-g1.jpg', 'Realizace vířivky ze starého webu', 'Obsahová fotografie vířivky ze starého Arctic webu' );
$legacy_reference_hot_tub_2 = arctic_seed_attachment( 'legacy-reference-virivka-arctic-g7', 'uploads/import/legacy-references/virivka-arctic-g7.jpg', 'Realizace vířivky ze starého webu', 'Obsahová fotografie vířivky ze starého Arctic webu' );
$legacy_reference_hot_tub_3 = arctic_seed_attachment( 'legacy-reference-virivka-arctic-g21', 'uploads/import/legacy-references/virivka-arctic-g21.jpg', 'Realizace vířivky ze starého webu', 'Obsahová fotografie vířivky ze starého Arctic webu' );
$legacy_reference_swimspa_1 = arctic_seed_attachment( 'legacy-reference-swimspa-arctic-g1', 'uploads/import/legacy-references/swimspa-arctic-g1.jpg', 'Realizace swimspa ze starého webu', 'Obsahová fotografie swimspa ze starého Arctic webu' );
$legacy_reference_swimspa_2 = arctic_seed_attachment( 'legacy-reference-swimspa-arctic-g4', 'uploads/import/legacy-references/swimspa-arctic-g4.jpg', 'Realizace swimspa ze starého webu', 'Obsahová fotografie swimspa ze starého Arctic webu' );
$showroom = arctic_seed_attachment( 'figma-node-1-123-showroom-1', 'uploads/import/figma/showroom-1.png', 'Figma showroom 1', 'Showroom Arctic Spas podle grafiky' );
$showroom_2 = arctic_seed_attachment( 'figma-node-1-124-showroom-2', 'uploads/import/figma/showroom-2.png', 'Figma showroom 2', 'Showroom Arctic Spas podle grafiky' );
$figma_showroom_3 = arctic_seed_attachment( 'figma-node-1-125-showroom-3', 'uploads/import/figma/showroom-3.png', 'Figma showroom 3', 'Showroom Arctic Spas podle grafiky' );
$figma_showroom_hero = arctic_seed_attachment( 'figma-node-1-446-showroom-hero-bazeny', 'uploads/import/figma/showroom-hero-bazeny.jpg', 'Figma showroom hero', 'Hero showroomu Arctic Spas podle grafiky' );
$figma_showroom_detail_bazeny = arctic_seed_attachment( 'figma-node-1-443-showroom-detail-bazeny', 'uploads/import/figma/showroom-detail-bazeny.png', 'Figma showroom detail bazény', 'Detail showroomu Arctic Spas podle grafiky' );
$figma_showroom_detail_virivky = arctic_seed_attachment( 'figma-node-1-444-showroom-detail-virivky', 'uploads/import/figma/showroom-detail-virivky.png', 'Figma showroom detail vířivky', 'Vířivky v showroomu Arctic Spas podle grafiky' );
$figma_footer_map = arctic_seed_attachment( 'figma-node-1-242-footer-map', 'uploads/import/figma/footer-map.png', 'Figma footer mapa', 'Showroom Arctic Spas ve footeru' );
$figma_contact_map = arctic_seed_attachment( 'figma-node-1-1069-contact-map-showroom', 'uploads/import/figma/contact-map-showroom.png', 'Figma kontakt mapa/showroom', 'Kontaktní mapa a showroom podle grafiky' );
$figma_color_dakota = arctic_seed_attachment( 'figma-node-1-1476-color-dakota', 'uploads/import/figma/color-dakota.png', 'Figma barva Dakota', 'Vzorek barvy Dakota podle Figmy' );
$figma_color_kalahari = arctic_seed_attachment( 'figma-node-1-1479-color-kalahari', 'uploads/import/figma/color-kalahari.png', 'Figma barva Kalahari', 'Vzorek barvy Kalahari podle Figmy' );
$figma_color_odyssey = arctic_seed_attachment( 'figma-node-1-1482-color-odyssey', 'uploads/import/figma/color-odyssey.png', 'Figma barva Odyssey', 'Vzorek barvy Odyssey podle Figmy' );
$figma_color_platinum = arctic_seed_attachment( 'figma-node-1-1485-color-platinum-swirl', 'uploads/import/figma/color-platinum-swirl.png', 'Figma barva Platinum Swirl', 'Vzorek barvy Platinum Swirl podle Figmy' );
$figma_color_espresso = arctic_seed_attachment( 'figma-node-1-1488-color-espresso', 'uploads/import/figma/color-espresso.png', 'Figma barva Espresso', 'Vzorek barvy Espresso podle Figmy' );
$figma_cabinet_cedar = arctic_seed_attachment( 'figma-node-1-1492-cabinet-cedar', 'uploads/import/figma/cabinet-cedar.png', 'Figma kabinet cedr', 'Vzorek cedrového kabinetu podle Figmy' );
$figma_cabinet_maintenance_free = arctic_seed_attachment( 'figma-node-1-1495-cabinet-maintenance-free', 'uploads/import/figma/cabinet-maintenance-free.png', 'Figma kabinet bezúdržbový', 'Vzorek bezúdržbového kabinetu podle Figmy' );
$covana_main  = arctic_seed_attachment( 'covana-slide-1', 'uploads/import/covana-slide-1.jpg', 'Covana automatický kryt', 'Automatický kryt vířivky Covana' );
$sauna_main   = arctic_seed_attachment( 'other-sauna-luxus-1', 'uploads/import/other-sortiment/sauna-luxus-1.jpg', 'Luxusní sauna', 'Luxusní sauna na míru' );
$kirami_main  = arctic_seed_attachment( 'other-koupaci-sud-s1', 'uploads/import/other-sortiment/koupaci-sud-s1.jpg', 'Koupací sud Kirami', 'Koupací sud Kirami' );
$ikono_main   = arctic_seed_attachment( 'other-venkovni-nabytek-ikono', 'uploads/import/other-sortiment/venkovni-nabytek-ikono.jpg', 'Venkovní nábytek IKONO', 'Venkovní nábytek IKONO' );
$accessory_main = arctic_seed_attachment( 'other-doplnky-cedr', 'uploads/import/other-sortiment/doplnky-cedr.jpg', 'Cedrové doplňky', 'Cedrové doplňky k vířivkám' );
$cold_plunge_main = arctic_seed_attachment( 'other-ochlazovaci-bazenek', 'uploads/import/other-sortiment/ochlazovaci-bazenek.jpg', 'Ochlazovací bazének', 'Ochlazovací bazének pro domácí wellness' );
$prep_pdf     = arctic_seed_attachment( 'stavebni-pripravenost-pdf', 'uploads/import/stavebni-pripravenost.pdf', 'Stavební připravenost Arctic Spas' );

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

update_term_meta( $category_hot_tubs, 'category_image', $legacy_category_hot_tubs_life );
update_term_meta( $category_hot_tubs, 'category_heading_image', $legacy_category_hot_tubs_life );
update_term_meta( $category_hot_tubs, 'category_description_short', 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie.' );
update_term_meta( $category_hot_tubs, 'category_heading_title', 'Venkovní vířivky Arctic Spas' );
update_term_meta( $category_hot_tubs, 'category_heading_text', 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie.' );
update_term_meta( $category_hot_tubs, 'category_heading_cta_text', 'Vybrat vířivku' );
update_term_meta( $category_swimspa, 'category_image', $legacy_category_swimspa_life );
update_term_meta( $category_swimspa, 'category_heading_image', $legacy_category_swimspa_life );
update_term_meta( $category_swimspa, 'category_description_short', 'Rodinný bazén na zahradě je snem řady domácností. Mnohé nicméně odradí nesmírná náročnost souvisejících zemních a stavebních prací a v našich klimatických podmínkách také žalostně limitované využití omezené na krátkou letní sezónu.' );
update_term_meta( $category_swimspa, 'category_heading_cta_text', 'Vybrat bazén' );
update_term_meta( $category_covers, 'category_image', $covana_main );
update_term_meta( $category_covers, 'category_description_short', 'Automatické kryty, doplňky a navazující sortiment pro pohodlnější provoz vířivky.' );
update_term_meta( $category_covers, 'category_heading_cta_text', 'Prohlédnout sortiment' );
update_term_meta( $category_covers, 'category_type', 'accessories' );

$reference_customers = arctic_seed_term( 'reference-category', 'Reference zákazníků', 'reference-zakazniku' );

foreach ( array( 'figma-reference-1', 'figma-reference-2', 'figma-reference-3' ) as $stale_reference_key ) {
	$stale_references = get_posts( array(
		'post_type'      => 'reference',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_key'       => '_arctic_seed_key',
		'meta_value'     => $stale_reference_key,
	) );

	foreach ( $stale_references as $stale_reference_id ) {
		wp_delete_post( (int) $stale_reference_id, true );
	}
}

$reference_items = array(
	array(
		'key'         => 'legacy-reference-arctic-fox-life',
		'image'       => $legacy_reference_fox_life,
		'title'       => 'Arctic Fox po letech provozu',
		'description' => 'Téměř před patnácti lety jsme se rozhodli pořídit kanadskou venkovní vířivku Arctic Spas v provedení Fox. Od té doby ji s manželkou využíváme několikrát týdně, bez zásadních problémů a s perfektně vyřízenými dotazy v průběhu let.',
		'location'    => 'Arctic Fox',
		'year'        => '2021',
	),
	array(
		'key'         => 'legacy-reference-arctic-fox-life-2',
		'image'       => $legacy_reference_fox_life_2,
		'title'       => 'Fox se systémem Spa Boy',
		'description' => 'Po letech okukování nám na zahradě přistála čerstvá Foxka. Výborná domluva, instalace takřka na klíč, skvělá síla trysek a slaná voda se Spa Boy nám výrazně usnadnila péči o vodu.',
		'location'    => 'Arctic Spas',
		'year'        => '2016',
	),
	array(
		'key'         => 'legacy-reference-swimspa-arctic-g1',
		'image'       => $legacy_reference_swimspa_1,
		'title'       => 'Swimspa Wolverine',
		'description' => 'Swimspa používáme denně a hodnotíme ji jako jednu z nejlepších součástí domu. V zimní zahradě je použitelná kdykoliv a plavání na plovacím prutu nás baví víc než samotný protiproud.',
		'location'    => 'Wolverine',
		'year'        => '2016',
	),
	array(
		'key'         => 'customer-reference-low-energy',
		'image'       => $legacy_reference_hot_tub_1,
		'title'       => 'Nízká spotřeba v zimním provozu',
		'description' => 'Po jednoročním provozu jsme zjistili, že vířivka má velmi nízkou spotřebu i při častém zimním používání. Zakoupili jsme výjimečnou věc, kterou bychom doporučili každému.',
		'location'    => 'Arctic Spas',
		'year'        => '2014',
	),
	array(
		'key'         => 'customer-reference-fox-service',
		'image'       => $legacy_reference_hot_tub_2,
		'title'       => 'Arctic Fox předčil očekávání',
		'description' => 'Jsme rádi, že jsme při výběru dodavatele vířivky narazili na vaši firmu. Dodaná vířivka Arctic Fox předčila naše očekávání a poskytnuté služby odpovídají dobrému jménu firmy.',
		'location'    => 'Arctic Fox',
		'year'        => '2014',
	),
	array(
		'key'         => 'customer-reference-pool',
		'image'       => $legacy_reference_swimspa_2,
		'title'       => 'Vstřícný přístup při výběru bazénu',
		'description' => 'Velmi oceňujeme vstřícný přístup při prohlídce místa plánované instalace. Bazén jsme nakonec objednali a rychlé provedení i výsledný stav nám dělají radost.',
		'location'    => 'Bazén',
		'year'        => '2015',
	),
	array(
		'key'         => 'customer-reference-swimspa-showroom',
		'image'       => $legacy_reference_swimspa_1,
		'title'       => 'Výběr celoročního bazénu',
		'description' => 'Při výběru swimspa jsme navštívili hodně showroomů. Až u Arctic Spas jsme pochopili, co je důležité; dodávka proběhla v termínu, instalace rychle a zaškolení na vysoké úrovni.',
		'location'    => 'Celoroční bazén',
		'year'        => '2015',
	),
	array(
		'key'         => 'customer-reference-arctic-spas',
		'image'       => $legacy_reference_hot_tub_3,
		'title'       => 'Vířivka bez poruchy',
		'description' => 'Vířivka je super a bez poruchy. Parametry a dlouhodobá zkušenost nám potvrdily, že výběr kvalitní vířivky se opravdu vyplatí.',
		'location'    => 'Arctic Spas',
		'year'        => '2016',
	),
	array(
		'key'         => 'customer-reference-new-year',
		'image'       => $legacy_reference_zuz,
		'title'       => 'Novoroční přání z vířivky',
		'description' => 'Originální novoroční přání zákazníka vystihuje, proč lidé Arctic Spas používají: odpočinek, teplo a chvíle klidu přímo doma.',
		'location'    => 'Arctic Spas',
		'year'        => '2012',
	),
);

foreach ( $reference_items as $index => $reference_item ) {
	$reference_id = arctic_seed_post_by_meta( 'reference', '_arctic_seed_key', $reference_item['key'], array(
		'post_status'  => 'publish',
		'post_title'   => $reference_item['title'],
		'post_name'    => $reference_item['key'],
		'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $reference_item['description'] ) . '</p><!-- /wp:paragraph -->',
		'menu_order'   => ( $index + 1 ) * 10,
	) );

	set_post_thumbnail( $reference_id, $reference_item['image'] );
	update_post_meta( $reference_id, 'reference_single', 1 );
	update_post_meta( $reference_id, 'reference_description', $reference_item['description'] );
	update_post_meta( $reference_id, 'reference_location', $reference_item['location'] );
	update_post_meta( $reference_id, 'reference_year', $reference_item['year'] );
	arctic_seed_set_multi_meta( $reference_id, 'reference_images', array( $reference_item['image'] ) );
	wp_set_post_terms( $reference_id, array( $reference_customers ), 'reference-category' );
}

update_option( 'baspa_references_title', 'Ukázky realizací' );

$faq_terms = array(
	'obchodni'           => arctic_seed_term( 'faq-category', 'Obchodní', 'obchodni' ),
	'stavebni-priprava' => arctic_seed_term( 'faq-category', 'Stavební příprava', 'stavebni-priprava' ),
	'montaz'            => arctic_seed_term( 'faq-category', 'Montáž', 'montaz' ),
	'provoz'            => arctic_seed_term( 'faq-category', 'Provoz a údržba', 'provoz-a-udrzba' ),
	'servis'            => arctic_seed_term( 'faq-category', 'Servis', 'servis' ),
);

$faq_items = array(
	array(
		'key'      => 'support-faq-order',
		'title'    => 'Jak probíhá výběr a objednávka vířivky?',
		'text'     => 'Nejprve společně ověříme velikost, umístění a požadovanou výbavu. Poté připravíme konkrétní konfiguraci a navazující technickou přípravu.',
		'category' => 'obchodni',
	),
	array(
		'key'      => 'support-faq-installation',
		'title'    => 'Co je potřeba připravit před instalací?',
		'text'     => 'Důležitý je pevný podklad, přívod elektřiny a přístupová cesta pro usazení vířivky. Detaily řešíme podle konkrétního modelu.',
		'category' => 'stavebni-priprava',
	),
	array(
		'key'      => 'support-faq-delivery',
		'title'    => 'Zajišťujete dopravu a montáž?',
		'text'     => 'Ano, u nových realizací počítáme s dopravou, usazením a základním zaškolením obsluhy.',
		'category' => 'montaz',
	),
	array(
		'key'      => 'support-faq-water-care',
		'title'    => 'Jak náročná je běžná údržba vody?',
		'text'     => 'Údržba závisí na výbavě, četnosti používání a režimu filtrace. U Arctic Spas lze volit technologie, které péči výrazně zjednodušují.',
		'category' => 'provoz',
	),
	array(
		'key'      => 'support-faq-configuration',
		'title'    => 'Pomůžete s výběrem vhodné konfigurace?',
		'text'     => 'Ano. Společně projdeme počet osob, umístění, izolaci, masážní trysky a volitelnou výbavu tak, aby model odpovídal reálnému používání.',
		'category' => 'obchodni',
	),
	array(
		'key'      => 'support-faq-showroom',
		'title'    => 'Lze si vířivku prohlédnout osobně?',
		'text'     => 'Vybrané modely a technologie si můžete projít v showroomu v Moravanech u Brna. Návštěvu doporučujeme domluvit předem.',
		'category' => 'obchodni',
	),
	array(
		'key'      => 'support-faq-building-ready',
		'title'    => 'Jak se řeší stavební připravenost?',
		'text'     => 'Po výběru modelu připravíme podklady pro podkladovou desku, elektrický přívod, manipulační prostor a případné zapuštění.',
		'category' => 'stavebni-priprava',
	),
	array(
		'key'      => 'support-faq-service',
		'title'    => 'Umíte zajistit servis po instalaci?',
		'text'     => 'Servisní požadavky řešíme přes kontaktní formulář nebo telefonicky. Pomůže model, rok pořízení a stručný popis problému.',
		'category' => 'servis',
	),
	array(
		'key'      => 'support-faq-offer',
		'title'    => 'Jak rychle dostanu cenovou nabídku?',
		'text'     => 'Po upřesnění modelu, konfigurace a montážních podmínek připravíme nezávaznou kalkulaci včetně navazující přípravy.',
		'category' => 'obchodni',
	),
);

foreach ( $faq_items as $index => $faq_item ) {
	$faq_id = arctic_seed_post_by_meta( 'faq', '_arctic_seed_key', $faq_item['key'], array(
		'post_status'  => 'publish',
		'post_title'   => $faq_item['title'],
		'post_name'    => $faq_item['key'],
		'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $faq_item['text'] ) . '</p><!-- /wp:paragraph -->',
		'menu_order'   => ( $index + 1 ) * 10,
	) );

	if ( !empty( $faq_terms[ $faq_item['category'] ] ) ) {
		wp_set_post_terms( $faq_id, array( $faq_terms[ $faq_item['category'] ] ), 'faq-category' );
	}
}

$lunar_id = arctic_seed_post_by_meta( 'product', 'product_original_url', 'https://www.arctic-spas.cz/virivka-lunar.php', array(
	'post_status'  => 'publish',
	'post_title'   => 'Lunar',
	'post_name'    => 'lunar',
	'post_content' => '<!-- wp:paragraph --><p>Model Lunar z řady Core je nová kanadská vířivka pro rok 2025. Kombinuje jedno lehátko, čtyři sedadla a dvě konfigurace výbavy pro pohodlnou rodinnou relaxaci.</p><!-- /wp:paragraph -->',
	'menu_order'   => 10,
) );

update_post_meta( $lunar_id, 'product_type', 'standard' );
update_post_meta( $lunar_id, 'product_title_short', 'Lunar' );
update_post_meta( $lunar_id, 'product_price_text', 'od 249 000 Kč' );
update_post_meta( $lunar_id, 'product_price_suffix', 'vč. standardní výbavy' );
update_post_meta( $lunar_id, 'product_description_short', 'Nová vířivka Core pro rok 2025 s jedním lehátkem a čtyřmi sedadly.' );
update_post_meta( $lunar_id, 'product_description', 'Lunar je nový model řady Core vyráběný v Kanadě. V konfiguraci Prestige nabízí 20 trysek a jedno čerpadlo, v konfiguraci Signature 40 trysek a dvě čerpadla.' );
update_post_meta( $lunar_id, 'product_cta_text', 'Poptat Lunar' );
set_post_thumbnail( $lunar_id, $lunar_main );
arctic_seed_set_multi_meta( $lunar_id, 'product_image', array( $lunar_main ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_images', array( $lunar_main, $lunar_corner, $lunar_cover ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_model', array( 'Core Lunar' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_seats', array( '1 lehátko + 4 sedadla' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_nozzles', array( 'Prestige: 20 trysek / 1 čerpadlo', 'Signature: 40 trysek / 2 čerpadla' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_dimensions_external', array( '212 × 213 cm, výška: 99 cm' ) );
arctic_seed_set_multi_meta( $lunar_id, 'product_acrylic_colors', array( 'Platinum Swirl', 'Espresso', 'Kalahari', 'Dakota' ) );
arctic_seed_set_product_configurations( $lunar_id, array(
	array(
		'name'        => 'Prestige',
		'price'       => '249 000 Kč',
		'seats'       => '1 lehátko + 4 sedadla',
		'jets'        => '20',
		'pumps'       => '1',
		'dimensions'  => '212 x 213 x 99 cm',
		'description' => 'Základní konfigurace řady Core.',
	),
	array(
		'name'        => 'Signature',
		'price'       => '279 000 Kč',
		'seats'       => '1 lehátko + 4 sedadla',
		'jets'        => '40',
		'pumps'       => '2',
		'dimensions'  => '212 x 213 x 99 cm',
		'description' => 'Silnější masážní konfigurace s dvojnásobným počtem trysek.',
	),
) );
wp_set_object_terms( $lunar_id, array( $kind_hot_tubs ), 'product-kind' );
wp_set_object_terms( $lunar_id, array( $series_core ), 'product-series' );
wp_set_object_terms( $lunar_id, array( $category_hot_tubs ), 'product-category' );

$orion_id = arctic_seed_post_by_meta( 'product', 'product_original_url', 'https://www.arctic-spas.cz/virivka-orion.php', array(
	'post_status'  => 'publish',
	'post_title'   => 'Orion',
	'post_name'    => 'orion',
	'post_content' => '<!-- wp:paragraph --><p>Orion je nový model řady Core pro šest osob. Sdílí technický základ s modelem Lunar, ale místo lehátka nabízí sedací dispozici.</p><!-- /wp:paragraph -->',
	'menu_order'   => 20,
) );

update_post_meta( $orion_id, 'product_type', 'standard' );
update_post_meta( $orion_id, 'product_title_short', 'Orion' );
update_post_meta( $orion_id, 'product_price_text', 'od 249 000 Kč' );
update_post_meta( $orion_id, 'product_description_short', 'Nová vířivka Core pro šest osob.' );
update_post_meta( $orion_id, 'product_description', 'Orion je nový kanadský model pro rok 2025. Nabízí šest sedadel a konfigurace Prestige nebo Signature.' );
set_post_thumbnail( $orion_id, $orion_main );
arctic_seed_set_multi_meta( $orion_id, 'product_image', array( $orion_main ) );
arctic_seed_set_multi_meta( $orion_id, 'product_images', array( $orion_main, $orion_life ) );
arctic_seed_set_multi_meta( $orion_id, 'product_model', array( 'Core Orion' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_seats', array( '6 sedadel' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_nozzles', array( 'Prestige: 20 trysek / 1 čerpadlo', 'Signature: 40 trysek / 2 čerpadla' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_dimensions_external', array( '212 × 213 cm, výška: 99 cm' ) );
arctic_seed_set_multi_meta( $orion_id, 'product_acrylic_colors', array( 'Platinum Swirl', 'Espresso', 'Kalahari', 'Dakota' ) );
arctic_seed_set_product_configurations( $orion_id, array(
	array(
		'name'        => 'Prestige',
		'price'       => '249 000 Kč',
		'seats'       => '6 sedadel',
		'jets'        => '20',
		'pumps'       => '1',
		'dimensions'  => '212 x 213 x 99 cm',
		'description' => 'Základní konfigurace řady Core pro šest osob.',
	),
	array(
		'name'        => 'Signature',
		'price'       => '279 000 Kč',
		'seats'       => '6 sedadel',
		'jets'        => '40',
		'pumps'       => '2',
		'dimensions'  => '212 x 213 x 99 cm',
		'description' => 'Silnější masážní konfigurace se 40 tryskami.',
	),
) );
wp_set_object_terms( $orion_id, array( $kind_hot_tubs ), 'product-kind' );
wp_set_object_terms( $orion_id, array( $series_core ), 'product-series' );
wp_set_object_terms( $orion_id, array( $category_hot_tubs ), 'product-category' );

$covana_id = arctic_seed_post_by_meta( 'product', 'product_original_url', 'https://www.arctic-spas.cz/covana.php', array(
	'post_status'  => 'publish',
	'post_title'   => 'Covana',
	'post_name'    => 'covana',
	'post_content' => '<!-- wp:paragraph --><p>Covana je automatický kryt vířivky, který kombinuje funkci termokrytu, zvedáku a elegantního přístřešku.</p><!-- /wp:paragraph -->',
	'menu_order'   => 100,
) );

update_post_meta( $covana_id, 'product_type', 'landing_section' );
update_post_meta( $covana_id, 'product_title_short', 'Covana' );
update_post_meta( $covana_id, 'product_description_short', 'Automatický kryt, zvedák a altánek pro vířivku v jednom.' );
update_post_meta( $covana_id, 'product_description', 'Covana chrání vířivku, zjednodušuje manipulaci s krytem a vytváří pohodlné zastřešení pro každodenní používání.' );
update_post_meta( $covana_id, 'product_cta_text', 'Zjistit více o Covana' );
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
		'name'         => 'Luxusní sauny',
		'image'        => $sauna_main,
		'kind'         => $kind_saunas,
		'description'  => 'Luxusní sauny na míru, finské sauny a infrakabiny jako navazující wellness sortiment.',
	),
	array(
		'original_url' => 'https://www.arctic-spas.cz/koupaci-sudy-kirami.php',
		'slug'         => 'koupaci-sudy-kirami',
		'name'         => 'Koupací sudy Kirami',
		'image'        => $kirami_main,
		'kind'         => $kind_tubs,
		'description'  => 'Venkovní koupací sudy od finského výrobce Kirami pro relaxaci s ohřevem dřevem.',
	),
	array(
		'original_url' => 'https://www.arctic-spas.cz/prislusenstvi-doplnky.php',
		'slug'         => 'prislusenstvi-a-doplnky',
		'name'         => 'Příslušenství a doplňky',
		'image'        => $accessory_main,
		'kind'         => $kind_accessories,
		'description'  => 'Praktické příslušenství, schody, kryty, cedrové doplňky a vybavení pro pohodlnější používání vířivky.',
	),
	array(
		'original_url' => 'https://www.nabytek-ikono.cz/',
		'slug'         => 'ikono-nabytek',
		'name'         => 'IKONO nábytek',
		'image'        => $ikono_main,
		'kind'         => $kind_furniture,
		'description'  => 'Venkovní nábytek IKONO jako doplněk showroomu a zahradního wellness prostoru.',
	),
	array(
		'original_url' => 'https://www.arctic-spas.cz/ochlazovaci-bazenek.php',
		'slug'         => 'ochlazovaci-bazenek',
		'name'         => 'Ochlazovací bazének',
		'image'        => $cold_plunge_main,
		'kind'         => $kind_cold_plunge,
		'description'  => 'Ochlazovací bazének pro kontrastní terapii a doplnění domácího wellness.',
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
	array( 'source_slug' => 'virivka-timberwolf', 'slug' => 'timberwolf', 'name' => 'Timberwolf', 'type' => 'hot_tub', 'series' => $series_classic, 'dimensions' => '217 x 174 x 98 cm' ),
	array(
		'source_slug'    => 'virivka-husky',
		'slug'           => 'husky',
		'name'           => 'Husky',
		'type'           => 'hot_tub',
		'series'         => $series_core,
		'price'          => 'od 209 000 Kč',
		'description'    => 'Vířivka Husky má místo až pro 5 osob a je koncipována jako velký otevřený prostor s množstvím různých sedátek. Jednoduchý komfort a spousta relaxace, to vše vířivka Husky přináší, přitom nezabere tolik místa.',
		'dimensions'     => '213 x 186 x 99 cm',
		'seats'          => array( '5 osob' ),
		'nozzles'        => array( '20 trysek' ),
		'water_volume'   => array( '1030 litrů' ),
		'configurations' => array(
			array(
				'name'        => '20 trysek',
				'price'       => '209 000 Kč',
				'seats'       => '5 osob',
				'jets'        => '18 x 2,5" + 2 x 4"',
				'pumps'       => '1 dvourychlostní',
				'dimensions'  => '213 x 186 x 99 cm',
				'description' => 'Konfigurace Core s 20 tryskami a jedním dvourychlostním čerpadlem.',
			),
		),
	),
	array(
		'source_slug'    => 'bazen-athabascan',
		'slug'           => 'athabascan',
		'name'           => 'Athabascan',
		'type'           => 'swimspa',
		'series'         => $series_swimspa,
		'description'    => 'Athabascan je nejprostornější celoroční bazén Arctic. Disponuje velkou vodní plochou pro vodní radovánky, relaxaci a celoroční využití jen pár kroků od domu.',
		'dimensions'     => '436 x 236 x 129 cm',
		'nozzles'        => array( '3 trysky', 'bez protiproudu' ),
		'water_volume'   => array( '5100 litrů' ),
		'configurations' => array(
			array( 'name' => 'Athabascan', 'jets' => '3 trysky', 'pumps' => '1 dvourychlostní pro filtraci', 'dimensions' => '436 x 236 x 129 cm', 'description' => 'Největší volná vodní plocha bez masážních sedadel.' ),
		),
	),
	array(
		'source_slug'    => 'bazen-hudson',
		'slug'           => 'hudson',
		'name'           => 'Hudson',
		'type'           => 'swimspa',
		'series'         => $series_swimspa,
		'description'    => 'Hudson kombinuje plavecký systém Swim Tether, trysky Monsoon, hydroterapii a cvičení ve vodě. Nabízí dvě komfortní terapeutická sedátka.',
		'dimensions'     => '436 x 236 x 129 cm',
		'nozzles'        => array( '26 trysek', 'Monsoon protiproud', '2 masážní sedadla' ),
		'water_volume'   => array( '5100 litrů' ),
		'configurations' => array(
			array( 'name' => 'Hudson', 'jets' => '2 x 5", 22 x 3", 2 x Monsoon', 'pumps' => '1 dvourychlostní + 2 jednorychlostní', 'dimensions' => '436 x 236 x 129 cm', 'description' => 'Kombinace kondičního plavání, protiproudu a hydroterapie.' ),
		),
	),
	array(
		'source_slug'    => 'bazen-kingfisher',
		'slug'           => 'kingfisher',
		'name'           => 'Kingfisher',
		'type'           => 'swimspa',
		'series'         => $series_swimspa,
		'description'    => 'Kingfisher poskytne trénink kondičním plavcům. Široký prostor pro plavání doplňuje systém Swim Tether Resistance a plavecký systém Niagara.',
		'dimensions'     => '436 x 236 x 129 cm',
		'nozzles'        => array( '5 trysek', 'Niagara protiproud' ),
		'water_volume'   => array( '5100 litrů' ),
		'configurations' => array(
			array( 'name' => 'Kingfisher', 'jets' => '3 x 3", 2 x Niagara', 'pumps' => '1 dvourychlostní pro filtraci + Badustream', 'dimensions' => '436 x 236 x 129 cm', 'description' => 'Swimspa s důrazem na plavecký prostor a silný protiproud.' ),
		),
	),
	array(
		'source_slug'    => 'bazen-ocean',
		'slug'           => 'ocean',
		'name'           => 'Arctic Ocean',
		'type'           => 'swimspa',
		'series'         => $series_swimspa,
		'description'    => 'Arctic Ocean spojuje maximální sestavu luxusních masážních sedátek pro hydroterapii s prostorem pro plavání a cvičení. Počet masážních a protiproudých trysek lze zvolit podle konfigurace.',
		'dimensions'     => '436 x 236 x 129 cm',
		'nozzles'        => array( '20 až 60 trysek', 'Monsoon protiproud', '6 masážních sedadel' ),
		'water_volume'   => array( '5100 litrů' ),
		'configurations' => array(
			array( 'name' => 'Prestige 20/1', 'jets' => '20 trysek', 'pumps' => '1 dvourychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
			array( 'name' => 'Signature 40/2', 'jets' => '40 trysek', 'pumps' => '1 dvourychlostní + 1 jednorychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
			array( 'name' => 'Legend 40/3', 'jets' => '40 trysek', 'pumps' => '1 dvourychlostní + 2 jednorychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
			array( 'name' => 'Legend Select 60/3', 'jets' => '60 trysek', 'pumps' => '1 dvourychlostní + 2 jednorychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
			array( 'name' => 'SDS 60/4', 'jets' => '6 x 7", 48 x 3", 6 x Monsoon', 'pumps' => '1 dvourychlostní + 3 jednorychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
		),
	),
	array(
		'source_slug'    => 'bazen-okanagan',
		'slug'           => 'okanagan',
		'name'           => 'Okanagan',
		'type'           => 'swimspa',
		'series'         => $series_swimspa,
		'description'    => 'Okanagan přidává do celoročního bazénu bar se stoličkami, podsvícení, masáž a protiproud. Je navržený pro společenské využití i domácí wellness.',
		'dimensions'     => '436 x 236 x 129 cm',
		'nozzles'        => array( '20 až 40 trysek', 'Monsoon protiproud', '4 masážní sedadla' ),
		'water_volume'   => array( '5100 litrů' ),
		'configurations' => array(
			array( 'name' => 'Prestige 20/1', 'jets' => '20 trysek', 'pumps' => '1 dvourychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
			array( 'name' => 'Signature 40/2', 'jets' => '40 trysek', 'pumps' => '1 dvourychlostní + 1 jednorychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
			array( 'name' => 'Legend 40/3', 'jets' => '40 trysek', 'pumps' => '1 dvourychlostní + 2 jednorychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
			array( 'name' => 'SDS 40/3', 'jets' => '6 x 7", 32 x 3", 2 x Monsoon', 'pumps' => '1 dvourychlostní + 2 jednorychlostní', 'dimensions' => '436 x 236 x 129 cm' ),
		),
	),
	array(
		'source_slug'    => 'bazen-wolverine',
		'slug'           => 'wolverine',
		'name'           => 'Wolverine',
		'type'           => 'swimspa',
		'series'         => $series_swimspa,
		'description'    => 'Wolverine kombinuje plavecký systém Niagara, Swim Tether Resistance a dvě pohodlná hydromasážní sedátka s celkem 24 masážními tryskami.',
		'dimensions'     => '436 x 236 x 129 cm',
		'nozzles'        => array( '26 trysek', 'Niagara protiproud', '2 masážní sedadla' ),
		'water_volume'   => array( '5100 litrů' ),
		'configurations' => array(
			array( 'name' => 'Wolverine', 'jets' => '2 x 5", 22 x 3", 2 x Niagara', 'pumps' => '1 dvourychlostní + 1 jednorychlostní + 1 Badustream', 'dimensions' => '436 x 236 x 129 cm', 'description' => 'Plná výbava pro náročné plavání, cvičení a hydroterapii.' ),
		),
	),
);

$legacy_product_data = arctic_seed_legacy_products();

foreach ( $legacy_products as $index => $product ) {
	$is_swimspa      = 'swimspa' === $product['type'];
	$original_url    = 'https://www.arctic-spas.cz/' . $product['source_slug'] . '.php';
	$legacy          = $legacy_product_data[ $product['source_slug'] ] ?? array();
	$legacy_text     = !empty( $legacy['paragraphs'] ) ? implode( "\n\n", array_slice( $legacy['paragraphs'], 0, 2 ) ) : '';
	$legacy_seats    = arctic_seed_legacy_param( $legacy, array( 'Počet osob', 'Počet masážních sedadel' ) );
	$legacy_jets     = arctic_seed_legacy_param( $legacy, array( 'Počet trysek' ) );
	$legacy_volume   = arctic_seed_legacy_param( $legacy, array( 'Objem vody' ) );
	$legacy_size     = arctic_seed_legacy_param( $legacy, array( 'Rozměry' ) );
	$legacy_pump     = arctic_seed_legacy_param( $legacy, array( 'Čerpadlo 1', 'Čerpadlo 2', 'Čerpadla' ) );
	$series_term     = get_term( (int) $product['series'], 'product-series' );
	$series_label    = $series_term && !is_wp_error( $series_term ) ? $series_term->name : '';
	$image_id        = arctic_seed_attachment(
		'legacy-' . $product['source_slug'],
		'uploads/import/legacy-products/' . $product['source_slug'] . '.jpg',
		$product['name'],
		( $is_swimspa ? 'Swimspa ' : 'Vířivka ' ) . $product['name']
	);
	wp_update_post( array(
		'ID'        => $image_id,
		'post_name' => 'media-' . $product['source_slug'],
	) );

	if ( preg_match( '/^\d+$/', $legacy_jets ) ) {
		$legacy_jets .= ' trysek';
	}

	$default_content = $product['description'] ?? ( $legacy_text ?: ( $is_swimspa
		? 'Swimspa ' . $product['name'] . ' je celoroční bazén pro plavání, rehabilitaci a rodinnou relaxaci s technologií Arctic Spas.'
		: 'Vířivka ' . $product['name'] . ' je model Arctic Spas pro celoroční provoz, odolnou konstrukci a úspornou relaxaci v exteriéru.' ) );
	$order_offset    = $is_swimspa ? 200 : 40;
	$model_label     = trim( ( $series_label ? $series_label . ' ' : '' ) . $product['name'] );
	$configurations  = $product['configurations'] ?? array();

	if ( empty( $configurations ) ) {
		$default_configuration = array_filter( array(
			'name'        => $model_label ?: $product['name'],
			'seats'       => $legacy_seats,
			'jets'        => $legacy_jets,
			'pumps'       => $legacy_pump,
			'dimensions'  => $product['dimensions'] ?? $legacy_size,
			'description' => $default_content,
		) );

		if ( !empty( $default_configuration ) ) {
			$configurations = array( $default_configuration );
		}
	}

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
	arctic_seed_set_multi_meta( $product_id, 'product_model', arctic_seed_value_array( $model_label ) );
	arctic_seed_set_multi_meta( $product_id, 'product_dimensions_external', arctic_seed_value_array( $product['dimensions'] ?? $legacy_size ) );
	arctic_seed_set_multi_meta( $product_id, 'product_seats', $product['seats'] ?? arctic_seed_value_array( $legacy_seats ) );
	arctic_seed_set_multi_meta( $product_id, 'product_nozzles', $product['nozzles'] ?? arctic_seed_value_array( $legacy_jets ) );
	arctic_seed_set_multi_meta( $product_id, 'product_water_volume', $product['water_volume'] ?? arctic_seed_value_array( $legacy_volume ) );
	arctic_seed_set_product_configurations( $product_id, $configurations );
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

$timberwolf_description = 'Maximální terapii naleznete ve dvou pohodlných lehátkách, navíc je zde místo i pro třetího. Maximální komfort na minimálním prostoru.';
$timberwolf_id          = arctic_seed_post_by_meta( 'product', 'product_original_url', 'https://www.arctic-spas.cz/virivka-timberwolf.php', array(
	'post_status'  => 'publish',
	'post_title'   => 'Timberwolf',
	'post_name'    => 'timberwolf',
	'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $timberwolf_description ) . '</p><!-- /wp:paragraph -->',
	'menu_order'   => 50,
) );

update_post_meta( $timberwolf_id, 'product_type', 'standard' );
update_post_meta( $timberwolf_id, 'product_title_short', 'Timberwolf' );
update_post_meta( $timberwolf_id, 'product_price_text', 'od 246 700 Kč' );
update_post_meta( $timberwolf_id, 'product_price_suffix', 'vč. montáže' );
update_post_meta( $timberwolf_id, 'product_description_short', 'Kompaktní vířivka řady Classic se dvěma pohodlnými lehátky a místem pro třetí osobu.' );
update_post_meta( $timberwolf_id, 'product_description', $timberwolf_description );
update_post_meta( $timberwolf_id, 'product_cta_text', 'Poptat Timberwolf' );
set_post_thumbnail( $timberwolf_id, $timberwolf_signature );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_image', array( $timberwolf_signature ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_images', array( $timberwolf_signature, $timberwolf_side, $timberwolf_prestige ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_model', array( 'Classic Timberwolf' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_seats', array( '3 osoby' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_nozzles', array( 'Prestige 15/1', 'Signature 30/2' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_dimensions_external', array( '217 x 174 x 98 cm' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_water_volume', array( '884 litrů' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_acrylic_colors', array( 'Dakota', 'Kalahari', 'Odyssey', 'Platinum Swirl', 'Espresso' ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_cabinet_colors', array( 'Cedrový kabinet standardní', 'Bezúdržbový kabinet volitelný' ) );
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
arctic_seed_set_product_configurations( $timberwolf_id, array(
	array(
		'name'        => 'Prestige 15/1',
		'image'       => $timberwolf_prestige,
		'price'       => 'od 246 700 Kč',
		'seats'       => '3 osoby',
		'jets'        => '10 x 3" + 5 x 5"',
		'pumps'       => '1 dvourychlostní',
		'dimensions'  => '217 x 174 x 98 cm',
		'description' => 'Základní konfigurace s 15 tryskami a jedním dvourychlostním čerpadlem.',
	),
	array(
		'name'        => 'Signature 30/2',
		'image'       => $timberwolf_signature,
		'price'       => 'na dotaz',
		'seats'       => '3 osoby',
		'jets'        => '25 x 3" + 5 x 5"',
		'pumps'       => '1 dvourychlostní + 1 jednorychlostní',
		'dimensions'  => '217 x 174 x 98 cm',
		'description' => 'Silnější konfigurace podle původního webu Arctic Spas s 30 tryskami a dvěma čerpadly.',
	),
) );
wp_set_object_terms( $timberwolf_id, array( $kind_hot_tubs ), 'product-kind' );
wp_set_object_terms( $timberwolf_id, array( $series_classic ), 'product-series' );
wp_set_object_terms( $timberwolf_id, array( $category_hot_tubs ), 'product-category' );

$download_id = arctic_seed_post_by_meta( 'download', 'download_original_url', 'https://www.arctic-spas.cz/content/download/stavebni-pripravenost.pdf', array(
	'post_status'  => 'publish',
	'post_title'   => 'Stavební připravenost',
	'post_name'    => 'stavebni-pripravenost',
	'post_content' => '<!-- wp:paragraph --><p>Technický dokument pro přípravu místa před instalací vířivky Arctic Spas.</p><!-- /wp:paragraph -->',
	'menu_order'   => 10,
) );

update_post_meta( $download_id, 'download_file_url', wp_get_attachment_url( $prep_pdf ) );
update_post_meta( $download_id, 'download_document_type', 'preparation' );
wp_set_object_terms( $download_id, array( $download_preparation ), 'download-category' );

$download_definitions = array(
	array( 'path' => 'content/download/2020/navod-arctic-classic-2020.pdf', 'file' => 'download__2020__navod-arctic-classic-2020.pdf', 'title' => 'Návod Arctic Classic 2020', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/2020/navod-arctic-core-2020.pdf', 'file' => 'download__2020__navod-arctic-core-2020.pdf', 'title' => 'Návod Arctic Core 2020', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/2020/navod-arctic-custom-2020.pdf', 'file' => 'download__2020__navod-arctic-custom-2020.pdf', 'title' => 'Návod Arctic Custom 2020', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/AS-Brochure-2022_cz.pdf', 'file' => 'download__AS-Brochure-2022_cz.pdf', 'title' => 'Arctic Spas katalog 2022', 'term' => $download_catalogs, 'type' => 'catalog' ),
	array( 'path' => 'content/download/economy-mode.pdf', 'file' => 'download__economy-mode.pdf', 'title' => 'Economy mode', 'term' => $download_technical, 'type' => 'technical' ),
	array( 'path' => 'content/download/equipment-location.pdf', 'file' => 'download__equipment-location.pdf', 'title' => 'Equipment location', 'term' => $download_technical, 'type' => 'technical' ),
	array( 'path' => 'content/download/navod-arctic-2010.pdf', 'file' => 'download__navod-arctic-2010.pdf', 'title' => 'Návod Arctic 2010', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-arctic-2015.pdf', 'file' => 'download__navod-arctic-2015.pdf', 'title' => 'Návod Arctic 2015', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-arctic-2018.pdf', 'file' => 'download__navod-arctic-2018.pdf', 'title' => 'Návod Arctic 2018', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-arctic-2020-en.pdf', 'file' => 'download__navod-arctic-2020-en.pdf', 'title' => 'Arctic manual 2020 EN', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-coyote.pdf', 'file' => 'download__navod-coyote.pdf', 'title' => 'Návod Coyote', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-onzen.pdf', 'file' => 'download__navod-onzen.pdf', 'title' => 'Návod Onzen', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-spaboy-2017.pdf', 'file' => 'download__navod-spaboy-2017.pdf', 'title' => 'Návod Spa Boy 2017', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/navod-veslarsky-trenazer.pdf', 'file' => 'download__navod-veslarsky-trenazer.pdf', 'title' => 'Návod veslařský trenažér', 'term' => $download_manuals, 'type' => 'manual' ),
	array( 'path' => 'content/download/rozmery/rozmery-7-in.pdf', 'file' => 'download__rozmery__rozmery-7-in.pdf', 'title' => 'Rozměry 7 ft', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-8-in.pdf', 'file' => 'download__rozmery__rozmery-8-in.pdf', 'title' => 'Rozměry 8 ft', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-fox-in.pdf', 'file' => 'download__rozmery__rozmery-fox-in.pdf', 'title' => 'Rozměry Arctic Fox', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-frontier-in.pdf', 'file' => 'download__rozmery__rozmery-frontier-in.pdf', 'title' => 'Rozměry Frontier', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-ocean-in.pdf', 'file' => 'download__rozmery__rozmery-ocean-in.pdf', 'title' => 'Rozměry Arctic Ocean', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-summitxl-in.pdf', 'file' => 'download__rozmery__rozmery-summitxl-in.pdf', 'title' => 'Rozměry Summit XL', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/rozmery/rozmery-timberwolf-in.pdf', 'file' => 'download__rozmery__rozmery-timberwolf-in.pdf', 'title' => 'Rozměry Timberwolf', 'term' => $download_dimensions, 'type' => 'dimensions' ),
	array( 'path' => 'content/download/stavebni-pripravenost.pdf', 'file' => 'download__stavebni-pripravenost.pdf', 'title' => 'Stavební připravenost', 'term' => $download_preparation, 'type' => 'preparation' ),
	array( 'path' => 'content/download/zaklady-upravy-vody.pdf', 'file' => 'download__zaklady-upravy-vody.pdf', 'title' => 'Základy úpravy vody', 'term' => $download_water, 'type' => 'water' ),
	array( 'path' => 'content/download/zaruky/zaruky-arctic.pdf', 'file' => 'download__zaruky__zaruky-arctic.pdf', 'title' => 'Záruky Arctic Spas', 'term' => $download_warranty, 'type' => 'warranty' ),
	array( 'path' => 'content/download/zaruky/zaruky-bear.pdf', 'file' => 'download__zaruky__zaruky-bear.pdf', 'title' => 'Záruky Bear', 'term' => $download_warranty, 'type' => 'warranty' ),
	array( 'path' => 'content/img/vybava/onspa/moznosti-pripojeni-do-site.pdf', 'file' => 'img__vybava__onspa__moznosti-pripojeni-do-site.pdf', 'title' => 'Možnosti připojení do sítě', 'term' => $download_technical, 'type' => 'technical' ),
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
		'post_content' => '<!-- wp:paragraph --><p>Dokument importovaný z původního webu Arctic Spas.</p><!-- /wp:paragraph -->',
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
	'post_status'  => 'draft',
	'post_title'   => 'Lunar a Orion 2025',
	'post_content' => '<!-- wp:paragraph --><p>Nové modely řady Core s konfigurací Prestige nebo Signature a cenou od 249 000 Kč.</p><!-- /wp:paragraph -->',
	'menu_order'   => 20,
) );
set_post_thumbnail( $slide_lunar_id, $lunar_cover );
update_post_meta( $slide_lunar_id, 'button_text', 'Prohlédnout Lunar' );
update_post_meta( $slide_lunar_id, 'button_url_post', $lunar_id );

$slide_showroom_id = arctic_seed_post_by_meta( 'slide', '_arctic_seed_key', 'home-hero-showroom', array(
	'post_status'  => 'draft',
	'post_title'   => 'Showroom u Brna',
	'post_content' => '<!-- wp:paragraph --><p>Přijeďte si vířivku osahat, porovnat výbavu a projít technickou přípravu s konzultantem.</p><!-- /wp:paragraph -->',
	'menu_order'   => 30,
) );
set_post_thumbnail( $slide_showroom_id, $showroom );
update_post_meta( $slide_showroom_id, 'button_text', 'Navstivit showroom' );

$hot_tubs_url = home_url( '/virivky/' );
$covers_url   = get_term_link( $category_covers, 'product-category' );
$swimspa_url  = home_url( '/swimspa/' );
$covers_url   = is_wp_error( $covers_url ) ? home_url( '/catalog/dalsi-sortiment/' ) : $covers_url;

$home_id = arctic_seed_page(
	'uvod',
	'Uvod',
	'<!-- wp:heading {"textAlign":"center"} --><h2 class="wp-block-heading has-text-align-center">Jsme výhradní prodejce</h2><!-- /wp:heading -->'
	. '<!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">Rádi vám pomůžeme při výběru bazénu nebo vířivky. Jsme vám k dispozici, ať už máte přesnou představu nebo se myšlenkami na bazén či vířivku teprve začínáte zabývat. Nejprve se pobavíme o vašich přáních a potřebách, navrhneme vhodné řešení, zpracujeme podrobnou nabídku a nakonec projekt zrealizujeme.</p><!-- /wp:paragraph -->'
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
	'',
	'template-showroom.php'
);
set_post_thumbnail( $showroom_id, $figma_showroom_hero );
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
	'Vlastnosti a výhody venkovních vířivek a swimspa Arctic',
	'',
	'template-features.php'
);
update_post_meta( $features_id, 'page_description_text', 'Vířivky Arctic Spas jsou navrženy a vyrobeny tak, aby efektivně a spolehlivě fungovaly v extrémním klimatu severní Kanady. V místech, kde venkovní teploty kolísají od -30 °C v zimě do +30 °C v letních měsících, obstojí pouze skvěle tepelně izolovaná vířivka postavená z těch skutečně nejlepších komponentů.' );

$feature_insulation_id = arctic_seed_page(
	'izolace-virivky',
	'Obvodová izolace FreeHeat™',
	'',
	'template-feature-detail.php'
);
wp_update_post( array(
	'ID'          => $feature_insulation_id,
	'post_parent' => $features_id,
) );
update_post_meta( $feature_insulation_id, 'page_description_text', 'Investice do výroby samonosné skořepiny umožňuje izolovat vířivky Arctic Spas® lépe, než jak to provádí kdokoliv z konkurence. Pro inspiraci jsme nemuseli chodit daleko, naše domovy v severní Kanadě nám poskytly vše, co potřebujeme vědět.' );

$warranty_id = arctic_seed_page(
	'zaruka',
	'Záruka',
	'',
	'template-warranty.php'
);
update_post_meta( $warranty_id, 'page_description_text', 'Ve společnosti Arctic Spas® jsme pyšní na kvalitu našeho technického řešení a na vysokou úroveň zpracování. Na naše výrobky poskytujeme tyto prodloužené záruky.' );

$info_id = arctic_seed_page(
	'dalsi-informace',
	'Další informace',
	'',
	'template-more-info.php'
);

$services_id = arctic_seed_page(
	'sluzby',
	'Služby',
	'',
	'template-services.php'
);
update_post_meta( $services_id, 'page_description_text', 'K prémiovému sortimentu patří prvotřídní služby. Poskytneme vám mnohem víc, než je vůbec schopna nabídnout většina konkurence, mnohé naše služby přitom můžete využít nezávazně nebo zdarma v rámci ceny vířivky.' );

$service_request_id = arctic_seed_page(
	'servis',
	'Servis vířivek Arctic Spas',
	'',
	'template-service-request.php'
);
update_post_meta( $service_request_id, 'page_description_text', 'Okamžikem zprovoznění vaší masážní vířivky nebo celoročního bazénu naše péče o vaše pohodlí nekončí. Kromě záručního a pozáručního servisu můžeme nabídnout např. telefonické poradenství, asistenci při výměně vody nebo zazimování. Vyplňte prosím pečlivě náš servisní formulář, ozveme se co nejdříve. Veškeré servisní služby provádíme jen na vířivkách námi instalovaných, tedy Arctic Spas, Coyote Spas, Dream Maker Spas, Novitek.' );

$certificates_id = arctic_seed_page(
	'certifikaty',
	'Certifikáty',
	'',
	'template-certificates.php'
);
update_post_meta( $certificates_id, 'page_description_text', 'Návštěva jakékoliv výrobní haly firmy Arctic Spas® ukazuje to nejlepší z obou světů. Otvory pro vodní trysky jsou ve skořepině vyřezávány precizní robotickou technologií, zatímco odborně vyškolení pracovníci ručně sestavují dřevěné cedrové kabinety. Tyto postupy jsou kombinovány do optimálního poměru vyspělé technologie a ruční práce, což dodává vířivkám Arctic Spas® skutečně unikátní a nadčasovou hodnotu pro Váš domov!' );

$maintenance_id = arctic_seed_page(
	'kolik-stoji-udrzba',
	'Kolik stojí provoz a údržba vířivky?',
	'',
	'template-maintenance.php'
);
update_post_meta( $maintenance_id, 'page_description_text', 'Jedním z nejdůležitějších parametrů ve kterých značka Arctic Spas jednoznačně dominuje jsou velmi nízké provozní náklady a s tím spojená kvalita izolací a termo krytů. Žádná značka vířivek nemá obvodovou izolaci silnou 8 - 10 cm jako vířivky Arctic Spas a neexistují žádné alternativy, které takovouto izolaci nahradí. Vířivky Arctic Spas jsou vyráběny u nejseverněji položeného výrobce vířivek a i proto patří mezi nejúspornější vířivky na světě, číslo jedna na trhu jsou ve Skandinávii, Kanadě a na severu Spojených států.' );

$about_id = arctic_seed_page(
	'o-nas',
	'O nás',
	'',
	'template-about.php'
);
update_post_meta( $about_id, 'page_description_text', 'Pořízení bazénu nebo vířivky je investice, která by měla být pečlivě zvážena. Při výběru značky i dodavatele je rozumné seznámit se s profilem výrobce a prodejce.' );

$downloads_page_id = arctic_seed_page(
	'ke-stazeni',
	'Ke stažení',
	'',
	'template-downloads.php'
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

$privacy_content = <<<'HTML'
<!-- wp:heading --><h2 class="wp-block-heading">Základní ustanovení</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Správcem osobních údajů podle čl. 4 bod 7 nařízení Evropského parlamentu a Rady (EU) 2016/679 (GDPR) je BASPA s.r.o., se sídlem Bohunická cesta 727/15, 664 48 Moravany, IČ 02257467, DIČ CZ02257467, zapsaná u Krajského soudu v Brně, oddíl C, vložka 80736.</p><!-- /wp:paragraph -->
<!-- wp:list --><ul><li>Adresa: Bohunická cesta 727/15, 664 48 Moravany</li><li>E-mail: lukas.dusek@arctic-spas.cz</li><li>Telefon: +420 777 099 687</li></ul><!-- /wp:list -->
<!-- wp:paragraph --><p>Osobními údaji se rozumí veškeré informace o identifikované nebo identifikovatelné fyzické osobě. Správce nejmenoval pověřence pro ochranu osobních údajů.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Zdroje a kategorie zpracovávaných údajů</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Zpracováváme osobní údaje, které nám poskytnete prostřednictvím kontaktních formulářů, e-mailu, telefonu nebo při poptávce a realizaci zakázky. Jedná se zejména o identifikační a kontaktní údaje, údaje potřebné pro vyřízení poptávky, objednávky, servisu nebo plnění smlouvy.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Zákonný důvod a účel zpracování</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Zákonným důvodem zpracování je plnění smlouvy nebo jednání o smlouvě, oprávněný zájem správce na komunikaci se zákazníky a poskytování přímého marketingu, případně váš souhlas se zasíláním obchodních sdělení, pokud je vyžadován.</p><!-- /wp:paragraph -->
<!-- wp:list --><ul><li>vyřízení poptávky, objednávky, servisu nebo reklamace,</li><li>příprava nabídky a technické konzultace,</li><li>plnění práv a povinností ze smluvního vztahu,</li><li>zasílání obchodních sdělení a související marketingová komunikace.</li></ul><!-- /wp:list -->
<!-- wp:paragraph --><p>Ze strany správce nedochází k automatickému individuálnímu rozhodování ve smyslu čl. 22 GDPR.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Doba uchovávání údajů</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Osobní údaje uchováváme po dobu nezbytnou k vyřízení poptávky, plnění smlouvy, ochraně právních nároků a splnění zákonných povinností. Údaje zpracovávané na základě souhlasu uchováváme do odvolání souhlasu, nejdéle po dobu uvedenou při jeho udělení.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Příjemci osobních údajů</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Příjemci osobních údajů mohou být osoby a společnosti podílející se na dodání zboží, služeb, servisu, účetnictví, provozu webové prezentace, správě poptávek a marketingové komunikaci. Osobní údaje nemáme v úmyslu předávat do třetí země mimo EU nebo mezinárodní organizaci.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Vaše práva</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Za podmínek stanovených v GDPR máte právo na přístup ke svým osobním údajům, jejich opravu, výmaz, omezení zpracování, přenositelnost údajů, právo vznést námitku proti zpracování a právo odvolat souhlas se zpracováním. Máte také právo podat stížnost u Úřadu pro ochranu osobních údajů.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Zabezpečení osobních údajů</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Správce přijal vhodná technická a organizační opatření k zabezpečení osobních údajů. K osobním údajům mají přístup pouze pověřené osoby a smluvní zpracovatelé v rozsahu potřebném pro zajištění služeb.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Závěrečná ustanovení</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Odesláním kontaktního nebo poptávkového formuláře potvrzujete, že jste se seznámili s těmito zásadami zpracování osobních údajů. Správce je oprávněn tyto podmínky aktualizovat zveřejněním nové verze na webových stránkách.</p><!-- /wp:paragraph -->
HTML;

$privacy_id = arctic_seed_page(
	'ochrana-osobnich-udaju',
	'Ochrana osobních údajů',
	$privacy_content
);
update_post_meta( $privacy_id, 'page_description_text', 'Informace o tom, jak BASPA s.r.o. jako provozovatel Arctic Spas CZ zpracovává osobní údaje z kontaktních formulářů, poptávek a zákaznické komunikace.' );
update_option( 'wp_page_for_privacy_policy', $privacy_id );

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
	array(
		'title'    => 'Vířivky',
		'url'      => $hot_tubs_url,
		'classes'  => array( 'arctic-menu-products' ),
		'children' => array(
			array( 'title' => 'Vybrat podle parametrů', 'url' => $hot_tubs_url ),
			array( 'title' => 'Série Core', 'url' => $hot_tubs_url . '#serie-core' ),
			array( 'title' => 'Série Classic', 'url' => $hot_tubs_url . '#serie-classic' ),
			array( 'title' => 'Série Custom', 'url' => $hot_tubs_url . '#serie-custom' ),
		),
	),
	array(
		'title'    => 'Celoroční bazény',
		'url'      => $swimspa_url,
		'classes'  => array( 'arctic-menu-products' ),
		'children' => array(
			array( 'title' => 'Vybrat podle parametrů', 'url' => $swimspa_url ),
			array( 'title' => 'Série Core', 'url' => $swimspa_url . '#serie-core' ),
			array( 'title' => 'Série Classic', 'url' => $swimspa_url . '#serie-classic' ),
		),
	),
	array(
		'title'    => 'Vlastnosti',
		'url'      => '#',
		'classes'  => array( 'arctic-menu-features' ),
		'children' => array(
			array( 'title' => 'Tepelná izolace', 'url' => get_permalink( $feature_insulation_id ) ),
			array( 'title' => 'Skořepina', 'url' => home_url( '/vlastnosti/#skorepina' ) ),
			array( 'title' => 'Termokryt', 'url' => home_url( '/vlastnosti/#termokryt' ) ),
			array( 'title' => 'Podlaha', 'url' => home_url( '/vlastnosti/#podlaha' ) ),
			array( 'title' => 'Servisní přístup', 'url' => home_url( '/podpora/#servis' ) ),
			array( 'title' => 'Variabilita', 'url' => home_url( '/vlastnosti/#variabilita' ) ),
		),
	),
	array(
		'title'    => 'Další informace',
		'url'      => '#',
		'classes'  => array( 'arctic-menu-info' ),
		'children' => array(
			array( 'title' => 'Služby', 'url' => get_permalink( $services_id ) ),
			array( 'title' => 'Certifikáty', 'url' => get_permalink( $certificates_id ) ),
			array( 'title' => 'Záruka', 'url' => get_permalink( $warranty_id ) ),
			array( 'title' => 'Kolik stojí provoz a údržba', 'url' => get_permalink( $maintenance_id ) ),
			array( 'title' => 'Podpora', 'url' => get_permalink( $support_id ) ),
			array( 'title' => 'Reference', 'url' => get_permalink( $references_page_id ) ),
			array( 'title' => 'O nás', 'url' => get_permalink( $about_id ) ),
			array( 'title' => 'Showroom', 'url' => get_permalink( $showroom_id ) ),
			array( 'title' => 'Servis', 'url' => get_permalink( $service_request_id ) ),
			array( 'title' => 'Kontakt', 'url' => get_permalink( $contact_id ) ),
		),
	),
) );

arctic_seed_menu( 'Arctic horni lista', 'navigation_bar', array(
	array( 'title' => 'Lunar', 'url' => get_permalink( $lunar_id ) ),
	array( 'title' => 'Orion', 'url' => get_permalink( $orion_id ) ),
	array( 'title' => 'Ke stažení', 'url' => get_permalink( $downloads_page_id ) ),
) );

arctic_seed_menu( 'Arctic paticka', 'navigation_footer', array(
	array( 'title' => 'Vířivky', 'url' => $hot_tubs_url ),
	array( 'title' => 'Celoroční bazény', 'url' => $swimspa_url ),
	array( 'title' => 'Další sortiment', 'url' => $covers_url ),
	array( 'title' => 'Vlastnosti vířivek', 'url' => get_permalink( $features_id ) ),
	array( 'title' => 'Průběh realizace', 'url' => home_url( '/#order-progress' ) ),
	array( 'title' => 'Podpora', 'url' => get_permalink( $support_id ) ),
	array( 'title' => 'Servis', 'url' => get_permalink( $service_request_id ) ),
	array( 'title' => 'Reference', 'url' => get_permalink( $references_page_id ) ),
	array( 'title' => 'O nás', 'url' => get_permalink( $about_id ) ),
	array( 'title' => 'Showroom', 'url' => get_permalink( $showroom_id ) ),
	array( 'title' => 'Kontakt', 'url' => get_permalink( $contact_id ) ),
) );

flush_rewrite_rules();

if ( function_exists( 'WP_CLI' ) ) {
	WP_CLI::success( 'Seeded Arctic pilot content, shell pages, menus, media, and local settings.' );
}
