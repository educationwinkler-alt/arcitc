<?php
/**
 * Template Name: Figma Akční nabídky
 */

$offers_query = function_exists( 'baspa_offers_query' ) ? baspa_offers_query() : new WP_Query( array(
	'post_type'      => 'offer',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
) );

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--offers-archive">
	<section class="f-section f-section--offers-archive">
		<div class="f-section__container a-container">
			<h2 class="screen-reader-text"><?php echo esc_html( get_the_title() ); ?></h2>
			<?php if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();

					if ( trim( wp_strip_all_tags( get_the_content() ) ) !== '' ) { ?>
						<div class="f-offers-archive__intro f-content" data-content-source="wp-editor">
							<?php the_content(); ?>
						</div>
					<?php }
				}
				rewind_posts();
			} ?>

			<?php if ( $offers_query->have_posts() ) { ?>
				<div class="f-offer-grid" data-content-source="offer-cpt">
					<?php while ( $offers_query->have_posts() ) {
						$offers_query->the_post();
						get_template_part( 'modules/offers/templates/post/card' );
					} ?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php } else { ?>
				<div class="f-offers-empty" data-content-source="offer-cpt">
					<h2><?php echo esc_html__( 'Zatím nejsou vypsané žádné akční nabídky', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Novou akci přidáte ve WordPress administraci v sekci Offers.', 'baspa' ); ?></p>
				</div>
			<?php } ?>
		</div>
	</section>
</main>

<?php
get_footer();
