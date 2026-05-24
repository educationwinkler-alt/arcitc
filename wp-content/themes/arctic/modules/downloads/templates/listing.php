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
	<?php if ( is_page_template( 'template-support.php' ) ) {
		$downloads = array();

		while ( $downloads_query->have_posts() ) {
			$downloads_query->the_post();
			$downloads[] = array(
				'id'    => get_the_ID(),
				'title' => get_the_title(),
				'file'  => get_post_meta( get_the_ID(), 'download_file_url', true ),
				'type'  => get_post_meta( get_the_ID(), 'download_document_type', true ),
			);
		}

		$featured_downloads = array_slice( array_values( array_filter( $downloads, function ( $download ) {
			return in_array( $download['type'], array( 'catalog', 'manual', 'preparation' ), true );
		} ) ), 0, 3 );

		if ( count( $featured_downloads ) < 3 ) {
			$featured_downloads = array_slice( $downloads, 0, 3 );
		}

		$thumbs = array(
			content_url( 'uploads/import/figma/support-download-1.png' ),
			content_url( 'uploads/import/figma/support-download-2.png' ),
			content_url( 'uploads/import/figma/support-download-3.png' ),
		);
		?>

		<div class="f-downloads f-downloads--support-figma">
			<section class="f-download-group f-download-group--open">
				<header class="f-download-group__header">
					<span class="f-download-group__icon" aria-hidden="true">−</span>
					<h3><?php echo esc_html__( 'Série custom', 'baspa' ); ?></h3>
					<span class="f-download-group__tag"><?php echo esc_html__( 'Katalogy vířivek', 'baspa' ); ?></span>
				</header>

				<div class="f-download-group__items">
					<?php foreach ( $featured_downloads as $index => $download ) { ?>
						<article class="f-download-card">
							<img class="f-download-card__thumb" src="<?php echo esc_url( $thumbs[ $index ] ); ?>" alt="" loading="lazy" decoding="async">
							<div class="f-download-card__body">
								<h4><?php echo esc_html( $download['title'] ); ?></h4>
								<p><?php echo esc_html__( 'Dokument Arctic Spas, PDF ke stažení.', 'baspa' ); ?></p>
							</div>
							<?php if ( !empty( $download['file'] ) ) { ?>
								<a class="f-download-card__button f-button a-button a-button--outline" href="<?php echo esc_url( $download['file'] ); ?>">
									<?php echo esc_html__( 'Stáhnout', 'baspa' ); ?>
								</a>
							<?php } ?>
						</article>
					<?php } ?>
				</div>
			</section>

			<section class="f-download-group f-download-group--closed">
				<span class="f-download-group__icon" aria-hidden="true">+</span>
				<h3><?php echo esc_html__( 'Série classic', 'baspa' ); ?></h3>
				<span class="f-download-group__tag"><?php echo esc_html__( 'Katalogy vířivek', 'baspa' ); ?></span>
			</section>

			<section class="f-download-group f-download-group--closed">
				<span class="f-download-group__icon" aria-hidden="true">+</span>
				<h3><?php echo esc_html__( 'Série core', 'baspa' ); ?></h3>
				<span class="f-download-group__tag"><?php echo esc_html__( 'Katalogy vířivek', 'baspa' ); ?></span>
			</section>
		</div>
	<?php } else { ?>
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
	<?php } ?>
<?php }

wp_reset_postdata();
