<?php

/**
 * Page
 */

$page_product_category = get_post_meta( get_the_ID(), 'page_product_category', true );
//do_action( 'qm/debug', $page_product_category );

if ( !empty( $page_product_category ) ) {
	$product_category = get_term( $page_product_category );
//	do_action( 'qm/debug', $product_category );

	if ( isset( $product_category->term_id ) ) {
		wp_redirect( get_term_link( $product_category->term_id ) );
	} else {
		wp_redirect(home_url());
	}

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
