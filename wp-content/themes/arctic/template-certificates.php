<?php
/**
 * Template Name: Figma Certifikaty
 */

$asset = static function ( string $filename ): string {
	return content_url( 'uploads/import/' . ltrim( $filename, '/' ) );
};

$post_id = get_queried_object_id();

$default_sections = array(
	array(
		'title' => __( 'Ruční výroba kombinovaná s high-tech', 'baspa' ),
		'text'  => __( 'Jako mezinárodní výrobce vířivek musíme ze zákona splňovat kvalitativní a bezpečnostní standardy. Pro certifikaci našich výrobků jsme se obrátili na organizaci TÜV, která je jednou z nejpřísnějších a nejvíce respektovaných certifikačních organizací na světě.', 'baspa' ),
	),
	array(
		'title' => __( 'Certifikovaný výrobce, na špičce v oboru', 'baspa' ),
		'text'  => __( 'Před nákupem vířivky byste se měli ujistit, že byla respektovanou agenturou certifikována a je v souladu s normami UL 1563, CSA C22.2 #218 a IEC 60335-1/IEC 60335-2-60.', 'baspa' ),
	),
);

$raw_section_rows = get_post_meta( $post_id, 'certificates_sections' );
$section_rows     = array();

foreach ( $raw_section_rows as $raw_section_row ) {
	if ( !is_array( $raw_section_row ) ) {
		continue;
	}

	if ( array_key_exists( 'title', $raw_section_row ) || array_key_exists( 'text', $raw_section_row ) ) {
		$section_rows[] = $raw_section_row;
		continue;
	}

	foreach ( $raw_section_row as $nested_section_row ) {
		if ( is_array( $nested_section_row ) ) {
			$section_rows[] = $nested_section_row;
		}
	}
}

$sections = array();
foreach ( $section_rows as $section_row ) {
	$title = trim( wp_strip_all_tags( (string) ( $section_row['title'] ?? '' ) ) );
	$text  = trim( wp_strip_all_tags( (string) ( $section_row['text'] ?? '' ) ) );

	if ( '' === $title && '' === $text ) {
		continue;
	}

	$sections[] = array(
		'title' => $title,
		'text'  => $text,
	);
}

if ( empty( $sections ) && function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ) {
	$sections = $default_sections;
}

$certificate_image_ids = array_values( array_filter( array_map( 'absint', get_post_meta( $post_id, 'certificates_images' ) ) ) );
$fallback_images       = array(
	$asset( 'figma/certificate-tuv-1.png' ),
	$asset( 'figma/certificate-tuv-2.png' ),
	$asset( 'figma/certificate-tuv-3.png' ),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--certificates">
	<section class="f-section f-section--certificates">
		<div class="f-section__container a-container">
			<h2 class="screen-reader-text"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
			<div class="f-certificate-layout">
				<div class="f-certificate-copy" data-content-source="certificates-meta">
					<?php foreach ( $sections as $section ) { ?>
						<section>
							<?php if ( '' !== $section['title'] ) { ?>
								<h2><?php echo esc_html( $section['title'] ); ?></h2>
							<?php } ?>
							<?php if ( '' !== $section['text'] ) { ?>
								<p><?php echo esc_html( $section['text'] ); ?></p>
							<?php } ?>
						</section>
					<?php } ?>
				</div>
				<div class="f-certificate-images" data-content-source="certificates-media" role="group" aria-label="<?php echo esc_attr__( 'Certifikáty Arctic Spas', 'baspa' ); ?>">
					<?php
					if ( !empty( $certificate_image_ids ) ) {
						foreach ( $certificate_image_ids as $certificate_image_id ) {
							echo wp_get_attachment_image( $certificate_image_id, 'full', false, array(
								'alt'               => '',
								'loading'           => 'lazy',
								'decoding'          => 'async',
								'data-asset-status' => 'admin-certificate',
							) );
						}
					} elseif ( function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ) {
						foreach ( $fallback_images as $certificate ) {
							?>
							<img src="<?php echo esc_url( $certificate ); ?>" alt="" loading="lazy" decoding="async" data-asset-status="seed-fallback">
						<?php }
					} ?>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
