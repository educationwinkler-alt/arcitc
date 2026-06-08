<?php
/**
 * Local smoke for admin-backed page section meta helpers.
 *
 * Run locally:
 * docker compose run --rm wpcli wp eval-file wp-content/themes/arctic/tools/page-section-meta-helpers-smoke.php --allow-root
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'arctic_meta_fieldset_rows' ) || !function_exists( 'arctic_meta_attachment_ids' ) ) {
	throw new RuntimeException( 'Page section meta helpers are not loaded.' );
}

$post_id = wp_insert_post( array(
	'post_type'   => 'page',
	'post_status' => 'draft',
	'post_title'  => 'Page section meta helper smoke',
) );

if ( is_wp_error( $post_id ) || (int) $post_id <= 0 ) {
	throw new RuntimeException( 'Could not create temporary smoke page.' );
}

try {
	add_post_meta( $post_id, '_smoke_rows', array(
		'title' => 'First',
		'text'  => 'Stored as one row.',
	) );

	add_post_meta( $post_id, '_smoke_rows', array(
		array(
			'title' => 'Second',
			'text'  => 'Stored as a nested clone row.',
		),
		array(
			'title' => 'Third',
			'text'  => 'Stored as another nested clone row.',
		),
	) );

	add_post_meta( $post_id, '_smoke_rows', array(
		'group' => array(
			array(
				'title' => 'Fourth',
				'text'  => 'Stored as a deeper admin payload.',
			),
		),
	) );

	add_post_meta( $post_id, '_smoke_images', 101 );
	add_post_meta( $post_id, '_smoke_images', array( 102, '103' ) );
	add_post_meta( $post_id, '_smoke_images', array( 'ID' => 104, 'width' => 1600 ) );
	add_post_meta( $post_id, '_smoke_images', (object) array( 'id' => 105 ) );

	$rows = arctic_meta_fieldset_rows( (int) $post_id, '_smoke_rows', array( 'title', 'text' ) );
	$ids  = arctic_meta_attachment_ids( (int) $post_id, '_smoke_images' );

	$titles = array_map( static function ( array $row ): string {
		return (string) ( $row['title'] ?? '' );
	}, $rows );

	sort( $titles );
	sort( $ids );

	if ( array( 'First', 'Fourth', 'Second', 'Third' ) !== $titles ) {
		throw new RuntimeException( 'Fieldset helper did not preserve all row shapes: ' . wp_json_encode( $titles ) );
	}

	if ( array( 101, 102, 103, 104, 105 ) !== $ids ) {
		throw new RuntimeException( 'Attachment helper did not preserve all image ID shapes: ' . wp_json_encode( $ids ) );
	}

	echo 'Page section meta helper smoke passed.' . PHP_EOL;
} finally {
	wp_delete_post( (int) $post_id, true );
}
