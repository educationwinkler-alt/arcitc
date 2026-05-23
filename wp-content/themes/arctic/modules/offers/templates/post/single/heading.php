<?php

/**
 * Default Heading
 */

$title       = get_post_meta( get_the_ID(), 'offer_title_short', true );
$description = get_post_meta( get_the_ID(), 'offer_description', true );

$heading_class   = array( 'f-heading' );
$heading_class[] = has_post_thumbnail() || has_header_image() ? 'f-heading--background' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--m">
			<div class="a-stack a-stack--align-start a-gap--xs">
				<?php
				get_template_part( 'modules/offers/templates/post/uni/type' );

				if ( get_post_meta( get_the_ID(), 'page_title', true ) != 0 ) { ?>
					<h1><?php if ( !empty( $title ) ) {
							echo wp_kses_post( strip_tags( $title, "<strong><em><br>" ) );
						} else {
							the_title();
						} ?></h1>
				<?php } ?>
			</div>

			<?php if ( !empty( $description ) ) { ?>
				<div class="f-heading__description">
					<?php echo wp_kses_post( strip_tags( $description, "<strong><em><br>" ) ); ?>
				</div>
			<?php } ?>

			<?php
			get_template_part( 'templates/button/contact', '', array(
				'text'          => esc_html__( 'I am interested', 'baspa' ),
				'class_replace' => array(
					'f-button',
					'f-button--outline',
					'f-button--reversed',
					'a-button',
					'a-button--outline',
					'f-off__trigger',
					'js-off__trigger',
				),
			) );
			?>
		</div>

	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
