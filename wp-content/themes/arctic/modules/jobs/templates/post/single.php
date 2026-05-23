<?php

/**
 * Single
 */

// Post
$post_class = array( 'f-job--single' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<?php get_template_part( 'templates/content' ); ?>
</article>
