<?php

/**
 * Term Heading
 */

$term_id       = get_queried_object_id();
$term_image_id = get_term_meta( $term_id, 'category_heading_image', true );
$term_image_id = !empty( $term_image_id ) ? $term_image_id : get_term_meta( $term_id, 'category_image', true );
$heading_title = get_term_meta( $term_id, 'category_heading_title', true );
$heading_title = !empty( $heading_title ) ? $heading_title : single_term_title( '', false );
$heading_text  = get_term_meta( $term_id, 'category_heading_text', true );

$heading_class   = array( 'f-heading', 'f-heading--term' );
$heading_class[] = !empty( $term_image_id ) ? 'f-heading--background' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--s">
			<h1><?php echo esc_html( $heading_title ); ?></h1>

			<?php if ( !empty( $heading_text ) || term_description() ) { ?>
				<div class="f-heading__description">
					<?php echo !empty( $heading_text ) ? wp_kses_post( wpautop( $heading_text ) ) : term_description(); ?>
				</div>
			<?php } ?>

			<?php get_template_part( 'templates/button/contact', '', array(
				'text'          => __( 'Vybrat vířivku', 'baspa' ),
				'class_replace' => array(
					'f-button',
					'a-button',
					'a-button--accent',
					'f-off__trigger',
					'js-off__trigger',
				),
			) ); ?>
		</div>
	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
