<?php

/**
 * Section - Related
 */

// Tags
$tags    = wp_get_post_tags( get_the_ID() );
$tag_ids = array();

foreach ( $tags as $tag ) {
	$tag_ids[] = $tag->term_id;
}

// Categories
$categories   = get_the_category( get_the_ID() );
$category_ids = array();

foreach ( $categories as $category ) {
	$category_ids[] = $category->term_id;
}

// Query Arguments
$posts_rel_query_args = array(
	'post__not_in'   => array( get_the_ID() ),
	'tax_query'      => array(
		'relation' => 'OR',
		array(
			'taxonomy' => 'post_tag',
			'field'    => 'id',
			'terms'    => $tag_ids,
		),
		array(
			'taxonomy' => 'category',
			'field'    => 'id',
			'terms'    => $category_ids,
		),
	),
	'orderby'        => 'modified',
	'no_found_rows'  => true,
);

// Query
$posts_rel_query = new WP_Query( $posts_rel_query_args );

if ( $posts_rel_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'posts-related', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--posts f-section--posts-related">

		<div class="f-section__container a-container">

			<header class="f-section__header">
				<h2><?php echo esc_html__( 'Related Posts', 'baspa' ); ?></h2>
			</header>

			<?php
			get_template_part( 'templates/loop', '', array(
				'query_args'           => $posts_rel_query_args,
				'query_pagination'     => false,
				'query_posts_per_page' => 3,
			) );
			?>

		</div>

	</section>

<?php }
