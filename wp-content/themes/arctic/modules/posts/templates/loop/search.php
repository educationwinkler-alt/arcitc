<?php

/**
 * Search Loop
 */

if ( have_posts() ) { ?>

	<div class="a-container--50 a-container--align-start">
		<div class="a-stack a-gap--xs">

			<?php while ( have_posts() ) {
				the_post();

				get_template_part( 'modules/posts/templates/post/listing', 'search' );
			} ?>

		</div>
	</div>

	<?php if ( function_exists( 'baspa_pagination' ) ) {
		baspa_pagination();
	}

	wp_reset_query();
} else { ?>
	<div class="a-container--50 a-container--align-start">
		<div class="f-content f-content--empty">
			<h4><?php echo esc_html__( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'baspa' ); ?></h4>
			<?php get_search_form(); ?>
		</div>
	</div>
<?php }
