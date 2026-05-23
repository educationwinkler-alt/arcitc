<?php

/**
 * Downloads Listing
 */

$downloads_query = new WP_Query( array(
	'post_type'      => 'download',
	'post_status'    => 'publish',
	'orderby'        => array(
		'menu_order' => 'ASC',
		'title'      => 'ASC',
	),
	'posts_per_page' => -1,
) );

if ( $downloads_query->have_posts() ) { ?>
	<div class="f-downloads a-stack a-gap--s">
		<?php while ( $downloads_query->have_posts() ) {
			$downloads_query->the_post();
			$file = get_post_meta( get_the_ID(), 'download_file_url', true ); ?>

			<article id="download-<?php the_ID(); ?>" <?php post_class( 'f-download' ); ?>>
				<h3 class="f-download__title"><?php the_title(); ?></h3>
				<?php if ( !empty( $file ) ) { ?>
					<a class="f-download__link f-button a-button a-button--outline" href="<?php echo esc_url( $file ); ?>">
						<?php echo esc_html_x( 'Download', 'download link', 'baspa' ); ?>
					</a>
				<?php } ?>
			</article>

		<?php } ?>
	</div>
<?php }

wp_reset_postdata();
