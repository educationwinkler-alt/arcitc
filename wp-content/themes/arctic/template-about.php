<?php

/**
 * Template Name: About
 */

get_header();
get_template_part( 'templates/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--about f-main--top-0">

		<?php
		get_template_part( 'templates/navigation/about' );
		if ( get_the_content() ) { ?>

			<section id="<?php echo sanitize_title( esc_attr_x( 'our-company', 'anchor', 'baspa' ) ); ?>"
			         class="f-section f-section--content js-links__section">
				<div class="f-section__container a-container">
					<?php
					get_template_part( 'templates/content' );
					?>
				</div>
				<?php
				get_template_part( 'modules/members/templates/section' );
				?>
			</section>

		<?php }
		get_template_part( 'modules/partners/templates/section' );
		get_template_part( 'modules/jobs/templates/section' );
		?>

	</main>

<?php
get_footer();
