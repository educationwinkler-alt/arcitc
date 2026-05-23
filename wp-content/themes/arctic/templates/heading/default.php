<?php

/**
 * Default Heading
 */

$description = get_post_meta( get_the_ID(), 'page_description_text', true );

$badge_text = get_post_meta( get_the_ID(), 'page_badge_text', true );

$heading_class   = array( 'f-heading' );
$heading_class[] = has_post_thumbnail() || has_header_image() ? 'f-heading--background' : '';
$heading_class[] = get_post_meta( get_the_ID(), 'page_title', true ) == 0 ? 'f-heading--empty' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--m">
			<?php
			if ( !empty( $badge_text ) ) { ?>
				<div class="a-stack a-stack--row a-stack--align-center a-gap--s">
					<?php
					get_template_part( 'templates/heading/page/title' );
					get_template_part( 'templates/heading/page/badge' );
					?>
				</div>
			<?php } else {
				get_template_part( 'templates/heading/page/title' );
			}

			if ( !empty( $description ) ) { ?>
				<div class="f-heading__description">
					<?php echo wp_kses_post( strip_tags( $description, "<strong><em><br>" ) ); ?>
				</div>
			<?php }

			get_template_part( 'templates/heading/page/button' );

			if ( is_page( 'showroom' ) ) {
				get_template_part( 'templates/about/address' );
			} ?>
		</div>

	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
