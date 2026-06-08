<?php
/**
 * Repair homepage admin-backed sections after customer edit regression.
 *
 * Preferred local run:
 * wp eval-file wp-content/themes/arctic/tools/repair-homepage-admin-meta-2026-06-08.php
 *
 * Temporary production web run is guarded by a token:
 * token = hash_hmac('sha256', 'repair-homepage-admin-meta-2026-06-08', DB_PASSWORD)
 */

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';

if ( !defined( 'ABSPATH' ) && file_exists( $wp_load ) ) {
	require_once $wp_load;
}

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

$is_cli = 'cli' === PHP_SAPI;

if ( !$is_cli ) {
	$secret   = defined( 'DB_PASSWORD' ) ? (string) DB_PASSWORD : wp_salt( 'auth' );
	$expected = hash_hmac( 'sha256', 'repair-homepage-admin-meta-2026-06-08', $secret );
	$provided = isset( $_GET['token'] ) ? (string) wp_unslash( $_GET['token'] ) : '';

	if ( !hash_equals( $expected, $provided ) ) {
		status_header( 403 );
		exit( 'Forbidden' );
	}

	header( 'Content-Type: application/json; charset=utf-8' );
}

$home_id = (int) get_option( 'page_on_front' );

if ( $home_id <= 0 || 'page' !== get_option( 'show_on_front' ) ) {
	throw new RuntimeException( 'Front page is not configured as a static page.' );
}

$summary = array(
	'home_id' => $home_id,
	'updated' => array(),
	'kept' => array(),
	'attachments' => array(),
	'warnings' => array(),
);

$set_multi_meta = static function ( int $post_id, string $key, array $values ): void {
	delete_post_meta( $post_id, $key );

	foreach ( $values as $value ) {
		add_post_meta( $post_id, $key, $value );
	}
};

$attachment_for_upload = static function ( string $relative_file, string $title ) use ( &$summary ): int {
	$upload_dir = wp_upload_dir();
	$file       = trailingslashit( $upload_dir['basedir'] ) . ltrim( $relative_file, '/' );

	$existing = get_posts( array(
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'posts_per_page' => 1,
		'fields' => 'ids',
		'meta_key' => '_wp_attached_file',
		'meta_value' => $relative_file,
	) );

	if ( !empty( $existing ) ) {
		$summary['attachments'][] = array(
			'file' => $relative_file,
			'id' => (int) $existing[0],
			'action' => 'kept',
		);

		return (int) $existing[0];
	}

	if ( !file_exists( $file ) ) {
		$summary['warnings'][] = 'Missing upload file: ' . $relative_file;

		return 0;
	}

	$filetype = wp_check_filetype( basename( $file ), null );

	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => $filetype['type'] ?: 'image/png',
		'post_title' => $title,
		'post_content' => '',
		'post_status' => 'inherit',
	), $file );

	if ( is_wp_error( $attachment_id ) || (int) $attachment_id <= 0 ) {
		$summary['warnings'][] = 'Could not create attachment for: ' . $relative_file;

		return 0;
	}

	update_attached_file( (int) $attachment_id, $file );

	if ( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		wp_update_attachment_metadata( (int) $attachment_id, wp_generate_attachment_metadata( (int) $attachment_id, $file ) );
	}

	$summary['attachments'][] = array(
		'file' => $relative_file,
		'id' => (int) $attachment_id,
		'action' => 'created',
	);

	return (int) $attachment_id;
};

$benefits = array(
	array(
		'title' => 'Montáž',
		'text' => 'Odborně na klíč',
		'icon' => 'box',
	),
	array(
		'title' => 'Podpora',
		'text' => 'Se vším vám poradíme',
		'icon' => 'support',
	),
	array(
		'title' => 'Servis',
		'text' => 'Jsme tu pro vás 24/7',
		'icon' => 'service',
	),
);

$benefit_images = array_filter( array(
	$attachment_for_upload( 'import/figma/hp-benefit-montaz.png', 'Homepage benefit - Montáž' ),
	$attachment_for_upload( 'import/figma/hp-benefit-podpora.png', 'Homepage benefit - Podpora' ),
	$attachment_for_upload( 'import/figma/hp-benefit-servis.png', 'Homepage benefit - Servis' ),
) );

$existing_benefits = get_post_meta( $home_id, 'homepage_benefits' );
if ( count( $existing_benefits ) < count( $benefits ) ) {
	$set_multi_meta( $home_id, 'homepage_benefits', $benefits );
	$summary['updated'][] = 'homepage_benefits';
} else {
	$summary['kept'][] = 'homepage_benefits';
}

$existing_benefit_images = array_filter( array_map( 'absint', get_post_meta( $home_id, 'homepage_benefit_images' ) ) );
if ( count( $existing_benefit_images ) < count( $benefit_images ) ) {
	$set_multi_meta( $home_id, 'homepage_benefit_images', array_values( $benefit_images ) );
	$summary['updated'][] = 'homepage_benefit_images';
} else {
	$summary['kept'][] = 'homepage_benefit_images';
}

if ( '' === trim( (string) get_post_meta( $home_id, 'homepage_progress_title', true ) ) ) {
	update_post_meta( $home_id, 'homepage_progress_title', 'Průběh zakázky' );
	$summary['updated'][] = 'homepage_progress_title';
}

if ( '' === trim( (string) get_post_meta( $home_id, 'homepage_progress_text', true ) ) ) {
	update_post_meta( $home_id, 'homepage_progress_text', 'Od první poptávky až po odbornou montáž vás provedeme celým procesem tak, aby byl výběr vířivky nebo celoročního bazénu jednoduchý a přehledný.' );
	$summary['updated'][] = 'homepage_progress_text';
}

$progress_steps = array(
	array(
		'title' => 'Vaše poptávka',
		'text' => 'Ozvěte se nám přes poptávkový formulář, e-mail nebo telefonicky.',
	),
	array(
		'title' => 'Konzultace',
		'text' => 'Doporučíme vhodnou řadu, konfiguraci a případnou návštěvu showroomu.',
	),
	array(
		'title' => 'Nabídka',
		'text' => 'Připravíme nezávaznou kalkulaci včetně dopravy, montáže a příslušenství.',
	),
	array(
		'title' => 'Potvrzení řešení',
		'text' => 'Doladíme konfiguraci, termín dodání a technické požadavky na místo instalace.',
	),
	array(
		'title' => 'Příprava místa',
		'text' => 'Předem poradíme se stavební připraveností, přístupem a napojením.',
	),
	array(
		'title' => 'Montáž a předání',
		'text' => 'Zajistíme odbornou instalaci, zaškolení a předáme vše pro pohodlný provoz.',
	),
);

$existing_steps = get_post_meta( $home_id, 'homepage_progress_steps' );
if ( count( $existing_steps ) < count( $progress_steps ) ) {
	$set_multi_meta( $home_id, 'homepage_progress_steps', $progress_steps );
	$summary['updated'][] = 'homepage_progress_steps';
} else {
	$summary['kept'][] = 'homepage_progress_steps';
}

echo wp_json_encode( $summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . PHP_EOL;
