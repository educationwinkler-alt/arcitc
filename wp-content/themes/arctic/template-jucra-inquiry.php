<?php
/**
 * Template Name: Arctic 3D poptávka
 */

get_header();
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--jucra-inquiry f-main--top-0">
	<?php get_template_part( 'templates/section/jucra-inquiry' ); ?>
</main>

<?php
get_footer();
