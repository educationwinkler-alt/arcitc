<?php

/**
 * Section Template
 */

// Query Arguments
$references_query_args = array(
	'post_type'      => 'reference',
//	'meta_query'     => array(
//		array(
//			'key'   => 'reference_featured',
//			'value' => 1,
//		),
//	),
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
	'posts_per_page' => 7,
);

// Query
$references_query = new WP_Query( $references_query_args );

if ( $references_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'references', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--references f-section--references-recent js-images js-links__section">

		<div class="f-section__container a-container">

			<div class="a-flex a-flex--align-center a-gap--m">
				<div class="a-flex__item--100 a-flex__item--33:m">

					<div class="f-section__heading a-stack a-gap--s">
						<header class="f-section__header">
							<h2>
								<?php if ( !empty( get_option( 'baspa_references_title' ) ) ) {
									echo wp_kses_post( get_option( 'baspa_references_title' ) );
								} else {
									echo wp_kses_post( __( 'References', 'baspa' ) );
								} ?>
							</h2>
						</header>

						<?php if ( !empty( get_option( 'baspa_references_subtitle' ) ) ) { ?>
							<div class="f-section__subtitle">
								<?php echo wp_kses_post( get_option( 'baspa_references_subtitle' ) ); ?>
							</div>
						<?php } ?>

						<?php if ( is_tax( 'product-category', 'virivky' ) ) { ?>

							<div class="f-section__actions a-buttons">
								<a class="f-button a-button a-button--outline" href="<?php echo esc_url( home_url( '/reference/' ) ); ?>">
									<?php echo esc_html__( 'Zobrazit další reference', 'baspa' ); ?>
								</a>
							</div>

						<?php } else if ( function_exists( 'forqy_get_page_by_template' ) && !empty( forqy_get_page_by_template( 'template-references.php' ) ) ) { ?>

							<div class="f-section__actions a-buttons">
								<?php echo function_exists( 'baspa_button_next' ) ? baspa_button_next( __( 'View Next References', 'baspa' ), forqy_get_page_by_template( 'template-references.php' )[ 'permalink' ] ) : ''; ?>
							</div>

						<?php } ?>
					</div>

				</div>
				<div class="a-flex__item--100 a-flex__item--66:m">

					<div class="f-carousel f-carousel--cols-2 f-carousel--references swiper js-carousel js-carousel--cols-2 js-carousel--gap-40">

						<div class="f-carousel__wrapper swiper-wrapper">

							<?php while ( $references_query->have_posts() ) {
								$references_query->the_post(); ?>

								<div class="f-carousel__item swiper-slide">
									<?php
									get_template_part( 'modules/references/templates/post/listing' );
									?>
								</div>

							<?php } ?>

						</div>

						<button type="button"
						        class="f-carousel__control f-carousel__control--prev f-button f-button--accent a-button a-button--accent js-carousel__prev">
							<?php get_template_part( 'images/icon/arrow-left' ); ?></button>
						<button type="button"
						        class="f-carousel__control f-carousel__control--next f-button f-button--accent a-button a-button--accent js-carousel__next">
							<?php get_template_part( 'images/icon/arrow-right' ); ?></button>

					</div>

				</div>
			</div>

		</div>

	</section>

	<?php
	$references_query->reset_postdata();
	wp_reset_query();
}
