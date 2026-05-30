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
$title_prefix  = __( 'Venkovní vířivka ', 'baspa' );

if ( has_term( 'swimspa', 'product-category', get_the_ID() ) || has_term( 'swimspa', 'product-kind', get_the_ID() ) ) {
	$title_prefix = __( 'Celoroční bazén ', 'baspa' );
} elseif ( has_term( 'dalsi-sortiment', 'product-category', get_the_ID() ) ) {
	$title_prefix = '';
}

$heading_class   = array( 'f-heading', 'f-heading--product-detail' );
$heading_class[] = !empty( $images ) || has_post_thumbnail() || has_header_image() ? 'f-heading--background' : '';
if ( !empty( $images ) ) {
	$heading_class[] = 'f-heading--gallery';
}
if ( get_post_field( 'post_name', get_the_ID() ) === 'timberwolf' ) {
	$heading_class[] = 'f-heading--timberwolf';
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
					<span><strong><?php echo esc_html( preg_replace( '/[^0-9+]/', '', $seats[0] ) ?: $seats[0] ); ?></strong><?php echo esc_html__( 'míst', 'baspa' ); ?></span>
				<?php } ?>
				<?php if ( !empty( $dimensions ) ) { ?>
					<span><strong><?php echo esc_html( $dimensions[0] ); ?></strong><?php echo esc_html__( 'rozměr', 'baspa' ); ?></span>
				<?php } ?>
				<?php if ( !empty( $water_volume ) ) { ?>
					<span><strong><?php echo esc_html( preg_replace( '/[^0-9]/', '', $water_volume[0] ) ?: $water_volume[0] ); ?></strong><?php echo esc_html__( 'litrů', 'baspa' ); ?></span>
				<?php } elseif ( !empty( $jets ) ) { ?>
					<span><strong><?php echo esc_html( preg_replace( '~[^0-9/]~', '', $jets[0] ) ?: $jets[0] ); ?></strong><?php echo esc_html__( 'trysek / čerpadel', 'baspa' ); ?></span>
				<?php } ?>
			</div>
		</div>
	</div>

	<?php
	if ( !empty( $images ) ) {
		get_template_part( 'templates/image/gallery', 'slideshow', array(
			'meta_key'   => 'product_images',
			'image_size' => 'huge',
			'image_ids'  => $images,
		) );
	} else {
		get_template_part( 'templates/image/background' );
	} ?>
</header>
