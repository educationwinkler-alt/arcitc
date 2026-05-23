<?php

/**
 * Pages Section Template
 */

$pages_query_args = array(
	'post_type'      => 'page',
	'post_parent'    => 0,
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
	'no_found_rows'  => -1,
	'posts_per_page' => -1,
);

$pages_query = new WP_Query( $pages_query_args );

if ( $pages_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'pages', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--soft f-section--pages">
		<div class="f-section__container a-container--wide">

			<header class="f-section__header f-section__header--center screen-reader-text">
				<h2><?php esc_html_e( 'Products', 'baspa' ); ?></h2>
			</header>

			<ul class="f-pages f-pages--product a-grid a-grid--cols-2 a-gap--m">
				<?php while ( $pages_query->have_posts() ) {
					$pages_query->the_post();

					$page_class = array( 'f-page', 'f-page--products', 'f-page--parent' );
					?>
					<li <?php forqy_class( $page_class ); ?>>
						<?php if ( has_post_thumbnail() ) { ?>
							<figure class="f-page__image">
								<?php the_post_thumbnail( get_template() . '-category' ); ?>
							</figure>
						<?php } ?>

						<div class="f-page__container">
							<a href="<?php the_permalink(); ?>">
								<h3><?php the_title(); ?></h3>
							</a>

							<?php
							/**
							 * Children
							 */
							$children_query_args = array(
								'post_type'      => 'page',
								'post_parent'    => get_the_ID(),
								'orderby'        => array(
									'menu_order' => 'ASC',
									'date'       => 'DESC',
								),
								'no_found_rows'  => -1,
								'posts_per_page' => -1,
							);
							$children_query      = new WP_Query( $children_query_args );

							if ( $children_query->have_posts() ) { ?>
								<ul class="f-page__children f-tags">
									<?php while ( $children_query->have_posts() ) {
										$children_query->the_post();
										?>
										<li>
											<a href="<?php the_permalink(); ?>">
												<?php
												the_title();
												get_template_part( 'images/icon/arrow-right', 'xs' );
												?>
											</a>
											<?php if ( has_post_thumbnail() ) { ?>
												<figure class="f-child__image">
													<?php the_post_thumbnail( get_template() . '-category' ); ?>
												</figure>
											<?php } ?>
										</li>
									<?php } ?>
								</ul>
								<?php
								$children_query->reset_postdata();
								wp_reset_query();
							} ?>
						</div>
					</li>
				<?php } ?>
			</ul>

		</div>
	</section>

	<?php
	$pages_query->reset_postdata();
	wp_reset_query();
}
