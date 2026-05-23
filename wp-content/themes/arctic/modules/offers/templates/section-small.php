<?php

/**
 * Section
 */

// Query Arguments
$offers_query_args = array(
	'post_type'   => 'offer',
	'post_status' => 'publish',
	'meta_query'  => array(
		array(
			'key'   => 'offer_featured',
			'value' => 1,
		),
	),
);

if ( baspa_products_is_term_or_product( 'bazeny' ) ) {
	$offers_query_args[ 'meta_query' ] = array(
		'relation' => 'OR',
		array(
			'key'   => 'offer_display',
			'value' => 'pools',
		),
		array(
			'key'   => 'offer_display',
			'value' => 'default',
		),
	);
}

if ( baspa_products_is_term_or_product( 'virivky' ) ) {
	$offers_query_args[ 'meta_query' ] = array(
		'relation' => 'OR',
		array(
			'key'   => 'offer_display',
			'value' => 'jacuzzis',
		),
		array(
			'key'   => 'offer_display',
			'value' => 'default',
		),
	);
}

// Query
$offer_query = new WP_Query( $offers_query_args );

if ( $offer_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'offers', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--offers f-section--offers-small f-section--soft">

		<div class="f-section__container a-container">

			<header class="f-section__header f-section__header--center screen-reader-text">
				<h2><?php echo wp_kses_post( __( 'Offers', 'baspa' ) ); ?></h2>
			</header>

			<?php
			get_template_part( 'templates/loop', '', array(
				'query_module'         => 'offers',
				'query_args'           => $offers_query_args,
				'query_listing'        => 'small',
				'query_class'          => array(
					'f-listings',
					'a-grid',
					'a-grid--cols-1',
					'a-gap--xs',
				),
				'query_pagination'     => false,
				'query_posts_per_page' => 3,
			) );
			?>

		</div>

	</section>

<?php }
