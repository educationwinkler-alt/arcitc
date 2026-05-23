<?php

/**
 * Single
 */

// Post
$post_class = array( 'f-post', 'f-post--single' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<?php get_template_part( 'modules/posts/templates/post/single/heading' ); ?>

	<div class="f-single__container a-container--75">

		<div class="a-stack a-gap--l:m">
			<div class="a-stack a-gap--s">
				<?php
				if ( has_post_thumbnail() ) {
					get_template_part( 'templates/image/single' );
				}
				get_template_part( 'modules/posts/templates/post/single/excerpt' );
				?>
			</div>
			<?php
			get_template_part( 'templates/content' );
			get_template_part( 'modules/posts/templates/post/single/footer' );
			?>
		</div>


	</div>

</article>
