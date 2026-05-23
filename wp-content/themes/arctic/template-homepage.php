<?php

/**
 * Template Name: Homepage
 */

get_header();
get_template_part( 'modules/slides/templates/section' );
//get_template_part( 'templates/heading/hero' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--homepage">

		<?php
		get_template_part( 'modules/offers/templates/section', 'small' );
		get_template_part( 'modules/products/templates/section', 'categories' );

		get_template_part( 'templates/content', '', array(
			'container' => true,
		) );
		get_template_part( 'templates/section/benefits' );
		get_template_part( 'templates/section/showroom' );
		get_template_part( 'templates/section/progress' );
		get_template_part( 'modules/references/templates/section', 'recent' );
		?>

	</main>

<?php
get_footer();
