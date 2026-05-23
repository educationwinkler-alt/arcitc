<?php

/**
 * Section Template
 */

// Section
$section_class        = array( 'f-section', 'f-section--accessories', 'js-links__section' );
$section_header_class = array( 'f-section__header' );

// Accessories
$accessories_category = baspa_products_get_accessories_category( get_the_ID() );
if ( empty( $accessories_category ) ) {
	return;
}

$accessories_terms = get_terms( array(
	'taxonomy'   => 'product-category',
	'parent'     => $accessories_category->term_id,
	'hide_empty' => true,
) );

// Query Arguments
$accessories_query_args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'meta_query'     => array(
		array(
			'key'   => 'product_type',
			'value' => 'affiliate',
		),
	),
	'tax_query'      => array(
		array(
			'taxonomy' => 'product-category',
			'field'    => 'id',
			'terms'    => $accessories_category->term_id,
		),
	),
	//	'orderby'        => array(
	//		'menu_order' => 'ASC',
	//		'date'       => 'ASC',
	//	),
	'orderby'        => 'rand',
	'posts_per_page' => 12,
);

$accessories_query = new WP_Query( $accessories_query_args );

if ( !empty( $accessories_terms ) && !is_wp_error( $accessories_terms ) ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'accessories', 'anchor', 'baspa' ) ); ?>" <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_class ); ?>>

		<div class="f-section__container a-container">

			<?php if ( !empty( $accessories_terms ) && !is_wp_error( $accessories_terms ) ) { ?>
				<style>
					<?php foreach ( $accessories_terms as $accessories_term ) {
					/** Filter Categories by CSS */
					echo '.f-carousel.accessories-category-all { display: none; }';
					echo '#accessories-category-all:checked ~ .f-carousel.accessories-category-all {';
						echo 'display: block;';
					echo '}';

					echo '.f-carousel.accessories-category-' . esc_attr( $accessories_term->slug ) . ' { display: none; }';
					echo '#accessories-category-'. esc_attr( $accessories_term->slug ) . ':checked ~ .f-carousel.accessories-category-'. esc_attr( $accessories_term->slug ) . ' {';
						echo 'display: block;';
					echo '}';

					echo '#accessories-category-all:checked ~ .f-section__heading .f-terms .f-term[for="accessories-category-all"] {';
						echo 'color: var(--a--color--contrast);';
						echo 'background-color: var(--a--color);';
						echo 'border-color: transparent;';
					echo '}';
					echo '#accessories-category-'. esc_attr( $accessories_term->slug ) .':checked ~ .f-section__heading .f-terms .f-term[for="accessories-category-'. esc_attr( $accessories_term->slug ) .'"] {';
						echo 'color: var(--a--color--contrast);';
						echo 'background-color: var(--a--color);';
						echo 'border-color: transparent;';
					echo '}';
					} ?>
				</style>
				<input type="radio"
				       id="accessories-category-all"
				       class="f-term--radio"
				       name="f-term" checked>
				<?php foreach ( $accessories_terms as $accessories_term ) { ?>
					<input type="radio"
					       id="accessories-category-<?php echo esc_attr( $accessories_term->slug ); ?>"
					       name="f-term"
					       class="f-term--radio">
				<?php } ?>
			<?php } ?>

			<div class="f-section__heading">

				<div class="a-flex a-flex--align-center a-gap--m">
					<div class="a-flex__item--100 a-flex__item--auto:s">

						<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_header_class ); ?>>
							<h2><?php echo esc_html__( 'Accessories', 'baspa' ); ?></h2>
						</header>

					</div>
					<div class="a-flex__item--100 a-flex__item:s">

						<?php if ( !empty( $accessories_terms ) && !is_wp_error( $accessories_terms ) ) { ?>
							<ul class="f-terms">
								<li><label class="f-term"
								           for="accessories-category-all">
										<?php echo esc_html__( 'All', 'baspa' ); ?>
									</label></li>
								<?php foreach ( $accessories_terms as $accessories_term ) { ?>
									<li><label class="f-term"
									           for="accessories-category-<?php echo esc_attr( $accessories_term->slug ); ?>">
											<?php
											echo esc_html( $accessories_term->name );
											$posts_number = baspa_get_term_posts_number( $accessories_term->term_id, 'product', 'product-category' );
											if ( !empty( $posts_number ) ) { ?>
												<span class="f-count">(<?php echo esc_html( $posts_number ); ?>)</span>
											<?php } ?>
										</label></li>
								<?php } ?>
							</ul>
						<?php } ?>

					</div>
				</div>

			</div>

			<?php if ( $accessories_query->have_posts() ) { ?>
				<div class="f-carousel f-carousel--cols-6 f-carousel--accessories swiper js-carousel js-carousel--cols-6 accessories-category-all">

					<div class="f-carousel__wrapper swiper-wrapper">

						<?php while ( $accessories_query->have_posts() ) {
							$accessories_query->the_post(); ?>

							<div class="f-carousel__item swiper-slide">
								<?php
								get_template_part( 'modules/products/templates/post/listing', 'accessory' );
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
				<?php
				$accessories_query->reset_postdata();
				wp_reset_query();
			}

			foreach ( $accessories_terms as $accessories_term ) {

				// Query Arguments
				$accessories_term_query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'meta_query'     => array(
						array(
							'key'   => 'product_type',
							'value' => 'affiliate',
						),
					),
					'tax_query'      => array(
						array(
							'taxonomy' => 'product-category',
							'field'    => 'id',
							'terms'    => $accessories_term->term_id,
						),
					),
					'orderby'        => array(
						'menu_order' => 'ASC',
						'date'       => 'ASC',
					),
					'posts_per_page' => 12,
				);

				// Query
				$accessories_term_query = new WP_Query( $accessories_term_query_args );

//				do_action( 'qm/debug', $accessories_term_query->posts );

				if ( $accessories_term_query->have_posts() ) { ?>
					<div class="f-carousel f-carousel--cols-6 f-carousel--accessories swiper js-carousel js-carousel--cols-6 accessories-category-<?php echo esc_attr( $accessories_term->slug ); ?>">

						<div class="f-carousel__wrapper swiper-wrapper">

							<?php while ( $accessories_term_query->have_posts() ) {
								$accessories_term_query->the_post(); ?>

								<div class="f-carousel__item swiper-slide">
									<?php
									get_template_part( 'modules/products/templates/post/listing', 'accessory' );
									?>
								</div>

							<?php } ?>

						</div>

						<button type="button"
						        class="f-carousel__control f-carousel__control--prev f-button f-button--accent a-button a-button--accent js-carousel__prev swiper-button-disabled">
							<?php get_template_part( 'images/icon/arrow-left' ); ?></button>
						<button type="button"
						        class="f-carousel__control f-carousel__control--next f-button f-button--accent a-button a-button--accent js-carousel__next swiper-button-disabled">
							<?php get_template_part( 'images/icon/arrow-right' ); ?></button>

					</div>
					<?php
					$accessories_term_query->reset_postdata();
					wp_reset_query();
				}
			} ?>

		</div>

	</section>

	<?php
}
