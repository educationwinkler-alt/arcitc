<?php

/**
 * Section Template
 */

// Section
$section_class        = array( 'f-section', 'f-section--accessories', 'js-links__section' );
$section_header_class = array( 'f-section__header' );

if ( !baspa_accessories_product_has_accessories( get_the_ID() ) ) {
	return;
}

// Get product accessory categories
$product_accessory_categories = baspa_accessories_get_product_accessory_categories( get_the_ID() );
//do_action( 'qm/debug', $product_accessory_categories );
if ( empty( $product_accessory_categories ) ) {
	return;
}

// Convert product accessory categories to ids
$product_accessory_categories_ids = array();
foreach ( $product_accessory_categories as $product_accessory_category ) {
	if ( $product_accessory_category->parent == 0 ) {
		$product_accessory_categories_ids[] = $product_accessory_category->term_id;
	}
}

// Get accessory sub-categories by parent
$accessory_subcategories = get_terms( array(
	'taxonomy'   => 'accessory-category',
	'parent'     => $product_accessory_categories_ids[ 0 ],
	'hide_empty' => true,
) );
do_action( 'qm/debug', $accessory_subcategories );

if ( !empty( $accessory_subcategories ) && !is_wp_error( $accessory_subcategories ) ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'accessories', 'anchor', 'baspa' ) ); ?>" <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_class ); ?>>

		<div class="f-section__container a-container">

			<style>
				<?php foreach ( $accessory_subcategories as $accessory_subcategory ) {
					if ( baspa_accessories_has_product_in_accessory_category(get_the_ID(), $accessory_subcategory->term_id) ) {
						/** Filter Categories by CSS */
						echo '.f-carousel.accessories-category-all { display: none; }';
						echo '#accessories-category-all:checked ~ .f-carousel.accessories-category-all {';
							echo 'display: block;';
						echo '}';

						echo '.f-carousel.accessories-category-' . esc_attr( $accessory_subcategory->slug ) . ' { display: none; }';
						echo '#accessories-category-'. esc_attr( $accessory_subcategory->slug ) . ':checked ~ .f-carousel.accessories-category-'. esc_attr( $accessory_subcategory->slug ) . ' {';
							echo 'display: block;';
						echo '}';

						echo '#accessories-category-all:checked ~ .f-section__heading .f-terms .f-term[for="accessories-category-all"] {';
							echo 'color: var(--a--color--contrast);';
							echo 'background-color: var(--a--color);';
							echo 'border-color: transparent;';
						echo '}';
						echo '#accessories-category-'. esc_attr( $accessory_subcategory->slug ) .':checked ~ .f-section__heading .f-terms .f-term[for="accessories-category-'. esc_attr( $accessory_subcategory->slug ) .'"] {';
							echo 'color: var(--a--color--contrast);';
							echo 'background-color: var(--a--color);';
							echo 'border-color: transparent;';
						echo '}';
					}
				} ?>
			</style>
			<input type="radio" id="accessories-category-all" class="f-term--radio" name="f-term" checked>
			<?php foreach ( $accessory_subcategories as $accessory_subcategory ) {
				if ( baspa_accessories_has_product_in_accessory_category( get_the_ID(), $accessory_subcategory->term_id ) ) { ?>
					<input type="radio" id="accessories-category-<?php echo esc_attr( $accessory_subcategory->slug ); ?>" name="f-term" class="f-term--radio">
				<?php }
			} ?>

			<div class="f-section__heading">

				<div class="a-flex a-flex--align-center a-gap--m">
					<div class="a-flex__item--100 a-flex__item--auto:s">

						<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_header_class ); ?>>
							<h2><?php echo esc_html__( 'Accessories', 'baspa' ); ?></h2>
						</header>

					</div>
					<div class="a-flex__item--100 a-flex__item:s">

						<ul class="f-terms">
							<li><label class="f-term"
							           for="accessories-category-all">
									<?php echo esc_html__( 'All', 'baspa' ); ?>
								</label></li>
							<?php foreach ( $accessory_subcategories as $accessory_subcategory ) {
								if ( baspa_accessories_has_product_in_accessory_category( get_the_ID(), $accessory_subcategory->term_id ) ) { ?>
									<li><label class="f-term" for="accessories-category-<?php echo esc_attr( $accessory_subcategory->slug ); ?>">
											<?php echo esc_html( $accessory_subcategory->name ); ?>
											<span class="f-count">(<?php echo baspa_accessories_count_product_in_accessory_category( get_the_ID(), $accessory_subcategory->term_id ); ?>)</span>
										</label></li>
								<?php }
							} ?>
						</ul>

					</div>
				</div>

			</div>

			<?php
			// Query Arguments
			$accessories_all_query_args = array(
				'post_type'      => 'accessory',
				'post_status'    => 'publish',
				'meta_query'     => array(
					array(
						'key'   => 'accessory_products',
						'value' => get_the_ID(),
					),
				),
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
				//				'orderby'        => 'rand',
				'posts_per_page' => 12,
			);
			$accessories_all_query      = new WP_Query( $accessories_all_query_args );

			if ( $accessories_all_query->have_posts() ) { ?>
				<div class="f-carousel f-carousel--cols-6 f-carousel--accessories swiper js-carousel js-carousel--cols-6 accessories-category-all">

					<div class="f-carousel__wrapper swiper-wrapper">

						<?php while ( $accessories_all_query->have_posts() ) {
							$accessories_all_query->the_post(); ?>

							<div class="f-carousel__item swiper-slide">
								<?php
								get_template_part( 'modules/accessories/templates/post/listing' );
								?>
							</div>

						<?php } ?>

					</div>

					<button type="button" class="f-carousel__control f-carousel__control--prev f-button f-button--accent a-button a-button--accent js-carousel__prev">
						<?php get_template_part( 'images/icon/arrow-left' ); ?></button>
					<button type="button" class="f-carousel__control f-carousel__control--next f-button f-button--accent a-button a-button--accent js-carousel__next">
						<?php get_template_part( 'images/icon/arrow-right' ); ?></button>

				</div>
				<?php
				$accessories_all_query->reset_postdata();
				wp_reset_query();
			}

			foreach ( $accessory_subcategories as $accessory_subcategory ) {

				// Query Arguments
				$accessory_subcategory_query_args = array(
					'post_type'      => 'accessory',
					'post_status'    => 'publish',
					'meta_query'     => array(
						array(
							'key'   => 'accessory_products',
							'value' => get_the_ID(),
						),
					),
					'tax_query'      => array(
						array(
							'taxonomy' => 'accessory-category',
							'field'    => 'id',
							'terms'    => $accessory_subcategory->term_id,
						),
					),
					'orderby'        => array(
						'menu_order' => 'ASC',
						'date'       => 'ASC',
					),
					'posts_per_page' => 12,
				);

				// Query
				$accessory_subcategory_query = new WP_Query( $accessory_subcategory_query_args );

				if ( $accessory_subcategory_query->have_posts() ) { ?>
					<div class="f-carousel f-carousel--cols-6 f-carousel--accessories swiper js-carousel js-carousel--cols-6 accessories-category-<?php echo esc_attr( $accessory_subcategory->slug ); ?>">

						<div class="f-carousel__wrapper swiper-wrapper">

							<?php while ( $accessory_subcategory_query->have_posts() ) {
								$accessory_subcategory_query->the_post(); ?>

								<div class="f-carousel__item swiper-slide">
									<?php
									get_template_part( 'modules/accessories/templates/post/listing' );
									?>
								</div>

							<?php } ?>

						</div>

						<button type="button" class="f-carousel__control f-carousel__control--prev f-button f-button--accent a-button a-button--accent js-carousel__prev swiper-button-disabled">
							<?php get_template_part( 'images/icon/arrow-left' ); ?></button>
						<button type="button" class="f-carousel__control f-carousel__control--next f-button f-button--accent a-button a-button--accent js-carousel__next swiper-button-disabled">
							<?php get_template_part( 'images/icon/arrow-right' ); ?></button>

					</div>
					<?php
					$accessory_subcategory_query->reset_postdata();
					wp_reset_query();
				}
			} ?>

		</div>

	</section>

	<?php
}
