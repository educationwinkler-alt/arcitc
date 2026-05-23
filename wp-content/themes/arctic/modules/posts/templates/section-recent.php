<?php

/**
 * Section
 */

$post_number = 1;

// Query Arguments
$posts_query_args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	),
	'no_found_rows'  => true,
	'posts_per_page' => 5,
);

// Query
$posts_query = new WP_Query( $posts_query_args );

if ( $posts_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'posts', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--posts f-section--posts-recent">

		<div class="f-section__container a-container">

			<header class="f-section__header screen-reader-text">
				<h2><?php echo esc_html__( 'Posts', 'baspa' ); ?></h2>
			</header>

			<div class="f-listings f-listings--posts a-grid a-gap--s">
				<?php while ( $posts_query->have_posts() ) {
					$posts_query->the_post();

					if ( $post_number == 1 ) {
						get_template_part( 'modules/posts/templates/post/listing', 'cover', array(
							'post_number' => $post_number,
						) );
					} else {
						get_template_part( 'modules/posts/templates/post/listing', '', array(
							'post_number' => $post_number,
							'image'       => false,
						) );
					}

					$post_number++;
				} ?>
			</div>

		</div>

	</section>

<?php }
