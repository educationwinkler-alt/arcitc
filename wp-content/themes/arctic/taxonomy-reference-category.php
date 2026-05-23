<?php

/**
 * Reference Category
 */

// Query Arguments
$reference_query_args = array(
	'post_type' => 'reference',
	'tax_query' => array(
		array(
			'taxonomy' => 'reference-category',
			'terms'    => get_queried_object_id(),
		),
	),
	'orderby'   => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
);

// Query
$reference_query = new WP_Query( $reference_query_args );

get_header();
get_template_part( 'modules/references/templates/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--listings f-main--references f-main--top-0">

		<?php //get_template_part( 'modules/references/templates/navigation' ); ?>

		<div class="f-listings__container a-container">
			<?php
			if ( $reference_query->have_posts() ) {
				get_template_part( 'templates/loop', '', array(
					'query_module' => 'references',
					'query_args'   => $reference_query_args,
				) );
			}
			?>
		</div>

	</main>

<?php
get_footer();
