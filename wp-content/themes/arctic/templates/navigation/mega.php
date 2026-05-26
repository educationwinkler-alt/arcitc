<?php

/**
 * Figma desktop mega navigation panels.
 */

$query_products = static function ( string $category_slug, ?string $series_slug = null, int $limit = 8 ): array {
	$tax_query = array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'product-category',
			'field'    => 'slug',
			'terms'    => $category_slug,
		),
	);

	if ( $series_slug !== null && $series_slug !== '' ) {
		$tax_query[] = array(
			'taxonomy' => 'product-series',
			'field'    => 'slug',
			'terms'    => $series_slug,
		);
	}

	return get_posts( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'tax_query'      => $tax_query,
	) );
};

$format_title = static function ( string $title ): string {
	return preg_replace( '/^(Vířivka|Celoroční bazén)\s+/u', '', $title );
};

$hot_tub_columns = array(
	array(
		'label'    => __( 'Série core', 'baspa' ),
		'products' => $query_products( 'virivky', 'core', 6 ),
	),
	array(
		'label'    => __( 'Série classic', 'baspa' ),
		'products' => $query_products( 'virivky', 'classic', 6 ),
	),
	array(
		'label'    => __( 'Série custom', 'baspa' ),
		'products' => $query_products( 'virivky', 'custom', 6 ),
	),
);

$swimspa_products = $query_products( 'swimspa', 'swimspa', 18 );

if ( empty( $swimspa_products ) ) {
	$swimspa_products = $query_products( 'swimspa', null, 18 );
}

$swimspa_labels  = array(
	__( 'Celoroční bazény', 'baspa' ),
	__( 'Plavání a fitness', 'baspa' ),
	__( 'Relaxace', 'baspa' ),
);
$swimspa_columns = array();
$swimspa_chunks  = array_chunk( $swimspa_products, max( 1, (int) ceil( count( $swimspa_products ) / 3 ) ) );

for ( $index = 0; $index < 3; $index++ ) {
	$swimspa_columns[] = array(
		'label'    => $swimspa_labels[ $index ],
		'products' => $swimspa_chunks[ $index ] ?? array(),
	);
}

$menus = array(
	array(
		'key'     => 'hot-tubs',
		'label'   => __( 'Vířivky', 'baspa' ),
		'url'     => home_url( '/virivky/' ),
		'columns' => $hot_tub_columns,
	),
	array(
		'key'     => 'swimspa',
		'label'   => __( 'Celoroční bazény', 'baspa' ),
		'url'     => home_url( '/swimspa/' ),
		'columns' => $swimspa_columns,
	),
);
?>

<div class="f-mega-nav">
	<?php foreach ( $menus as $menu ) { ?>
		<div class="f-mega-menu f-mega-menu--<?php echo esc_attr( $menu['key'] ); ?>">
			<div class="f-mega-menu__grid">
				<?php foreach ( $menu['columns'] as $column ) { ?>
					<section class="f-mega-menu__column" aria-label="<?php echo esc_attr( $column['label'] ); ?>">
						<h2><?php echo esc_html( $column['label'] ); ?></h2>

						<div class="f-mega-menu__products">
							<?php foreach ( $column['products'] as $product ) { ?>
								<a class="f-mega-menu__product" href="<?php echo esc_url( get_permalink( $product ) ); ?>">
									<span class="f-mega-menu__thumb">
										<?php
										echo get_the_post_thumbnail( $product->ID, 'thumbnail', array(
											'loading'  => 'lazy',
											'decoding' => 'async',
										) );
										?>
									</span>
									<span><?php echo esc_html( $format_title( get_the_title( $product ) ) ); ?></span>
								</a>
							<?php } ?>
						</div>
					</section>
				<?php } ?>

				<a class="f-mega-menu__promo" href="<?php echo esc_url( $menu['url'] ); ?>">
					<img src="<?php echo esc_url( content_url( 'uploads/import/figma/hp-fixed-banner-product.png' ) ); ?>" alt="" loading="lazy" decoding="async">
					<strong><?php echo esc_html__( 'Akční nabídka skladových vířivek', 'baspa' ); ?></strong>
					<span><?php echo esc_html__( 'Zobrazit nabídku', 'baspa' ); ?></span>
				</a>
			</div>
		</div>
	<?php } ?>
</div>
