<?php

/**
 * Index Heading
 */

$description = get_post_meta( get_option( 'page_for_posts' ), 'page_description_text', true );

$heading_class   = array( 'f-heading', 'f-heading--index' );
$heading_class[] = has_post_thumbnail( get_option( 'page_for_posts' ) ) ? 'f-heading--background' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--s">

			<h1><?php if ( !is_front_page() ) {
					echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) );
				} else {
					echo get_bloginfo( 'description' );
				} ?></h1>

			<?php if ( !empty( $description ) ) { ?>
				<div class="f-heading__description">
					<?php echo wp_kses_post( strip_tags( $description, "<strong><em><br>" ) ); ?>
				</div>
			<?php } ?>
		</div>

	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
