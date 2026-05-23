<?php

/**
 * Template Name: Contact
 */

get_header();
get_template_part( 'templates/heading/contact' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--contact f-main--top-0">

		<?php
		get_template_part( 'templates/section/map' );
		get_template_part( 'templates/section/contact-directory' );
		?>

	</main>

<?php
get_footer();
