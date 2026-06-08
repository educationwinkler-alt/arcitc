<?php
/**
 * Local smoke for homepage metabox registration during admin edit/save.
 *
 * Run locally:
 * docker compose run --rm wpcli wp eval-file wp-content/themes/arctic/tools/homepage-admin-metabox-smoke.php --allow-root
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'baspa_pages_metabox_register' ) ) {
	throw new RuntimeException( 'Page metabox registration is not loaded.' );
}

$post_id = wp_insert_post( array(
	'post_type'   => 'page',
	'post_status' => 'draft',
	'post_title'  => 'Homepage metabox smoke',
) );

if ( is_wp_error( $post_id ) || (int) $post_id <= 0 ) {
	throw new RuntimeException( 'Could not create temporary homepage metabox page.' );
}

$old_get     = $_GET;
$old_post    = $_POST;
$old_request = $_REQUEST;

$field_ids = static function ( array $meta_boxes ): array {
	$ids = array();

	foreach ( $meta_boxes as $box ) {
		foreach ( (array) ( $box['fields'] ?? array() ) as $field ) {
			if ( !empty( $field['id'] ) ) {
				$ids[] = (string) $field['id'];
			}
		}
	}

	return $ids;
};

$assert_homepage_fields = static function ( array $meta_boxes, string $context ) use ( $field_ids ): void {
	$ids = $field_ids( $meta_boxes );

	foreach ( array( 'homepage_benefits', 'homepage_benefit_images', 'homepage_progress_steps' ) as $required_id ) {
		if ( !in_array( $required_id, $ids, true ) ) {
			throw new RuntimeException( sprintf( '%s is missing %s. Registered fields: %s', $context, $required_id, implode( ', ', $ids ) ) );
		}
	}
};

try {
	update_post_meta( (int) $post_id, '_wp_page_template', 'template-homepage.php' );

	$_GET     = array( 'post' => (string) $post_id );
	$_POST    = array();
	$_REQUEST = $_GET;
	$assert_homepage_fields( baspa_pages_metabox_register( array() ), 'GET edit context' );

	$_GET     = array();
	$_POST    = array( 'post_ID' => (string) $post_id );
	$_REQUEST = $_POST;
	$assert_homepage_fields( baspa_pages_metabox_register( array() ), 'POST save context' );

	$_GET     = array();
	$_POST    = array(
		'post_ID'       => (string) $post_id,
		'page_template' => 'template-homepage.php',
	);
	$_REQUEST = $_POST;
	$assert_homepage_fields( baspa_pages_metabox_register( array() ), 'POST template switch context' );

	echo 'Homepage admin metabox smoke passed.' . PHP_EOL;
} finally {
	$_GET     = $old_get;
	$_POST    = $old_post;
	$_REQUEST = $old_request;

	wp_delete_post( (int) $post_id, true );
}
