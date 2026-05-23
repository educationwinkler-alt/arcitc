<?php

/**
 * Product Acrylic Colors
 */

$colors          = array_filter( get_post_meta( get_the_ID(), 'product_acrylic_colors' ) );
$color_options   = array_filter( get_post_meta( get_the_ID(), 'product_acrylic_color_options' ), 'is_array' );
$cabinet_options = array_filter( get_post_meta( get_the_ID(), 'product_cabinet_color_options' ), 'is_array' );

if ( !empty( $color_options ) || !empty( $colors ) || !empty( $cabinet_options ) ) { ?>
	<section id="barvy" class="f-section f-section--product-colors js-links__section">
		<div class="f-section__container a-container">
			<div class="f-product-colors a-stack a-gap--xs">
				<h2><?php echo esc_html_x( 'Vyberte si barvu skořepiny a kabinetu', 'product colors', 'baspa' ); ?></h2>

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
