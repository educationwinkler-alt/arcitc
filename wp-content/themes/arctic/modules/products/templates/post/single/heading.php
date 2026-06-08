<?php

/**
 * Product Heading
 */

$description  = get_post_meta( get_the_ID(), 'product_description', true );
$images       = array_values( array_filter( array_map( 'absint', get_post_meta( get_the_ID(), 'product_images' ) ), static function ( int $image_id ): bool {
	return $image_id > 0 && wp_attachment_is_image( $image_id );
} ) );
$title_short  = get_post_meta( get_the_ID(), 'product_title_short', true );
$title        = !empty( $title_short ) ? $title_short : get_the_title();
$series       = wp_get_post_terms( get_the_ID(), 'product-series', array( 'fields' => 'names' ) );
$seats        = get_post_meta( get_the_ID(), 'product_seats', false );
$jets         = get_post_meta( get_the_ID(), 'product_nozzles', false );
$dimensions   = get_post_meta( get_the_ID(), 'product_dimensions_external', false );
$water_volume = get_post_meta( get_the_ID(), 'product_water_volume', false );
$thumbnail_id = (int) get_post_thumbnail_id();
$hero_media   = function_exists( 'arctic_hero_media_from_post' )
	? arctic_hero_media_from_post( get_the_ID(), 'product_hero', $thumbnail_id, array(
		'source'     => 'product-hero',
		'image_size' => get_template() . '-huge',
	) )
	: array( 'type' => 'none' );
$has_hero_video = 'video' === ( $hero_media['type'] ?? 'none' );
$format_hero_dimensions = static function ( string $value ): string {
	$value      = trim( wp_strip_all_tags( $value ) );
	$times      = html_entity_decode( '&times;', ENT_QUOTES, 'UTF-8' );
	$normalized = str_replace( array( $times, 'X' ), 'x', remove_accents( $value ) );

	if ( preg_match( '~^\s*([0-9]+(?:[.,][0-9]+)?)\s*x\s*([0-9]+(?:[.,][0-9]+)?)\s*cm\s*,?\s*vyska\s*:?\s*([0-9]+(?:[.,][0-9]+)?)\s*cm\s*$~i', $normalized, $matches ) ) {
		return sprintf( '%s x %s x %s cm', $matches[1], $matches[2], $matches[3] );
	}

	if ( preg_match( '~^\s*([0-9]+(?:[.,][0-9]+)?)\s*x\s*([0-9]+(?:[.,][0-9]+)?)\s*x\s*([0-9]+(?:[.,][0-9]+)?)\s*cm\s*$~i', $normalized, $matches ) ) {
		return sprintf( '%s x %s x %s cm', $matches[1], $matches[2], $matches[3] );
	}

	return $value;
};
$title_prefix  = __( 'Venkovní vířivka ', 'baspa' );

if ( has_term( 'swimspa', 'product-category', get_the_ID() ) || has_term( 'swimspa', 'product-kind', get_the_ID() ) ) {
	$title_prefix = __( 'Celoroční bazén ', 'baspa' );
} elseif ( has_term( 'dalsi-sortiment', 'product-category', get_the_ID() ) ) {
	$title_prefix = '';
}

$heading_class   = array( 'f-heading', 'f-heading--product-detail' );
$heading_class[] = $has_hero_video || !empty( $images ) || has_post_thumbnail() || has_header_image() ? 'f-heading--background' : '';
if ( !empty( $images ) && !$has_hero_video ) {
	$heading_class[] = 'f-heading--gallery';
}
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--justify-start a-gap--s">
			<?php if ( !empty( $series ) ) { ?>
				<div class="f-product-hero__series"><?php echo esc_html( implode( ' / ', $series ) ); ?></div>
			<?php } ?>

			<h1><?php echo esc_html( $title_prefix . $title ); ?></h1>

			<?php if ( !empty( $description ) ) { ?>
				<div class="f-heading__description">
					<?php echo wp_kses_post( wp_trim_words( strip_tags( $description ), 26, '...' ) ); ?>
				</div>
			<?php } ?>

			<div class="f-product-hero__facts">
				<?php if ( !empty( $seats ) ) { ?>
					<span class="f-product-hero__fact f-product-hero__fact--seats">
						<span class="f-product-hero__fact-icon" aria-hidden="true"><?php get_template_part( 'images/icon/param/seats' ); ?></span>
						<span class="f-product-hero__fact-copy">
							<span class="f-product-hero__fact-label"><?php echo esc_html__( 'Počet osob', 'baspa' ); ?></span>
							<strong><?php echo esc_html( preg_replace( '/[^0-9+]/', '', $seats[0] ) ?: $seats[0] ); ?></strong>
						</span>
					</span>
				<?php } ?>
				<?php if ( !empty( $dimensions ) ) { ?>
					<span class="f-product-hero__fact f-product-hero__fact--dimensions">
						<span class="f-product-hero__fact-icon" aria-hidden="true"><?php get_template_part( 'images/icon/param/dimensions-external' ); ?></span>
						<span class="f-product-hero__fact-copy">
							<span class="f-product-hero__fact-label"><?php echo esc_html__( 'Rozměry', 'baspa' ); ?></span>
							<strong><?php echo esc_html( $format_hero_dimensions( (string) $dimensions[0] ) ); ?></strong>
						</span>
					</span>
				<?php } ?>
				<?php if ( !empty( $water_volume ) ) { ?>
					<span class="f-product-hero__fact f-product-hero__fact--water">
						<span class="f-product-hero__fact-icon" aria-hidden="true"><?php get_template_part( 'images/icon/param/water-volume' ); ?></span>
						<span class="f-product-hero__fact-copy">
							<span class="f-product-hero__fact-label"><?php echo esc_html__( 'Objem vody', 'baspa' ); ?></span>
							<strong><?php echo esc_html( preg_replace( '/[^0-9]/', '', $water_volume[0] ) ?: $water_volume[0] ); ?></strong>
						</span>
					</span>
				<?php } elseif ( !empty( $jets ) ) { ?>
					<span class="f-product-hero__fact f-product-hero__fact--jets">
						<span class="f-product-hero__fact-icon" aria-hidden="true"><?php get_template_part( 'images/icon/param/nozzles' ); ?></span>
						<span class="f-product-hero__fact-copy">
							<span class="f-product-hero__fact-label"><?php echo esc_html__( 'Trysky', 'baspa' ); ?></span>
							<strong><?php echo esc_html( preg_replace( '~[^0-9/]~', '', $jets[0] ) ?: $jets[0] ); ?></strong>
						</span>
					</span>
				<?php } ?>
			</div>
		</div>
	</div>

	<?php
	if ( $has_hero_video && function_exists( 'arctic_render_hero_media' ) ) {
		arctic_render_hero_media(
			$hero_media,
			array( 'f-background', 'f-background__image', 'f-background__image--thumb', 'a-image--cover' )
		);
	} else if ( !empty( $images ) ) {
		get_template_part( 'templates/image/gallery', 'slideshow', array(
			'meta_key'   => 'product_images',
			'image_size' => 'huge',
			'image_ids'  => $images,
		) );
	} else {
		get_template_part( 'templates/image/background' );
	} ?>
</header>
