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

function arctic_seed_post_id_by_slug( string $post_type, string $slug ): int {
	$post = get_page_by_path( $slug, OBJECT, $post_type );

	return $post instanceof WP_Post ? (int) $post->ID : 0;
}

function arctic_seed_set_product_gallery_by_slug( string $slug, array $image_ids, bool $update_primary = false, int $primary_image_id = 0 ): void {
	$post_id = arctic_seed_post_id_by_slug( 'product', $slug );

	if ( !$post_id ) {
		return;
	}

	$gallery = array();

	foreach ( $image_ids as $image_id ) {
		$image_id = absint( $image_id );
		if ( $image_id && !in_array( $image_id, $gallery, true ) ) {
			$gallery[] = $image_id;
		}
	}

	foreach ( get_post_meta( $post_id, 'product_images', false ) as $image_id ) {
		$image_id = absint( $image_id );
		if ( $image_id && !in_array( $image_id, $gallery, true ) ) {
			$gallery[] = $image_id;
		}
	}

	if ( empty( $gallery ) ) {
		return;
	}

	arctic_seed_set_multi_meta( $post_id, 'product_images', $gallery );

	if ( $update_primary ) {
		$primary_image_id = $primary_image_id ? absint( $primary_image_id ) : $gallery[0];
		set_post_thumbnail( $post_id, $primary_image_id );
		arctic_seed_set_multi_meta( $post_id, 'product_image', array( $primary_image_id ) );
	}
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

	$resolve_object_item = static function( array $item ): array {
		$object_id = !empty( $item['object_id'] ) ? (int) $item['object_id'] : 0;
		$object    = $item['object'] ?? 'page';
		$type      = $item['type'] ?? 'post_type';
		$url       = (string) ( $item['url'] ?? '' );

		if ( $object_id > 0 ) {
			return array( $object_id, $object, $type );
		}

		if ( '' !== $url && '#' !== $url ) {
			$parts = wp_parse_url( $url );

			if ( empty( $parts['query'] ) && empty( $parts['fragment'] ) ) {
				$post_id = url_to_postid( $url );
				$post    = $post_id ? get_post( $post_id ) : null;

				if ( $post instanceof WP_Post ) {
					return array( (int) $post->ID, $post->post_type, 'post_type' );
				}
			}
		}

		if ( '#' === $url || '' === $url ) {
			$page = get_page_by_path( sanitize_title( (string) ( $item['title'] ?? '' ) ) );

			if ( $page instanceof WP_Post ) {
				return array( (int) $page->ID, 'page', 'post_type' );
			}
		}

		return array( 0, '', '' );
	};

	$add_item = function( array $item, int $parent_id = 0 ) use ( &$add_item, $menu_id, $resolve_object_item ): int {
		list( $object_id, $object, $type ) = $resolve_object_item( $item );

		$menu_item_args = array(
			'menu-item-title'     => $item['title'],
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent_id,
			'menu-item-classes'   => implode( ' ', $item['classes'] ?? array() ),
		);

		if ( $object_id > 0 ) {
			$menu_item_args['menu-item-object-id'] = $object_id;
			$menu_item_args['menu-item-object']    = $object;
			$menu_item_args['menu-item-type']      = $type;
		} else {
			$menu_item_args['menu-item-url']  = $item['url'] ?? '#';
			$menu_item_args['menu-item-type'] = 'custom';
		}

		$menu_item_id = wp_update_nav_menu_item( $menu_id, 0, $menu_item_args );

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
$series_swimspa_classic = arctic_seed_term( 'product-series', 'Bazény ARCTIC Classic', 'swimspa-classic' );
$series_swimspa_custom = arctic_seed_term( 'product-series', 'Bazény ARCTIC Custom', 'swimspa-custom' );
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
$figma_offer_promo_product = arctic_seed_attachment( 'figma-node-1-254-hp-fixed-banner-product', 'uploads/import/figma/hp-fixed-banner-product.png', 'Figma akční nabídka - promo produkt', 'Produktový obrázek pro promo kartu akční nabídky' );
$figma_home_benefit_install = arctic_seed_attachment( 'figma-home-benefit-montaz', 'uploads/import/figma/hp-benefit-montaz.png', 'Homepage benefit - montáž', '' );
$figma_home_benefit_support = arctic_seed_attachment( 'figma-home-benefit-podpora', 'uploads/import/figma/hp-benefit-podpora.png', 'Homepage benefit - podpora', '' );
$figma_home_benefit_service = arctic_seed_attachment( 'figma-home-benefit-servis', 'uploads/import/figma/hp-benefit-servis.png', 'Homepage benefit - servis', '' );
$figma_category_hero_virivky = arctic_seed_attachment( 'figma-node-1-263-category-hero-virivky', 'uploads/import/figma/category-hero-virivky.jpg', 'Figma kategorie - hero vířivky', 'Hero fotografie kategorie vířivek Arctic Spas' );
$figma_category_swimspa_intro = arctic_seed_attachment( 'figma-category-celorocni-bazeny-intro', 'uploads/import/figma-category-celorocni-bazeny.jpg', 'Figma kategorie - celoroční bazény', 'Fotografie k intro sekci celoročních bazénů Arctic Spas' );
$figma_category_vlastnosti = arctic_seed_attachment( 'figma-node-1-273-category-vlastnosti', 'uploads/import/figma/category-vlastnosti.jpg', 'Figma kategorie - vlastnosti', 'Fotografie k sekci vlastnosti vířivek Arctic Spas' );
$figma_category_zaruka = arctic_seed_attachment( 'figma-node-1-274-category-zaruka', 'uploads/import/figma/category-zaruka.jpg', 'Figma kategorie - záruka', 'Fotografie k sekci záruka Arctic Spas' );
$figma_popup_shell_detail = arctic_seed_attachment( 'figma-popup-shell-detail', 'uploads/import/figma/popup-shell-detail.png', 'Figma popup - detail skořepiny', 'Detail skořepiny Arctic Spas pro popup výhod' );
$legacy_feature_insulation = arctic_seed_attachment( 'legacy-feature-freeheat', 'uploads/import/legacy-features/feature-izolace-freeheat.jpg', 'FreeHeat - izolace vířivky', 'Obvodová izolace FreeHeat vířivky Arctic Spas' );
$legacy_feature_shell = arctic_seed_attachment( 'legacy-feature-shell', 'uploads/import/legacy-features/feature-skorepina.jpg', 'Samonosná skořepina vířivky', 'Samonosná kompozitní skořepina vířivky Arctic Spas' );
$legacy_feature_cover = arctic_seed_attachment( 'legacy-feature-cover', 'uploads/import/legacy-features/feature-termokryt.jpg', 'Termokryt Mylovac', 'Termokryt Mylovac vířivky Arctic Spas' );
$legacy_feature_floor = arctic_seed_attachment( 'legacy-feature-floor', 'uploads/import/legacy-features/feature-podlaha.jpg', 'Podlaha Forever Floor', 'Podlaha Forever Floor vířivky Arctic Spas' );
$legacy_feature_service_access = arctic_seed_attachment( 'legacy-feature-service-access', 'uploads/import/legacy-features/feature-servisni-pristup.jpg', 'Servisní přístup vířivky', 'Servisní přístup ke komponentům vířivky Arctic Spas' );
$legacy_feature_variability = arctic_seed_attachment( 'legacy-feature-variability', 'uploads/import/legacy-features/feature-variabilita.jpg', 'Variabilita výběru vířivky', 'Variabilita výběru modelu, barev a výbavy Arctic Spas' );
$legacy_feature_spa_boy = arctic_seed_attachment( 'legacy-feature-spa-boy', 'uploads/import/legacy-features/feature-spa-boy.jpg', 'Automatická dezinfekce Spa Boy', 'Automatická péče o vodu Spa Boy Arctic Spas' );
$figma_feature_freeheat_diagram = arctic_seed_attachment( 'figma-feature-freeheat-diagram', 'uploads/import/figma/feature-freeheat-diagram.png', 'Diagram izolace FreeHeat', 'Schéma obvodové izolace FreeHeat Arctic Spas' );
$figma_configurator = arctic_seed_attachment( 'figma-node-1-409-category-configurator', 'uploads/import/figma/category-configurator.png', 'Figma konfigurátor', 'Konfigurátor vířivky Arctic Spas' );
$curator_hot_tub_hero = arctic_seed_attachment( 'curator-hot-tub-hero-lake-01', 'uploads/import/curator/01-header-virivky/virivky-hero-leto-jezero-01.jpg', 'Curator hot tub hero - lake', 'Arctic Spas hot tub in a landscape setting' );
$curator_hot_tub_hero_winter = arctic_seed_attachment( 'curator-hot-tub-hero-winter-01', 'uploads/import/curator/01-header-virivky/virivky-hero-zima-snih-01.jpg', 'Curator hot tub hero - winter', 'Arctic Spas hot tub in winter' );
$curator_hot_tub_core_hero = arctic_seed_attachment( 'curator-hot-tub-core-husky', 'uploads/import/curator/03-virivky-modelove-rady/virivky-core-husky-reprezentacni.jpg', 'Curator hot tub Core - Husky', 'Arctic Spas Core representative hot tub' );
$curator_hot_tub_core_top = arctic_seed_attachment( 'curator-hot-tub-core-nova-top-view', 'uploads/import/curator/03-virivky-modelove-rady/virivky-core-nova-top-view.png', 'Curator hot tub Core - Nova top view', 'Arctic Spas Core top view' );
$curator_hot_tub_classic_hero = arctic_seed_attachment( 'curator-hot-tub-classic-eagle', 'uploads/import/curator/03-virivky-modelove-rady/virivky-classic-eagle-reprezentacni.jpg', 'Curator hot tub Classic - Eagle', 'Arctic Spas Classic representative hot tub' );
$curator_hot_tub_classic_top = arctic_seed_attachment( 'curator-hot-tub-classic-whistler-top-view', 'uploads/import/curator/03-virivky-modelove-rady/virivky-classic-whistler-top-view.png', 'Curator hot tub Classic - Whistler top view', 'Arctic Spas Classic top view' );
$curator_hot_tub_custom_fox = arctic_seed_attachment( 'curator-hot-tub-custom-arctic-fox', 'uploads/import/curator/03-virivky-modelove-rady/virivky-custom-arctic-fox-reprezentacni.jpg', 'Curator hot tub Custom - Arctic Fox', 'Arctic Spas Custom Arctic Fox' );
$curator_hot_tub_custom_yukon = arctic_seed_attachment( 'curator-hot-tub-custom-yukon', 'uploads/import/curator/03-virivky-modelove-rady/virivky-custom-yukon-reprezentacni.jpg', 'Curator hot tub Custom - Yukon', 'Arctic Spas Custom Yukon' );
$curator_swimspa_hero = arctic_seed_attachment( 'curator-swimspa-hero-mountains', 'uploads/import/curator/02-header-celorocni-bazeny/celorocni-bazen-hero-hory-lifestyle-01.jpg', 'Curator swimspa hero - mountains', 'Arctic all-weather pool in a mountain setting' );
$curator_swimspa_ocean_hero = arctic_seed_attachment( 'curator-swimspa-ocean-side-render', 'uploads/import/curator/02-header-celorocni-bazeny/celorocni-bazen-hero-ocean-side-render-01.png', 'Curator swimspa Custom - Ocean render', 'Arctic Ocean all-weather pool render' );
$curator_swimspa_polar_bear_hero = arctic_seed_attachment( 'curator-swimspa-polar-bear-render', 'uploads/import/curator/02-header-celorocni-bazeny/celorocni-bazen-hero-polar-bear-render-01.png', 'Curator swimspa Classic - Polar Bear render', 'Arctic Classic all-weather pool render' );
$curator_swimspa_product_images = array(
	'athabascan' => arctic_seed_attachment( 'curator-awp-athabascan-card-render', 'uploads/import/curator/04-celorocni-bazeny-modely-menu-karty/awp-athabascan-card-render.png', 'Curator AWP Athabascan render', 'Arctic Athabascan all-weather pool' ),
	'hudson'     => arctic_seed_attachment( 'curator-awp-hudson-card-render', 'uploads/import/curator/04-celorocni-bazeny-modely-menu-karty/awp-hudson-card-render.png', 'Curator AWP Hudson render', 'Arctic Hudson all-weather pool' ),
	'kingfisher' => arctic_seed_attachment( 'curator-awp-kingfisher-card-render', 'uploads/import/curator/04-celorocni-bazeny-modely-menu-karty/awp-kingfisher-card-render.png', 'Curator AWP Kingfisher render', 'Arctic Kingfisher all-weather pool' ),
	'ocean'      => arctic_seed_attachment( 'curator-awp-ocean-card-render', 'uploads/import/curator/04-celorocni-bazeny-modely-menu-karty/awp-ocean-card-render.png', 'Curator AWP Ocean render', 'Arctic Ocean all-weather pool' ),
	'okanagan'   => arctic_seed_attachment( 'curator-awp-okanagan-card-render', 'uploads/import/curator/04-celorocni-bazeny-modely-menu-karty/awp-okanagan-card-render.png', 'Curator AWP Okanagan render', 'Arctic Okanagan all-weather pool' ),
	'wolverine'  => arctic_seed_attachment( 'curator-awp-wolverine-card-render', 'uploads/import/curator/04-celorocni-bazeny-modely-menu-karty/awp-wolverine-card-render.png', 'Curator AWP Wolverine render', 'Arctic Wolverine all-weather pool' ),
);
$curator_feature_cover = arctic_seed_attachment( 'curator-feature-cover-hot-tub', 'uploads/import/curator/05-vlastnosti-termokryt/vlastnosti-termokryt-virivka-01.jpg', 'Curator feature - cover hot tub', 'Arctic Spas thermal cover on a hot tub' );
$curator_feature_cover_swimspa = arctic_seed_attachment( 'curator-feature-cover-swimspa', 'uploads/import/curator/05-vlastnosti-termokryt/vlastnosti-termokryt-celorocni-bazen-01.jpg', 'Curator feature - cover swimspa', 'Arctic all-weather pool thermal cover' );
$curator_feature_floor = arctic_seed_attachment( 'curator-feature-forever-floor-detail', 'uploads/import/curator/06-vlastnosti-podlaha-forever-floor/vlastnosti-podlaha-forever-floor-detail-01.jpg', 'Curator feature - Forever Floor', 'Arctic Spas Forever Floor detail' );
$curator_feature_service_access = arctic_seed_attachment( 'curator-feature-cabinet-detail', 'uploads/import/curator/07-vlastnosti-kabinet-barvy/vlastnosti-kabinet-detail-01.jpg', 'Curator feature - service access', 'Arctic Spas cabinet detail' );
$curator_feature_variability = arctic_seed_attachment( 'curator-feature-cabinet-colors', 'uploads/import/curator/07-vlastnosti-kabinet-barvy/vlastnosti-kabinet-detail-02.jpg', 'Curator feature - cabinet colors', 'Arctic Spas cabinet colors' );
$curator_feature_disinfection = arctic_seed_attachment( 'curator-feature-control-panel', 'uploads/import/curator/08-vlastnosti-ovladani-trysky/vlastnosti-ovladani-ruka-panel-01.jpg', 'Curator feature - control panel', 'Arctic Spas control panel' );
$curator_feature_shell = arctic_seed_attachment( 'curator-feature-acrylic-shell', 'uploads/import/curator/09-vlastnosti-skorepina-akryl/vlastnosti-akryl-platinum-swirl-01.jpg', 'Curator feature - acrylic shell', 'Arctic Spas acrylic shell detail' );
$certificate_images = array(
	arctic_seed_attachment( 'certificate-tuv-1', 'uploads/import/figma/certificate-tuv-1.png', 'Certifikát TÜV 1', 'Certifikát TÜV Arctic Spas' ),
	arctic_seed_attachment( 'certificate-tuv-2', 'uploads/import/figma/certificate-tuv-2.png', 'Certifikát TÜV 2', 'Certifikát TÜV Arctic Spas' ),
	arctic_seed_attachment( 'certificate-tuv-3', 'uploads/import/figma/certificate-tuv-3.png', 'Certifikát TÜV 3', 'Certifikát TÜV Arctic Spas' ),
);
$legacy_category_hot_tubs_life = arctic_seed_attachment( 'legacy-category-virivky', 'uploads/import/legacy-categories/virivky.jpg', 'Arctic Spas - vířivky ze starého webu', 'Obsahová fotografie vířivky ze starého Arctic webu' );
$legacy_category_swimspa_life = arctic_seed_attachment( 'legacy-category-swimspa', 'uploads/import/legacy-categories/swimspa.jpg', 'Arctic Spas - swimspa ze starého webu', 'Obsahová fotografie swimspa ze starého Arctic webu' );
$service_images = array(
	'consultation'    => arctic_seed_attachment( 'service-card-consultation', 'uploads/import/legacy-services/service-consultation.jpg', 'Konzultace Arctic Spas', 'Konzultace služeb Arctic Spas' ),
	'meeting'         => arctic_seed_attachment( 'service-card-meeting', 'uploads/import/legacy-services/service-meeting.jpg', 'Osobní schůzka Arctic Spas', 'Osobní schůzka a posouzení místa instalace' ),
	'catalog'         => arctic_seed_attachment( 'service-card-catalog', 'uploads/import/legacy-services/service-catalog.jpg', 'Katalog a cenová nabídka Arctic Spas', 'Katalog a cenová nabídka vířivek' ),
	'showroom'        => arctic_seed_attachment( 'service-card-showroom', 'uploads/import/legacy-services/service-showroom.jpg', 'Vzorková prodejna Arctic Spas', 'Vzorková prodejna a showroom Arctic Spas' ),
	'delivery'        => arctic_seed_attachment( 'service-card-delivery', 'uploads/import/legacy-services/service-delivery.jpg', 'Dovoz a instalace Arctic Spas', 'Dovoz, instalace a zaškolení vířivky' ),
	'service-support' => arctic_seed_attachment( 'service-card-support', 'uploads/import/legacy-services/service-support.jpg', 'Záruční a pozáruční servis Arctic Spas', 'Záruční a pozáruční servis Arctic Spas' ),
);
$legacy_reference_fox_life = arctic_seed_attachment( 'legacy-reference-arctic-fox-life', 'uploads/import/legacy-references/arctic-fox-lidi.jpg', 'Reference Arctic Fox', 'Reference zákazníka ze starého Arctic webu' );
$legacy_reference_fox_life_2 = arctic_seed_attachment( 'legacy-reference-arctic-fox-life-2', 'uploads/import/legacy-references/arctic-fox-lidi-2.jpg', 'Reference Arctic Fox 2', 'Reference zákazníka ze starého Arctic webu' );
$legacy_reference_zuz = arctic_seed_attachment( 'legacy-reference-zuz', 'uploads/import/legacy-references/reference-zuz.jpg', 'Reference zákazníka', 'Reference ze starého Arctic webu' );
$legacy_reference_hot_tub_1 = arctic_seed_attachment( 'legacy-reference-virivka-arctic-g1', 'uploads/import/legacy-references/virivka-arctic-g1.jpg', 'Realizace vířivky ze starého webu', 'Obsahová fotografie vířivky ze starého Arctic webu' );
$legacy_reference_hot_tub_2 = arctic_seed_attachment( 'legacy-reference-virivka-arctic-g7', 'uploads/import/legacy-references/virivka-arctic-g7.jpg', 'Realizace vířivky ze starého webu', 'Obsahová fotografie vířivky ze starého Arctic webu' );
$legacy_reference_hot_tub_3 = arctic_seed_attachment( 'legacy-reference-virivka-arctic-g21', 'uploads/import/legacy-references/virivka-arctic-g21.jpg', 'Realizace vířivky ze starého webu', 'Obsahová fotografie vířivky ze starého Arctic webu' );
$legacy_reference_swimspa_1 = arctic_seed_attachment( 'legacy-reference-swimspa-arctic-g1', 'uploads/import/legacy-references/swimspa-arctic-g1.jpg', 'Realizace swimspa ze starého webu', 'Obsahová fotografie swimspa ze starého Arctic webu' );
$legacy_reference_swimspa_2 = arctic_seed_attachment( 'legacy-reference-swimspa-arctic-g4', 'uploads/import/legacy-references/swimspa-arctic-g4.jpg', 'Realizace swimspa ze starého webu', 'Obsahová fotografie swimspa ze starého Arctic webu' );
$showroom = arctic_seed_attachment( 'owner-showroom-main-web', 'uploads/import/owner-showroom/showroom-main-web.jpg', 'Showroom Arctic Spas Moravany', 'Showroom Arctic Spas v Moravanech' );
$showroom_2 = arctic_seed_attachment( 'owner-showroom-detail-web', 'uploads/import/owner-showroom/showroom-detail-web.jpg', 'Showroom Arctic Spas - vířivka', 'Vířivka v showroomu Arctic Spas v Moravanech' );
$showroom_3 = arctic_seed_attachment( 'owner-showroom-covana-interior-web', 'uploads/import/owner-showroom/showroom-covana-interior-web.jpg', 'Showroom Arctic Spas - Covana', 'Covana a vířivka v showroomu Arctic Spas' );
$figma_footer_map = arctic_seed_attachment( 'figma-node-1-242-footer-map', 'uploads/import/figma/footer-map.png', 'Figma footer mapa', 'Showroom Arctic Spas ve footeru' );
$figma_contact_map = arctic_seed_attachment( 'figma-node-1-1069-contact-map-showroom', 'uploads/import/figma/contact-map-showroom.png', 'Figma kontakt mapa/showroom', 'Kontaktní mapa a showroom podle grafiky' );
$owner_color_dakota = arctic_seed_attachment( 'owner-acrylic-dakota', 'uploads/import/owner-swatches/acrylic-dakota.jpg', 'Dakota acrylic swatch', 'Owner supplied acrylic swatch Dakota' );
$owner_color_kalahari = arctic_seed_attachment( 'owner-acrylic-kalahari', 'uploads/import/owner-swatches/acrylic-kalahari.jpg', 'Kalahari acrylic swatch', 'Owner supplied acrylic swatch Kalahari' );
$owner_color_odyssey = arctic_seed_attachment( 'owner-acrylic-odyssey', 'uploads/import/owner-swatches/acrylic-odyssey.jpg', 'Odyssey acrylic swatch', 'Owner supplied acrylic swatch Odyssey' );
$owner_color_espresso = arctic_seed_attachment( 'owner-acrylic-espresso', 'uploads/import/owner-swatches/acrylic-espresso.jpg', 'Espresso acrylic swatch', 'Owner supplied acrylic swatch Espresso' );
$figma_color_platinum_swirl = arctic_seed_attachment( 'figma-color-platinum-swirl', 'uploads/import/figma/color-platinum-swirl.png', 'Platinum Swirl acrylic swatch', 'Acrylic swatch Platinum Swirl' );
$figma_cabinet_cedar = arctic_seed_attachment( 'figma-cabinet-cedar', 'uploads/import/figma/cabinet-cedar.png', 'Cedrovy kabinet', 'Cedrovy kabinet Arctic Spas' );
$figma_cabinet_maintenance_free = arctic_seed_attachment( 'figma-cabinet-maintenance-free', 'uploads/import/figma/cabinet-maintenance-free.png', 'Bezudrzbovy kabinet', 'Bezudrzbovy kabinet Arctic Spas' );

$seed_product_color = static function ( string $key, string $name, string $type, int $image_id, int $order, string $hex = '' ): int {
	$color_id = arctic_seed_post_by_meta( 'spa_color', '_arctic_seed_key', $key, array(
		'post_status' => 'publish',
		'post_title'  => $name,
		'post_name'   => sanitize_title( remove_accents( $name ) ),
		'menu_order'  => $order,
	) );

	update_post_meta( $color_id, 'spa_color_type', $type );

	if ( '' !== $hex ) {
		update_post_meta( $color_id, 'spa_color_hex', $hex );
	} else {
		delete_post_meta( $color_id, 'spa_color_hex' );
	}

	if ( $image_id ) {
		set_post_thumbnail( $color_id, $image_id );
	}

	return $color_id;
};

$product_shell_color_ids = array(
	'dakota'         => $seed_product_color( 'spa-color-shell-dakota', 'Dakota', 'shell', $owner_color_dakota, 10 ),
	'kalahari'       => $seed_product_color( 'spa-color-shell-kalahari', 'Kalahari', 'shell', $owner_color_kalahari, 20 ),
	'odyssey'        => $seed_product_color( 'spa-color-shell-odyssey', 'Odyssey', 'shell', $owner_color_odyssey, 30 ),
	'platinum-swirl' => $seed_product_color( 'spa-color-shell-platinum-swirl', 'Platinum Swirl', 'shell', $figma_color_platinum_swirl, 40 ),
	'espresso'       => $seed_product_color( 'spa-color-shell-espresso', 'Espresso', 'shell', $owner_color_espresso, 50 ),
);

$product_cabinet_color_ids = array(
	'cedar'            => $seed_product_color( 'spa-color-cabinet-cedar', 'Cedrový kabinet standardní', 'cabinet', $figma_cabinet_cedar, 10 ),
	'maintenance-free' => $seed_product_color( 'spa-color-cabinet-maintenance-free', 'Bezúdržbový kabinet volitelný', 'cabinet', $figma_cabinet_maintenance_free, 20 ),
);

$product_shell_colors_all = array_values( $product_shell_color_ids );
$product_shell_colors_core = array(
	$product_shell_color_ids['platinum-swirl'],
	$product_shell_color_ids['espresso'],
	$product_shell_color_ids['kalahari'],
	$product_shell_color_ids['dakota'],
);
$covana_main  = arctic_seed_attachment( 'covana-slide-1', 'uploads/import/covana-slide-1.jpg', 'Covana automatický kryt', 'Automatický kryt vířivky Covana' );
$sauna_main   = arctic_seed_attachment( 'other-sauna-luxus-1', 'uploads/import/other-sortiment/sauna-luxus-1.jpg', 'Luxusní sauna', 'Luxusní sauna na míru' );
$kirami_main  = arctic_seed_attachment( 'other-koupaci-sud-s1', 'uploads/import/other-sortiment/koupaci-sud-s1.jpg', 'Koupací sud Kirami', 'Koupací sud Kirami' );
$ikono_main   = arctic_seed_attachment( 'other-venkovni-nabytek-ikono', 'uploads/import/other-sortiment/venkovni-nabytek-ikono.jpg', 'Venkovní nábytek IKONO', 'Venkovní nábytek IKONO' );
$accessory_main = arctic_seed_attachment( 'other-doplnky-cedr', 'uploads/import/other-sortiment/doplnky-cedr.jpg', 'Cedrové doplňky', 'Cedrové doplňky k vířivkám' );
$cold_plunge_main = arctic_seed_attachment( 'other-ochlazovaci-bazenek', 'uploads/import/other-sortiment/ochlazovaci-bazenek.jpg', 'Ochlazovací bazének', 'Ochlazovací bazének pro domácí wellness' );
$prep_pdf     = arctic_seed_attachment( 'stavebni-pripravenost-pdf', 'uploads/import/stavebni-pripravenost.pdf', 'Stavební připravenost Arctic Spas' );
$site_icon_attachment = arctic_seed_attachment(
	'arctic-site-icon',
	'themes/arctic/images/icon.png',
	'Arctic Spas favicon',
	'Arctic Spas favicon'
);
update_post_meta( $site_icon_attachment, '_arctic_site_icon_asset', '1' );

$member_images = array(
	'vlastimil-zhor' => arctic_seed_attachment( 'member-vlastimil-zhor-photo-portrait', 'uploads/import/figma/about-team-vladimir-portrait.png', 'Vlastimil Zhoř', 'Vlastimil Zhoř' ),
	'lukas-dusek'    => arctic_seed_attachment( 'member-lukas-dusek-photo-portrait', 'uploads/import/figma/about-team-lukas-portrait.png', 'Ing. Lukáš Dušek', 'Ing. Lukáš Dušek' ),
	'helena-antonyova' => arctic_seed_attachment( 'member-helena-antonyova-photo-portrait', 'uploads/import/figma/about-team-helena-portrait.png', 'Helena Antonyová', 'Helena Antonyová' ),
	'alena-janulikova' => arctic_seed_attachment( 'member-alena-janulikova-photo-portrait', 'uploads/import/figma/about-team-alena-portrait.png', 'Alena Janulíková', 'Alena Janulíková' ),
	'tomas-koutny'   => arctic_seed_attachment( 'member-tomas-koutny-photo-portrait', 'uploads/import/figma/about-team-tomas-portrait.jpg', 'Bc. Tomáš Koutný', 'Bc. Tomáš Koutný' ),
);

$member_avatars = array(
	'lukas-dusek'  => arctic_seed_attachment( 'member-lukas-dusek-avatar', 'uploads/import/figma/contact-lukas-dusek.png', 'Ing. Lukáš Dušek - kontaktní avatar', 'Ing. Lukáš Dušek' ),
	'tomas-koutny' => arctic_seed_attachment( 'member-tomas-koutny-avatar', 'uploads/import/figma/contact-tomas-koutny.jpg', 'Bc. Tomáš Koutný - kontaktní avatar', 'Bc. Tomáš Koutný' ),
);

$seed_members = array(
	array(
		'key'      => 'member-vlastimil-zhor',
		'name'     => 'Vlastimil Zhoř',
		'position' => 'Jednatel společnosti',
		'scope'    => 'Prodej vířivek',
		'email'    => 'vlastimil.zhor@baspa.cz',
		'phone'    => '+420 602 545 067',
		'image'    => $member_images['vlastimil-zhor'],
	),
	array(
		'key'      => 'member-lukas-dusek',
		'name'     => 'Ing. Lukáš Dušek',
		'position' => 'Jednatel společnosti',
		'scope'    => 'Komunikace s dodavateli a prodej bazénů',
		'email'    => 'lukas.dusek@arctic-spas.cz',
		'phone'    => '+420 777 099 687',
		'image'    => $member_images['lukas-dusek'],
		'avatar'   => $member_avatars['lukas-dusek'],
	),
	array(
		'key'      => 'member-helena-antonyova',
		'name'     => 'Helena Antonyová',
		'position' => 'Prodej bazénů',
		'scope'    => 'Prodej bazénů a příslušenství',
		'email'    => 'helena.antonyova@baspa.cz',
		'phone'    => '+420 777 099 687',
		'image'    => $member_images['helena-antonyova'],
	),
	array(
		'key'      => 'member-alena-janulikova',
		'name'     => 'Alena Janulíková',
		'position' => 'Logistika a fakturace',
		'scope'    => 'Organizace dopravy a fakturace.',
		'email'    => 'alena.janulikova@baspa.cz',
		'phone'    => '+420 792 640 005',
		'image'    => $member_images['alena-janulikova'],
	),
	array(
		'key'      => 'member-tomas-koutny',
		'name'     => 'Bc. Tomáš Koutný',
		'position' => 'Prodej vířivek',
		'scope'    => 'Prodej vířivek a koupacích sudů',
		'email'    => 'tomas.koutny@baspa.cz',
		'phone'    => '+420 602 149 106',
		'image'    => $member_images['tomas-koutny'],
		'avatar'   => $member_avatars['tomas-koutny'],
	),
	array(
		'key'      => 'member-pavel-novacek',
		'name'     => 'Pavel Nováček',
		'position' => 'Vedoucí technického úseku',
		'scope'    => 'Organizace montáží a servisů',
		'email'    => 'pavel.novacek@baspa.cz',
		'phone'    => '+420 774 080 775',
		'image'    => 0,
	),
);

$seed_member_ids = array();
foreach ( $seed_members as $index => $seed_member ) {
	$member_id = arctic_seed_post_by_meta( 'member', '_arctic_seed_key', $seed_member['key'], array(
		'post_status'  => 'publish',
		'post_title'   => $seed_member['name'],
		'post_name'    => str_replace( 'member-', '', $seed_member['key'] ),
		'post_content' => '',
		'menu_order'   => ( $index + 1 ) * 10,
	) );

	update_post_meta( $member_id, 'member_contacts', 1 );
	update_post_meta( $member_id, 'member_position', $seed_member['position'] );
	update_post_meta( $member_id, 'member_scope', $seed_member['scope'] );
	update_post_meta( $member_id, 'member_email', $seed_member['email'] );
	update_post_meta( $member_id, 'member_phone', $seed_member['phone'] );
	delete_post_meta( $member_id, 'member_avatar' );
	if ( !empty( $seed_member['avatar'] ) ) {
		add_post_meta( $member_id, 'member_avatar', (int) $seed_member['avatar'] );
	}

	if ( !empty( $seed_member['image'] ) ) {
		set_post_thumbnail( $member_id, (int) $seed_member['image'] );
	} else {
		delete_post_thumbnail( $member_id );
	}

	$seed_member_ids[ $seed_member['key'] ] = $member_id;
}

if ( !empty( $seed_member_ids['member-tomas-koutny'] ) ) {
	update_option( 'baspa_member_contact_cta_id', (int) $seed_member_ids['member-tomas-koutny'] );
	update_option( 'baspa_member_product_sidebar_id', (int) $seed_member_ids['member-tomas-koutny'] );
	update_option( 'baspa_member_offer_sidebar_id', (int) $seed_member_ids['member-tomas-koutny'] );
	update_option( 'baspa_member_footer_quick_id', (int) $seed_member_ids['member-tomas-koutny'] );
	update_option( 'baspa_member_showroom_contact_id', (int) $seed_member_ids['member-tomas-koutny'] );
	update_option( 'baspa_member_support_help_id', (int) $seed_member_ids['member-tomas-koutny'] );
}

$seed_jobs = array(
	array(
		'key'     => 'job-montazni-technik',
		'slug'    => 'montazni-technik',
		'title'   => 'Montážní technik',
		'content' => '<!-- wp:paragraph --><p>Hledáme spolehlivého technika pro montáže, servisní výjezdy a péči o zákazníky po instalaci.</p><!-- /wp:paragraph -->'
			. '<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading --><h2 class="wp-block-heading">Požadujeme</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>technickou zručnost a pečlivost</li><!-- /wp:list-item --><!-- wp:list-item --><li>řidičský průkaz skupiny B</li><!-- /wp:list-item --><!-- wp:list-item --><li>slušné jednání se zákazníky</li><!-- /wp:list-item --><!-- wp:list-item --><li>samostatnost při montážích a servisu</li><!-- /wp:list-item --><!-- wp:list-item --><li>chuť učit se produkty Arctic Spas</li><!-- /wp:list-item --></ul><!-- /wp:list --></div><!-- /wp:column -->'
			. '<!-- wp:column --><div class="wp-block-column"><!-- wp:heading --><h2 class="wp-block-heading">Nabízíme</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>stabilní práci v malém týmu</li><!-- /wp:list-item --><!-- wp:list-item --><li>zaškolení na produktech Arctic Spas</li><!-- /wp:list-item --><!-- wp:list-item --><li>férové jednání a dlouhodobou spolupráci</li><!-- /wp:list-item --><!-- wp:list-item --><li>zázemí showroomu v Moravanech u Brna</li><!-- /wp:list-item --><!-- wp:list-item --><li>podmínky podle zkušeností a domluvy</li><!-- /wp:list-item --></ul><!-- /wp:list --></div><!-- /wp:column --></div><!-- /wp:columns -->'
			. '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:lukas.dusek@arctic-spas.cz">Kontaktujte nás</a></div><!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="' . esc_url( home_url( '/o-nas/#career' ) ) . '">Více na pracovním portále</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
	),
	array(
		'key'     => 'job-obchodnik-moravany-showroom',
		'slug'    => 'obchodnik-prodejna-moravany',
		'title'   => 'Obchodník na prodejně v Moravanech',
		'content' => '<!-- wp:paragraph --><p>Hledáme obchodníka pro showroom v Moravanech, který zvládne zákazníkům srozumitelně představit vířivky, bazény a navazující služby.</p><!-- /wp:paragraph -->'
			. '<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading --><h2 class="wp-block-heading">Požadujeme</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>příjemnou a věcnou komunikaci se zákazníky</li><!-- /wp:list-item --><!-- wp:list-item --><li>orientaci v obchodním jednání</li><!-- /wp:list-item --><!-- wp:list-item --><li>pečlivost při přípravě nabídek</li><!-- /wp:list-item --><!-- wp:list-item --><li>ochotu naučit se technické detaily sortimentu</li><!-- /wp:list-item --><!-- wp:list-item --><li>spolehlivost a samostatnost</li><!-- /wp:list-item --></ul><!-- /wp:list --></div><!-- /wp:column -->'
			. '<!-- wp:column --><div class="wp-block-column"><!-- wp:heading --><h2 class="wp-block-heading">Nabízíme</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>práci v zavedeném showroomu u Brna</li><!-- /wp:list-item --><!-- wp:list-item --><li>produktové zaškolení Arctic Spas</li><!-- /wp:list-item --><!-- wp:list-item --><li>menší tým s přímou komunikací</li><!-- /wp:list-item --><!-- wp:list-item --><li>možnost podílet se na realizaci zakázek od poptávky po předání</li><!-- /wp:list-item --><!-- wp:list-item --><li>férové podmínky podle zkušeností</li><!-- /wp:list-item --></ul><!-- /wp:list --></div><!-- /wp:column --></div><!-- /wp:columns -->'
			. '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:lukas.dusek@arctic-spas.cz">Napsat zprávu</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
	),
	array(
		'key'     => 'job-obchodnik-moravany-projects',
		'slug'    => 'obchodnik-prodejna-moravany-projekty',
		'title'   => 'Obchodník na prodejně v Moravanech',
		'content' => '<!-- wp:paragraph --><p>Do obchodního týmu hledáme posilu pro komunikaci se zájemci, správu poptávek a koordinaci dalších kroků s technickým týmem.</p><!-- /wp:paragraph -->'
			. '<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading --><h2 class="wp-block-heading">Požadujeme</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>zkušenost s prodejem nebo klientskou péčí</li><!-- /wp:list-item --><!-- wp:list-item --><li>schopnost dotahovat rozpracované poptávky</li><!-- /wp:list-item --><!-- wp:list-item --><li>dobrou organizaci práce</li><!-- /wp:list-item --><!-- wp:list-item --><li>ochotu pracovat s produktovým katalogem</li><!-- /wp:list-item --><!-- wp:list-item --><li>spolupráci s techniky a logistikou</li><!-- /wp:list-item --></ul><!-- /wp:list --></div><!-- /wp:column -->'
			. '<!-- wp:column --><div class="wp-block-column"><!-- wp:heading --><h2 class="wp-block-heading">Nabízíme</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>stabilní zázemí autorizovaného prodejce</li><!-- /wp:list-item --><!-- wp:list-item --><li>jasné produktové portfolio a podporu týmu</li><!-- /wp:list-item --><!-- wp:list-item --><li>kontakt se zákazníky v showroomu i online</li><!-- /wp:list-item --><!-- wp:list-item --><li>dlouhodobou spolupráci</li><!-- /wp:list-item --><!-- wp:list-item --><li>podmínky podle domluvené role</li><!-- /wp:list-item --></ul><!-- /wp:list --></div><!-- /wp:column --></div><!-- /wp:columns -->'
			. '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:lukas.dusek@arctic-spas.cz">Napsat zprávu</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
	),
);

foreach ( $seed_jobs as $index => $seed_job ) {
	arctic_seed_post_by_meta( 'job', '_arctic_seed_key', $seed_job['key'], array(
		'post_status'  => 'publish',
		'post_title'   => $seed_job['title'],
		'post_name'    => $seed_job['slug'],
		'post_content' => $seed_job['content'],
		'menu_order'   => ( $index + 1 ) * 10,
	) );
}

update_option( 'baspa_jobs_title', 'Kariéra v Arctic spas' );
update_option( 'baspa_jobs_subtitle', 'Uplatnění u nás najdou šikovní lidé, kteří se nebojí komunikovat se zákazníky a odvádět dobrou práci každý den.' );

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

update_term_meta( $category_hot_tubs, 'category_image', $figma_category_hero_virivky );
update_term_meta( $category_hot_tubs, 'category_heading_image', $figma_category_hero_virivky );
update_term_meta( $category_hot_tubs, 'category_description_short', 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie.' );
update_term_meta( $category_hot_tubs, 'category_heading_title', 'Venkovní vířivky Arctic Spas' );
update_term_meta( $category_hot_tubs, 'category_heading_text', 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie.' );
update_term_meta( $category_hot_tubs, 'category_heading_cta_text', 'Vybrat vířivku' );
update_term_meta( $category_hot_tubs, 'category_intro_1_title', 'Vlastnosti vířivek' );
update_term_meta( $category_hot_tubs, 'category_intro_1_text', 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie. Unikátní technická řešení jako obvodová izolace FreeHeat™, sklolaminátová podlaha Forever Floor™ nebo termokryt Mylovac™ z nich dělají dobrou volbu pro celoroční provoz.' );
update_term_meta( $category_hot_tubs, 'category_intro_1_button_text', 'Více o vlastnostech' );
update_term_meta( $category_hot_tubs, 'category_intro_1_button_url', '/vlastnosti/' );
update_term_meta( $category_hot_tubs, 'category_intro_1_image', $figma_category_vlastnosti );
update_term_meta( $category_hot_tubs, 'category_intro_1_alt', 'Vlastnosti vířivek Arctic Spas' );
update_term_meta( $category_hot_tubs, 'category_intro_2_title', 'Záruka' );
update_term_meta( $category_hot_tubs, 'category_intro_2_text', 'Za kvalitou našich výrobků si stojíme. Arctic Spas nabízí doživotní záruku na vodotěsnost skořepiny a pětiletou záruku na většinu komponentů včetně ohřevu.' );
update_term_meta( $category_hot_tubs, 'category_intro_2_button_text', 'Více o záruce' );
update_term_meta( $category_hot_tubs, 'category_intro_2_button_url', '/zaruka/' );
update_term_meta( $category_hot_tubs, 'category_intro_2_image', $figma_category_zaruka );
update_term_meta( $category_hot_tubs, 'category_intro_2_alt', 'Záruka Arctic Spas' );
update_term_meta( $category_swimspa, 'category_image', $curator_swimspa_hero );
update_term_meta( $category_swimspa, 'category_heading_image', $curator_swimspa_hero );
update_term_meta( $category_swimspa, 'category_description_short', 'Rodinný bazén na zahradě je snem řady domácností. Mnohé nicméně odradí nesmírná náročnost souvisejících zemních a stavebních prací a v našich klimatických podmínkách také žalostně limitované využití omezené na krátkou letní sezónu.' );
update_term_meta( $category_swimspa, 'category_heading_title', 'Celoroční bazény Arctic Spas' );
update_term_meta( $category_swimspa, 'category_heading_text', 'Celoroční bazény Arctic přinášejí relaxaci, plavání i rodinnou zábavu bez složitých stavebních prací. Díky izolaci, termokrytu a kvalitní konstrukci jsou připravené na používání po celý rok.' );
update_term_meta( $category_swimspa, 'category_heading_cta_text', 'Vybrat bazén' );
update_term_meta( $category_swimspa, 'category_intro_1_title', 'Výhody celoročních bazénů Arctic' );
update_term_meta( $category_swimspa, 'category_intro_1_text', 'Swimspa Arctic přivezeme kompletní včetně filtrace, automatické dezinfekce, elektroohřevu, obvodové izolace FreeHeat™ a bezpečného termokrytu. Stačí ji postavit na rovnou plochu, připojit k elektřině a napustit vodou.' );
update_term_meta( $category_swimspa, 'category_intro_1_button_text', 'Více o vlastnostech' );
update_term_meta( $category_swimspa, 'category_intro_1_button_url', '/vlastnosti/' );
update_term_meta( $category_swimspa, 'category_intro_1_image', $figma_category_swimspa_intro );
update_term_meta( $category_swimspa, 'category_intro_1_alt', 'Celoroční bazén Arctic Spas' );
update_term_meta( $category_swimspa, 'category_intro_2_title', 'Celoroční provoz bez stavebních prací' );
update_term_meta( $category_swimspa, 'category_intro_2_text', 'Celoroční bazén Arctic vám přinese zábavu, relaxaci i sportovní vyžití bez výkopů a složitých stavebních prací. Díky kvalitní konstrukci, izolaci a termokrytu je připravený pro pohodlné používání po celý rok.' );
update_term_meta( $category_swimspa, 'category_intro_2_button_text', 'Více o záruce' );
update_term_meta( $category_swimspa, 'category_intro_2_button_url', '/zaruka/' );
update_term_meta( $category_swimspa, 'category_intro_2_image', $legacy_category_swimspa_life );
update_term_meta( $category_swimspa, 'category_intro_2_alt', 'Venkovní swimspa Arctic Spas' );
update_term_meta( $category_covers, 'category_image', $covana_main );
update_term_meta( $category_covers, 'category_description_short', 'Automatické kryty, doplňky a navazující sortiment pro pohodlnější provoz vířivky.' );
update_term_meta( $category_covers, 'category_heading_cta_text', 'Prohlédnout sortiment' );
update_term_meta( $category_covers, 'category_type', 'accessories' );

$reference_customers = arctic_seed_term( 'reference-category', 'Reference zákazníků', 'reference-zakazniku' );
$reference_hot_tubs  = arctic_seed_term( 'reference-category', 'Vířivky', 'virivky' );
$reference_swimspa   = arctic_seed_term( 'reference-category', 'Celoroční bazény', 'swimspa' );

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
		'contexts'    => array( $reference_hot_tubs ),
	),
	array(
		'key'         => 'legacy-reference-arctic-fox-life-2',
		'image'       => $legacy_reference_fox_life_2,
		'title'       => 'Fox se systémem Spa Boy',
		'description' => 'Po letech okukování nám na zahradě přistála čerstvá Foxka. Výborná domluva, instalace takřka na klíč, skvělá síla trysek a slaná voda se Spa Boy nám výrazně usnadnila péči o vodu.',
		'location'    => 'Arctic Spas',
		'year'        => '2016',
		'contexts'    => array( $reference_hot_tubs ),
	),
	array(
		'key'         => 'legacy-reference-swimspa-arctic-g1',
		'image'       => $legacy_reference_swimspa_1,
		'title'       => 'Swimspa Wolverine',
		'description' => 'Swimspa používáme denně a hodnotíme ji jako jednu z nejlepších součástí domu. V zimní zahradě je použitelná kdykoliv a plavání na plovacím prutu nás baví víc než samotný protiproud.',
		'location'    => 'Wolverine',
		'year'        => '2016',
		'contexts'    => array( $reference_swimspa ),
	),
	array(
		'key'         => 'customer-reference-low-energy',
		'image'       => $legacy_reference_hot_tub_1,
		'title'       => 'Nízká spotřeba v zimním provozu',
		'description' => 'Po jednoročním provozu jsme zjistili, že vířivka má velmi nízkou spotřebu i při častém zimním používání. Zakoupili jsme výjimečnou věc, kterou bychom doporučili každému.',
		'location'    => 'Arctic Spas',
		'year'        => '2014',
		'contexts'    => array( $reference_hot_tubs ),
	),
	array(
		'key'         => 'customer-reference-fox-service',
		'image'       => $legacy_reference_hot_tub_2,
		'title'       => 'Arctic Fox předčil očekávání',
		'description' => 'Jsme rádi, že jsme při výběru dodavatele vířivky narazili na vaši firmu. Dodaná vířivka Arctic Fox předčila naše očekávání a poskytnuté služby odpovídají dobrému jménu firmy.',
		'location'    => 'Arctic Fox',
		'year'        => '2014',
		'contexts'    => array( $reference_hot_tubs ),
	),
	array(
		'key'         => 'customer-reference-pool',
		'image'       => $legacy_reference_swimspa_2,
		'title'       => 'Vstřícný přístup při výběru bazénu',
		'description' => 'Velmi oceňujeme vstřícný přístup při prohlídce místa plánované instalace. Bazén jsme nakonec objednali a rychlé provedení i výsledný stav nám dělají radost.',
		'location'    => 'Bazén',
		'year'        => '2015',
		'contexts'    => array( $reference_swimspa ),
	),
	array(
		'key'         => 'customer-reference-swimspa-showroom',
		'image'       => $legacy_reference_swimspa_1,
		'title'       => 'Výběr celoročního bazénu',
		'description' => 'Při výběru swimspa jsme navštívili hodně showroomů. Až u Arctic Spas jsme pochopili, co je důležité; dodávka proběhla v termínu, instalace rychle a zaškolení na vysoké úrovni.',
		'location'    => 'Celoroční bazén',
		'year'        => '2015',
		'contexts'    => array( $reference_swimspa ),
	),
	array(
		'key'         => 'customer-reference-arctic-spas',
		'image'       => $legacy_reference_hot_tub_3,
		'title'       => 'Vířivka bez poruchy',
		'description' => 'Vířivka je super a bez poruchy. Parametry a dlouhodobá zkušenost nám potvrdily, že výběr kvalitní vířivky se opravdu vyplatí.',
		'location'    => 'Arctic Spas',
		'year'        => '2016',
		'contexts'    => array( $reference_hot_tubs ),
	),
	array(
		'key'         => 'customer-reference-new-year',
		'image'       => $legacy_reference_zuz,
		'title'       => 'Novoroční přání z vířivky',
		'description' => 'Originální novoroční přání zákazníka vystihuje, proč lidé Arctic Spas používají: odpočinek, teplo a chvíle klidu přímo doma.',
		'location'    => 'Arctic Spas',
		'year'        => '2012',
		'contexts'    => array( $reference_hot_tubs ),
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
	update_post_meta( $reference_id, 'reference_single', 0 );
	update_post_meta( $reference_id, 'reference_description', $reference_item['description'] );
	update_post_meta( $reference_id, 'reference_location', $reference_item['location'] );
	update_post_meta( $reference_id, 'reference_year', $reference_item['year'] );
	arctic_seed_set_multi_meta( $reference_id, 'reference_images', array( $reference_item['image'] ) );
	wp_set_post_terms( $reference_id, array_merge( array( $reference_customers ), $reference_item['contexts'] ?? array() ), 'reference-category' );
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
arctic_seed_set_multi_meta( $lunar_id, 'product_shell_color_ids', $product_shell_colors_core );
delete_post_meta( $lunar_id, 'product_cabinet_color_ids' );
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
arctic_seed_set_multi_meta( $orion_id, 'product_shell_color_ids', $product_shell_colors_core );
delete_post_meta( $orion_id, 'product_cabinet_color_ids' );
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
		'series'         => $series_swimspa_classic,
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
		'series'         => $series_swimspa_classic,
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
		'series'         => $series_swimspa_classic,
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
		'series'         => $series_swimspa_custom,
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
		'series'         => $series_swimspa_custom,
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
		'series'         => $series_swimspa_classic,
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
	arctic_seed_set_multi_meta( $product_id, 'product_shell_color_ids', $product_shell_colors_all );
	delete_post_meta( $product_id, 'product_cabinet_color_ids' );
	if ( !empty( $product['badge'] ) ) {
		update_post_meta( $product_id, 'product_badge', $product['badge'] );
	} else {
		delete_post_meta( $product_id, 'product_badge' );
	}

	if ( $is_swimspa ) {
		wp_set_object_terms( $product_id, array( $kind_swimspa ), 'product-kind' );
		wp_set_object_terms( $product_id, array( $product['series'] ), 'product-series' );
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
arctic_seed_set_multi_meta( $timberwolf_id, 'product_shell_color_ids', $product_shell_colors_all );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_cabinet_color_ids', array_values( $product_cabinet_color_ids ) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_acrylic_color_options', array(
	array( 'name' => 'Dakota', 'image' => $owner_color_dakota ),
	array( 'name' => 'Kalahari', 'image' => $owner_color_kalahari ),
	array( 'name' => 'Odyssey', 'image' => $owner_color_odyssey ),
	array( 'name' => 'Espresso', 'image' => $owner_color_espresso ),
) );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_cabinet_color_options', array() );
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
update_post_meta( $timberwolf_id, 'product_benefits_heading', 'Výhody vířivek Arctic Spas - série Classic' );
update_post_meta( $timberwolf_id, 'product_benefits_description', 'Vířivky Arctic Spas série Classic jsou standardně dodávány s řadou funkcí. Kliknutím na vybrané funkce se o nich dozvíte více.' );

$seed_product_benefits = array();
foreach ( function_exists( 'arctic_product_default_benefits' ) ? arctic_product_default_benefits() : array() as $benefit ) {
	$seed_product_benefits[] = array(
		'title'         => wp_strip_all_tags( (string) ( $benefit['title'] ?? '' ) ),
		'summary'       => wp_strip_all_tags( (string) ( $benefit['summary'] ?? 'Návrh vychází z požadavků na provoz v chladném klimatu, jednoduchou údržbu a stabilní výkon po mnoho sezon.' ) ),
		'icon'          => (string) ( $benefit['icon'] ?? 'feature' ),
		'interactive'   => !empty( $benefit['interactive'] ) ? '1' : '0',
		'popup_title'   => wp_strip_all_tags( (string) ( $benefit['popup_title'] ?? '' ) ),
		'popup_content' => (string) ( $benefit['popup_content'] ?? '' ),
	);
}
arctic_seed_set_multi_meta( $timberwolf_id, 'product_benefit_items', $seed_product_benefits );
delete_post_meta( $timberwolf_id, 'product_benefit_images' );
arctic_seed_set_multi_meta( $timberwolf_id, 'product_benefit_popup_images', array( $figma_popup_shell_detail ) );

update_post_meta( $timberwolf_id, 'product_options_heading', 'Volitelná výbava' );
update_post_meta( $timberwolf_id, 'product_options_description', 'Doplňky a technologie se vybírají podle modelu, konfigurace a způsobu používání vířivky.' );

$seed_product_options = array();
foreach ( function_exists( 'arctic_product_default_options' ) ? arctic_product_default_options() : array() as $option ) {
	$seed_product_options[] = array(
		'title'   => wp_strip_all_tags( (string) ( $option['title'] ?? '' ) ),
		'summary' => 'Konkrétní dostupnost a doporučenou kombinaci potvrdíme v nabídce pro vybraný model.',
		'icon'    => (string) ( $option['icon'] ?? 'option' ),
	);
}
arctic_seed_set_multi_meta( $timberwolf_id, 'product_option_items', $seed_product_options );
delete_post_meta( $timberwolf_id, 'product_option_images' );
wp_set_object_terms( $timberwolf_id, array( $kind_hot_tubs ), 'product-kind' );
wp_set_object_terms( $timberwolf_id, array( $series_classic ), 'product-series' );
wp_set_object_terms( $timberwolf_id, array( $category_hot_tubs ), 'product-category' );

$curator_core_gallery = array( $curator_hot_tub_core_hero, $curator_hot_tub_core_top, $curator_hot_tub_hero );
foreach ( array( 'husky', 'lunar', 'orion' ) as $slug ) {
	arctic_seed_set_product_gallery_by_slug( $slug, $curator_core_gallery );
}

$curator_classic_gallery = array( $curator_hot_tub_classic_hero, $curator_hot_tub_classic_top, $curator_hot_tub_hero_winter );
foreach ( array( 'eagle', 'mckinley', 'mustang', 'totem' ) as $slug ) {
	arctic_seed_set_product_gallery_by_slug( $slug, $curator_classic_gallery );
}

foreach ( array( 'cub', 'klondiker', 'kodiak', 'summit', 'summit-xl', 'tundra' ) as $slug ) {
	arctic_seed_set_product_gallery_by_slug( $slug, array( $curator_hot_tub_custom_yukon, $curator_hot_tub_custom_fox, $curator_hot_tub_hero ) );
}
arctic_seed_set_product_gallery_by_slug( 'fox', array( $curator_hot_tub_custom_fox, $curator_hot_tub_custom_yukon, $curator_hot_tub_hero ) );
arctic_seed_set_product_gallery_by_slug( 'yukon', array( $curator_hot_tub_custom_yukon, $curator_hot_tub_custom_fox, $curator_hot_tub_hero ) );

foreach ( $curator_swimspa_product_images as $slug => $image_id ) {
	$series_hero = in_array( $slug, array( 'ocean', 'okanagan' ), true ) ? $curator_swimspa_ocean_hero : $curator_swimspa_hero;
	$series_extra = in_array( $slug, array( 'ocean', 'okanagan' ), true ) ? $curator_swimspa_hero : $curator_swimspa_polar_bear_hero;
	arctic_seed_set_product_gallery_by_slug( $slug, array( $series_hero, $series_extra, $image_id ), true, (int) $image_id );
}

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

$seed_offer_id = arctic_seed_post_by_meta( 'offer', '_arctic_seed_key', 'offer-stock-hot-tubs', array(
	'post_status'  => 'publish',
	'post_title'   => 'Výprodej skladových vířivek',
	'post_name'    => 'vyprodej-skladovych-virivek',
	'post_content' => '<!-- wp:paragraph --><p>Ukázková akční nabídka pro administraci. Owner ji může v detailu nabídky upravit, doplnit fotku a přepnout na zobrazení na homepage.</p><!-- /wp:paragraph -->',
	'menu_order'   => 10,
) );
set_post_thumbnail( $seed_offer_id, $timberwolf_signature );
update_post_meta( $seed_offer_id, 'offer_featured', 1 );
update_post_meta( $seed_offer_id, 'offer_type', 'winter' );
update_post_meta( $seed_offer_id, 'offer_type_custom', 'Skladové vířivky' );
update_post_meta( $seed_offer_id, 'offer_title_short', 'Výprodej skladových vířivek' );
update_post_meta( $seed_offer_id, 'offer_title', 'Výprodej skladových vířivek Arctic Spas' );
update_post_meta( $seed_offer_id, 'offer_description', 'Vybrané skladové modely lze po domluvě dodat rychleji než běžnou objednávku. Dostupnost a konkrétní konfiguraci potvrdíme podle aktuálního stavu skladu.' );
update_post_meta( $seed_offer_id, 'offer_status', 'Skladem / na cestě' );
update_post_meta( $seed_offer_id, 'offer_discount', 'Individuální zvýhodnění' );
update_post_meta( $seed_offer_id, 'offer_price', 'Na vyžádání' );
update_post_meta( $seed_offer_id, 'offer_display', 'default' );
update_post_meta( $seed_offer_id, 'offer_contact', 'default' );
update_post_meta( $seed_offer_id, 'offer_contact_member_id', (int) ( $seed_member_ids['member-tomas-koutny'] ?? 0 ) );
update_post_meta( $seed_offer_id, 'offer_button_text', 'Zjistit dostupnost' );
update_post_meta( $seed_offer_id, 'offer_button_url', home_url( '/kontakt/#formular' ) );
arctic_seed_set_multi_meta( $seed_offer_id, 'offer_promo_image_id', array( $figma_offer_promo_product ) );

$offers_page_id = arctic_seed_page(
	'akcni-nabidky',
	'Akční nabídky',
	'<!-- wp:paragraph --><p>V akčních nabídkách najdete skladové vířivky, showroomové kusy nebo modely, které už mají pevně danou konfiguraci a mohou být dostupné rychleji než běžná objednávka z výroby.</p><!-- /wp:paragraph -->'
	. '<!-- wp:paragraph --><p>Konkrétní stav se průběžně mění. U každé nabídky proto v administraci udržujeme aktuální dostupnost, zvýhodnění a hlavní fotku, aby si majitel webu mohl akci upravit bez zásahu do šablony.</p><!-- /wp:paragraph -->',
	'template-offers.php'
);
delete_post_thumbnail( $offers_page_id );
update_post_meta( $offers_page_id, 'page_description_text', 'Aktuální akční nabídky vířivek Arctic Spas: skladové modely, showroomové kusy a konfigurace dostupné rychleji než běžná objednávka.' );

$hot_tubs_url = home_url( '/virivky/' );
$covers_url   = get_term_link( $category_covers, 'product-category' );
$swimspa_url  = home_url( '/swimspa/' );
$offers_url   = get_permalink( $offers_page_id );
$covers_url   = is_wp_error( $covers_url ) ? home_url( '/catalog/dalsi-sortiment/' ) : $covers_url;

$home_id = arctic_seed_page(
	'uvod',
	'Uvod',
	'<!-- wp:heading {"textAlign":"center"} --><h2 class="wp-block-heading has-text-align-center">Jsme výhradní prodejce</h2><!-- /wp:heading -->'
	. '<!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">Rádi vám pomůžeme při výběru bazénu nebo vířivky. Jsme vám k dispozici, ať už máte přesnou představu nebo se myšlenkami na bazén či vířivku teprve začínáte zabývat. Nejprve se pobavíme o vašich přáních a potřebách, navrhneme vhodné řešení, zpracujeme podrobnou nabídku a nakonec projekt zrealizujeme.</p><!-- /wp:paragraph -->'
	. '<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="' . esc_url( home_url( '/o-nas/' ) ) . '">Více o nás</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
	'template-homepage.php'
);
arctic_seed_set_multi_meta( $home_id, 'homepage_benefits', array(
	array(
		'title' => 'Montáž',
		'text'  => 'Odborně na klíč',
		'icon'  => 'box',
	),
	array(
		'title' => 'Podpora',
		'text'  => 'Se vším vám poradíme',
		'icon'  => 'support',
	),
	array(
		'title' => 'Servis',
		'text'  => 'Jsme tu pro vás 24/7',
		'icon'  => 'service',
	),
) );
arctic_seed_set_multi_meta( $home_id, 'homepage_benefit_images', array( $figma_home_benefit_install, $figma_home_benefit_support, $figma_home_benefit_service ) );
update_post_meta( $home_id, 'homepage_showroom_title', 'Navštivte náš showroom' );
update_post_meta( $home_id, 'homepage_showroom_text', 'Chcete si osobně prohlédnout různá řešení a pobavit se o možnostech realizace vašeho nového bazénu nebo vířivky? Rádi si s vámi dáme kávu v našem showroomu.' );
update_post_meta( $home_id, 'homepage_showroom_button_text', 'Více o nás' );
update_post_meta( $home_id, 'homepage_showroom_button_url', home_url( '/showroom/' ) );
update_post_meta( $home_id, 'homepage_showroom_address', 'Bohunická cesta 15, Moravany u Brna' );
update_post_meta( $home_id, 'homepage_showroom_badge_value', '280 m²' );
update_post_meta( $home_id, 'homepage_showroom_badge_label', 'prezentačních ploch' );
arctic_seed_set_multi_meta( $home_id, 'homepage_showroom_images', array( $showroom, $showroom_3, $showroom_2 ) );
update_post_meta( $home_id, 'homepage_progress_title', 'Průběh zakázky' );
update_post_meta( $home_id, 'homepage_progress_text', 'Od první poptávky až po odbornou montáž vás provedeme celým procesem tak, aby byl výběr vířivky nebo bazénu jednoduchý a přehledný.' );
arctic_seed_set_multi_meta( $home_id, 'homepage_progress_steps', array(
	array(
		'title' => 'Vaše poptávka',
		'text'  => 'Ozvěte se nám přes kontaktní formulář, e-mail nebo telefonicky.',
	),
	array(
		'title' => 'Konzultace',
		'text'  => 'Doporučíme výběr bazénu nebo vířivky a návštěvu showroomu.',
	),
	array(
		'title' => 'Nabídka',
		'text'  => 'Na základě požadavků připravíme nezávaznou kalkulaci.',
	),
	array(
		'title' => 'Uzavření smlouvy',
		'text'  => 'Potvrdíme průběh dodávky, montáže a přípravy.',
	),
	array(
		'title' => 'Stavební příprava',
		'text'  => 'Poradíme vám, co a jak připravit před montáží.',
	),
	array(
		'title' => 'Montáž',
		'text'  => 'Rádi se postaráme o odbornou montáž a organizaci celé akce.',
	),
) );

$hot_tubs_page_id = arctic_seed_page(
	'virivky',
	'Vířivky Arctic Spas',
	'<!-- wp:paragraph --><p>Výběr kanadských vířivek Arctic Spas je postavený pro celoroční provoz, nízké provozní náklady a pohodlnou údržbu. Aktivní produkty v novém webu zůstávají v katalogu, vyřazené modely se řeší přes redirecty.</p><!-- /wp:paragraph -->'
);
update_post_meta( $hot_tubs_page_id, 'page_product_category', $category_hot_tubs );

$swimspa_id = arctic_seed_page(
	'swimspa',
	'Celoroční bazény',
	''
);
update_post_meta( $swimspa_id, 'page_product_category', $category_swimspa );

$showroom_id = arctic_seed_page(
	'showroom',
	'Showroom',
	'<!-- wp:paragraph --><p>Naše hlavní vzorková prodejna se nachází v Moravanech u Brna a je velmi dobře dostupná z dálnice D1. Vystavené vířivky, swimspa a vybrané příslušenství si u nás můžete nejen prohlédnout. Minimálně dvě vířivky míváme zprovozněné pro mokrý test, k dispozici je samozřejmě kompletní zázemí.</p><!-- /wp:paragraph -->',
	'template-showroom.php'
);
set_post_thumbnail( $showroom_id, $showroom_3 );
update_post_meta( $showroom_id, 'page_title_text', 'Showroom' );
update_post_meta( $showroom_id, 'page_description_text', 'U nás najdete od každého něco a obvykle alespoň dva modely vířivek napuštěné vodou, abyste je mohli podrobit mokré zkoušce.' );
update_post_meta( $showroom_id, 'showroom_gallery_button_text', 'Fotogalerie' );
update_post_meta( $showroom_id, 'showroom_area_value', '280 m²' );
update_post_meta( $showroom_id, 'showroom_area_label_1', 'prezentační' );
update_post_meta( $showroom_id, 'showroom_area_label_2', 'plochy' );
update_post_meta( $showroom_id, 'showroom_mini_cta_title', 'Přijďte se pobavit o své nové vířivce nebo swimspa' );
update_post_meta( $showroom_id, 'showroom_mini_cta_text', 'Na pobočce nebo online s vámi vaše představy rádi probereme. Že žádné nemáte? Tím spíš se zastavte pro inspiraci.' );
update_post_meta( $showroom_id, 'showroom_mini_cta_button_text', 'Domluvit si schůzku' );
update_post_meta( $showroom_id, 'showroom_mini_cta_button_url', home_url( '/kontakt/#formular' ) );
update_post_meta( $showroom_id, 'showroom_reasons_heading', 'Proč navštívit náš showroom?' );
arctic_seed_set_multi_meta( $showroom_id, 'showroom_reasons', array(
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
) );
update_post_meta( $showroom_id, 'showroom_primary_title', 'Naše bazény a vířivky si prohlédnete, případně vyzkoušíte' );
update_post_meta( $showroom_id, 'showroom_secondary_content', '<p>Showroom najdete v Moravanech u Brna, kousek od dálnice D1. Při návštěvě s vámi projdeme výběr modelu, přípravu místa, možnosti usazení i další kroky realizace. Přijet můžete po domluvě v otevírací době a rovnou probrat další postup.</p>' );
arctic_seed_set_multi_meta( $showroom_id, 'showroom_gallery_images', array( $showroom_2, $showroom ) );
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

$feature_insulation_content = <<<'HTML'
<section>
	<h2>Izolace na správném místě šetří vaše peníze každý den!</h2>
	<p>Izolujeme vnější stěny, podlahu, a především strop či střechu. Vše důležité se přitom nachází uvnitř tohoto izolovaného prostoru. Vzduch uvnitř kabinetu se ohřívá zbytkovým teplem čerpadel a pomáhá udržovat vodu na požadované teplotě.</p>
</section>
<section>
	<h2>Další inovace</h2>
	<p>Jakákoliv překážka mezi elektrickým zařízením a skořepinou omezí přenos zbytkového tepla a znamená zvýšené náklady pro uživatele. Arctic Spas proto drží technologii v izolovaném, ale servisně přístupném prostoru.</p>
</section>
<section>
	<h2>Nejnižší provozní náklady</h2>
	<p>Zbytkové teplo z provozu zařízení, především čerpadel, může prostupovat přes sklolaminátovou skořepinu a přispívá k přihřívání vody. Výsledkem je méně ohřevových cyklů a nižší spotřeba energie.</p>
</section>
<section>
	<h2>Skutečná ochrana proti mrazu</h2>
	<p>Obvodová izolace FreeHeat® umí využít teplo kumulované ve vodě vířivky k ochraně proti mrazu při výpadku proudu. Právě proto je vhodná pro celoroční provoz v náročném klimatu.</p>
</section>
HTML;

$feature_insulation_id = arctic_seed_page(
	'izolace-virivky',
	'Obvodová izolace FreeHeat™',
	$feature_insulation_content,
	'template-feature-detail.php'
);
wp_update_post( array(
	'ID'          => $feature_insulation_id,
	'post_parent' => $features_id,
) );
update_post_meta( $feature_insulation_id, 'page_description_text', 'Investice do výroby samonosné skořepiny umožňuje izolovat vířivky Arctic Spas® lépe, než jak to provádí kdokoliv z konkurence. Pro inspiraci jsme nemuseli chodit daleko, naše domovy v severní Kanadě nám poskytly vše, co potřebujeme vědět.' );

wp_update_post( array(
	'ID'           => $feature_insulation_id,
	'post_content' => <<<'HTML'
<section>
	<h2>Izolace na správném místě šetří vaše peníze každý den!</h2>
	<p>Arctic Spas neobaluje skořepinu nepraktickou montážní pěnou. Izoluje vnější stěny, podlahu a hlavně prostor pod termokrytem, takže všechny důležité technologie zůstávají uvnitř chráněného a servisně přístupného kabinetu. Zbytkové teplo z čerpadel se vrací zpět k vodě a pomáhá snižovat provozní náklady.</p>
	<p>Právě obvodová izolace FreeHeat™ je důvod, proč vířivky Arctic obstojí v extrémních kanadských zimách a zároveň dávají smysl pro celoroční provoz v českých podmínkách.</p>
</section>
<section>
	<h2>Všechny vrstvy spolupracují</h2>
	<p>Samonosná skořepina, silná podlaha Forever Floor™, izolovaný kabinet, servisní panely, systém RossExhaust a termokryt Mylovac™ tvoří jeden celek. Teplo neuniká zbytečnými dutinami, voda se dohřívá méně často a technologie není schovaná v pěně, kterou by bylo nutné při opravě vyřezávat.</p>
</section>
<section>
	<h2>Nižší spotřeba bez kompromisu v servisu</h2>
	<p>U běžných vířivek se izolace často používá jako náhrada slabší konstrukce. Arctic Spas staví na pevné skořepině a izolaci používá tam, kde má skutečný efekt. Výsledkem je nízká spotřeba, lepší ochrana proti promrznutí a zároveň přístup ke všem důležitým komponentům.</p>
</section>
<section>
	<h2>Ochrana při výpadku proudu</h2>
	<p>Když dojde k výpadku elektřiny, teplo uložené ve vodě a v izolovaném kabinetu pomáhá chránit technologii před mrazem. Tato rezerva je pro celoroční venkovní provoz zásadní a odlišuje Arctic Spas od systémů, které spoléhají jen na hmotu pěny kolem skořepiny.</p>
</section>
HTML,
) );

$feature_shell_warranty_content = <<<'HTML'
<section>
	<h2>Skořepina je základ celé vířivky</h2>
	<p>Skořepina nese vodu, technologii i každodenní zatížení. Arctic Spas proto používá akrylát Aristech s povrchem Bio-Lok a ručně nanášený sklolaminátový kompozit. Vrstvy se aplikují postupně tak, aby vznikla pevná samonosná skořepina bez nutnosti dodatečných podpěr a bez zalití technologie pěnou.</p>
</section>
<section>
	<h2>Přesná výroba a doživotní záruka</h2>
	<p>Otvory pro trysky a komponenty jsou vyřezávány roboticky, takže každá skořepina drží přesnou geometrii. U řady Custom poskytuje Arctic Spas doživotní záruku na vodotěsnost skořepiny, u ostatních řad dlouhé prodloužené záruky podle záruční matice.</p>
</section>
<section>
	<h2>Propojení s celkovou konstrukcí</h2>
	<p>Pevná skořepina dává smysl jen v kombinaci s kvalitním kabinetem, obvodovou izolací FreeHeat™ a podlahou Forever Floor™. Díky tomu vířivka drží tvar, dobře izoluje a zároveň zůstává servisně přístupná po celou dobu životnosti.</p>
</section>
<section>
	<h2>Detailní záruční podmínky</h2>
	<p>Konkrétní délky záruk pro řady Custom, Classic a Core jsou spravované v samostatné záruční stránce a v adminu je lze upravit v záruční matici.</p>
	<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link" href="/zaruka/">Zobrazit záruční matici</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</section>
HTML;

$feature_shell_warranty_id = arctic_seed_page(
	'zaruka-na-skorepinu',
	'Záruka na skořepinu',
	$feature_shell_warranty_content,
	'template-feature-detail.php'
);
wp_update_post( array(
	'ID'          => $feature_shell_warranty_id,
	'post_parent' => $features_id,
) );
update_post_meta( $feature_shell_warranty_id, 'page_description_text', 'Pevná samonosná skořepina je nejdůležitější konstrukční část vířivky Arctic Spas. Díky ručnímu vrstvení kompozitu, přesné výrobě a kvalitnímu akrylátu může Arctic poskytovat mimořádně dlouhou záruku.' );

$feature_cover_content = <<<'HTML'
<section>
	<h2>Termokryt Mylovac™ chrání největší únikovou plochu</h2>
	<p>Nejvíce tepla odchází z vířivky směrem vzhůru. Termokryt Arctic Spas proto není obyčejný kus pěny, ale konstrukční prvek izolace. Jádro Castcore® má vysokou hustotu, je zpevněné kovovými U profily a je uzavřené tak, aby do něj nepronikala vlhkost.</p>
</section>
<section>
	<h2>Suchá izolace má delší životnost</h2>
	<p>Běžný termokryt po čase nasákne vodou, ztěžkne a ztratí izolační schopnost. Mylovac™ používá vakuově utěsněnou ochrannou vrstvu, která pomáhá udržet izolační jádro suché. Díky tomu kryt déle drží tvar, lépe izoluje a neprodražuje provoz.</p>
</section>
<section>
	<h2>Detaily proti větru a úniku tepla</h2>
	<p>Součástí jsou boční přesahy, popruhy, madla a izolovaný středový spoj. Tyto detaily omezují profukování i únik tepla v místě, kde je u běžných krytů nejslabší místo.</p>
</section>
<section>
	<h2>Povrch Weathershield</h2>
	<p>U vybraných konfigurací lze zvolit povrch Weathershield v tmavě šedé nebo hnědé barvě. Je odolnější proti počasí než běžná koženka a lépe odpovídá celoročnímu venkovnímu používání.</p>
</section>
HTML;

$feature_cover_id = arctic_seed_page(
	'termokryt',
	'Termokryt Mylovac™',
	$feature_cover_content,
	'template-feature-detail.php'
);
wp_update_post( array(
	'ID'          => $feature_cover_id,
	'post_parent' => $features_id,
) );
update_post_meta( $feature_cover_id, 'page_description_text', 'Termokryt je nejdůležitější izolační prvek horní plochy vířivky. Mylovac™ a Castcore® pomáhají držet teplo, chránit vodu a prodloužit životnost krytu.' );

$feature_floor_content = <<<'HTML'
<section>
	<h2>Forever Floor™ chrání vířivku zespodu</h2>
	<p>Podlaha vířivky je vystavená vlhkosti, zimě, nerovnostem i dlouhodobému zatížení. Forever Floor™ je kompozitní sklolaminátová podlaha, která chrání spodní část vířivky a odděluje technologii od země.</p>
</section>
<section>
	<h2>Není potřeba speciální stavební základ</h2>
	<p>Vířivku Arctic Spas lze postavit na pevnou rovnou plochu. Není nutné budovat betonovou desku, dlažbu nebo terasu jen proto, aby podlaha unesla konstrukci. Důležité je rovné a stabilní podloží.</p>
</section>
<section>
	<h2>Odolnost proti vlhkosti a času</h2>
	<p>Kompozitní podlaha nepodléhá hnilobě jako dřevo, lépe odolává vlhkosti a chrání vířivku před nečistotami i drobnými škůdci. U řady Custom je Forever Floor™ součástí konstrukce standardně.</p>
</section>
<section>
	<h2>Mobilní řešení po celou životnost</h2>
	<p>Díky samostatné podlaze zůstává vířivka přemístitelná. Pokud se změní dispozice zahrady nebo se majitel stěhuje, konstrukce není trvale svázaná se stavebním základem.</p>
</section>
HTML;

$feature_floor_id = arctic_seed_page(
	'podlaha-virivky',
	'Podlaha Forever Floor™',
	$feature_floor_content,
	'template-feature-detail.php'
);
wp_update_post( array(
	'ID'          => $feature_floor_id,
	'post_parent' => $features_id,
) );
update_post_meta( $feature_floor_id, 'page_description_text', 'Podlaha Forever Floor™ chrání vířivku zespodu, zjednodušuje přípravu místa a podporuje dlouhou životnost celé konstrukce.' );

$feature_service_access_content = <<<'HTML'
<section>
	<h2>Servisní přístup bez bourání izolace</h2>
	<p>Arctic Spas používá odnímatelné izolované panely. Technik se dostane k čerpadlům, elektronice, rozvodům i dalším komponentům bez vyřezávání pěny a bez poškození konstrukce vířivky.</p>
</section>
<section>
	<h2>Nižší riziko drahých oprav</h2>
	<p>U vířivek zapěněných kolem technologie se i jednoduchý servis může změnit v časově náročnou opravu. Přístupný kabinet umožňuje závadu rychle najít, opravit a vrátit vířivku do provozu s minimálním zásahem.</p>
</section>
<section>
	<h2>Připraveno i na budoucí upgrade</h2>
	<p>Stejný princip pomáhá při doplnění vybraných prvků výbavy, například světel, audia, trysek, čerpadel nebo automatické úpravy vody Spa Boy. Vířivka tak není uzavřený výrobek, který po letech nejde rozumně servisovat.</p>
</section>
<section>
	<h2>Servisní požadavek</h2>
	<p>Servisní formulář je samostatná stránka, kde owner spravuje texty a klient odešle požadavek k řešení.</p>
	<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link" href="/servis/">Přejít na servis</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</section>
HTML;

$feature_service_access_id = arctic_seed_page(
	'servisni-pristup',
	'Servisní přístup',
	$feature_service_access_content,
	'template-feature-detail.php'
);
wp_update_post( array(
	'ID'          => $feature_service_access_id,
	'post_parent' => $features_id,
) );
update_post_meta( $feature_service_access_id, 'page_description_text', 'Odnímatelné izolované panely dávají technikům přístup ke všem důležitým komponentům bez ničení izolace a bez zbytečně drahých oprav.' );

$feature_variability_content = <<<'HTML'
<section>
	<h2>Vířivka se skládá podle potřeb zákazníka</h2>
	<p>Arctic Spas nabízí širokou variabilitu výbavy. U vybraných modelů lze volit počet trysek, čerpadla, systém dezinfekce, osvětlení, audio, doplňky i barevné kombinace skořepiny a kabinetu.</p>
</section>
<section>
	<h2>Miliony konfigurací</h2>
	<p>Kombinací modelu, řady, barev a výbavy vzniká více než 2,4 milionu možností. Klient tak nevybírá jen z několika skladových variant, ale může si sestavit vířivku podle způsobu používání a rozpočtu.</p>
</section>
<section>
	<h2>Pomoc s výběrem</h2>
	<p>Prodejce pomůže vybrat konfiguraci podle počtu osob, umístění, požadované masáže, nároků na údržbu a rozpočtu. Po objednání lze zákazníkovi předat výrobní číslo a orientační termín dodání.</p>
</section>
<section>
	<h2>Skladové nabídky pro rychlé dodání</h2>
	<p>Pokud zákazník potřebuje vířivku rychle, lze vybrat z aktuálních skladových nebo zvýhodněných nabídek. Ty jsou spravované samostatně přes CPT Akční nabídky.</p>
	<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link" href="/akcni-nabidky/">Zobrazit akční nabídky</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</section>
HTML;

$feature_variability_id = arctic_seed_page(
	'variabilita',
	'Variabilita výběru',
	$feature_variability_content,
	'template-feature-detail.php'
);
wp_update_post( array(
	'ID'          => $feature_variability_id,
	'post_parent' => $features_id,
) );
update_post_meta( $feature_variability_id, 'page_description_text', 'Arctic Spas umožňuje sestavit vířivku podle konkrétních potřeb, výbavy, barev a způsobu používání. Prodejce pomůže vybrat konfiguraci i skladovou alternativu.' );

$feature_disinfection_content = <<<'HTML'
<section>
	<h2>Spa Boy™ hlídá kvalitu vody automaticky</h2>
	<p>Systém Spa Boy™ průběžně sleduje hodnoty ORP a pH pomocí přesných senzorů. Na základě naměřených hodnot pomáhá řídit úpravu vody a snižuje množství ruční kontroly, kterou musí majitel provádět.</p>
</section>
<section>
	<h2>Solná technologie a chytré hlášení</h2>
	<p>Ve spojení se solnou technologií systém upravuje výkon podle skutečné potřeby vody. Pokud je třeba upravit pH nebo doplnit servisní krok, informace se zobrazí na ovládání, v aplikaci nebo přes webové rozhraní podle konfigurace.</p>
</section>
<section>
	<h2>Napojení na EcoPack a aplikaci</h2>
	<p>Spa Boy™ spolupracuje s inteligentní elektronikou Arctic Spas. U vířivek vybavených EcoPackem a síťovým připojením lze sledovat stav vody vzdáleně a rychleji reagovat na změny.</p>
</section>
<section>
	<h2>Možnost doplnění u vybraných vířivek</h2>
	<p>U některých starších modelů lze automatickou úpravu vody doplnit dodatečně, pokud to umožňuje elektronika a konfigurace vířivky. Konkrétní možnosti je potřeba ověřit podle modelu a výbavy.</p>
</section>
HTML;

$feature_disinfection_id = arctic_seed_page(
	'automaticka-dezinfekce',
	'Automatická dezinfekce Spa Boy™',
	$feature_disinfection_content,
	'template-feature-detail.php'
);
wp_update_post( array(
	'ID'          => $feature_disinfection_id,
	'post_parent' => $features_id,
) );
update_post_meta( $feature_disinfection_id, 'page_description_text', 'Spa Boy™ automaticky sleduje a pomáhá řídit kvalitu vody ve vířivce. Majitel má méně ruční práce a lepší přehled o stavu vody.' );

$feature_detail_editor_updates = array(
	$feature_insulation_id => array(
		'title'       => 'Obvodová izolace FreeHeat™',
		'description' => 'Investice do výroby samonosné skořepiny umožňuje izolovat vířivky Arctic Spas® jinak než u běžných zapěněných konstrukcí. Teplo vznikající provozem technologie zůstává uvnitř izolovaného kabinetu a pomáhá snižovat náklady na celoroční provoz.',
		'hero'        => $legacy_feature_insulation,
		'diagram'     => $figma_feature_freeheat_diagram,
		'content'     => <<<'HTML'
<section>
	<h2>Izolace na správném místě šetří peníze každý den</h2>
	<p>Arctic Spas vychází z jednoduché zkušenosti ze severní Kanady: izolovat má celý prostor, který drží vodu a technologii v bezpečí. Proto se neobaluje skořepina nepraktickou montážní pěnou, ale izolují se vnější stěny, podlaha a hlavně horní plocha pod termokrytem. Všechna důležitá zařízení zůstávají uvnitř chráněného kabinetu.</p>
	<p>Zbytkové teplo z čerpadel a motorů se v tomto prostoru neztratí. Může prostupovat přes sklolaminátovou skořepinu zpět k vodě a pomáhat s dohřevem. To je podstata systému FreeHeat™ a hlavní důvod, proč dává vířivka Arctic smysl pro skutečný celoroční provoz.</p>
</section>
<section>
	<h2>Uzavřený izolovaný prostor</h2>
	<p>Vířivka se chová podobně jako dobře navržený dům. Stěny, podlaha a střecha pracují dohromady. Termokryt Mylovac™ kryje největší únikovou plochu shora, kabinet omezuje tepelné ztráty po stranách a podlaha Forever Floor™ uzavírá chráněný prostor zespodu.</p>
	<p>Technologie není zalitá v pěně, takže zůstává servisně přístupná. To je důležité nejen při opravách, ale i pro dlouhodobou životnost. U zapěněných vířivek se může i drobný servis změnit v hledání závady uvnitř tvrdé pěny.</p>
</section>
<section>
	<h2>Další inovace: RossExhaust a izolovaná dvířka</h2>
	<p>Jakákoliv překážka mezi elektrickým zařízením a skořepinou omezuje přenos zbytkového tepla. Arctic proto nechává teplo pracovat tam, kde má efekt, a zároveň hlídá podmínky uvnitř kabinetu. Systém RossExhaust™ monitoruje teplotu v prostoru technologie a podle potřeby pomáhá upravit odvětrání.</p>
	<p>Přesahující izolace přístupových dvířek omezuje poslední slabé místo, kudy může teplo unikat. Celý princip je navržený pro řady Custom a Classic, kde obvodová izolace FreeHeat™ tvoří jeden ze základních konstrukčních rozdílů proti běžným vířivkám.</p>
</section>
<section>
	<h2>Nižší provozní náklady</h2>
	<p>Majitel nejvíc vnímá náklady, které se opakují každý měsíc. Když se zbytkové teplo z technologie vrací zpět do vody, vířivka potřebuje méně ohřevových cyklů a elektroohřev běží kratší dobu. Úspora není v jedné součástce, ale v tom, že konstrukce pracuje jako celek.</p>
	<p>Proto je při výběru vířivky důležité porovnávat nejen pořizovací cenu, ale i izolaci, servisní přístup, kvalitu termokrytu a dlouhodobé náklady na energii.</p>
</section>
<section>
	<h2>Ochrana proti mrazu při výpadku proudu</h2>
	<p>FreeHeat® má ještě jednu praktickou výhodu: umí využít teplo nahromaděné ve vodě k ochraně technologie při výpadku elektřiny. Protože mezi vodou a prostorem kabinetu není izolační bariéra, tepelná energie se může pohybovat oběma směry.</p>
	<p>Při silných mrazech tak konstrukce poskytuje časovou rezervu, ve které lze zajistit náhradní zdroj tepla nebo obnovu provozu. Právě tato rezerva je u venkovní vířivky používané v zimě zásadní.</p>
</section>
<section>
	<h2>Proč nestačí jen plná pěna</h2>
	<p>Kabinet vyplněný pěnou může na první pohled působit bezpečně, ale zároveň brání využití tepla z čerpadel a komplikuje servis. Pokud se závada nachází v pěně, musí se materiál vyřezávat, hledat únik nebo poškozený díl a následně konstrukci znovu uzavírat.</p>
	<p>Arctic Spas volí jinou cestu: pevnou samonosnou skořepinu, izolovaný přístupný kabinet a technologii, která zůstává opravitelná i po letech.</p>
</section>
HTML,
	),
	$feature_shell_warranty_id => array(
		'title'       => 'Záruka na skořepinu',
		'description' => 'Skořepina je základ celé vířivky. U Arctic Spas se vyrábí jako pevná samonosná konstrukce z kvalitního akrylátu a ručně nanášeného kompozitu, aby dlouhodobě unesla vodu, technologii i každodenní zatížení.',
		'hero'        => $curator_feature_shell,
		'diagram'     => 0,
		'content'     => <<<'HTML'
<section>
	<h2>Skořepina je konstrukční základ vířivky</h2>
	<p>Skořepina drží vodu, nese osazení trysek, opírá se o ni technologie a každý den pracuje s teplotními změnami. Proto Arctic Spas nestaví skořepinu jako tenký akrylát podepřený pěnou, ale jako samonosný kompozitní celek.</p>
	<p>Výroba začíná vakuovým formováním prémiového akrylátu Aristech s povrchovou úpravou Bio-Lok™. Na něj se ručně nanáší sklolaminátový kompozit vrstvu po vrstvě, aby vznikla pevná skořepina s dlouhou životností a přesnou geometrií.</p>
</section>
<section>
	<h2>Ručně vrstvený kompozit bez zkratek</h2>
	<p>U skořepiny nemá smysl hledat úsporné zkratky. Arctic používá silnou vrstvu sklolaminátu a postupné vrstvení, díky kterému je skořepina pevná bez dodatečných podpěr a bez toho, aby technologie musela být schovaná v montážní pěně.</p>
	<p>Po kontrolách pevnosti, tvaru a rozměrů následuje přesné robotické vyřezání otvorů pro trysky a komponenty. Přesnost otvorů je důležitá pro vodotěsnost, servis i dlouhodobou stabilitu celé vířivky.</p>
</section>
<section>
	<h2>Custom, Classic a Core</h2>
	<p>Řady Custom a Classic staví na kompozitní skořepině navržené pro náročné celoroční používání. U vybraných řad Core se používá konstrukce Elastocast™, která kombinuje akrylát, ABS vrstvu a technologii podporující stabilní konstrukci skořepiny.</p>
	<p>Smysl je u všech řad stejný: vytvořit pevný základ, který umožní kvalitní izolaci, přesné osazení komponentů a dlouhou životnost bez kompromisů v servisním přístupu.</p>
</section>
<section>
	<h2>Proč k detailu patří záruka</h2>
	<p>Dlouhá záruka na skořepinu není samostatný marketingový slogan. Je výsledkem konstrukce, materiálů a výrobního postupu. Proto detail vlastnosti vysvětluje samotnou skořepinu a samostatná stránka Záruka doplňuje konkrétní záruky podle řad a komponentů.</p>
	<p>Owner může záruční matici spravovat zvlášť na stránce Záruka, zatímco tento detail slouží zákazníkovi k pochopení, proč je skořepina tak důležitá při výběru vířivky.</p>
	<p><a href="/zaruka/">Zobrazit kompletní záruky Arctic Spas</a></p>
</section>
HTML,
	),
	$feature_cover_id => array(
		'title'       => 'Termokryt Mylovac™',
		'description' => 'Termokryt chrání největší únikovou plochu vířivky. Mylovac™ a Castcore® pomáhají držet teplo, zabraňují nasáknutí izolace a prodlužují životnost krytu.',
		'hero'        => $curator_feature_cover,
		'diagram'     => 0,
		'content'     => <<<'HTML'
<section>
	<h2>Termokryt chrání největší únikovou plochu</h2>
	<p>Nejvíce tepla z vířivky uniká směrem nahoru. Proto termokryt není doplněk, ale zásadní část izolace. Mylovac™ je navržený tak, aby dlouhodobě držel teplo, chránil vodu a pomáhal udržet provozní náklady pod kontrolou.</p>
	<p>Jádro Castcore® používá hustší izolační pěnu než běžné kryty a je zpevněné kovovými U profily. Díky tomu kryt lépe drží tvar a zvládá zatížení sněhem i běžným používáním.</p>
</section>
<section>
	<h2>Suchá izolace izoluje déle</h2>
	<p>Velký problém běžných krytů je nasáknutí vodou. Jakmile pěna zvlhne, kryt ztěžkne, hůř se otevírá a rychle ztrácí izolační schopnost. Mylovac™ uzavírá izolační jádro do ochranné vrstvy, ze které je odstraněn vzduch a která pomáhá bránit průniku vlhkosti.</p>
	<p>Suchý kryt má delší životnost, lépe drží tepelný odpor a méně zvyšuje spotřebu elektřiny v zimním období.</p>
</section>
<section>
	<h2>Detaily, které omezují profukování</h2>
	<p>Kvalitní termokryt musí řešit i zdánlivé drobnosti: delší boční chlopně, zabezpečovací popruhy, madla a izolovaný středový spoj. Právě středový šev bývá u levnějších krytů slabým místem, kudy uniká teplo.</p>
	<p>Mylovac™ je standardní výbavou u řady Custom a u vybraných konfigurací lze jeho provedení dále přizpůsobit.</p>
</section>
<section>
	<h2>Weathershield pro venkovní provoz</h2>
	<p>Pro náročnější použití je dostupná varianta s povrchem Weathershield v tmavě šedém nebo tmavě hnědém provedení. Povrch je vhodný pro venkovní podmínky a lépe odolává počasí než běžná koženková úprava.</p>
	<p>Konkrétní dostupnost povrchu a barvy je potřeba ověřit podle modelu, rozměru a aktuální nabídky výrobce.</p>
</section>
HTML,
	),
	$feature_floor_id => array(
		'title'       => 'Podlaha Forever Floor™',
		'description' => 'Forever Floor™ chrání vířivku zespodu, zjednodušuje přípravu místa a podporuje dlouhou životnost celé konstrukce.',
		'hero'        => $curator_feature_floor,
		'diagram'     => 0,
		'content'     => <<<'HTML'
<section>
	<h2>Vestavěný základ vířivky</h2>
	<p>Spodní část vířivky je dlouhodobě vystavená vlhkosti, chladu, nečistotám a zatížení. Forever Floor™ je kompozitní sklolaminátová podlaha, která chrání konstrukci zespodu a odděluje technologii od okolního prostředí.</p>
	<p>Podlaha pomáhá uzavřít izolovaný prostor kabinetu a zároveň tvoří pevný základ vířivky. U řady Custom je součástí konstrukce standardně.</p>
</section>
<section>
	<h2>Stačí rovná a stabilní plocha</h2>
	<p>Pro instalaci není nutné budovat speciální betonovou desku jen kvůli základně vířivky. Potřebujete pouze rovnou plochu, která je pevná a stabilní, aby se na ni vířivka mohla umístit, připojit a napustit vodou.</p>
	<p>Tím se zjednodušuje příprava místa a zákazník má větší volnost při umístění na zahradě, terase nebo u domu.</p>
</section>
<section>
	<h2>Odolnost proti vlhkosti, škůdcům a času</h2>
	<p>Kompozitní podlaha nepodléhá hnilobě jako dřevěné konstrukce a lépe odolává dlouhodobé vlhkosti. Zároveň chrání spodní část vířivky proti nečistotám a drobným škůdcům, kteří se mohou dostat pod běžné zahradní instalace.</p>
	<p>Podlaha tak není jen instalační detail, ale jeden z prvků, který prodlužuje životnost celé vířivky.</p>
</section>
<section>
	<h2>Vířivka zůstává přemístitelná</h2>
	<p>Protože má vířivka vlastní pevnou základnu, není trvale svázaná se stavebním podkladem. Pokud se změní dispozice zahrady nebo se majitel stěhuje, vířivku lze přemístit a základ cestuje s ní.</p>
	<p>To je praktický rozdíl proti řešením, která vyžadují pevnou stavební úpravu jen kvůli stabilitě spodní konstrukce.</p>
</section>
HTML,
	),
	$feature_service_access_id => array(
		'title'       => 'Servisní přístup',
		'description' => 'Odnímatelné izolované panely umožňují servis, opravy i budoucí upgrade bez ničení izolace a bez hledání komponentů v montážní pěně.',
		'hero'        => $curator_feature_service_access,
		'diagram'     => 0,
		'content'     => <<<'HTML'
<section>
	<h2>Ke komponentům se musí dát dostat</h2>
	<p>Vířivka je technologie, která má sloužit mnoho let. Proto dává smysl, aby se technik dostal k čerpadlům, elektronice, rozvodům i dalším komponentům bez bourání konstrukce. Arctic Spas používá izolované odnímatelné panely, které se dají sejmout, servisovat a znovu vrátit.</p>
	<p>U plně zapěněných vířivek bývá jednoduchá oprava složitá, protože komponenty a hadice jsou pohřbené v pěně. Servis pak znamená vyřezávání, hledání závady a následné složité uzavření prostoru.</p>
</section>
<section>
	<h2>Rychlejší servis a menší zásah</h2>
	<p>Přístupný kabinet zkracuje čas hledání závady a snižuje rozsah zásahu do vířivky. Technik pracuje v prostoru, který byl pro servis navržený, ne v materiálu, který překáží opravě.</p>
	<p>To je důležité u záručního i pozáručního servisu. Dobrá vířivka nemá být po letech neopravitelná jen proto, že se špatně navrhla izolace.</p>
</section>
<section>
	<h2>Připraveno na budoucí upgrade</h2>
	<p>Servisní přístup pomáhá i ve chvíli, kdy chce majitel vířivku dovybavit. Podle konkrétního modelu a elektroniky lze řešit doplnění vybraných prvků, například světel, audia, některých technologií nebo automatické péče o vodu Spa Boy™.</p>
	<p>Nemusí tak dávat smysl měnit celou vířivku jen proto, že se za několik let změní potřeby majitele nebo dostupná výbava.</p>
</section>
<section>
	<h2>Servisní požadavek jako samostatný krok</h2>
	<p>Tento detail vysvětluje konstrukční výhodu servisního přístupu. Pokud zákazník potřebuje skutečný servis, navazuje samostatná stránka Servis s formulářem a kontaktem.</p>
	<p><a href="/servis/">Přejít na servisní formulář</a></p>
</section>
HTML,
	),
	$feature_variability_id => array(
		'title'       => 'Variabilita výběru',
		'description' => 'Arctic Spas umožňuje sestavit vířivku podle konkrétních potřeb, výbavy, barev a způsobu používání. Prodejce pomůže vybrat individuální konfiguraci i rychle dostupnou skladovou alternativu.',
		'hero'        => $curator_feature_variability,
		'diagram'     => 0,
		'content'     => <<<'HTML'
<section>
	<h2>Nikde jinde tolik možností</h2>
	<p>U vířivky, kterou si zákazník pořizuje na mnoho let, nedává smysl dělat zbytečné kompromisy. Arctic Spas nabízí širokou variabilitu modelů, trysek, čerpadel, dezinfekce, osvětlení, hudby, doplňků i barevných kombinací skořepiny a kabinetu.</p>
	<p>Zákazník tak nevybírá jen hotovou položku ze skladu, ale může si sestavit vířivku podle počtu osob, požadované masáže, nároků na údržbu a vzhledu zahrady.</p>
</section>
<section>
	<h2>Více než 2,4 milionu kombinací</h2>
	<p>Kombinací modelu, řady, barvy skořepiny, kabinetu a výbavy vzniká více než 2,4 milionu možností. Smyslem není zahltit zákazníka volbami, ale umožnit vybrat takovou konfiguraci, která bude skutečně odpovídat způsobu používání.</p>
	<p>Pro někoho je nejdůležitější silná hydromasáž, pro jiného snadná péče o vodu, nízké provozní náklady nebo barevné sladění s terasou.</p>
</section>
<section>
	<h2>Pomoc s výběrem a termínem dodání</h2>
	<p>Prodejce pomůže zákazníkovi projít výbavu a vybrat konfiguraci bez zbytečných prvků i bez podcenění důležitých funkcí. Po upřesnění specifikace se vířivka zadá do systému výrobce, získá výrobní číslo a lze odhadnout termín dodání.</p>
	<p>V hlavní sezóně se může u některých modelů dodací lhůta prodloužit. Právě proto je důležité včas vědět, které prvky jsou pro zákazníka zásadní a kde je možné zvolit rozumnou alternativu.</p>
</section>
<section>
	<h2>Skladové a zvýhodněné nabídky</h2>
	<p>Pokud zákazník netrvá na přesné individuální konfiguraci a chce vířivku co nejdříve, může dávat smysl výběr ze skladových nebo akčních nabídek. Tyto nabídky se spravují samostatně přes WP admin jako Akční nabídky.</p>
	<p><a href="/akcni-nabidky/">Zobrazit aktuální akční nabídky</a></p>
</section>
HTML,
	),
	$feature_disinfection_id => array(
		'title'       => 'Automatická dezinfekce Spa Boy™',
		'description' => 'Spa Boy™ automaticky sleduje a pomáhá řídit kvalitu vody ve vířivce. Majitel má méně ruční práce, přesnější kontrolu a lepší přehled o stavu vody.',
		'hero'        => $curator_feature_disinfection,
		'diagram'     => 0,
		'content'     => <<<'HTML'
<section>
	<h2>Automatická péče o vodu</h2>
	<p>Spa Boy™ je systém automatické péče o vodu, který průběžně sleduje důležité parametry a pomáhá řídit úpravu vody bez neustálého ručního testování. Cílem je čistá voda, méně odhadů a méně zbytečné chemie.</p>
	<p>Systém měří hodnoty ORP a pH přes přesné senzory a předává informace řídicí jednotce vířivky. Pokud se hodnota dostane mimo doporučený rozsah, majitel dostane upozornění podle konkrétní konfigurace.</p>
</section>
<section>
	<h2>Spa Boy™ a slaná voda</h2>
	<p>Ve spojení se solnou technologií dokáže Spa Boy™ řídit výkon úpravy vody podle skutečné potřeby. Místo pravidelného ručního dávkování reaguje systém na stav vody a pomáhá udržet sanitaci v optimální úrovni.</p>
	<p>Majitel tak nemusí spoléhat jen na testovací proužky a nepřesné odhady. Péče o vodu je přehlednější a lépe odpovídá reálnému používání vířivky.</p>
</section>
<section>
	<h2>Napojení na EcoPack a aplikaci</h2>
	<p>EcoPack integruje Spa Boy™ s dalšími funkcemi vířivky a umožňuje komunikaci s aplikací Arctic Spas nebo webovým rozhraním podle výbavy a připojení. Majitel má díky tomu lepší přehled o tom, co se ve vířivce děje.</p>
	<p>Vzdálený přehled je praktický hlavně u celoročního provozu, kdy zákazník nechce čekat, až si problému všimne až při koupání.</p>
</section>
<section>
	<h2>Možnost dodatečné instalace</h2>
	<p>U některých starších vířivek Arctic Spas lze systém Spa Boy™ doplnit dodatečně, pokud to umožňuje řídicí systém, elektrická výbava a síťové připojení. Konkrétní možnost je potřeba ověřit podle modelu a roku výroby.</p>
	<p>Spa Boy™ navazuje na další prvky péče o vodu, například solnou technologii Onzen nebo vzdálenou správu. V detailu nabídky je proto vhodné vždy ověřit, co je součástí konkrétní konfigurace.</p>
</section>
HTML,
	),
);

foreach ( $feature_detail_editor_updates as $feature_page_id => $feature_detail_update ) {
	wp_update_post( array(
		'ID'           => (int) $feature_page_id,
		'post_title'   => $feature_detail_update['title'],
		'post_content' => $feature_detail_update['content'],
	) );

	update_post_meta( (int) $feature_page_id, 'page_description_text', $feature_detail_update['description'] );
	arctic_seed_set_multi_meta( (int) $feature_page_id, 'feature_detail_hero_images', array( (int) $feature_detail_update['hero'] ) );
	arctic_seed_set_multi_meta( (int) $feature_page_id, 'feature_detail_diagram_images', !empty( $feature_detail_update['diagram'] ) ? array( (int) $feature_detail_update['diagram'] ) : array() );
	update_post_meta( (int) $feature_page_id, 'feature_detail_related_heading', 'Další vlastnosti vířivek Arctic Spas' );
}

$warranty_id = arctic_seed_page(
	'zaruka',
	'Záruka',
	'',
	'template-warranty.php'
);
update_post_meta( $warranty_id, 'page_description_text', 'Ve společnosti Arctic Spas® jsme pyšní na kvalitu našeho technického řešení a na vysokou úroveň zpracování. Na naše výrobky poskytujeme tyto prodloužené záruky.' );
arctic_seed_set_multi_meta( $warranty_id, 'warranty_tiers', array(
	array(
		'name'    => 'Custom',
		'shell'   => 'Doživotní',
		'acrylic' => '5 let',
		'floor'   => 'Doživotní',
		'parts'   => '5 let',
		'labor'   => '5 let',
	),
	array(
		'name'    => 'Classic',
		'shell'   => '10 let',
		'acrylic' => '4 roky',
		'floor'   => '3 roky',
		'parts'   => '3 roky',
		'labor'   => '3 roky',
	),
	array(
		'name'    => 'Core',
		'shell'   => '7 let',
		'acrylic' => '1 rok',
		'floor'   => '3 roky',
		'parts'   => '3 roky',
		'labor'   => '1 rok',
	),
) );
update_post_meta( $warranty_id, 'warranty_note', 'Dopravné: první dva roky záruky hradí cestu servisního technika prodávající a od třetího roku tuto platí zákazník, ať jde o opravu dílů spadajících do prodloužené záruky, nebo o pozáruční servis.<br><br>Konkrétní záruční podmínky naleznete v uživatelském manuálu, viz sekce <a href="' . esc_url( home_url( '/ke-stazeni/' ) ) . '">Ke stažení</a>.' );

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

$seed_feature_images = array(
	'feature-insulation'             => $legacy_feature_insulation,
	'feature-shell-warranty'         => $curator_feature_shell,
	'feature-cover'                  => $curator_feature_cover,
	'feature-floor'                  => $curator_feature_floor,
	'feature-service-access'         => $curator_feature_service_access,
	'feature-variability'            => $curator_feature_variability,
	'feature-automatic-disinfection' => $curator_feature_disinfection,
	'feature-services'               => $showroom,
);

$seed_features = array(
	array(
		'key'            => 'feature-insulation',
		'slug'           => 'izolace',
		'title'          => 'Izolace vířivky',
		'excerpt'        => 'Obvodová izolace FreeHeat™ zaručí nízké náklady na provoz i v kruté zimě.',
		'anchor'         => 'izolace',
		'detail_page_id' => $feature_insulation_id,
	),
	array(
		'key'            => 'feature-shell-warranty',
		'slug'           => 'zaruka-na-skorepinu',
		'title'          => 'Záruka na skořepinu',
		'excerpt'        => 'Doživotní záruka na vodotěsnost skořepiny, nejdůležitější součást vířivky.',
		'anchor'         => 'skorepina',
		'detail_page_id' => $feature_shell_warranty_id,
	),
	array(
		'key'        => 'feature-cover',
		'slug'       => 'termokryt',
		'title'      => 'Termokryt',
		'excerpt'    => 'Kryty s nejdelší životností. To jsou kryty Mylovac - Castcore®.',
		'anchor'     => 'termokryt',
		'detail_page_id' => $feature_cover_id,
	),
	array(
		'key'        => 'feature-floor',
		'slug'       => 'podlaha',
		'title'      => 'Podlaha vířivky',
		'excerpt'    => 'Vířivku s věčnou podlahou Forever Floor™ je možno umístit na jakýkoliv povrch.',
		'anchor'     => 'podlaha',
		'detail_page_id' => $feature_floor_id,
	),
	array(
		'key'        => 'feature-service-access',
		'slug'       => 'servis',
		'title'      => 'Servis',
		'excerpt'    => 'Snadný přístup pro servis a upgrade vířivky.',
		'anchor'     => 'servis',
		'detail_page_id' => $feature_service_access_id,
	),
	array(
		'key'        => 'feature-variability',
		'slug'       => 'variabilita',
		'title'      => 'Variabilita',
		'excerpt'    => 'Nikde jinde nenaleznete tolik možností.',
		'anchor'     => 'variabilita',
		'detail_page_id' => $feature_variability_id,
	),
	array(
		'key'        => 'feature-automatic-disinfection',
		'slug'       => 'automaticka-dezinfekce',
		'title'      => 'Automatická dezinfekce',
		'excerpt'    => 'Čistá a bezpečná voda, volte systém Spa Boy.',
		'anchor'     => 'automaticka-dezinfekce',
		'detail_page_id' => $feature_disinfection_id,
	),
	array(
		'key'            => 'feature-services',
		'slug'           => 'sluzby',
		'title'          => 'Služby',
		'excerpt'        => 'Nezávazná konzultace a osobní schůzka. Dovoz a instalace zdarma.',
		'anchor'         => 'sluzby',
		'detail_page_id' => $services_id,
	),
);

$seed_feature_overrides = array(
	'feature-shell-warranty' => array(
		'slug'           => 'zaruka-na-skorepinu',
		'title'          => 'Záruka na skořepinu',
		'excerpt'        => 'Doživotní záruka na vodotěsnost skořepiny, nejdůležitější součást vířivky.',
		'detail_page_id' => $feature_shell_warranty_id,
		'custom_url'     => '',
	),
	'feature-cover' => array(
		'slug'           => 'termokryt',
		'title'          => 'Termokryt',
		'excerpt'        => 'Kryty s nejdelší životností. To jsou kryty Mylovac - Castcore®.',
		'detail_page_id' => $feature_cover_id,
		'custom_url'     => '',
	),
	'feature-floor' => array(
		'slug'           => 'podlaha-virivky',
		'title'          => 'Podlaha vířivky',
		'excerpt'        => 'Vířivku s věčnou podlahou Forever Floor™ je možno umístit na jakýkoliv pevný rovný povrch.',
		'detail_page_id' => $feature_floor_id,
		'custom_url'     => '',
	),
	'feature-service-access' => array(
		'slug'           => 'servisni-pristup',
		'title'          => 'Servisní přístup',
		'excerpt'        => 'Snadný přístup pro servis, opravy i budoucí upgrade vířivky.',
		'anchor'         => 'servisni-pristup',
		'detail_page_id' => $feature_service_access_id,
		'custom_url'     => '',
	),
	'feature-variability' => array(
		'slug'           => 'variabilita',
		'title'          => 'Variabilita',
		'excerpt'        => 'Miliony kombinací výbavy, barev a technologií pro vířivku podle vašich potřeb.',
		'detail_page_id' => $feature_variability_id,
		'custom_url'     => '',
	),
	'feature-automatic-disinfection' => array(
		'slug'           => 'automaticka-dezinfekce',
		'title'          => 'Automatická dezinfekce',
		'excerpt'        => 'Čistá a bezpečná voda s inteligentním systémem Spa Boy™.',
		'detail_page_id' => $feature_disinfection_id,
		'custom_url'     => '',
	),
);

foreach ( $seed_features as $index => $seed_feature ) {
	if ( isset( $seed_feature_overrides[ $seed_feature['key'] ] ) ) {
		$seed_feature = array_merge( $seed_feature, $seed_feature_overrides[ $seed_feature['key'] ] );
	}

	$feature_id = arctic_seed_post_by_meta( 'feature', '_arctic_seed_key', $seed_feature['key'], array(
		'post_status'  => 'publish',
		'post_title'   => $seed_feature['title'],
		'post_name'    => $seed_feature['slug'],
		'post_excerpt' => $seed_feature['excerpt'],
		'post_content' => '',
		'menu_order'   => ( $index + 1 ) * 10,
	) );

	update_post_meta( $feature_id, 'feature_card_anchor', $seed_feature['anchor'] );
	update_post_meta( $feature_id, 'feature_detail_page_id', (int) ( $seed_feature['detail_page_id'] ?? 0 ) );
	update_post_meta( $feature_id, 'feature_custom_url', (string) ( $seed_feature['custom_url'] ?? '' ) );
	if ( !empty( $seed_feature_images[ $seed_feature['key'] ] ) ) {
		set_post_thumbnail( $feature_id, (int) $seed_feature_images[ $seed_feature['key'] ] );
	} else {
		delete_post_thumbnail( $feature_id );
	}
}

$seed_services = array(
	array(
		'key'     => 'service-consultation',
		'slug'    => 'konzultace',
		'title'   => 'Konzultace',
		'content' => '<!-- wp:paragraph --><p>Pokud si ještě nejste jisti, že skutečně chcete koupit vířivku, využijte naši nabídku nezávazné konzultace. Obrátit se na nás můžete telefonicky či e-mailem. Obecná doporučení, jak postupovat při výběru vířivky, jistě oceníte, ať už se rozhodnete jakkoliv.</p><!-- /wp:paragraph -->',
		'image'   => $service_images['consultation'],
	),
	array(
		'key'     => 'service-meeting',
		'slug'    => 'osobni-schuzka',
		'title'   => 'Osobní schůzka',
		'content' => '<!-- wp:paragraph --><p>Současná situace na trhu vířivek se může jevit jako nepřehledná. Nejvíce informací můžete získat při osobním jednání. Po dohodě se rádi přijedeme nezávazně podívat na místo plánované instalace vířivky a poradíme vám s jejím umístěním a technickými podmínkami připojení.</p><!-- /wp:paragraph -->',
		'image'   => $service_images['meeting'],
	),
	array(
		'key'     => 'service-catalog',
		'slug'    => 'katalog-a-cenova-nabidka',
		'title'   => 'Katalog a cenová nabídka',
		'content' => '<!-- wp:paragraph --><p>Na prodejně máme k dispozici tištěný katalog a spolu s ceníkem si z naší prodejny odnesete také veškeré potřebné informace. A pokud vás v naší nabídce osloví konkrétní model vířivky, zpracujeme vám individuální cenovou nabídku.</p><!-- /wp:paragraph -->',
		'image'   => $service_images['catalog'],
	),
	array(
		'key'     => 'service-showroom',
		'slug'    => 'vzorkova-prodejna',
		'title'   => 'Vzorková prodejna',
		'content' => '<!-- wp:paragraph --><p>Zázemí naší vzorkové prodejny umožňuje nejen prohlídku, ale také vyzkoušení vybraných vířivek. Doufáme, že vás snadná dostupnost z D1 a příjemné prostředí nalákají k návštěvě, určitě to má při vážném zájmu smysl.</p><!-- /wp:paragraph -->',
		'image'   => $service_images['showroom'],
	),
	array(
		'key'     => 'service-delivery',
		'slug'    => 'dovoz-a-instalace',
		'title'   => 'Dovoz a instalace',
		'content' => '<!-- wp:paragraph --><p>Férovým jednáním při prodeji naše nadstandardní služby nekončí. Objednanou vířivku vám v dohodnutém termínu zdarma přivezeme, zajistíme přesun z veřejné komunikace na místo určení, zapojíme a zaškolíme.</p><!-- /wp:paragraph -->',
		'image'   => $service_images['delivery'],
	),
	array(
		'key'     => 'service-support',
		'slug'    => 'zarucni-a-pozarucni-servis',
		'title'   => 'Záruční a pozáruční servis',
		'content' => '<!-- wp:paragraph --><p>Považujeme za samozřejmost postarat se o naše zákazníky také v rámci záručního a pozáručního servisu kdekoliv v ČR nebo na Slovensku. Jsme tu pro vás již více než 14 let.</p><!-- /wp:paragraph -->',
		'image'   => $service_images['service-support'],
	),
);

foreach ( $seed_services as $index => $seed_service ) {
	$service_id = arctic_seed_post_by_meta( 'service', '_arctic_seed_key', $seed_service['key'], array(
		'post_status'  => 'publish',
		'post_title'   => $seed_service['title'],
		'post_name'    => $seed_service['slug'],
		'post_content' => $seed_service['content'],
		'menu_order'   => ( $index + 1 ) * 10,
	) );

	set_post_thumbnail( $service_id, (int) $seed_service['image'] );
}

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
arctic_seed_set_multi_meta( $certificates_id, 'certificates_sections', array(
	array(
		'title' => 'Ruční výroba kombinovaná s high-tech',
		'text'  => 'Jako mezinárodní výrobce vířivek musíme ze zákona splňovat kvalitativní a bezpečnostní standardy. Pro certifikaci našich výrobků jsme se obrátili na organizaci TÜV, která je jednou z nejpřísnějších a nejvíce respektovaných certifikačních organizací na světě.',
	),
	array(
		'title' => 'Certifikovaný výrobce, na špičce v oboru',
		'text'  => 'Před nákupem vířivky byste se měli ujistit, že byla respektovanou agenturou certifikována a je v souladu s normami UL 1563, CSA C22.2 #218 a IEC 60335-1/IEC 60335-2-60.',
	),
) );
arctic_seed_set_multi_meta( $certificates_id, 'certificates_images', $certificate_images );

$maintenance_content = <<<'HTML'
<section>
	<h2>Náklady na vlastnictví a provozování vířivky</h2>
	<p>Stejně jako zvyklost používání vířivky, náklady na pořízení a provozování vířivky se během posledních několika let drasticky změnily. Když byly vířivky původně distribuovány pro domácí použití, jejich celkový design a energetická účinnost měly zásadní nedostatky; výrobci se soustředili více na praktické využití než na provozní náklady.</p>
	<p>Pokroky u výrobců jako je Arctic Spas výrazně zlepšily vývoj a technologie, čímž se snížily počáteční náklady a hlavně provozní náklady spojené s vlastnictvím přenosné vířivky. Jednou z takových inovací je technologie FreeHeat, při níž teplo generované vnitřními čerpadly a motory slouží k udržení teploty vody na optimální úrovni.</p>
	<ul>
		<li>Izolované termokryty pomáhají zachytit a udržovat stoupající teplo ve vířivce.</li>
		<li>Vynikající filtrační systémy udržují čistou vodu s menší potřebou chemie.</li>
		<li>Lepší chemikálie Arctic Pure zkracují čas potřebný k běžné údržbě.</li>
	</ul>
	<p>Konstrukce vířivky se počítá také. Vířivky Arctic Spas jsou navrženy tak, aby odolaly náročnému klimatu a poskytovaly dlouhodobou spolehlivost. Robustní konstrukce umožňuje komponentům, čerpadlům, motorům a dalším částem naplno využít svůj potenciál.</p>
	<p>Křehké nafukovací vířivky nejsou schopny pracovat v nepříjemném studeném počasí. Velké náklady na udržení správně nahřáté nafukovací nebo špatně izolované vířivky rychle vyrovnávají jakýkoli vnímaný přínos získaný nákupem levnějšího řešení.</p>
	<p>Venkovní vířivka je velkou investicí a náklady na vlastnictví vířivky bývají často ztraceny ve směsi celkových ročních nákladů. Pokud si koupíte vysoce kvalitní vířivku s ekonomickým provozním systémem, můžete čekat nízké každodenní provozní náklady po celý rok.</p>
	<p>Počáteční nákup, nové čerpadlo, nový termokryt a další náklady jsou zřetelnější než každodenní výdaje. Právě proto je důležité porovnávat nejen cenu pořízení, ale i konstrukci, izolaci, účinnost filtrace a dlouhodobou spotřebu energie.</p>
	<p>Rozsáhlá studie společnosti Arctic Spas ukazuje účinnost robustního designu a inteligentních vlastností zadržujících teplo. Vířivka Arctic Spas vykazovala denní provozní náklady nižší než 60 centů, zatímco vířivka s vnitřním prostorem vyplněným pěnou spotřebovala přes 75 centů za den.</p>
	<p>Tato studie se uskutečnila počátkem ledna v Coloradu a využívala standardní osmihodinový provoz filtračního cyklu. Průměrná venkovní teplota při studii byla 7 °F, tedy −13,8 °C, a ukázala, že vířivky Arctic Spas jsou výrazně účinnější než typické vířivky vyplněné pěnou.</p>
	<p>Navíc testovaná vířivka Arctic Spas měla kapacitu 450 galonů, tedy přibližně 1700 litrů, zatímco pěnová vana měla pouze 400 galonů. I s větším množstvím vody k ohřevu vyšla vířivka Arctic Spas z testu nejlépe.</p>
	<p>Tepelný výkon je dalším klíčovým faktorem při určování toho, jak je cenově výhodný provoz vířivky. Různé studie srovnávaly tři vířivky Arctic Spas proti jiným modelům v testu stálé spotřeby.</p>
	<p>Výzkumníci chtěli určit energeticky účinné atributy různých modelů vířivek. Všechny tři modely Arctic Spas se umístily do pátého místa, včetně prvních dvou míst, v kategorii nejnižší spotřeba energie.</p>
	<p>V závěrečném hodnocení celkové energetické účinnosti se vířivky Arctic Spas opět dostaly na první a druhé místo s modely Kodiak SS-1 a Kodiak SS-2. Tento test tepelného ohodnocení pracoval se spotřebou energie, ochlazováním v čase a teplotou vody.</p>
	<ul>
		<li>Spotřeba energie v ustáleném stavu</li>
		<li>Ochlazování v čase</li>
		<li>Specifická spotřeba energie v ustáleném stavu</li>
		<li>Teplota vody</li>
		<li>Spotřeba energie čerpadel</li>
		<li>Spotřeba energie ohřevu</li>
	</ul>
	<p>Jedním z nejzajímavějších výsledků vyhodnocení bylo dosažení stálého tepelného stavu při určité okolní teplotě. Arctic Spas Kodiak SS-2 spotřeboval pouze 149 wattů během 128 hodin, zatímco Cal Spas Atlantic spotřeboval 503 wattů za 130 hodin.</p>
	<p>Špičková tepelná izolace a venkovní provedení vířivky Kodiak SS-2 pomáhají vysvětlit drastické rozdíly ve spotřebě elektrické energie a tedy i v provozních nákladech. V závěrečném hodnocení celkové energetické účinnosti se vířivky Arctic Spas opět dostaly na přední místa.</p>
	<p>Pro běžného uživatele je důležité, že tato technická řešení nejsou jen laboratorní hodnoty. V každodenním provozu znamenají stabilnější teplotu vody, méně zbytečných ohřevových cyklů a pohodlnější celoroční používání.</p>
	<p>Nízké provozní náklady vířivky dokazují, že kvalitní venkovní vířivka nemusí znamenat nepříjemně vysoké měsíční účty za energii.</p>
</section>
<section>
	<h2>Další inovace</h2>
	<p>Jakákoliv překážka mezi elektrickým zařízením a skořepinou omezí přenos zbytkového tepla a znamená zvýšené náklady pro uživatele. Naše přesahující izolace přístupových dvířek zamezuje poslední možnosti tepelného úniku a ovládací systém RossExhaust™ monitoruje teplotu uvnitř kabinetu. Žádná jiná vířivka na světě nemá FreeHeat™.</p>
	<p>Schopnost kontrolovat a upravovat teplotu pod kabinetem je natolik unikátní a cenná, že jsme si toto řešení ochránili patentem. Výsledek je stabilnější prostředí pro technologii, efektivnější využití odpadního tepla a klidnější celoroční provoz i při náročném počasí.</p>
</section>
<section>
	<h2>Nejnižší provozní náklady</h2>
	<p>Zbytkové teplo z provozu zařízení, především čerpadel, může prostupovat přes sklolaminátovou skořepinu. To přispívá k přihřívání vody a snižuje počet ohřevových cyklů a provozní dobu elektroohřevu. Na rozdíl od vířivek vyplněných pěnou tak méně časté spínání elektroohřevu znamená nižší účet za elektrickou energii každý měsíc a předvídatelnější provozní náklady.</p>
</section>
<section>
	<h2>Skutečná ochrana proti mrazu</h2>
	<p>Obvodová izolace FreeHeat® má ještě jednu výhodu: schopnost využít teplo kumulované ve vodě vířivky k ochraně proti mrazu při výpadku proudu. Při mrazech −20 °C máte podle našich zkušeností přibližně 5 dnů na to, abyste zabránili promrznutí technologie. Pokud si zajistíte externí zdroj tepla, může být tato doba prakticky neomezená. Není špatné mít připravené řešení i pro extrémní situace, které mohou v zimě přijít bez varování a vyžadují rychlou reakci ještě před příjezdem servisu nebo obnovením elektřiny v celé lokalitě.</p>
</section>
HTML;

$maintenance_id = arctic_seed_page(
	'kolik-stoji-udrzba',
	'Kolik stojí provoz a údržba vířivky?',
	$maintenance_content,
	'template-maintenance.php'
);
update_post_meta( $maintenance_id, 'page_description_text', 'Jedním z nejdůležitějších parametrů ve kterých značka Arctic Spas jednoznačně dominuje jsou velmi nízké provozní náklady a s tím spojená kvalita izolací a termo krytů. Žádná značka vířivek nemá obvodovou izolaci silnou 8 - 10 cm jako vířivky Arctic Spas a neexistují žádné alternativy, které takovouto izolaci nahradí. Vířivky Arctic Spas jsou vyráběny u nejseverněji položeného výrobce vířivek a i proto patří mezi nejúspornější vířivky na světě, číslo jedna na trhu jsou ve Skandinávii, Kanadě a na severu Spojených států.' );

$about_content = <<<'HTML'
<!-- wp:paragraph -->
<p>Prodejem vířivek Arctic Spas se zabýváme již od roku 2005 a základ našeho týmu se za tu dobu nezměnil. Máme více než 15 let osobních zkušeností s dovozem, prodejem, instalacemi a servisem vířivek Arctic Spas, které se zúročily při stovkách realizací v České republice i na Slovensku.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Při naší práci se můžeme jako autorizovaný dealer spolehnout také na podporu kanadského výrobce s celosvětovou působností a tradicí od roku 1994. Veškeré získané know-how je plně k dispozici našim zákazníkům.</p>
<!-- /wp:paragraph -->
HTML;

$about_id = arctic_seed_page(
	'o-nas',
	'O nás',
	$about_content,
	'template-about.php'
);
update_post_meta( $about_id, 'page_description_text', 'Pořízení bazénu nebo vířivky je investice, která by měla být pečlivě zvážena. Při výběru značky i dodavatele je rozumné seznámit se s profilem výrobce a prodejce.' );
update_post_meta( $about_id, 'about_intro_title', 'Naše společnost' );
arctic_seed_set_multi_meta( $about_id, 'about_stats', array(
	array(
		'value' => '21+',
		'label' => 'let zkušeností',
	),
	array(
		'value' => '1000+',
		'label' => 'spokojených klientů',
	),
	array(
		'value' => '11',
		'label' => 'členů týmu',
	),
) );

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
set_theme_mod( 'baspa_copyright', sprintf( 'Copyright © %s Arctic Spas CZ. Všechna práva vyhrazena.', date_i18n( 'Y' ) ) );
set_theme_mod( 'baspa_billing_title', 'Fakturační údaje' );
set_theme_mod( 'baspa_billing_company', 'BASPA s.r.o.' );
set_theme_mod( 'baspa_billing_address', 'Bohunická cesta 727/15, 664 48 Moravany' );
set_theme_mod( 'baspa_billing_ico', 'IČ 02257467' );
set_theme_mod( 'baspa_billing_dic', 'DIČ CZ02257467' );
set_theme_mod( 'baspa_billing_registry', 'Společnost je zapsána v obchodním rejstříku vedeném u Krajského soudu v Brně, oddíl C, vložka 80736.' );
set_theme_mod( 'baspa_street', 'Bohunicka cesta 15' );
set_theme_mod( 'baspa_zip', '664 48' );
set_theme_mod( 'baspa_city', 'Moravany u Brna' );
set_theme_mod( 'baspa_map', 'https://maps.app.goo.gl/ZsYfoZ2aQGF1JnZG6' );
set_theme_mod( 'arctic_home_promo_url', '/akcni-nabidky/' );
set_theme_mod( 'arctic_map_embed', function_exists( 'arctic_get_default_map_embed_url' ) ? arctic_get_default_map_embed_url() : 'https://www.google.com/maps?q=Bohunick%C3%A1%20cesta%2015%2C%20664%2048%20Moravany%20u%20Brna&output=embed' );
set_theme_mod( 'arctic_jucra_default_model', 'Timberwolf' );
set_theme_mod( 'arctic_jucra_model_definitions', function_exists( 'arctic_jucra_model_definitions_to_text' ) ? arctic_jucra_model_definitions_to_text() : '' );
update_option( 'baspa_contacts_interest_options', "pool|Swimspa\njacuzzi|Vířivka\nservice|Servis\noffer|Akční nabídka" );
update_option( 'baspa_contacts_form_text_placeholder_interest', 'Vyberte, o co máte zájem ...' );
update_option( 'baspa_contacts_form_text_submit_contact', 'Odeslat' );
update_option( 'baspa_contacts_form_text_submit_jucra', 'Odeslat poptávku' );
update_option( 'baspa_contacts_form_text_submit_service', 'Odeslat požadavek' );
update_option( 'site_icon', $site_icon_attachment );

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
		'object_id' => $hot_tubs_page_id,
		'classes'  => array( 'arctic-menu-products' ),
		'children' => array(
			array( 'title' => 'Vybrat podle parametrů', 'url' => $hot_tubs_url ),
			array( 'title' => 'Série Core', 'url' => $hot_tubs_url . '#serie-core' ),
			array( 'title' => 'Série Classic', 'url' => $hot_tubs_url . '#serie-classic' ),
			array( 'title' => 'Série Custom', 'url' => $hot_tubs_url . '#serie-custom' ),
			array( 'title' => 'Akční nabídky', 'url' => $offers_url ),
		),
	),
	array(
		'title'    => 'Celoroční bazény',
		'url'      => $swimspa_url,
		'classes'  => array( 'arctic-menu-products' ),
		'children' => array(
			array( 'title' => 'Vybrat podle parametrů', 'url' => $swimspa_url ),
			array( 'title' => 'Bazény ARCTIC Classic', 'url' => $swimspa_url . '#serie-swimspa-classic' ),
			array( 'title' => 'Bazény ARCTIC Custom', 'url' => $swimspa_url . '#serie-swimspa-custom' ),
		),
	),
	array(
		'title'    => 'Vlastnosti',
		'url'      => '#',
		'classes'  => array( 'arctic-menu-features' ),
		'children' => array(
			array( 'title' => 'Tepelná izolace', 'url' => get_permalink( $feature_insulation_id ) ),
			array( 'title' => 'Záruka na skořepinu', 'url' => get_permalink( $feature_shell_warranty_id ) ),
			array( 'title' => 'Termokryt', 'url' => get_permalink( $feature_cover_id ) ),
			array( 'title' => 'Podlaha vířivky', 'url' => get_permalink( $feature_floor_id ) ),
			array( 'title' => 'Servisní přístup', 'url' => get_permalink( $feature_service_access_id ) ),
			array( 'title' => 'Variabilita', 'url' => get_permalink( $feature_variability_id ) ),
			array( 'title' => 'Automatická dezinfekce', 'url' => get_permalink( $feature_disinfection_id ) ),
		),
	),
	array(
		'title'    => 'Další informace',
		'url'      => '#',
		'classes'  => array( 'arctic-menu-info' ),
		'children' => array(
			array( 'title' => 'Služby', 'url' => get_permalink( $services_id ) ),
			array( 'title' => 'Akční nabídky', 'url' => $offers_url ),
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
	array(
		'title'    => 'Vířivky',
		'url'      => $hot_tubs_url,
		'children' => array(
			array( 'title' => 'Série Core', 'url' => home_url( '/virivky/?series=core' ) ),
			array( 'title' => 'Série Classic', 'url' => home_url( '/virivky/?series=classic' ) ),
			array( 'title' => 'Série Custom', 'url' => home_url( '/virivky/?series=custom' ) ),
			array( 'title' => 'Skladové vířivky', 'url' => $offers_url ),
			array(
				'title'    => 'Celoroční bazény',
				'url'      => $swimspa_url,
				'children' => array(
					array( 'title' => 'Bazény ARCTIC Classic', 'url' => home_url( '/swimspa/#serie-swimspa-classic' ) ),
					array( 'title' => 'Bazény ARCTIC Custom', 'url' => home_url( '/swimspa/#serie-swimspa-custom' ) ),
				),
			),
		),
	),
	array(
		'title'     => 'Vlastnosti vířivek',
		'object_id' => $features_id,
		'children'  => array(
			array( 'title' => 'Izolace vířivky', 'object_id' => $feature_insulation_id ),
			array( 'title' => 'Záruka na skořepinu', 'object_id' => $feature_shell_warranty_id ),
			array( 'title' => 'Termokryt', 'object_id' => $feature_cover_id ),
			array( 'title' => 'Podlaha vířivky', 'object_id' => $feature_floor_id ),
			array( 'title' => 'Servisní přístup', 'object_id' => $feature_service_access_id ),
			array( 'title' => 'Variabilita', 'object_id' => $feature_variability_id ),
			array( 'title' => 'Automatická dezinfekce', 'object_id' => $feature_disinfection_id ),
		),
	),
	array(
		'title'    => 'Další informace',
		'url'      => '#',
		'children' => array(
			array( 'title' => 'Průběh realizace', 'url' => home_url( '/#order-progress' ) ),
			array( 'title' => 'Podpora', 'object_id' => $support_id ),
			array( 'title' => 'Služby', 'object_id' => $services_id ),
			array( 'title' => 'Kolik stojí provoz a údržba', 'object_id' => $maintenance_id ),
			array( 'title' => 'Časté otázky', 'url' => home_url( '/podpora/#caste-dotazy' ) ),
			array( 'title' => 'Reference', 'object_id' => $references_page_id ),
			array( 'title' => 'O nás', 'object_id' => $about_id ),
			array( 'title' => 'Showroom', 'object_id' => $showroom_id ),
			array( 'title' => 'Servis', 'object_id' => $service_request_id ),
			array( 'title' => 'Kontakt', 'object_id' => $contact_id ),
		),
	),
) );

flush_rewrite_rules();

if ( function_exists( 'arctic_flush_mega_menu_cache' ) ) {
	arctic_flush_mega_menu_cache();
}

if ( function_exists( 'WP_CLI' ) ) {
	WP_CLI::success( 'Seeded Arctic pilot content, shell pages, menus, media, and local settings.' );
}
