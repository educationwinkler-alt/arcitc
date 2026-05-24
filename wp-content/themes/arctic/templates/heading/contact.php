<?php

/**
 * Default Heading
 */

$title       = get_post_meta( get_the_ID(), 'page_title_text', true );
$description = get_post_meta( get_the_ID(), 'page_description_text', true );

$heading_class   = array( 'f-heading' );
$heading_class[] = has_post_thumbnail() || has_header_image() ? 'f-heading--background' : '';
$heading_class[] = get_post_meta( get_the_ID(), 'page_title', true ) == 0 ? 'f-heading--empty' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="a-flex a-flex--align-center a-gap--m a-gap--xl:m">
			<div class="a-flex__item--100 a-flex__item--auto:m">

				<div class="f-heading__headline f-heading__headline--full a-stack a-stack--align-start a-gap--m">
					<?php if ( get_post_meta( get_the_ID(), 'page_title', true ) != 0 ) { ?>
						<h1><?php if ( !empty( $title ) ) {
								echo wp_kses_post( strip_tags( $title, "<strong><em><br>" ) );
							} else {
								the_title();
							} ?></h1>
					<?php } ?>

					<?php if ( !empty( $description ) ) { ?>
						<div class="f-heading__description">
							<?php echo wp_kses_post( strip_tags( $description, "<strong><em><br>" ) ); ?>
						</div>
					<?php } ?>

					<?php
					get_template_part( 'templates/heading/page/button' );
					?>
				</div>

			</div>
			<div class="a-flex__item--100 a-flex__item:m">
				<div class="f-heading__contacts f-contacts a-stack a-gap--xs">
					<div class="a-stack a-stack--align-start a-gap--0">
						<?php
						get_template_part( 'templates/about/phone' );
						get_template_part( 'templates/about/hours' );
						?>
						<small class="f-contact__note f-contact__note--hours"><?php echo esc_html__( 'Po - Pá 8:00-17:00 h', 'baspa' ); ?></small>
					</div>
					<div class="a-stack a-stack--align-start a-gap--0">
						<?php
						get_template_part( 'templates/about/email' );
						?>
						<small class="f-contact__note"><?php echo esc_html__( 'Odpovíme do dvou pracovních dní', 'baspa' ); ?></small>
					</div>
				</div>
			</div>
			<div class="a-flex__item--100 a-flex__item:m">
				<div class="f-heading__buttons a-stack a-stack--justify-between a-gap--xs">
					<?php
					get_template_part( 'templates/button/contact', '', array(
						'text'          => __( 'Poptávkový formulář', 'baspa' ),
						'class_replace' => array(
							'f-button',
							'f-button--reversed',
							'f-button--outline',
							'a-button',
							'a-button--outline',
							'f-off__trigger',
							'js-off__trigger',
						),
					) );
					get_template_part( 'templates/button/service', '', array(
						'class_replace' => array(
							'f-button',
							'f-button--reversed',
							'f-button--outline',
							'a-button',
							'a-button--outline',
							'f-off__trigger',
							'js-off__trigger',
						),
					) );
					?>
				</div>
			</div>
		</div>

	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
