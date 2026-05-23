<?php

/**
 * Accessory Listing
 */

// Class
$post_class = array( 'f-listing--product', 'f-listing', 'f-listing--accessory' );
?>

<article id="product-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<?php
	get_template_part( 'modules/products/templates/post/listing/accessory/image' );
	?>

	<div class="f-listing__container a-stack a-gap--xs">
		<?php
		get_template_part( 'modules/products/templates/post/listing/accessory/header' );
		get_template_part( 'modules/products/templates/post/listing/excerpt' );
		get_template_part( 'modules/products/templates/post/listing/accessory/link' );
		?>
	</div>

</article>
