<?php

/**
 * Section Template
 */

// Terms
$terms = get_terms( array(
	'taxonomy'   => 'product-category',
	'hide_empty' => false,
) );

if ( !empty( $terms ) && !is_wp_error( $terms ) ) { ?>
	<?php
	$homepage_slugs = array( 'virivky', 'swimspa' );

	if ( is_front_page() ) {
		$terms = array_values( array_filter( $terms, static function ( $term ) use ( $homepage_slugs ) {
			return in_array( $term->slug, $homepage_slugs, true );
		} ) );
	}

	$homepage_images = array(
		'virivky' => array(
			'desktop' => content_url( 'uploads/import/figma/hp-category-virivky.jpg' ),
			'mobile'  => content_url( 'uploads/import/figma/mobile-category-virivky.jpg' ),
		),
		'swimspa' => array(
			'desktop' => content_url( 'uploads/import/figma/hp-category-celorocni-bazeny.png' ),
			'mobile'  => content_url( 'uploads/import/figma/mobile-category-celorocni-bazeny.png' ),
		),
	);

	usort( $terms, static function ( $a, $b ) use ( $homepage_slugs ) {
		$a_position = array_search( $a->slug, $homepage_slugs, true );
		$b_position = array_search( $b->slug, $homepage_slugs, true );

		$a_position = false === $a_position ? 99 : $a_position;
		$b_position = false === $b_position ? 99 : $b_position;

		return $a_position <=> $b_position;
	} );
	?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'categories', 'anchor', 'baspa' ) ); ?>" class="f-section f-section--soft f-section--product-categories">
		<div class="f-section__container a-container--wide">

			<header class="f-section__header f-section__header--center screen-reader-text">
				<h2><?php esc_html_e( 'Product Categories', 'baspa' ); ?></h2>
			</header>

			<ul class="f-categories f-categories--product a-grid a-grid--cols-2 a-gap--m">
				<?php foreach ( $terms as $term ) {
					$category_class = array( 'f-category', 'f-category--product' );

					if ( !empty( $term->slug ) ) {
						$category_class[] = esc_attr( sanitize_title( $term->slug ) );
					}
					if ( $term->parent === 0 ) {
						$category_class[] = 'f-category--parent';
					} else {
						$category_class[] = 'f-category--sub';
					}

					$category_image_id          = get_term_meta( $term->term_id, 'category_image', true );
					$category_description_short = get_term_meta( $term->term_id, 'category_description_short', true );
					$category_link              = get_term_link( $term->term_id );
					$category_link              = is_wp_error( $category_link ) ? '#' : $category_link;
					$is_homepage_card           = is_front_page();
					$homepage_image             = $homepage_images[ $term->slug ] ?? null;

					if ( $term->parent === 0 ) { ?>
						<li <?php forqy_class( $category_class ); ?>>
							<?php if ( !empty( $category_image_id ) || ( $is_homepage_card && !empty( $homepage_image ) ) ) { ?>
								<figure class="f-category__image">
									<?php if ( $is_homepage_card && !empty( $homepage_image ) ) { ?>
										<picture>
											<source media="(max-width: 767px)" srcset="<?php echo esc_url( $homepage_image['mobile'] ); ?>">
											<img src="<?php echo esc_url( $homepage_image['desktop'] ); ?>" alt="<?php echo esc_attr( $term->name ); ?>">
										</picture>
									<?php } else { ?>
										<?php echo wp_get_attachment_image( $category_image_id, get_template() . '-category' ); ?>
									<?php } ?>
								</figure>
							<?php } ?>

							<?php if ( $is_homepage_card ) { ?>
								<a href="<?php echo esc_url( $category_link ); ?>" class="f-category__container" aria-label="<?php echo esc_attr( $term->name ); ?>">
									<div class="a-stack a-gap--xs">
										<h3><?php echo esc_html( $term->name ); ?></h3>
										<?php if ( !empty( $category_description_short ) ) { ?>
											<p><?php echo wp_kses_post( $category_description_short ); ?></p>
										<?php } ?>
									</div>
								</a>
							<?php } else { ?>
								<div class="f-category__container">
									<a href="<?php echo esc_url( $category_link ); ?>">
										<div class="a-stack a-gap--xs">
											<h3><?php echo esc_html( $term->name ); ?></h3>
											<?php if ( !empty( $category_description_short ) ) { ?>
												<p><?php echo wp_kses_post( $category_description_short ); ?></p>
											<?php } ?>
										</div>
									</a>

									<?php
									/**
									 * Children
									 */
									$children = get_terms( array(
										'taxonomy'   => 'product-category',
										'parent'     => $term->term_id,
										'hide_empty' => false,
									) );

									if ( !empty( $children ) && !is_wp_error( $children ) ) { ?>
										<ul class="f-category__children f-tags">
											<?php foreach ( $children as $child ) {

												$child_image_id = get_term_meta( $child->term_id, 'category_image', true );
												$child_link     = get_term_link( $child->term_id );
												$child_link     = is_wp_error( $child_link ) ? '#' : $child_link;
												?>
												<li>
													<a href="<?php echo esc_url( $child_link ); ?>">
														<?php
														echo esc_html( $child->name );
														get_template_part( 'images/icon/arrow-right', 'xs' );
														?>
													</a>
													<?php if ( !empty( $child_image_id ) ) { ?>
														<figure class="f-child__image">
															<?php echo wp_get_attachment_image( $child_image_id, get_template() . '-category', false, array(
																'fetchpriority' => 'low',
															) ); ?>
														</figure>
													<?php } ?>
												</li>
											<?php } ?>
										</ul>
									<?php } ?>
								</div>
							<?php } ?>
						</li>
					<?php }
				} ?>
			</ul>

		</div>
	</section>

<?php }
