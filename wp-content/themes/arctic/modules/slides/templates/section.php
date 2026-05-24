<?php

/**
 * Section
 */

// Count
$slide_count = 1;

$is_homepage_slides = is_front_page();

// Query
$slides_args = array(
	'post_type'      => 'slide',
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
	'posts_per_page' => -1,
);

if ( $is_homepage_slides ) {
	$slides_args['meta_key']       = '_arctic_seed_key';
	$slides_args['meta_value']     = 'home-hero-arctic';
	$slides_args['posts_per_page'] = 1;
}

$slides_query = new WP_Query( $slides_args );

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
			<?php } else if ( $is_homepage_slides ) { ?>
				<div class="f-slides__controls f-slides__controls--navigation f-slides__controls--static" aria-hidden="true">
					<button type="button" class="f-slides__control f-slides__control--prev a-button a-button--icon a-button--accent" tabindex="-1" disabled>
						<?php get_template_part( 'images/icon/arrow-left' ); ?></button>
					<button type="button" class="f-slides__control f-slides__control--next a-button a-button--icon a-button--accent" tabindex="-1" disabled>
						<?php get_template_part( 'images/icon/arrow-right' ); ?></button>
				</div>

				<div class="f-slides__controls f-slides__controls--pagination f-slides__controls--static a-container" aria-hidden="true">
					<div class="f-slides__pagination">
						<span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span>
						<span class="swiper-pagination-bullet"></span>
						<span class="swiper-pagination-bullet"></span>
					</div>
				</div>
			<?php } ?>

		</div>

		<?php if ( $is_homepage_slides ) { ?>
			<?php get_template_part( 'templates/section/hero-promo' ); ?>
		<?php } ?>

	</div>

	<?php
	$slides_query->reset_postdata();
	wp_reset_postdata();
} else {
	get_template_part( 'templates/heading/hero' );
}
