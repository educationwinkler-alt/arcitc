<?php

/**
 * Single
 */

// Post
$product_id         = get_the_ID();
$has_configurations = function_exists( 'baspa_products_product_has_configurations' ) && baspa_products_product_has_configurations( $product_id );
$post_class         = array( 'f-product--single', 'f-post', 'f-post--single' );
?>

<article id="product-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>
	<h2 class="screen-reader-text"><?php echo esc_html( get_the_title( $product_id ) ); ?></h2>
	<?php if ( $has_configurations ) {
		get_template_part( 'modules/products/templates/post/single/figma-detail-body' );
	} else if ( baspa_products_query_product_has_parameters( get_the_ID() ) ) {
		/**
		 * Variation
		 */
		get_template_part( 'modules/products/templates/post/single/parameters-and-description' );
	} else {
		/**
		 * Parent
		 */
		get_template_part( 'modules/products/templates/post/single/description' );
	} ?>
</article>
