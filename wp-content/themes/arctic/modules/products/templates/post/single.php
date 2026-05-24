<?php

/**
 * Single
 */

// Post
$post_class = array( 'f-product--single', 'f-post', 'f-post--single' );
?>

<article id="product-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>
	<?php if ( get_post_field( 'post_name', get_the_ID() ) === 'timberwolf' ) {
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
