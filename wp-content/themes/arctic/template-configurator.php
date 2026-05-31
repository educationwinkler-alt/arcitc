<?php
/**
 * Template Name: Arctic 3D konfigurátor
 */

get_header();
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--jucra-builder f-main--top-0">
	<?php get_template_part( 'templates/section/jucra-builder' ); ?>
</main>

<?php
get_footer();
