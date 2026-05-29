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

$reference_target_count   = 9;
$reference_fallback_images = array(
	content_url( 'uploads/import/figma/realizace-1.jpg' ),
	content_url( 'uploads/import/figma/realizace-2.jpg' ),
	content_url( 'uploads/import/figma/realizace-3.jpg' ),
);
$reference_placeholder_cards = array(
	array(
		'title'    => 'Ukázková reference',
		'location' => 'Nová realizace',
		'year'     => 'Připravujeme',
	),
	array(
		'title'    => 'Další realizace',
		'location' => 'Reference přibude',
		'year'     => 'Brzy',
	),
	array(
		'title'    => 'Místo pro reference',
		'location' => 'Aktualizace obsahu',
		'year'     => 'Brzy',
	),
);

$references      = array();
$reference_index = 0;
if ( $references_query->have_posts() ) {
	while ( $references_query->have_posts() ) {
		$references_query->the_post();
		$thumbnail_id = get_post_thumbnail_id();
		$image_meta   = $thumbnail_id ? wp_get_attachment_metadata( $thumbnail_id ) : array();
		$image        = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'full' ) : '';

		$references[] = array(
			'title'          => get_the_title(),
			'image'          => $image ?: $reference_fallback_images[ $reference_index % count( $reference_fallback_images ) ],
			'image_width'    => !empty( $image_meta[ 'width' ] ) ? (int) $image_meta[ 'width' ] : 1600,
			'image_height'   => !empty( $image_meta[ 'height' ] ) ? (int) $image_meta[ 'height' ] : 1169,
			'location'       => get_post_meta( get_the_ID(), 'reference_location', true ) ?: 'Moravany',
			'year'           => get_post_meta( get_the_ID(), 'reference_year', true ) ?: '2025',
			'is_placeholder' => false,
		);
		$reference_index++;
	}
	wp_reset_postdata();
}

$reference_placeholder_count = 0;
while ( count( $references ) < $reference_target_count ) {
	$placeholder = $reference_placeholder_cards[ $reference_placeholder_count % count( $reference_placeholder_cards ) ];
	$references[] = array(
		'title'          => $placeholder['title'],
		'image'          => $reference_fallback_images[ $reference_placeholder_count % count( $reference_fallback_images ) ],
		'image_width'    => 1600,
		'image_height'   => 1169,
		'location'       => $placeholder['location'],
		'year'           => $placeholder['year'],
		'is_placeholder' => true,
	);
	$reference_placeholder_count++;
}

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--references-figma">
	<section class="f-section f-section--references-figma f-reference-section f-reference-section--archive-grid js-images">
		<div class="f-section__container a-container">
			<div class="f-reference-grid">
				<?php foreach ( array_slice( $references, 0, 9 ) as $reference ) { ?>
					<?php $is_placeholder = !empty( $reference['is_placeholder'] ); ?>
					<?php if ( $is_placeholder ) { ?>
						<article class="f-reference-card f-reference-card--placeholder" aria-label="<?php echo esc_attr__( 'Ukázková reference', 'baspa' ); ?>">
					<?php } else { ?>
						<a class="f-reference-card f-reference-card--lightbox js-image"
						   href="<?php echo esc_url( $reference['image'] ); ?>"
						   data-pswp-width="<?php echo esc_attr( $reference['image_width'] ); ?>"
						   data-pswp-height="<?php echo esc_attr( $reference['image_height'] ); ?>"
						   data-cropped="true"
						   aria-label="<?php echo esc_attr( $reference['title'] ); ?>">
					<?php } ?>
						<img src="<?php echo esc_url( $reference['image'] ); ?>" alt="" loading="lazy" decoding="async">
						<span class="f-reference-card__meta">
							<span><?php echo esc_html( $reference['location'] ); ?></span>
							<span><?php echo esc_html( $reference['year'] ); ?></span>
						</span>
						<?php if ( $is_placeholder ) { ?>
							<span class="f-reference-card__state"><?php echo esc_html__( 'Ukázková karta', 'baspa' ); ?></span>
						<?php } ?>
						<strong><?php echo esc_html( $reference['title'] ); ?></strong>
					<?php if ( $is_placeholder ) { ?>
						</article>
					<?php } else { ?>
						</a>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
