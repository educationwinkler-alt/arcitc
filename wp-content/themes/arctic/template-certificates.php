<?php
/**
 * Template Name: Figma Certifikáty
 */

$certificates = array(
	content_url( 'uploads/import/figma/certificate-tuv-1.png' ),
	content_url( 'uploads/import/figma/certificate-tuv-2.png' ),
	content_url( 'uploads/import/figma/certificate-tuv-3.png' ),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--certificates">
	<section class="f-section f-section--certificates">
		<div class="f-section__container a-container">
			<div class="f-certificate-layout">
				<div class="f-certificate-copy">
					<section>
						<h2><?php echo esc_html__( 'Ruční výroba kombinovaná s high-tech', 'baspa' ); ?></h2>
						<p><?php echo esc_html__( 'Jako mezinárodní výrobce vířivek musíme ze zákona splňovat kvalitativní a bezpečnostní standardy. Pro certifikaci našich výrobků jsme se obrátili na organizaci TÜV, která je jednou z nejpřísnějších a nejvíce respektovaných certifikačních organizací na světě.', 'baspa' ); ?></p>
					</section>
					<section>
						<h2><?php echo esc_html__( 'Certifikovaný výrobce, na špičce v oboru', 'baspa' ); ?></h2>
						<p><?php echo esc_html__( 'Před nákupem vířivky byste se měli ujistit, že byla respektovanou agenturou certifikována a je v souladu s normami UL 1563, CSA C22.2 #218 a IEC 60335-1/IEC 60335-2-60.', 'baspa' ); ?></p>
					</section>
				</div>
				<div class="f-certificate-images" aria-label="<?php echo esc_attr__( 'Certifikáty Arctic Spas', 'baspa' ); ?>">
					<?php foreach ( $certificates as $certificate ) { ?>
						<img src="<?php echo esc_url( $certificate ); ?>" alt="" loading="lazy" decoding="async">
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
