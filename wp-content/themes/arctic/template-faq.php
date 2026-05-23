<?php

/**
 * Template Name: FAQ
 */

// Query Arguments
$faqs_query_args = array(
	'post_type' => 'faq',
	'orderby'   => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
);

// Query
$faqs_query = new WP_Query( $faqs_query_args );

get_header();
get_template_part( 'templates/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--faq">

		<div class="a-container">
			<?php
			get_template_part( 'templates/content' );

			if ( $faqs_query->have_posts() ) {
				get_template_part( 'templates/loop', '', array(
					'query_args'           => $faqs_query_args,
					'query_module'         => 'faqs',
					'query_class'          => array(
						'f-listings',
						'a-grid',
						'a-grid--cols-1',
						'a-gap--xs',
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
