<?php

/**
 * Single
 */

// Post
$post_class = array( 'f-reference', 'f-single' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<?php
	get_template_part( 'modules/references/templates/post/single/heading' );
	get_template_part( 'modules/references/templates/post/single/description' );
	?>

	<div class="a-container">
		<div class="a-stack">
			<?php
			get_template_part( 'templates/content' );
			?>
		</div>
	</div>

	<?php get_template_part( 'modules/references/templates/post/single/gallery' ); ?>

</article>
