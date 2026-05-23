<?php

/**
 * Hero Heading
 */

$title    = get_post_meta( get_the_ID(), 'page_title_text', true );
$button_text = get_post_meta( get_the_ID(), 'page_button_text', true );
$button_url  = get_post_meta( get_the_ID(), 'page_button_url', true );

$heading_class   = array( 'f-heading', 'f-heading--hero' );
$heading_class[] = has_post_thumbnail() || has_header_image() ? 'f-heading--background' : '';
$heading_class[] = ( get_post_meta( get_the_ID(), 'page_title', true ) == 0 ) ? 'f-heading--empty' : '';
$title_class[]   = ( get_post_meta( get_the_ID(), 'page_title', true ) == 0 ) ? 'screen-reader-text' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--s">
			<h1 <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $title_class ); ?>>
				<?php if ( !empty( $title ) ) {
					echo wp_kses_post( strip_tags( $title, "<strong><em><br>" ) );
				} else {
					the_title();
				} ?>
			</h1>

			<?php if ( !empty( $button_text ) && !empty( $button_url ) ) { ?>
				<a href="<?php echo esc_url( $button_url ); ?>" class="f-button a-button a-button--accent">
					<?php echo wp_kses_post( $button_text ); ?>
				</a>
			<?php } ?>

			<?php
			get_template_part( 'templates/heading/page/button' );
			?>
		</div>

	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
