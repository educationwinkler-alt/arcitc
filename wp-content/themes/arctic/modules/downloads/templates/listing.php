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

$downloads_defaults = function_exists( 'arctic_downloads_option_defaults' ) ? arctic_downloads_option_defaults() : array();
$downloads_featured_group_title = function_exists( 'arctic_downloads_get_option' ) ? arctic_downloads_get_option( 'arctic_downloads_featured_group_title', $downloads_defaults['arctic_downloads_featured_group_title'] ?? 'Série custom' ) : 'Série custom';
$downloads_closed_group_1_title = function_exists( 'arctic_downloads_get_option' ) ? arctic_downloads_get_option( 'arctic_downloads_closed_group_1_title', $downloads_defaults['arctic_downloads_closed_group_1_title'] ?? 'Série classic' ) : 'Série classic';
$downloads_closed_group_2_title = function_exists( 'arctic_downloads_get_option' ) ? arctic_downloads_get_option( 'arctic_downloads_closed_group_2_title', $downloads_defaults['arctic_downloads_closed_group_2_title'] ?? 'Série core' ) : 'Série core';
$downloads_group_tag = function_exists( 'arctic_downloads_get_option' ) ? arctic_downloads_get_option( 'arctic_downloads_group_tag', $downloads_defaults['arctic_downloads_group_tag'] ?? 'Katalogy vířivek' ) : 'Katalogy vířivek';
$downloads_card_description = function_exists( 'arctic_downloads_get_option' ) ? arctic_downloads_get_option( 'arctic_downloads_card_description', $downloads_defaults['arctic_downloads_card_description'] ?? 'Dokument Arctic Spas, PDF ke stažení.' ) : 'Dokument Arctic Spas, PDF ke stažení.';
$downloads_button_text = function_exists( 'arctic_downloads_get_option' ) ? arctic_downloads_get_option( 'arctic_downloads_button_text', $downloads_defaults['arctic_downloads_button_text'] ?? 'Stáhnout' ) : 'Stáhnout';

$allow_seed_fallbacks = function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks();

if ( $downloads_query->have_posts() ) { ?>
	<?php if ( is_page_template( 'template-support.php' ) || is_page_template( 'template-downloads.php' ) || is_page( 'ke-stazeni' ) ) {
		$downloads = array();

		while ( $downloads_query->have_posts() ) {
			$downloads_query->the_post();
			$document_type = (string) get_post_meta( get_the_ID(), 'download_document_type', true );
			$filter_type   = function_exists( 'arctic_downloads_filter_key_for_document_type' ) ? arctic_downloads_filter_key_for_document_type( $document_type ) : 'manual';
			$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), get_template() . '-thumbnail' );

			$downloads[] = array(
				'id'          => get_the_ID(),
				'title'       => get_the_title(),
				'file'        => get_post_meta( get_the_ID(), 'download_file_url', true ),
				'type'        => $document_type,
				'filter_type' => $filter_type,
				'thumb'       => $thumbnail_url ?: '',
			);
		}

		$featured_downloads = array();
		$featured_ids       = array();

		foreach ( $downloads as $download ) {
			if ( count( $featured_downloads ) >= 3 ) {
				break;
			}

			if ( !in_array( $download['type'], array( 'catalog', 'manual', 'preparation' ), true ) ) {
				continue;
			}

			$featured_downloads[] = $download;
			$featured_ids[] = (int) $download['id'];
		}

		if ( count( $featured_downloads ) < 3 ) {
			foreach ( $downloads as $download ) {
				if ( count( $featured_downloads ) >= 3 ) {
					break;
				}

				if ( in_array( (int) $download['id'], $featured_ids, true ) ) {
					continue;
				}

				$featured_downloads[] = $download;
				$featured_ids[] = (int) $download['id'];
			}
		}

		$remaining_downloads = array_values( array_filter( $downloads, function ( $download ) use ( $featured_ids ) {
			return !in_array( (int) $download['id'], $featured_ids, true );
		} ) );

		$closed_group_1_downloads = array_slice( $remaining_downloads, 0, 3 );
		$closed_group_2_downloads = array_slice( $remaining_downloads, 3 );

		if ( empty( $closed_group_1_downloads ) && !empty( $closed_group_2_downloads ) ) {
			$closed_group_1_downloads = array_slice( $closed_group_2_downloads, 0, 3 );
			$closed_group_2_downloads = array_slice( $closed_group_2_downloads, 3 );
		}

		$thumbs = array(
			content_url( 'uploads/import/figma/support-download-1.png' ),
			content_url( 'uploads/import/figma/support-download-2.png' ),
			content_url( 'uploads/import/figma/support-download-3.png' ),
		);

		$render_download_card = static function ( array $download, string $thumb, string $card_description, string $button_text ): void {
			?>
			<article class="f-download-card f-download-card--contract"
			         data-download-card
			         data-download-filter-type="<?php echo esc_attr( $download['filter_type'] ); ?>">
				<?php if ( !empty( $thumb ) ) { ?>
					<img class="f-download-card__thumb" src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" decoding="async" data-asset-status="<?php echo esc_attr( !empty( $download['thumb'] ) ? 'admin-download-thumb' : 'seed-fallback' ); ?>">
				<?php } else { ?>
					<span class="f-download-card__thumb f-download-card__thumb--placeholder" data-asset-status="admin-empty" aria-hidden="true">PDF</span>
				<?php } ?>
				<div class="f-download-card__body">
					<h4><?php echo esc_html( $download['title'] ); ?></h4>
					<p><?php echo esc_html( $card_description ); ?></p>
				</div>
				<?php if ( !empty( $download['file'] ) ) { ?>
					<a class="f-download-card__button f-button a-button a-button--outline" href="<?php echo esc_url( $download['file'] ); ?>">
						<?php echo esc_html( $button_text ); ?>
					</a>
				<?php } ?>
			</article>
			<?php
		};
		?>

		<div class="f-downloads f-downloads--support-figma f-downloads--contract" data-downloads-root>
			<section class="f-download-group f-download-group--contract f-download-group--open is-open" data-download-group>
				<header class="f-download-group__header">
					<button type="button"
					        class="f-download-group__icon"
					        data-download-group-toggle
					        aria-expanded="true"
					        aria-controls="downloads-group-open-panel">
						<span aria-hidden="true">−</span>
						<span class="screen-reader-text"><?php echo esc_html( $downloads_featured_group_title ); ?></span>
					</button>
					<h3><?php echo esc_html( $downloads_featured_group_title ); ?></h3>
					<span class="f-download-group__tag"><?php echo esc_html( $downloads_group_tag ); ?></span>
				</header>

				<div id="downloads-group-open-panel" class="f-download-group__items" data-download-group-panel>
					<?php foreach ( $featured_downloads as $index => $download ) {
						$thumb = !empty( $download['thumb'] ) ? $download['thumb'] : ( $allow_seed_fallbacks ? $thumbs[ $index % count( $thumbs ) ] : '' );
						$render_download_card( $download, $thumb, $downloads_card_description, $downloads_button_text );
					} ?>
				</div>
			</section>

			<section class="f-download-group f-download-group--contract f-download-group--closed" data-download-group>
				<header class="f-download-group__header">
					<button type="button"
					        class="f-download-group__icon"
					        data-download-group-toggle
					        aria-expanded="false"
					        aria-controls="downloads-group-closed-1-panel">
						<span aria-hidden="true">+</span>
						<span class="screen-reader-text"><?php echo esc_html( $downloads_closed_group_1_title ); ?></span>
					</button>
					<h3><?php echo esc_html( $downloads_closed_group_1_title ); ?></h3>
					<span class="f-download-group__tag"><?php echo esc_html( $downloads_group_tag ); ?></span>
				</header>
				<div id="downloads-group-closed-1-panel" class="f-download-group__items" data-download-group-panel hidden>
					<?php foreach ( $closed_group_1_downloads as $index => $download ) {
						$thumb = !empty( $download['thumb'] ) ? $download['thumb'] : ( $allow_seed_fallbacks ? $thumbs[ ( $index + 1 ) % count( $thumbs ) ] : '' );
						$render_download_card( $download, $thumb, $downloads_card_description, $downloads_button_text );
					} ?>
				</div>
			</section>

			<section class="f-download-group f-download-group--contract f-download-group--closed" data-download-group>
				<header class="f-download-group__header">
					<button type="button"
					        class="f-download-group__icon"
					        data-download-group-toggle
					        aria-expanded="false"
					        aria-controls="downloads-group-closed-2-panel">
						<span aria-hidden="true">+</span>
						<span class="screen-reader-text"><?php echo esc_html( $downloads_closed_group_2_title ); ?></span>
					</button>
					<h3><?php echo esc_html( $downloads_closed_group_2_title ); ?></h3>
					<span class="f-download-group__tag"><?php echo esc_html( $downloads_group_tag ); ?></span>
				</header>
				<div id="downloads-group-closed-2-panel" class="f-download-group__items" data-download-group-panel hidden>
					<?php foreach ( $closed_group_2_downloads as $index => $download ) {
						$thumb = !empty( $download['thumb'] ) ? $download['thumb'] : ( $allow_seed_fallbacks ? $thumbs[ ( $index + 2 ) % count( $thumbs ) ] : '' );
						$render_download_card( $download, $thumb, $downloads_card_description, $downloads_button_text );
					} ?>
				</div>
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
