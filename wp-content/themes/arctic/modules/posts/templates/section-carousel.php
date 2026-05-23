<?php

/**
 * Section - Carousel
 */

// Query Arguments
$posts_query_args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	),
	'no_found_rows'  => true,
	'posts_per_page' => 7,
);

// Query
$posts_query = new WP_Query( $posts_query_args );

if ( $posts_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'posts-recent', 'anchor', 'baspa' ) ); ?>" class="f-section f-section--posts f-section--posts-recent">

		<div class="f-section__container a-container">

			<header class="f-section__header f-section__header--center">
				<h2><?php echo esc_html__( 'Recent Posts', 'baspa' ); ?></h2>
			</header>

			<div class="f-carousel f-carousel--posts swiper js-carousel">

				<div class="f-carousel__wrapper swiper-wrapper">

					<?php while ( $posts_query->have_posts() ) {
						$posts_query->the_post(); ?>

						<div class="f-carousel__item swiper-slide">
							<?php
							get_template_part( 'modules/posts/templates/post/listing' );
							?>
						</div>

					<?php } ?>

				</div>

				<button type="button" class="f-carousel__control f-carousel__control--prev a-button js-carousel__prev">
					<?php forqy_get_icon( 'caret-left--small' ); ?></button>
				<button type="button" class="f-carousel__control f-carousel__control--next a-button js-carousel__next">
					<?php forqy_get_icon( 'caret-right--small' ); ?></button>

			</div>

		</div>

	</section>

<?php }
