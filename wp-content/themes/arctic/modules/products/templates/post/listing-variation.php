<?php

/**
 * Variation Listing
 */

// Class
$post_class = array( 'f-listing--product', 'f-listing', 'f-listing--variation' );
?>

<article id="product-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="f-listing__container a-stack a-gap--xs">
		<div class="a-flex a-flex--align-center a-gap--s">
			<div class="a-flex__item--100 a-flex__item:s">
				<?php
				get_template_part( 'modules/products/templates/post/listing/image', '', array(
					'image_size'     => 'product',
					'image_ratio'    => 'square',
					'image_position' => 'contain',
				) );
				?>
			</div>
			<div class="a-flex__item--auto  a-flex__item--auto:s">

				<div class="a-stack a-stack--align-start a-gap--xxs">
					<?php
					get_template_part( 'modules/products/templates/post/listing/header' );
					get_template_part( 'modules/products/templates/post/common/price' );
					get_template_part( 'modules/products/templates/post/listing/excerpt' );
					?>
				</div>

			</div>
			<div class="a-flex__item a-flex__item:s">

				<?php
				get_template_part( 'modules/products/templates/post/listing/button' );
				?>

			</div>
		</div>
	</div>

</article>
