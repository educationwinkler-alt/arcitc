<?php
/**
 * Template Name: Figma Reference
 */

$references_query = new WP_Query( array(
	'post_type'      => 'reference',
	'posts_per_page' => -1,
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
) );

$references = array();
if ( $references_query->have_posts() ) {
	while ( $references_query->have_posts() ) {
		$references_query->the_post();
		$references[] = array(
			'title'    => get_the_title(),
			'image'    => get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: content_url( 'uploads/import/figma/realizace-1.jpg' ),
			'location' => get_post_meta( get_the_ID(), 'reference_location', true ) ?: 'Moravany',
			'year'     => get_post_meta( get_the_ID(), 'reference_year', true ) ?: '2025',
			'url'      => get_permalink(),
		);
	}
	wp_reset_postdata();
}

if ( empty( $references ) ) {
	$references[] = array(
		'title'    => 'Realizace vířivky Arctic Spas',
		'image'    => content_url( 'uploads/import/figma/realizace-1.jpg' ),
		'location' => 'Moravany',
		'year'     => '2025',
		'url'      => home_url( '/reference/' ),
	);
}

$reference_seed = $references;
while ( count( $references ) < 9 ) {
	$references[] = $reference_seed[ count( $references ) % count( $reference_seed ) ];
}

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--references-figma">
	<section class="f-section f-section--references-figma">
		<div class="f-section__container a-container">
			<div class="f-reference-grid">
				<?php foreach ( array_slice( $references, 0, 9 ) as $reference ) { ?>
					<a class="f-reference-card" href="<?php echo esc_url( $reference['url'] ); ?>">
						<img src="<?php echo esc_url( $reference['image'] ); ?>" alt="" loading="lazy" decoding="async">
						<span class="f-reference-card__meta">
							<span><?php echo esc_html( $reference['location'] ); ?></span>
							<span><?php echo esc_html( $reference['year'] ); ?></span>
						</span>
						<strong><?php echo esc_html( $reference['title'] ); ?></strong>
					</a>
				<?php } ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
