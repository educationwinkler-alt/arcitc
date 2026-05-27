<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-listing--reference', 'f-listing' );

if ( !has_post_thumbnail() ) {
	$post_class[] = 'f-listing--no-image';
}

if ( get_post_meta( get_the_ID(), 'reference_single', true ) != 0 ) {
	$post_class[] = 'f-listing--single';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="f-listing__image--container">
		<?php
		get_template_part( 'modules/references/templates/post/listing/image' );

		get_template_part( 'modules/references/templates/post/listing/meta' ); ?>
	</div>

	<div class="f-listing__container a-stack a-stack--start a-gap--xs">
		<?php
		get_template_part( 'modules/references/templates/post/listing/header' );
//		get_template_part( 'modules/references/templates/post/listing/buttons' );
		?>
	</div>

</article>
