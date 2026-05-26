<?php

/**
 * 404 Heading
 */

$heading_class   = array( 'f-heading', 'f-heading--404' );
$heading_class[] = has_post_thumbnail() || has_header_image() ? 'f-heading--background' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--s">

			<h1><?php echo esc_html__( 'Stránka nenalezena', 'baspa' ); ?></h1>

		</div>

	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
