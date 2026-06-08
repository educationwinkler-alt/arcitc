<?php

/**
 * Figma desktop mega navigation panels.
 */

$menus = function_exists( 'arctic_get_desktop_mega_menus' ) ? arctic_get_desktop_mega_menus() : array();
$promo = function_exists( 'baspa_offers_promo_data' ) ? baspa_offers_promo_data() : array(
	'archive_url' => home_url( '/akcni-nabidky/' ),
	'short_title' => __( 'Akční nabídky', 'baspa' ),
	'button_text' => __( 'Zobrazit nabídku', 'baspa' ),
	'image_id'    => 0,
);

$promo_url      = !empty( $promo[ 'archive_url' ] ) ? (string) $promo[ 'archive_url' ] : home_url( '/akcni-nabidky/' );
$promo_title    = !empty( $promo[ 'short_title' ] ) ? (string) $promo[ 'short_title' ] : __( 'Akční nabídky', 'baspa' );
$promo_button   = !empty( $promo[ 'button_text' ] ) ? (string) $promo[ 'button_text' ] : __( 'Zobrazit nabídku', 'baspa' );
$promo_image_id = !empty( $promo[ 'promo_image_id' ] ) ? (int) $promo[ 'promo_image_id' ] : ( !empty( $promo[ 'image_id' ] ) ? (int) $promo[ 'image_id' ] : 0 );
$promo_source   = !empty( $promo[ 'id' ] ) ? 'offer-cpt' : 'computed-fallback';
$promo_mark     = trim( wp_strip_all_tags( $promo_title ) );
$promo_mark     = '' !== $promo_mark ? ( function_exists( 'mb_substr' ) ? mb_substr( $promo_mark, 0, 1 ) : substr( $promo_mark, 0, 1 ) ) : '';
$promo_mark     = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $promo_mark ) : strtoupper( $promo_mark );

if ( empty( $menus ) ) {
	return;
}
?>

<div class="f-mega-nav">
	<?php foreach ( $menus as $menu ) { ?>
		<?php $column_count = max( 1, (int) ( $menu[ 'column_count' ] ?? count( $menu[ 'columns' ] ?? array() ) ) ); ?>
		<div class="f-mega-menu f-mega-menu--<?php echo esc_attr( $menu[ 'key' ] ); ?> f-mega-menu--columns-<?php echo esc_attr( (string) $column_count ); ?>" data-product-count="<?php echo esc_attr( (string) ( $menu[ 'product_count' ] ?? 0 ) ); ?>">
			<div class="f-mega-menu__grid">
				<?php foreach ( $menu[ 'columns' ] as $column ) { ?>
					<section class="f-mega-menu__column" aria-label="<?php echo esc_attr( $column[ 'label' ] ); ?>">
						<h2><?php echo esc_html( $column[ 'label' ] ); ?></h2>

						<div class="f-mega-menu__products">
							<?php if ( !empty( $column[ 'products' ] ) ) { ?>
								<?php foreach ( $column[ 'products' ] as $product ) { ?>
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
								<a class="f-mega-menu__product f-mega-menu__product--fallback" href="<?php echo esc_url( $menu[ 'url' ] ); ?>">
									<span class="f-mega-menu__thumb" aria-hidden="true"></span>
									<span><?php echo esc_html__( 'Prohlédnout nabídku', 'baspa' ); ?></span>
								</a>
							<?php } ?>
						</div>
					</section>
				<?php } ?>

				<a class="f-mega-menu__promo" href="<?php echo esc_url( $promo_url ); ?>" data-content-source="<?php echo esc_attr( $promo_source ); ?>" data-offer-id="<?php echo esc_attr( (string) ( $promo[ 'id' ] ?? 0 ) ); ?>">
					<?php if ( $promo_image_id > 0 ) {
						echo wp_get_attachment_image( $promo_image_id, 'medium', false, array(
							'alt'               => '',
							'loading'           => 'lazy',
							'decoding'          => 'async',
							'data-asset-status' => 'admin-offer-promo',
						) );
					} else { ?>
						<span class="f-mega-menu__promo-placeholder" data-asset-status="admin-empty" aria-hidden="true">
							<?php echo esc_html( $promo_mark ); ?>
						</span>
					<?php } ?>
					<strong><?php echo esc_html( $promo_title ); ?></strong>
					<span><?php echo esc_html( $promo_button ); ?></span>
				</a>
			</div>
		</div>
	<?php } ?>
</div>
