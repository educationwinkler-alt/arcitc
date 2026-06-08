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
$download_filter_definitions = function_exists( 'arctic_downloads_filter_definitions' )
	? arctic_downloads_filter_definitions()
	: array(
		array( 'key' => 'catalog', 'label' => __( 'Katalogy vířivek', 'baspa' ) ),
		array( 'key' => 'manual', 'label' => __( 'Návody', 'baspa' ) ),
		array( 'key' => 'dimensions', 'label' => __( 'Rozměry', 'baspa' ) ),
		array( 'key' => 'warranty', 'label' => __( 'Záruky', 'baspa' ) ),
	);
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
      class="f-main f-main--support f-main--support-contract f-main--downloads f-main--top-0 page-template-template-support">

	<section id="ke-stazeni" class="f-section f-section--support-downloads f-section--support-downloads-contract">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html( $downloads_title ); ?></h2>
			<div class="f-chip-list f-chip-list--interactive f-chip-list--contract" role="group" aria-label="<?php echo esc_attr__( 'Kategorie ke stažení', 'baspa' ); ?>">
				<?php foreach ( $download_filter_definitions as $definition ) { ?>
					<button type="button"
					        class=""
					        data-download-filter="<?php echo esc_attr( $definition['key'] ?? 'catalog' ); ?>"
					        aria-pressed="false">
						<?php echo esc_html( $definition['label'] ?? '' ); ?>
					</button>
				<?php } ?>
			</div><?php echo trim( (string) do_shortcode( '[arctic-downloads]' ) ); ?>
		</div>
	</section>
</main>

<?php
get_footer();
