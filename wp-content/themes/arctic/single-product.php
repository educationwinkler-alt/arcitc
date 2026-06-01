<?php

/**
 * Single Product
 */

global $post;

$product_id     = get_the_ID();
$is_hot_tub     = has_term( 'virivky', 'product-category', $product_id );
$is_wider_range = has_term( 'dalsi-sortiment', 'product-category', $product_id );

if ( in_array( get_post_meta( get_the_ID(), 'product_type', true ), array( 'affiliate', 'external_shop', 'hidden_or_retired' ), true ) ) {
	$url = get_post_meta( get_the_ID(), 'product_url', true );
	if ( !empty( $url ) ) {
		wp_redirect( esc_url( $url ), 301 );
	} else {
		wp_redirect( home_url(), 301 );
	}
	exit;
}

get_header();
get_template_part( 'modules/products/templates/post/single/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--single f-main--single-product f-main--top-0 js-autohide--hide">

		<?php
		get_template_part( 'modules/products/templates/post/single/navigation' );
		get_template_part( 'modules/products/templates/post/single' );

		if ( !$is_wider_range ) {
			get_template_part( 'modules/products/templates/post/single/acrylic-colors' );
		}

		if ( $is_hot_tub ) {
			get_template_part( 'templates/section/product-benefits' );
			get_template_part( 'templates/section/product-options' );
		}

		if ( !$is_wider_range ) {
			get_template_part( 'modules/references/templates/section', 'recent' );
		}
		?>

	</main>

<?php
get_footer();
