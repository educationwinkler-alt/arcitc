<?php

/**
 * Page
 */

$page_product_category = get_post_meta( get_the_ID(), 'page_product_category', true );
//do_action( 'qm/debug', $page_product_category );

if ( !empty( $page_product_category ) ) {
	$product_category = get_term( $page_product_category );
//	do_action( 'qm/debug', $product_category );

	if ( isset( $product_category->term_id ) && !is_wp_error( $product_category ) ) {
		global $wp_query;

		$wp_query->is_page           = false;
		$wp_query->is_singular       = false;
		$wp_query->is_tax            = true;
		$wp_query->is_archive        = true;
		$wp_query->queried_object    = $product_category;
		$wp_query->queried_object_id = (int) $product_category->term_id;

		set_query_var( 'taxonomy', 'product-category' );
		set_query_var( 'term', $product_category->slug );
		set_query_var( 'product-category', $product_category->slug );

		require get_theme_file_path( 'taxonomy-product-category.php' );
		exit;
	}

	wp_redirect( home_url() );
	exit;
}

if ( is_page( 'showroom' ) ) {
	require get_theme_file_path( 'template-showroom.php' );
	exit;
}

get_header();
get_template_part( 'templates/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--page">

		<?php
		get_template_part( 'modules/pages/templates/section', 'subpages' );
		get_template_part( 'templates/content', '', array(
			'container' => true,
		) );
		get_template_part( 'modules/pages/templates/section', 'subpages', array(
			'related' => true,
		) );
		?>

	</main>

<?php
if ( !is_page( 'showroom' ) ) {
	get_template_part( 'templates/section/showroom' );
}
get_footer();
