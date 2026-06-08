<?php
/**
 * Initial Arctic legacy redirects.
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', function (): void {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	$request_path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
	$request_path = '/' . ltrim( (string) $request_path, '/' );

	$redirects = array(
		'/virivka-lunar.php'             => '/product/lunar/',
		'/virivka-orion.php'             => '/product/orion/',
		'/virivka-cub.php'               => '/product/cub/',
		'/virivka-eagle.php'             => '/product/eagle/',
		'/virivka-fox.php'               => '/product/fox/',
		'/virivka-husky.php'             => '/product/husky/',
		'/virivka-klondiker.php'         => '/product/klondiker/',
		'/virivka-kodiak.php'            => '/product/kodiak/',
		'/virivka-mckinley.php'          => '/product/mckinley/',
		'/virivka-mustang.php'           => '/product/mustang/',
		'/virivka-summit.php'            => '/product/summit/',
		'/virivka-summit-xl.php'         => '/product/summit-xl/',
		'/virivka-timberwolf.php'        => '/product/timberwolf/',
		'/virivka-totem.php'             => '/product/totem/',
		'/virivka-tundra.php'            => '/product/tundra/',
		'/virivka-yukon.php'             => '/product/yukon/',
		'/bazen-athabascan.php'          => '/product/athabascan/',
		'/bazen-hudson.php'              => '/product/hudson/',
		'/bazen-kingfisher.php'          => '/product/kingfisher/',
		'/bazen-ocean.php'               => '/product/ocean/',
		'/bazen-okanagan.php'            => '/product/okanagan/',
		'/bazen-wolverine.php'           => '/product/wolverine/',
		'/covana.php'                    => '/product/covana/',
		'/sauny.php'                     => '/product/sauny/',
		'/koupaci-sudy-kirami.php'       => '/product/koupaci-sudy-kirami/',
		'/prislusenstvi-doplnky.php'     => '/product/prislusenstvi-a-doplnky/',
		'/arctic-virivky.php'            => '/virivky/',
		'/arctic-virivky-venkovni.php'   => '/virivky/',
		'/catalog/virivky/'              => '/virivky/',
		'/catalog/dalsi-sortiment/'      => '/katalog/dalsi-sortiment/',
		'/akce-virivky.php'              => '/virivky/',
		'/akce-vyhoda.php'               => '/virivky/',
		'/akce-vystava.php'              => '/virivky/',
		'/arctic-bazeny.php'             => '/swimspa/',
		'/arctic-swimspa.php'            => '/swimspa/',
		'/catalog/swimspa/'              => '/swimspa/',
		'/barva-skorepiny.php'           => '/virivky/',
		'/barva-skorepiny-core.php'      => '/virivky/',
		'/cedrove-doplnky.php'           => '/product/prislusenstvi-a-doplnky/',
		'/cerpadla-core.php'             => '/virivky/',
		'/digitalni-rizeni.php'          => '/virivky/',
		'/digitalni-rizeni-gecko.php'    => '/virivky/',
		'/filtrace-smart-virivky.php'    => '/virivky/',
		'/filtrace-virivky.php'          => '/virivky/',
		'/filtrace-virivky-core.php'     => '/virivky/',
		'/filtry.php'                    => '/product/prislusenstvi-a-doplnky/',
		'/fotogalerie-virivky.php'       => '/virivky/',
		'/hudba.php'                     => '/virivky/',
		'/in-touch.php'                  => '/virivky/',
		'/izolace-heatlock.php'          => '/vlastnosti/izolace-virivky/',
		'/izolace-virivky.php'           => '/vlastnosti/izolace-virivky/',
		'/kabinet-cedr.php'              => '/virivky/',
		'/kabinet-core.php'              => '/virivky/',
		'/kabinet-flex.php'              => '/virivky/',
		'/kolik-stoji-provoz-udrzba-virivky.php' => '/kolik-stoji-udrzba/',
		'/komfort-masaz.php'             => '/virivky/',
		'/kyslikova-koupel.php'          => '/virivky/',
		'/on-spa.php'                    => '/virivky/',
		'/onzen.php'                     => '/virivky/',
		'/operky-hlavy.php'              => '/product/prislusenstvi-a-doplnky/',
		'/operky-hlavy-core.php'         => '/product/prislusenstvi-a-doplnky/',
		'/ozonova-desinfekce-peak.php'   => '/virivky/',
		'/ozonova-uv-desinfekce.php'     => '/virivky/',
		'/plavaci-systemy.php'           => '/swimspa/',
		'/podlaha-everlast.php'          => '/virivky/',
		'/podlaha-virivky.php'           => '/virivky/',
		'/potrubi.php'                   => '/virivky/',
		'/sds.php'                       => '/virivky/',
		'/servisni-pristup.php'          => '/vlastnosti/',
		'/servisni-pristup-core.php'     => '/vlastnosti/',
		'/skorepina-elastocast.php'      => '/virivky/',
		'/skorepina-virivky.php'         => '/virivky/',
		'/skyfall.php'                   => '/virivky/',
		'/spa-boy.php'                   => '/virivky/',
		'/svetla.php'                    => '/virivky/',
		'/termokryt-virivky.php'         => '/product/prislusenstvi-a-doplnky/',
		'/termokryt-virivky-core.php'    => '/product/prislusenstvi-a-doplnky/',
		'/topne-teleso.php'              => '/virivky/',
		'/trysky.php'                    => '/virivky/',
		'/trysky-core.php'               => '/virivky/',
		'/trysky-ripple.php'             => '/virivky/',
		'/uprava-vody.php'               => '/kolik-stoji-udrzba/',
		'/variabilita.php'               => '/virivky/',
		'/venkovni-virivky-arctic-vyhody.php' => '/virivky/',
		'/virivky-arctic-jako-prvni.php' => '/virivky/',
		'/vodopad.php'                   => '/virivky/',
		'/vodopad-core.php'              => '/virivky/',
		'/zvedak-termokrytu.php'         => '/product/prislusenstvi-a-doplnky/',
		'/virivky-dreammaker.php'        => '/virivky/',
		'/virivka-dreammaker.php'        => '/virivky/',
		'/virivka-dream-maker.php'       => '/virivky/',
		'/dreammaker.php'                => '/virivky/',
		'/virivka-frontier.php'          => '/virivky/',
		'/virivka-ellesmere.php'         => '/virivky/',
		'/virivka-aurora.php'            => '/virivky/',
		'/virivka-orca.php'              => '/virivky/',
		'/virivka-grizzly.php'           => '/virivky/',
		'/baspa.php'                     => '/o-nas/',
		'/sluzby.php'                    => '/sluzby/',
		'/download.php'                  => '/ke-stazeni/',
		'/ke-stazeni/kategorie/katalogy/' => '/ke-stazeni/',
		'/ke-stazeni/kategorie/navody/'  => '/ke-stazeni/',
		'/ke-stazeni/kategorie/technicke-dokumenty/' => '/ke-stazeni/',
		'/ke-stazeni/kategorie/uprava-vody/' => '/ke-stazeni/',
		'/faq.php'                       => '/podpora/',
		'/certifikaty.php'               => '/certifikaty/',
		'/zaruka.php'                    => '/zaruka/',
		'/odkazy.php'                    => '/podpora/',
		'/diskuze.php'                   => '/reference/',
		'/kariera.php'                   => '/o-nas/',
		'/cookies.php'                   => '/ochrana-osobnich-udaju/',
		'/zasady-zpracovani-osobnich-udaju.php' => '/ochrana-osobnich-udaju/',
		'/kontakt.php'                   => '/kontakt/',
		'/prodejna-bazeny-virivky.php'   => '/showroom/',
		'/servis.php'                    => '/servis/',
		'/stavebni-pripravenost.php'     => '/ke-stazeni/',
	);

	if ( !isset( $redirects[ $request_path ] ) ) {
		if ( str_ends_with( $request_path, '.pdf' ) ) {
			$source_url = 'https://www.arctic-spas.cz' . $request_path;
			$downloads  = get_posts( array(
				'post_type'      => 'download',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => 'download_original_url',
				'meta_value'     => $source_url,
			) );

			if ( !empty( $downloads ) ) {
				$file_url = get_post_meta( (int) $downloads[0], 'download_file_url', true );
				if ( !empty( $file_url ) ) {
					wp_safe_redirect( $file_url, 301 );
					exit;
				}
			}

			if ( str_starts_with( $request_path, '/content/download/' ) ) {
				wp_safe_redirect( home_url( '/ke-stazeni/' ), 301 );
				exit;
			}
		}

		return;
	}

	wp_safe_redirect( home_url( $redirects[ $request_path ] ), 301 );
	exit;
} );
