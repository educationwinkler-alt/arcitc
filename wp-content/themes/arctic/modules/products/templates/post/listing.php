<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-listing--product', 'f-listing', 'f-listing--cover' );
?>

<article id="product-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<?php
	get_template_part( 'modules/products/templates/post/listing/image' );
	?>

	<div class="f-listing__container a-stack a-gap--xs">
		<div class="a-flex a-flex--align-end a-gap--s">
			<div class="a-flex__item--auto">

				<div class="a-stack a-stack--align-start a-gap--xxs">
					<?php
					get_template_part( 'modules/products/templates/post/common/price' );
					get_template_part( 'modules/products/templates/post/listing/header' );
					get_template_part( 'modules/products/templates/post/listing/excerpt' );
					?>
				</div>

			</div>
			<div class="a-flex__item">

				<?php
				get_template_part( 'modules/products/templates/post/listing/button' );
				?>

			</div>
		</div>
	</div>

</article>
