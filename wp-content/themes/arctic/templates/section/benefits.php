<?php

/**
 * Arctic Benefits
 */

$post_id = get_queried_object_id();

$default_benefits = array(
	array(
		'title' => __( 'Montáž', 'baspa' ),
		'text'  => __( 'Odborně na klíč', 'baspa' ),
		'icon'  => 'box',
		'image' => 'hp-benefit-montaz.png',
	),
	array(
		'title' => __( 'Podpora', 'baspa' ),
		'text'  => __( 'Se vším vám poradíme', 'baspa' ),
		'icon'  => 'support',
		'image' => 'hp-benefit-podpora.png',
	),
	array(
		'title' => __( 'Servis', 'baspa' ),
		'text'  => __( 'Jsme tu pro vás 24/7', 'baspa' ),
		'icon'  => 'service',
		'image' => 'hp-benefit-servis.png',
	),
);

$benefits          = array();
$raw_benefits      = function_exists( 'arctic_meta_fieldset_rows' ) ? arctic_meta_fieldset_rows( $post_id, 'homepage_benefits', array( 'title', 'text', 'icon' ) ) : array();
$benefit_image_ids = function_exists( 'arctic_meta_attachment_ids' ) ? arctic_meta_attachment_ids( $post_id, 'homepage_benefit_images' ) : array();

foreach ( $raw_benefits as $index => $raw_benefit ) {
	if ( !is_array( $raw_benefit ) ) {
		continue;
	}

	$title = trim( wp_strip_all_tags( (string) ( $raw_benefit['title'] ?? '' ) ) );
	$text  = trim( wp_strip_all_tags( (string) ( $raw_benefit['text'] ?? '' ) ) );

	if ( '' === $title && '' === $text ) {
		continue;
	}

	$icon = sanitize_title( (string) ( $raw_benefit['icon'] ?? '' ) );
	if ( '' === $icon ) {
		$icon = (string) ( $default_benefits[ $index ]['icon'] ?? 'box' );
	}

	$benefits[] = array(
		'title'    => $title,
		'text'     => $text,
		'icon'     => $icon,
		'image_id' => (int) ( $benefit_image_ids[ $index ] ?? 0 ),
		'image'    => (string) ( $default_benefits[ $index ]['image'] ?? '' ),
	);
}

$source = 'homepage-meta';

if ( empty( $benefits ) ) {
	if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() ) {
		return;
	}

	$source = 'figma-fallback';
	foreach ( $default_benefits as $default_benefit ) {
		$benefits[] = array_merge( $default_benefit, array( 'image_id' => 0 ) );
	}
}
?>

<section class="f-section f-section--arctic-benefits" data-content-source="<?php echo esc_attr( $source ); ?>">
	<h2 class="screen-reader-text"><?php echo esc_html__( 'Výhody Arctic Spas', 'baspa' ); ?></h2>
	<div class="f-section__container a-container">
		<div class="f-arctic-benefits">
			<?php foreach ( $benefits as $benefit ) { ?>
				<article class="f-arctic-benefit" data-content-source="<?php echo esc_attr( $source ); ?>">
					<?php
					$icon_class = 'f-arctic-benefit__icon f-arctic-benefit__icon--' . sanitize_html_class( (string) $benefit['icon'] );

					if ( !empty( $benefit['image_id'] ) ) {
						echo wp_get_attachment_image( (int) $benefit['image_id'], 'full', false, array(
							'class'    => $icon_class,
							'alt'      => '',
							'loading'  => 'eager',
							'decoding' => 'async',
						) );
					} elseif ( !empty( $benefit['image'] ) && function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ) {
						?>
						<img class="<?php echo esc_attr( $icon_class ); ?>"
						     src="<?php echo esc_url( content_url( 'uploads/import/figma/' . $benefit['image'] ) ); ?>"
						     alt=""
						     loading="eager"
						     decoding="async">
					<?php } ?>
					<div>
						<h3><?php echo esc_html( $benefit['title'] ); ?></h3>
						<p><?php echo esc_html( $benefit['text'] ); ?></p>
					</div>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
