<?php

/**
 * Template Name: Downloads
 */

get_header();
get_template_part( 'templates/heading/default' );

$downloads_defaults = function_exists( 'arctic_downloads_option_defaults' ) ? arctic_downloads_option_defaults() : array();
$downloads_title = function_exists( 'arctic_downloads_get_option' )
	? arctic_downloads_get_option( 'arctic_downloads_page_title', $downloads_defaults['arctic_downloads_page_title'] ?? 'Dokumenty ke stažení' )
	: 'Dokumenty ke stažení';
$download_filter_labels = function_exists( 'arctic_downloads_filter_labels' )
	? arctic_downloads_filter_labels()
	: array( 'Katalogy vířivek', 'Návody', 'Rozměry', 'Záruky' );
$download_filter_keys = array( 'catalog', 'manual', 'dimensions', 'warranty' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
      class="f-main f-main--support f-main--support-contract f-main--downloads f-main--top-0 page-template-template-support">

	<section id="ke-stazeni" class="f-section f-section--support-downloads f-section--support-downloads-contract">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html( $downloads_title ); ?></h2>
			<div class="f-chip-list f-chip-list--interactive f-chip-list--contract" role="tablist" aria-label="<?php echo esc_attr__( 'Kategorie ke stažení', 'baspa' ); ?>">
				<?php foreach ( $download_filter_labels as $index => $label ) { ?>
					<button type="button"
					        class=""
					        data-download-filter="<?php echo esc_attr( $download_filter_keys[ $index ] ?? 'catalog' ); ?>"
					        role="tab"
					        aria-selected="false">
						<?php echo esc_html( $label ); ?>
					</button>
				<?php } ?>
			</div><?php echo trim( (string) do_shortcode( '[arctic-downloads]' ) ); ?>
		</div>
	</section>
</main>

<?php
get_footer();
