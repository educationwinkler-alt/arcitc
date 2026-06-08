<?php

/**
 * Product Acrylic Colors
 */

$product_id      = get_the_ID();
$color_options   = function_exists( 'arctic_product_get_color_options' ) ? arctic_product_get_color_options( $product_id, 'shell' ) : array();
$cabinet_options = function_exists( 'arctic_product_get_color_options' ) ? arctic_product_get_color_options( $product_id, 'cabinet' ) : array();
$section_title   = !empty( $cabinet_options )
	? _x( 'Vyberte si barvu skořepiny a kabinetu', 'product colors', 'baspa' )
	: _x( 'Vyberte si barvu skořepiny', 'product colors', 'baspa' );

$render_color_list = static function ( array $items ): void {
	?>
	<ul class="f-product-colors__list">
		<?php foreach ( $items as $color ) {
			$name         = (string) ( $color['name'] ?? '' );
			$image        = isset( $color['image'] ) ? absint( $color['image'] ) : 0;
			$image_url    = (string) ( $color['image_url'] ?? '' );
			$asset_source = (string) ( $color['asset_source'] ?? '' );
			$asset_status = (string) ( $color['asset_status'] ?? 'available' );
			$source       = (string) ( $color['source'] ?? 'legacy-product-meta' );
			$color_id     = isset( $color['id'] ) ? absint( $color['id'] ) : 0;
			$color_hex    = (string) ( $color['color_hex'] ?? '' );
			$color_slug   = sanitize_title( remove_accents( $name ) );
			$has_visual   = 0 !== $image || '' !== $image_url || '' !== $color_hex;
			$classes      = array( 'f-product-colors__item' );

			if ( !$has_visual ) {
				$classes[] = 'f-product-colors__item--missing';
			}
			?>
			<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			    data-color-slug="<?php echo esc_attr( $color_slug ); ?>"
			    data-color-id="<?php echo esc_attr( (string) $color_id ); ?>"
			    data-content-source="<?php echo esc_attr( $source ); ?>"
			    data-asset-status="<?php echo esc_attr( $asset_status ); ?>"
			    data-asset-source="<?php echo esc_attr( $asset_source ); ?>">
				<?php if ( !empty( $image ) ) { ?>
					<?php echo wp_get_attachment_image( $image, 'thumbnail', false, array(
						'alt'               => $name,
						'loading'           => 'lazy',
						'decoding'          => 'async',
						'data-asset-status' => $asset_status,
					) ); ?>
				<?php } elseif ( '' !== $image_url ) { ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async">
				<?php } elseif ( '' !== $color_hex ) { ?>
					<span class="f-product-colors__placeholder" aria-hidden="true" style="background-color: <?php echo esc_attr( $color_hex ); ?>"></span>
				<?php } else {
					$initial = function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1, 'UTF-8' ) : substr( $name, 0, 1 );
					?>
					<span class="f-product-colors__placeholder" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
				<?php } ?>
				<span><?php echo esc_html( $name ); ?></span>
			</li>
		<?php } ?>
	</ul>
	<?php
};

if ( !empty( $color_options ) || !empty( $cabinet_options ) ) { ?>
	<section id="barvy" class="f-section f-section--product-colors js-links__section">
		<div class="f-section__container a-container">
			<div class="f-product-colors a-stack a-gap--xs">
				<h2><?php echo esc_html( $section_title ); ?></h2>

				<?php if ( !empty( $color_options ) ) { ?>
					<h3><?php echo esc_html__( 'Barvy skořepiny', 'baspa' ); ?></h3>
					<?php $render_color_list( $color_options ); ?>
				<?php } ?>

				<?php if ( !empty( $cabinet_options ) ) { ?>
					<h3><?php echo esc_html__( 'Barvy kabinetu', 'baspa' ); ?></h3>
					<?php $render_color_list( $cabinet_options ); ?>
				<?php } ?>
			</div>
		</div>
	</section>
<?php }
