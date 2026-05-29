<?php

/**
 * Arctic Showroom
 */

$showroom_images = array(
	array( 'file' => 'owner-showroom/showroom-main-web.jpg', 'width' => 1920, 'height' => 1440 ),
	array( 'file' => 'owner-showroom/showroom-covana-interior-web.jpg', 'width' => 1400, 'height' => 1050 ),
	array( 'file' => 'owner-showroom/showroom-detail-web.jpg', 'width' => 1400, 'height' => 1050 ),
);
?>

<section id="<?php echo sanitize_title( esc_attr_x( 'showroom', 'anchor', 'baspa' ) ); ?>" class="f-section f-section--showroom">
	<div class="f-section__container a-container">
		<div class="f-showroom-panel f-showroom-panel--collage">
			<div class="f-showroom-panel__media">
				<?php foreach ( $showroom_images as $index => $image ) { ?>
					<figure class="f-showroom-panel__image f-showroom-panel__image--<?php echo esc_attr( $index + 1 ); ?>">
						<img src="<?php echo esc_url( content_url( 'uploads/import/' . $image['file'] ) ); ?>"
							width="<?php echo esc_attr( $image['width'] ); ?>"
							height="<?php echo esc_attr( $image['height'] ); ?>"
							alt="<?php echo esc_attr__( 'Showroom Arctic Spas v Moravanech', 'baspa' ); ?>"
							loading="eager" decoding="async">
					</figure>
				<?php } ?>
				<div class="f-showroom-panel__badge">
					<strong>280 m²</strong>
					<span><?php echo esc_html__( 'prezentačních ploch', 'baspa' ); ?></span>
				</div>
			</div>

			<div class="f-showroom-panel__content">
				<h2><?php echo esc_html__( 'Navštivte náš showroom', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Chcete si osobně prohlédnout různá řešení a pobavit se o možnostech realizace vašeho nového bazénu nebo vířivky? Rádi si s vámi dáme kávu v našem showroomu.', 'baspa' ); ?></p>
				<div class="f-showroom-panel__actions">
					<a class="f-button a-button a-button--outline" href="<?php echo esc_url( home_url( '/showroom/' ) ); ?>">
						<?php echo esc_html__( 'Více o nás', 'baspa' ); ?>
					</a>
					<span><?php echo esc_html__( 'Bohunická cesta 15, Moravany u Brna', 'baspa' ); ?></span>
				</div>
			</div>
		</div>
	</div>
</section>
