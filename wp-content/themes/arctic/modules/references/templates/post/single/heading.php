<?php

/**
 * Default Heading
 */

$description = get_post_meta( get_the_ID(), 'reference_description', true );
$categories  = wp_get_post_terms( get_the_ID(), 'reference-category' );

$heading_class   = array( 'f-heading', 'f-heading--single', 'f-heading--reference' );
$heading_class[] = has_post_thumbnail() || has_header_image() ? 'f-heading--background' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--justify-start a-gap--xs">
			<?php if ( !empty( $categories ) && !is_wp_error( $categories ) ) { ?>
				<ul class="f-heading__terms f-terms">
					<?php foreach ( $categories as $category ) { ?>
						<li class="f-term">
							<?php echo esc_html( $category->name ); ?>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>

			<?php if ( get_post_meta( get_the_ID(), 'page_title', true ) != 0 ) { ?>
				<h1><?php if ( !empty( $title ) ) {
						echo wp_kses_post( $title );
					} else {
						the_title();
					} ?></h1>
			<?php } ?>
		</div>

	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
