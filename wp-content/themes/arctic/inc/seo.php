<?php

/**
 * Lightweight SEO output for the Arctic fork.
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function arctic_seo_trim( string $text, int $limit = 160 ): string {
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );

	if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
		if ( mb_strlen( $text ) > $limit ) {
			return rtrim( mb_substr( $text, 0, $limit - 1 ) ) . '…';
		}

		return $text;
	}

	if ( strlen( $text ) > $limit ) {
		return rtrim( substr( $text, 0, $limit - 1 ) ) . '…';
	}

	return $text;
}

function arctic_seo_description(): string {
	if ( is_singular( 'product' ) ) {
		$product_description = get_post_meta( get_the_ID(), 'product_description_short', true );
		$product_description = !empty( $product_description ) ? $product_description : get_post_meta( get_the_ID(), 'product_description', true );

		if ( !empty( $product_description ) ) {
			return arctic_seo_trim( $product_description );
		}
	}

	if ( is_singular() ) {
		$page_description = get_post_meta( get_the_ID(), 'page_description_text', true );

		if ( !empty( $page_description ) ) {
			return arctic_seo_trim( $page_description );
		}

		if ( has_excerpt() ) {
			return arctic_seo_trim( get_the_excerpt() );
		}

		$content = get_post_field( 'post_content', get_the_ID() );
		if ( !empty( $content ) ) {
			return arctic_seo_trim( $content );
		}
	}

	if ( is_tax() || is_category() || is_tag() ) {
		$term_id      = get_queried_object_id();
		$heading_text = get_term_meta( $term_id, 'category_heading_text', true );
		$description  = !empty( $heading_text ) ? $heading_text : term_description( $term_id );

		if ( !empty( $description ) ) {
			return arctic_seo_trim( $description );
		}
	}

	return arctic_seo_trim( __( 'Arctic Spas CZ nabízí venkovní vířivky, celoroční bazény, showroom v Moravanech u Brna, podporu, servis a dokumenty ke stažení.', 'baspa' ) );
}

function arctic_seo_canonical_url(): string {
	if ( function_exists( 'arctic_jucra_is_builder_request' ) && arctic_jucra_is_builder_request() ) {
		$path = isset( $_SERVER['REQUEST_URI'] ) ? parse_url( wp_unslash( (string) $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '/konfigurator/';
		$path = is_string( $path ) && $path !== '' ? $path : '/konfigurator/';

		return home_url( trailingslashit( trim( $path, '/' ) ) );
	}

	if ( is_singular() ) {
		return get_permalink();
	}

	if ( is_tax() || is_category() || is_tag() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? home_url( '/' ) : $link;
	}

	if ( is_front_page() || is_home() ) {
		return home_url( '/' );
	}

	return home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) . '/' );
}

function arctic_seo_image_url(): string {
	if ( is_singular() && has_post_thumbnail() ) {
		$image = wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' );
		if ( !empty( $image ) ) {
			return $image;
		}
	}

	if ( is_tax( 'product-category' ) ) {
		$term_id  = get_queried_object_id();
		$image_id = get_term_meta( $term_id, 'category_heading_image', true );
		$image_id = !empty( $image_id ) ? $image_id : get_term_meta( $term_id, 'category_image', true );
		$image    = !empty( $image_id ) ? wp_get_attachment_image_url( (int) $image_id, 'large' ) : '';

		if ( !empty( $image ) ) {
			return $image;
		}
	}

	return content_url( 'uploads/import/figma/hp-hero-arctic-spas-07.jpg' );
}

function arctic_seo_title(): string {
	return wp_get_document_title();
}

function arctic_seo_output_meta(): void {
	if ( is_admin() ) {
		return;
	}

	$title       = arctic_seo_title();
	$description = arctic_seo_description();
	$canonical   = arctic_seo_canonical_url();
	$image       = arctic_seo_image_url();
	$type        = is_singular( 'product' ) ? 'product' : 'website';
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<link rel="canonical" href="<?php echo esc_url( $canonical ); ?>">
	<meta property="og:locale" content="<?php echo esc_attr( str_replace( '_', '-', get_locale() ) ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $canonical ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<?php
}

function arctic_seo_output_product_schema(): void {
	if ( !is_singular( 'product' ) ) {
		return;
	}

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => get_the_title(),
		'description' => arctic_seo_description(),
		'image'       => array( arctic_seo_image_url() ),
		'brand'       => array(
			'@type' => 'Brand',
			'name'  => 'Arctic Spas',
		),
		'url'         => arctic_seo_canonical_url(),
	);
	?>
	<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php
}

function arctic_seo_robots( array $robots ): array {
	$environment = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';

	if ( 'production' !== $environment ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
	}

	return $robots;
}

remove_action( 'wp_head', 'rel_canonical' );
add_action( 'wp_head', 'arctic_seo_output_meta', 5 );
add_action( 'wp_head', 'arctic_seo_output_product_schema', 6 );
add_filter( 'wp_robots', 'arctic_seo_robots' );
