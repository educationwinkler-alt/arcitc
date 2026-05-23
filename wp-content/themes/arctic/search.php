<?php

/**
 * Search
 */

get_header();
get_template_part( 'templates/heading/search' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--listings f-main--search">

		<div class="a-container">
			<?php get_template_part( 'modules/posts/templates/loop/search' ); ?>
		</div>

	</main>

<?php
get_footer();
