<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-listing--partner', 'f-listing' );

if ( !has_post_thumbnail() ) {
	$post_class[] = 'f-listing--no-image';
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="f-listing__container a-stack a-stack--justify-center a-gap--s">
		<?php
		get_template_part( 'modules/partners/templates/post/listing/image' );
		?>
		<div class="a-stack a-stack--justify-center a-gap--xs">
			<?php
			get_template_part( 'modules/partners/templates/post/listing/header' );
			get_template_part( 'modules/partners/templates/post/listing/description' );
			?>
		</div>
	</div>

</article>
