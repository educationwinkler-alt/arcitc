<?php

/**
 * Template Name: Showroom
 */

get_header();
get_template_part( 'templates/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--showroom">

		<?php
		get_template_part( 'templates/section/showroom' );
		get_template_part( 'templates/section/progress' );
		get_template_part( 'modules/references/templates/section', 'recent' );
		get_template_part( 'templates/section/contact' );
		?>

	</main>

<?php
get_footer();
