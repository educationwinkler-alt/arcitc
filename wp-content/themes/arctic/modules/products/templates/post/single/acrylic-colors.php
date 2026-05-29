<?php

/**
 * Product Acrylic Colors
 */

$colors = array_filter( get_post_meta( get_the_ID(), 'product_acrylic_colors' ) );

$normalize_image_options = static function ( array $options ): array {
	$normalized = array();

	foreach ( $options as $option ) {
		if ( !is_array( $option ) ) {
			continue;
		}

		$name  = trim( (string) ( $option['name'] ?? '' ) );
		$image = isset( $option['image'] ) ? absint( $option['image'] ) : 0;

		if ( '' === $name || 0 === $image || !wp_attachment_is_image( $image ) ) {
			continue;
		}

		$normalized[] = array(
			'name'  => $name,
			'image' => $image,
		);
	}

	return $normalized;
};

$color_options   = $normalize_image_options( get_post_meta( get_the_ID(), 'product_acrylic_color_options' ) );
$cabinet_options = $normalize_image_options( get_post_meta( get_the_ID(), 'product_cabinet_color_options' ) );
$section_title   = !empty( $cabinet_options )
	? _x( 'Vyberte si barvu skořepiny a kabinetu', 'product colors', 'baspa' )
	: _x( 'Vyberte si barvu skořepiny', 'product colors', 'baspa' );

if ( !empty( $color_options ) || !empty( $colors ) || !empty( $cabinet_options ) ) { ?>
	<section id="barvy" class="f-section f-section--product-colors js-links__section">
		<div class="f-section__container a-container">
			<div class="f-product-colors a-stack a-gap--xs">
				<h2><?php echo esc_html( $section_title ); ?></h2>

				<?php if ( !empty( $color_options ) ) { ?>
					<h3><?php echo esc_html__( 'Barvy skořepiny', 'baspa' ); ?></h3>
					<ul class="f-product-colors__list">
						<?php foreach ( $color_options as $color ) {
							$name  = $color['name'] ?? '';
							$image = isset( $color['image'] ) ? absint( $color['image'] ) : 0;
							?>
							<li>
								<?php if ( !empty( $image ) ) { ?>
									<?php echo wp_get_attachment_image( $image, 'thumbnail' ); ?>
								<?php } ?>
								<span><?php echo esc_html( $name ); ?></span>
							</li>
						<?php } ?>
					</ul>
				<?php } elseif ( !empty( $colors ) ) { ?>
					<ul class="f-product-colors__list">
						<?php foreach ( $colors as $color ) { ?>
							<li><span><?php echo esc_html( $color ); ?></span></li>
						<?php } ?>
					</ul>
				<?php } ?>

				<?php if ( !empty( $cabinet_options ) ) { ?>
					<h3><?php echo esc_html__( 'Barvy kabinetu', 'baspa' ); ?></h3>
					<ul class="f-product-colors__list">
						<?php foreach ( $cabinet_options as $color ) {
							$name  = $color['name'] ?? '';
							$image = isset( $color['image'] ) ? absint( $color['image'] ) : 0;
							?>
							<li>
								<?php if ( !empty( $image ) ) { ?>
									<?php echo wp_get_attachment_image( $image, 'thumbnail' ); ?>
								<?php } ?>
								<span><?php echo esc_html( $name ); ?></span>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</div>
		</div>
	</section>
<?php }
