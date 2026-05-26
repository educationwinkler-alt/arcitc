<?php

/**
 * Arctic Showroom
 */

$showroom_images = array(
	array( 'file' => 'showroom-1.png', 'width' => 384, 'height' => 210 ),
	array( 'file' => 'showroom-3.png', 'width' => 334, 'height' => 341 ),
	array( 'file' => 'showroom-2.png', 'width' => 454, 'height' => 285 ),
);
?>

<section class="f-section f-section--showroom">
	<div class="f-section__container a-container">
		<div class="f-showroom-panel">
			<div class="f-showroom-panel__media">
				<?php foreach ( $showroom_images as $index => $image ) { ?>
					<figure class="f-showroom-panel__image f-showroom-panel__image--<?php echo esc_attr( $index + 1 ); ?>">
						<img src="<?php echo esc_url( content_url( 'uploads/import/figma/' . $image['file'] ) ); ?>"
							width="<?php echo esc_attr( $image['width'] ); ?>"
							height="<?php echo esc_attr( $image['height'] ); ?>"
							alt="<?php echo esc_attr__( 'Showroom Arctic Spas podle grafiky', 'baspa' ); ?>"
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
