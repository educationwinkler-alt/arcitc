<?php

/**
 * Figma desktop mega navigation panels.
 */

$menus = function_exists( 'arctic_get_desktop_mega_menus' ) ? arctic_get_desktop_mega_menus() : array();

if ( empty( $menus ) ) {
	return;
}
?>

<div class="f-mega-nav">
	<?php foreach ( $menus as $menu ) { ?>
		<div class="f-mega-menu f-mega-menu--<?php echo esc_attr( $menu['key'] ); ?>" data-product-count="<?php echo esc_attr( (string) ( $menu['product_count'] ?? 0 ) ); ?>">
			<div class="f-mega-menu__grid">
				<?php foreach ( $menu['columns'] as $column ) { ?>
					<section class="f-mega-menu__column" aria-label="<?php echo esc_attr( $column['label'] ); ?>">
						<h2><?php echo esc_html( $column['label'] ); ?></h2>

						<div class="f-mega-menu__products">
							<?php if ( !empty( $column['products'] ) ) { ?>
								<?php foreach ( $column['products'] as $product ) { ?>
									<?php
									$product_id    = $product instanceof WP_Post ? $product->ID : 0;
									$product_title = arctic_mega_menu_format_title( get_the_title( $product ) );
									$thumbnail_id  = $product_id > 0 ? absint( get_post_thumbnail_id( $product_id ) ) : 0;
									$has_thumbnail = $thumbnail_id > 0 && wp_attachment_is_image( $thumbnail_id );
									$fallback_mark = function_exists( 'mb_substr' ) ? mb_substr( $product_title, 0, 1 ) : substr( $product_title, 0, 1 );
									?>
									<a class="f-mega-menu__product" href="<?php echo esc_url( get_permalink( $product ) ); ?>">
										<span class="f-mega-menu__thumb<?php echo $has_thumbnail ? '' : ' f-mega-menu__thumb--missing'; ?>" data-product-media="<?php echo $has_thumbnail ? 'featured-image' : 'missing'; ?>">
											<?php if ( $has_thumbnail ) {
												echo wp_get_attachment_image( $thumbnail_id, 'thumbnail', false, array(
													'loading'  => 'lazy',
													'decoding' => 'async',
												) );
											} else { ?>
												<span aria-hidden="true"><?php echo esc_html( $fallback_mark ); ?></span>
											<?php } ?>
										</span>
										<span><?php echo esc_html( $product_title ); ?></span>
									</a>
								<?php } ?>
							<?php } else { ?>
								<a class="f-mega-menu__product f-mega-menu__product--fallback" href="<?php echo esc_url( $menu['url'] ); ?>">
									<span class="f-mega-menu__thumb" aria-hidden="true"></span>
									<span><?php echo esc_html__( 'Prohlédnout nabídku', 'baspa' ); ?></span>
								</a>
							<?php } ?>
						</div>
					</section>
				<?php } ?>

				<a class="f-mega-menu__promo" href="<?php echo esc_url( $menu['url'] ); ?>">
					<img src="<?php echo esc_url( content_url( 'uploads/import/figma/hp-fixed-banner-product.png' ) ); ?>" alt="" loading="lazy" decoding="async">
					<strong><?php echo esc_html__( 'Akční nabídka skladových vířivek', 'baspa' ); ?></strong>
					<span><?php echo esc_html__( 'Zobrazit nabídku', 'baspa' ); ?></span>
				</a>
			</div>
		</div>
	<?php } ?>
</div>
