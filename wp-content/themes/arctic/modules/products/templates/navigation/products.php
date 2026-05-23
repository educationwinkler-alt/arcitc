<?php

/**
 * Navigation Products
 */

$parent_id = $args[ 'term_parent_id' ] ?? null;

$products_query_args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'post_parent'    => 0,
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	),
	'posts_per_page' => -1,
);

if ( !empty( $parent_id ) ) {
	$products_query_args[ 'tax_query' ] = array(
		array(
			'taxonomy' => 'product-category',
			'field'    => 'id',
			'terms'    => $parent_id,
		),
	);
}

$products_query = new WP_Query( $products_query_args );

$products_class = array( 'f-categories--sub', 'f-categories--products', 'f-pages', 'a-grid', 'a-grid--cols-3', 'a-gap--xs' );

if ( $products_query->have_posts() ) { ?>

	<ul <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $products_class ); ?>>
		<?php while ( $products_query->have_posts() ) {
			$products_query->the_post();
			$product_class = array( 'f-category', 'f-category--product', 'f-category--sub' );
			?>
			<li <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $product_class ); ?>>
				<?php if ( has_post_thumbnail() ) { ?>
					<figure class="f-category__image">
						<?php the_post_thumbnail( get_template() . '-category' ); ?>
					</figure>
				<?php } ?>

				<a href="<?php the_permalink(); ?>" class="f-category__container">
					<div class="a-flex a-flex--align-end a-flex--nowrap a-gap--m">
						<div class="a-flex__item--auto">
							<div class="f-category__title"><?php the_title(); ?></div>
						</div>
						<div class="a-flex__item">
							<div class="f-icon"><?php get_template_part( 'images/icon/arrow-right', 'xs' ); ?></div>
						</div>
					</div>
				</a>
			</li>
		<?php } ?>
	</ul>

	<?php
	$products_query->reset_postdata();
	wp_reset_query();
}
