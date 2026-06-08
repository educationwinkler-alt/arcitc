<?php

/**
 * Arctic Showroom
 */

$post_id = get_queried_object_id();

$fallback_showroom_images = array(
	array( 'file' => 'owner-showroom/showroom-main-web.jpg', 'width' => 1920, 'height' => 1440 ),
	array( 'file' => 'owner-showroom/showroom-covana-interior-web.jpg', 'width' => 1400, 'height' => 1050 ),
	array( 'file' => 'owner-showroom/showroom-detail-web.jpg', 'width' => 1400, 'height' => 1050 ),
);

$showroom_image_ids = function_exists( 'arctic_meta_attachment_ids' ) ? arctic_meta_attachment_ids( $post_id, 'homepage_showroom_images' ) : array();
$showroom_title     = function_exists( 'arctic_meta_text' ) ? arctic_meta_text( $post_id, 'homepage_showroom_title' ) : '';
$showroom_text      = function_exists( 'arctic_meta_text' ) ? arctic_meta_text( $post_id, 'homepage_showroom_text' ) : '';
$button_text        = function_exists( 'arctic_meta_text' ) ? arctic_meta_text( $post_id, 'homepage_showroom_button_text' ) : '';
$button_url_raw     = trim( (string) get_post_meta( $post_id, 'homepage_showroom_button_url', true ) );
$address            = function_exists( 'arctic_meta_text' ) ? arctic_meta_text( $post_id, 'homepage_showroom_address' ) : '';
$badge_value        = function_exists( 'arctic_meta_text' ) ? arctic_meta_text( $post_id, 'homepage_showroom_badge_value' ) : '';
$badge_label        = function_exists( 'arctic_meta_text' ) ? arctic_meta_text( $post_id, 'homepage_showroom_badge_label' ) : '';

$has_showroom_meta = '' !== $showroom_title
	|| '' !== $showroom_text
	|| '' !== $button_text
	|| '' !== $button_url_raw
	|| '' !== $address
	|| '' !== $badge_value
	|| '' !== $badge_label
	|| !empty( $showroom_image_ids );

$source = 'homepage-meta';

if ( !$has_showroom_meta ) {
	if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() ) {
		return;
	}

	$source         = 'figma-fallback';
	$showroom_title = __( 'Navštivte náš showroom', 'baspa' );
	$showroom_text  = __( 'Chcete si osobně prohlédnout různá řešení a pobavit se o možnostech realizace vašeho nového bazénu nebo vířivky? Rádi si s vámi dáme kávu v našem showroomu.', 'baspa' );
	$button_text    = __( 'Více o nás', 'baspa' );
	$button_url_raw = '/showroom/';
	$address        = __( 'Bohunická cesta 15, Moravany u Brna', 'baspa' );
	$badge_value    = '280 m²';
	$badge_label    = __( 'prezentačních ploch', 'baspa' );
}

$button_url = function_exists( 'arctic_sections_url' )
	? arctic_sections_url( $button_url_raw, '/showroom/' )
	: ( $button_url_raw ? esc_url_raw( $button_url_raw ) : home_url( '/showroom/' ) );
?>

<section id="<?php echo sanitize_title( esc_attr_x( 'showroom', 'anchor', 'baspa' ) ); ?>" class="f-section f-section--showroom js-links__section" data-content-source="<?php echo esc_attr( $source ); ?>">
	<div class="f-section__container a-container">
		<div class="f-showroom-panel f-showroom-panel--collage">
			<div class="f-showroom-panel__media" data-content-source="<?php echo esc_attr( $source ); ?>">
				<?php for ( $index = 0; $index < 3; $index++ ) { ?>
					<figure class="f-showroom-panel__image f-showroom-panel__image--<?php echo esc_attr( $index + 1 ); ?>">
						<?php
						$image_id = (int) ( $showroom_image_ids[ $index ] ?? 0 );

						if ( $image_id ) {
							echo wp_get_attachment_image( $image_id, 'full', false, array(
								'alt'      => __( 'Showroom Arctic Spas v Moravanech', 'baspa' ),
								'loading'  => 'eager',
								'decoding' => 'async',
							) );
						} elseif ( 'figma-fallback' === $source || ( function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ) ) {
							$fallback_image = $fallback_showroom_images[ $index ];
							?>
							<img src="<?php echo esc_url( content_url( 'uploads/import/' . $fallback_image['file'] ) ); ?>"
								width="<?php echo esc_attr( $fallback_image['width'] ); ?>"
								height="<?php echo esc_attr( $fallback_image['height'] ); ?>"
								alt="<?php echo esc_attr__( 'Showroom Arctic Spas v Moravanech', 'baspa' ); ?>"
								loading="eager" decoding="async">
						<?php } ?>
					</figure>
				<?php } ?>
				<?php if ( '' !== $badge_value || '' !== $badge_label ) { ?>
					<div class="f-showroom-panel__badge">
						<strong><?php echo esc_html( $badge_value ); ?></strong>
						<span><?php echo esc_html( $badge_label ); ?></span>
					</div>
				<?php } ?>
			</div>

			<div class="f-showroom-panel__content" data-content-source="<?php echo esc_attr( $source ); ?>">
				<h2><?php echo esc_html( $showroom_title ); ?></h2>
				<p><?php echo esc_html( $showroom_text ); ?></p>
				<div class="f-showroom-panel__actions">
					<a class="f-button a-button a-button--outline" href="<?php echo esc_url( $button_url ); ?>">
						<?php echo esc_html( $button_text ); ?>
					</a>
					<?php if ( '' !== $address ) { ?>
						<span><?php echo esc_html( $address ); ?></span>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>
