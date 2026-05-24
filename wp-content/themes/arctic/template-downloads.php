<?php

/**
 * Template Name: Downloads
 */

get_header();
get_template_part( 'templates/heading/default' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
      class="f-main f-main--support f-main--downloads f-main--top-0 page-template-template-support">

	<section id="ke-stazeni" class="f-section f-section--support-downloads">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html__( 'Dokumenty ke stažení', 'baspa' ); ?></h2>
			<div class="f-chip-list">
				<span class="is-active"><?php echo esc_html__( 'Katalogy vířivek', 'baspa' ); ?></span>
				<span><?php echo esc_html__( 'Návody', 'baspa' ); ?></span>
				<span><?php echo esc_html__( 'Rozměry', 'baspa' ); ?></span>
				<span><?php echo esc_html__( 'Záruky', 'baspa' ); ?></span>
			</div>
			<?php echo do_shortcode( '[arctic-downloads]' ); ?>
		</div>
	</section>

	<?php get_template_part( 'templates/section/contact' ); ?>

</main>

<?php
get_footer();
