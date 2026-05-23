<?php

/**
 * Section Template
 */

// Section
$section_id    = esc_attr_x( 'variations', 'anchor', 'baspa' );
$section_title = __( 'Other Variations', 'baspa' );

$section_class        = array( 'f-section', 'f-section--products', 'js-links__section' );
$section_header_class = array( 'f-section__header' );

// Query
$products_query = baspa_products_query_related( get_the_ID() );

if ( $products_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( $section_id ); ?>" <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_class ); ?>>

		<div class="f-section__container a-container">

			<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_header_class ); ?>>
				<h2><?php echo esc_html( $section_title ); ?></h2>
			</header>

			<?php
			get_template_part( 'templates/loop', '', array(
				'query_module'         => 'products',
				'query_args'           => $products_query->query,
				'query_listing'        => is_singular( 'product' ) ? 'variation' : '',
				'query_pagination'     => false,
				'query_posts_per_page' => -1,
			) );
			?>

		</div>

	</section>

	<?php
	$products_query->reset_postdata();
	wp_reset_query();
}
