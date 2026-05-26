<?php

/**
 * Template Name: Downloads
 */

get_header();
get_template_part( 'templates/heading/default' );

$downloads_title        = function_exists( 'arctic_downloads_get_option' ) ? arctic_downloads_get_option( 'arctic_downloads_page_title', 'Dokumenty ke stazeni' ) : 'Dokumenty ke stazeni';
$download_filter_labels = function_exists( 'arctic_downloads_filter_labels' ) ? arctic_downloads_filter_labels() : array( 'Katalogy virivek', 'Navody', 'Rozmery', 'Zaruky' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
      class="f-main f-main--support f-main--downloads f-main--top-0 page-template-template-support">

	<section id="ke-stazeni" class="f-section f-section--support-downloads">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html( $downloads_title ); ?></h2>
			<div class="f-chip-list">
				<?php foreach ( $download_filter_labels as $index => $label ) { ?>
					<span class="<?php echo 0 === $index ? 'is-active' : ''; ?>"><?php echo esc_html( $label ); ?></span>
				<?php } ?>
			</div>
			<?php echo do_shortcode( '[arctic-downloads]' ); ?>
		</div>
	</section>
</main>

<?php
get_footer();
