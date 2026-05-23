<?php

/**
 * Section
 */

// Count
$slide_count = 1;

// Query
$slides_query = new WP_Query( array(
	'post_type'      => 'slide',
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
	'posts_per_page' => -1,
) );

// Number
$number = $slides_query->post_count;
set_query_var( 'baspa_slides_number', $number );

if ( $slides_query->have_posts() ) {

	// Class
	$slides_class = array( 'f-slides', 'swiper' );

	if ( $number >= 2 ) {
		// Activate JS Slideshow
		$slides_class[] = 'js-slides';
	} else if ( $number == 1 ) {
		$slides_class[] = 'f-slides--single-slide';
	} ?>

	<div class="f-section f-section--slides" aria-label="<?php echo esc_attr_x( 'Slideshow', 'slideshow accessibility', 'baspa' ); ?>">

		<div <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $slides_class ); ?>>

			<div id="slides" class="f-slides__wrapper swiper-wrapper">

				<?php while ( $slides_query->have_posts() ) {
					$slides_query->the_post();

					$slide_class   = array( 'f-slide', 'swiper-slide' );
					$slide_class[] = 'f-slide--' . esc_attr( (string)$slide_count );
					?>

					<div <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $slide_class ); ?>>
						<?php
						get_template_part( 'modules/slides/templates/slide/caption' );
						get_template_part( 'modules/slides/templates/slide/background', '', array(
							'slide_count' => $slide_count,
						) );
						?>
					</div>

					<?php
					$slide_count++;
				} ?>

			</div>

			<?php if ( $number >= 2 ) { ?>
				<div class="f-slides__controls f-slides__controls--navigation">
					<button type="button" class="f-slides__control f-slides__control--prev a-button a-button--icon a-button--accent js-slides__prev">
						<?php get_template_part( 'images/icon/arrow-left' ); ?></button>
					<button type="button" class="f-slides__control f-slides__control--next a-button a-button--icon a-button--accent js-slides__next">
						<?php get_template_part( 'images/icon/arrow-right' ); ?></button>
				</div>

				<div class="f-slides__controls f-slides__controls--pagination a-container">
					<div class="f-slides__pagination js-slides__pagination"></div>
				</div>
			<?php } ?>

			<?php if ( is_front_page() ) { ?>
				<aside class="f-hero-promo" aria-label="<?php echo esc_attr__( 'Výprodej skladových vířivek', 'baspa' ); ?>">
					<img class="f-hero-promo__image"
					     src="<?php echo esc_url( content_url( 'uploads/import/figma/hp-fixed-banner-product.png' ) ); ?>"
					     alt="" loading="eager" decoding="async">
					<span class="f-hero-promo__bubble" aria-hidden="true"></span>
					<strong><?php echo esc_html__( 'Výprodej skladových vířivek', 'baspa' ); ?></strong>
					<a class="f-hero-promo__button" href="<?php echo esc_url( home_url( '/catalog/virivky/' ) ); ?>">
						<?php echo esc_html__( 'Zobrazit nabídku', 'baspa' ); ?>
					</a>
				</aside>
			<?php } ?>

		</div>

	</div>

	<?php
	$slides_query->reset_postdata();
	wp_reset_postdata();
} else {
	get_template_part( 'templates/heading/hero' );
}
