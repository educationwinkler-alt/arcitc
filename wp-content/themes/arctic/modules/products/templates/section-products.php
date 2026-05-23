<?php

/**
 * Section Template
 */

// Section
$section_id    = esc_attr_x( 'products', 'anchor', 'baspa' );
$section_title = __( 'Products', 'baspa' );

$section_class        = array( 'f-section', 'f-section--products', 'js-links__section' );
$section_header_class = array( 'f-section__header' );

// Query Arguments
$products_query_args = array(
	'post_type'   => 'product',
	'post_status' => 'publish',
	'post_parent' => 0,
	'orderby'     => array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	),
);

if ( is_tax( 'product-category' ) ) {
	$products_query_args[ 'tax_query' ] = array(
		array(
			'taxonomy'         => 'product-category',
			'field'            => 'id',
			'terms'            => get_queried_object_id(),
			'include_children' => false,
		),
	);
}

if ( is_tax( 'product-category' ) && 'accessories' !== get_term_meta( get_queried_object_id(), 'category_type', true ) ) {
	$series_order = array( 'custom', 'classic', 'core', 'swimspa', 'covana' );
	$series_terms = get_terms( array(
		'taxonomy'   => 'product-series',
		'hide_empty' => false,
	) );

	if ( !empty( $series_terms ) && !is_wp_error( $series_terms ) ) {
		usort( $series_terms, static function ( $a, $b ) use ( $series_order ) {
			$a_position = array_search( $a->slug, $series_order, true );
			$b_position = array_search( $b->slug, $series_order, true );
			$a_position = false === $a_position ? 99 : $a_position;
			$b_position = false === $b_position ? 99 : $b_position;

			return $a_position <=> $b_position;
		} );
		?>

		<section id="<?php echo sanitize_title( $section_id ); ?>" class="f-section f-section--products f-section--products-grouped js-links__section">
			<div class="f-section__container a-container">
				<?php foreach ( $series_terms as $series_term ) {
					$series_query_args = $products_query_args;
					$series_query_args['tax_query'][] = array(
						'taxonomy' => 'product-series',
						'field'    => 'term_id',
						'terms'    => $series_term->term_id,
					);
					$series_query_args['tax_query']['relation'] = 'AND';

					$series_query = new WP_Query( $series_query_args );
					if ( !$series_query->have_posts() ) {
						continue;
					}

					$series_descriptions = array(
						'custom'  => __( 'Nejkompromisnější vířivky s bohatou výbavou a možností volby konfigurace.', 'baspa' ),
						'classic' => __( 'Kvalitní vířivky Arctic Spas s rozumnou výbavou a skvělým poměrem ceny a výkonu.', 'baspa' ),
						'core'    => __( 'Jednoduchá a úsporná řada pro celoroční relaxaci s nízkými provozními náklady.', 'baspa' ),
						'swimspa' => __( 'Celoroční bazény pro plavání, rehabilitaci i rodinnou relaxaci.', 'baspa' ),
					);
					$series_copy = array(
						'custom'  => array(
							'subtitle'    => __( 'Nekompromisně výjimečná', 'baspa' ),
							'description' => __( 'Dosud nevídaný komfort, konstrukce a technologie zcela podle vašich požadavků.', 'baspa' ),
						),
						'classic' => array(
							'subtitle'    => __( 'Léty prověřená klasika', 'baspa' ),
							'description' => __( 'Každá vířivka z řady Classic je postavena na základě více než 25 let zkušeností a vývoje.', 'baspa' ),
						),
						'core'    => array(
							'subtitle'    => __( 'Naše základní hodnoty', 'baspa' ),
							'description' => __( 'Cenově nejdostupnější řada vířivek, ve kterých jsou přesto zabudovány naše základní konstrukční principy.', 'baspa' ),
						),
						'swimspa' => array(
							'subtitle'    => __( 'Celoroční bazény', 'baspa' ),
							'description' => __( 'Celoroční bazény pro plavání, rehabilitaci i rodinnou relaxaci.', 'baspa' ),
						),
					);
					?>
					<section id="serie-<?php echo esc_attr( $series_term->slug ); ?>" class="f-products-series f-products-series--<?php echo esc_attr( $series_term->slug ); ?>">
						<header class="f-products-series__header">
							<h2><?php echo esc_html( 'Série ' . $series_term->name ); ?></h2>
							<?php if ( !empty( $series_copy[ $series_term->slug ]['subtitle'] ) ) { ?>
								<p class="f-products-series__subtitle"><?php echo esc_html( $series_copy[ $series_term->slug ]['subtitle'] ); ?></p>
							<?php } ?>
							<?php if ( !empty( $series_copy[ $series_term->slug ]['description'] ) ) { ?>
								<p class="f-products-series__description"><?php echo esc_html( $series_copy[ $series_term->slug ]['description'] ); ?></p>
							<?php } ?>
						</header>

						<?php get_template_part( 'templates/loop', '', array(
							'query_module'         => 'products',
							'query_args'           => $series_query_args,
							'query_listing'        => '',
							'query_pagination'     => false,
							'query_posts_per_page' => -1,
						) ); ?>
					</section>
					<?php
					$series_query->reset_postdata();
				} ?>
			</div>
		</section>

		<?php
		wp_reset_query();
		return;
	}
}

if ( is_singular( 'product' ) ) {
	$section_id      = esc_attr_x( 'variations', 'anchor', 'baspa' );
	$section_title   = __( 'Available Variations', 'baspa' );
	$section_class[] = 'f-section--variations';

	$products_query_args[ 'post_parent' ] = get_the_ID();
} else {
	$section_header_class[] = 'screen-reader-text';
}

// Query
$products_query = new WP_Query( $products_query_args );

//do_action( 'qm/debug', $products_query );

if ( $products_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( $section_id ); ?>" <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_class ); ?>>

		<div class="f-section__container a-container">

			<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_header_class ); ?>>
				<h2><?php echo esc_html( $section_title ); ?></h2>
			</header>

			<?php
			if ( is_tax( 'product-category' ) && get_term_meta( get_queried_object_id(), 'category_type', true ) == 'accessories' ) {

				get_template_part( 'templates/loop', '', array(
					'query_module'         => 'products',
					'query_args'           => $products_query_args,
					'query_listing'        => 'accessory',
					'query_class' => array(
							'f-listings',
							'f-listings--accessories',
							'a-grid',
							'a-grid--cols-6',
							'a-gap--xs',
					),
					'query_pagination'     => true,
					'query_posts_per_page' => 24,
				) );

			} else {

				get_template_part( 'templates/loop', '', array(
					'query_module'         => 'products',
					'query_args'           => $products_query_args,
					'query_listing'        => is_singular( 'product' ) ? 'variation' : '',
					'query_pagination'     => false,
					'query_posts_per_page' => -1,
				) );
			}
			?>

		</div>

	</section>

	<?php
	$products_query->reset_postdata();
	wp_reset_query();
}
