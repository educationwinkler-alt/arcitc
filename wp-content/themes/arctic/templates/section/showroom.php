<?php

/**
 * Arctic Showroom
 */

$image_keys = array(
	'figma-node-1-123-showroom-1',
	'figma-node-1-125-showroom-3',
	'figma-node-1-124-showroom-2',
);
$image_ids  = array();

foreach ( $image_keys as $image_key ) {
	$found = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_arctic_seed_key',
		'meta_value'     => $image_key,
	) );

	if ( !empty( $found ) ) {
		$image_ids[] = (int) $found[0];
	}
}
?>

<section class="f-section f-section--showroom">
	<div class="f-section__container a-container">
		<div class="f-showroom-panel">
			<div class="f-showroom-panel__media">
				<?php foreach ( $image_ids as $index => $image_id ) { ?>
					<figure class="f-showroom-panel__image f-showroom-panel__image--<?php echo esc_attr( $index + 1 ); ?>">
						<?php echo wp_get_attachment_image( $image_id, 'large', false, array(
							'data-lazy' => 'false',
							'loading'   => 'eager',
							'decoding'  => 'async',
						) ); ?>
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
