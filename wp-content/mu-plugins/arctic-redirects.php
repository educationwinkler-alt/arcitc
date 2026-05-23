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
		'/arctic-virivky.php'            => '/catalog/virivky/',
		'/arctic-virivky-venkovni.php'   => '/catalog/virivky/',
		'/virivky/'                      => '/catalog/virivky/',
		'/akce-virivky.php'              => '/catalog/virivky/',
		'/akce-vyhoda.php'               => '/catalog/virivky/',
		'/akce-vystava.php'              => '/catalog/virivky/',
		'/arctic-bazeny.php'             => '/catalog/swimspa/',
		'/arctic-swimspa.php'            => '/catalog/swimspa/',
		'/swimspa/'                      => '/catalog/swimspa/',
		'/barva-skorepiny.php'           => '/catalog/virivky/',
		'/barva-skorepiny-core.php'      => '/catalog/virivky/',
		'/cedrove-doplnky.php'           => '/product/prislusenstvi-a-doplnky/',
		'/cerpadla-core.php'             => '/catalog/virivky/',
		'/digitalni-rizeni.php'          => '/catalog/virivky/',
		'/digitalni-rizeni-gecko.php'    => '/catalog/virivky/',
		'/filtrace-smart-virivky.php'    => '/catalog/virivky/',
		'/filtrace-virivky.php'          => '/catalog/virivky/',
		'/filtrace-virivky-core.php'     => '/catalog/virivky/',
		'/filtry.php'                    => '/product/prislusenstvi-a-doplnky/',
		'/fotogalerie-virivky.php'       => '/catalog/virivky/',
		'/hudba.php'                     => '/catalog/virivky/',
		'/in-touch.php'                  => '/catalog/virivky/',
		'/izolace-heatlock.php'          => '/catalog/virivky/',
		'/izolace-virivky.php'           => '/catalog/virivky/',
		'/kabinet-cedr.php'              => '/catalog/virivky/',
		'/kabinet-core.php'              => '/catalog/virivky/',
		'/kabinet-flex.php'              => '/catalog/virivky/',
		'/kolik-stoji-provoz-udrzba-virivky.php' => '/podpora/',
		'/komfort-masaz.php'             => '/catalog/virivky/',
		'/kyslikova-koupel.php'          => '/catalog/virivky/',
		'/on-spa.php'                    => '/catalog/virivky/',
		'/onzen.php'                     => '/catalog/virivky/',
		'/operky-hlavy.php'              => '/product/prislusenstvi-a-doplnky/',
		'/operky-hlavy-core.php'         => '/product/prislusenstvi-a-doplnky/',
		'/ozonova-desinfekce-peak.php'   => '/catalog/virivky/',
		'/ozonova-uv-desinfekce.php'     => '/catalog/virivky/',
		'/plavaci-systemy.php'           => '/catalog/swimspa/',
		'/podlaha-everlast.php'          => '/catalog/virivky/',
		'/podlaha-virivky.php'           => '/catalog/virivky/',
		'/potrubi.php'                   => '/catalog/virivky/',
		'/sds.php'                       => '/catalog/virivky/',
		'/servisni-pristup.php'          => '/podpora/',
		'/servisni-pristup-core.php'     => '/podpora/',
		'/skorepina-elastocast.php'      => '/catalog/virivky/',
		'/skorepina-virivky.php'         => '/catalog/virivky/',
		'/skyfall.php'                   => '/catalog/virivky/',
		'/spa-boy.php'                   => '/catalog/virivky/',
		'/svetla.php'                    => '/catalog/virivky/',
		'/termokryt-virivky.php'         => '/product/prislusenstvi-a-doplnky/',
		'/termokryt-virivky-core.php'    => '/product/prislusenstvi-a-doplnky/',
		'/topne-teleso.php'              => '/catalog/virivky/',
		'/trysky.php'                    => '/catalog/virivky/',
		'/trysky-core.php'               => '/catalog/virivky/',
		'/trysky-ripple.php'             => '/catalog/virivky/',
		'/uprava-vody.php'               => '/podpora/',
		'/variabilita.php'               => '/catalog/virivky/',
		'/venkovni-virivky-arctic-vyhody.php' => '/catalog/virivky/',
		'/virivky-arctic-jako-prvni.php' => '/catalog/virivky/',
		'/vodopad.php'                   => '/catalog/virivky/',
		'/vodopad-core.php'              => '/catalog/virivky/',
		'/zvedak-termokrytu.php'         => '/product/prislusenstvi-a-doplnky/',
		'/virivky-dreammaker.php'        => '/catalog/virivky/',
		'/virivka-dreammaker.php'        => '/catalog/virivky/',
		'/virivka-dream-maker.php'       => '/catalog/virivky/',
		'/dreammaker.php'                => '/catalog/virivky/',
		'/virivka-frontier.php'          => '/catalog/virivky/',
		'/virivka-ellesmere.php'         => '/catalog/virivky/',
		'/virivka-aurora.php'            => '/catalog/virivky/',
		'/virivka-orca.php'              => '/catalog/virivky/',
		'/virivka-grizzly.php'           => '/catalog/virivky/',
		'/baspa.php'                     => '/showroom/',
		'/sluzby.php'                    => '/podpora/',
		'/download.php'                  => '/ke-stazeni/',
		'/faq.php'                       => '/podpora/',
		'/certifikaty.php'               => '/podpora/',
		'/zaruka.php'                    => '/podpora/',
		'/odkazy.php'                    => '/podpora/',
		'/diskuze.php'                   => '/podpora/',
		'/kariera.php'                   => '/kontakt/',
		'/cookies.php'                   => '/kontakt/',
		'/zasady-zpracovani-osobnich-udaju.php' => '/kontakt/',
		'/kontakt.php'                   => '/kontakt/',
		'/prodejna-bazeny-virivky.php'   => '/showroom/',
		'/servis.php'                    => '/podpora/',
		'/stavebni-pripravenost.php'     => '/podpora/',
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
