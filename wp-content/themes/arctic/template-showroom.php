<?php

/**
 * Template Name: Showroom
 */

get_header();
get_template_part( 'templates/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--showroom">

		<div class="a-container">
			<?php
			get_template_part( 'templates/content' );
			?>
		</div>

	</main>

<?php
get_template_part( 'modules/references/templates/section', 'recent' );
get_footer();
