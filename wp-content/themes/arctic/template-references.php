<?php

/**
 * Template Name: References
 */

// Query Arguments
$references_query_args = array(
	'post_type' => 'reference',
	'orderby'   => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
);

// Query
$references_query = new WP_Query( $references_query_args );

get_header();
get_template_part( 'templates/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--references">

		<div class="a-container">
			<?php
			get_template_part( 'templates/content' );

			if ( $references_query->have_posts() ) {
				get_template_part( 'templates/loop', '', array(
					'query_args'           => $references_query_args,
					'query_module'         => 'references',
					'query_class'          => array(
						'f-listings',
						'a-grid',
						'a-gap--xs',
						'js-images',
					),
					'query_pagination'     => false,
					'query_posts_per_page' => -1,
				) );
			}
			?>
		</div>

	</main>

<?php
get_footer();
