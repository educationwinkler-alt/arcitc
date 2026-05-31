<?php

/**
 * Product Configurations
 */

$product_id      = get_the_ID();
$configurations  = function_exists( 'baspa_products_get_configurations' ) ? baspa_products_get_configurations( $product_id ) : array();

if ( !empty( $configurations ) ) { ?>
	<div id="konfigurace" class="f-product-configurations a-stack a-gap--s">
		<h2><?php echo esc_html_x( 'Konfigurace', 'product configurations', 'baspa' ); ?></h2>

		<div class="f-product-configurations__grid">
			<?php foreach ( $configurations as $index => $configuration ) {
				$name        = $configuration['name'] ?? '';
				$image       = isset( $configuration['image_id'] ) ? absint( $configuration['image_id'] ) : 0;
				$has_image   = $image > 0 && wp_attachment_is_image( $image );
				$fallback    = $index % 2 === 0 ? 'detail-config-prestige.png' : 'detail-config-signature.png';
				$fallback_path = WP_CONTENT_DIR . '/uploads/import/figma/' . $fallback;
				$fallback_url = file_exists( $fallback_path ) ? content_url( 'uploads/import/figma/' . $fallback ) : '';
				$price       = !empty( $configuration['price_text'] ) ? $configuration['price_text'] : ( $configuration['price'] ?? '' );
				$seats       = $configuration['seats'] ?? '';
				$jets        = $configuration['jets'] ?? '';
				$pumps       = $configuration['pumps'] ?? '';
				$dimensions  = $configuration['dimensions'] ?? '';
				$description = $configuration['notes'] ?? '';
				$item_class  = array( 'f-product-configuration' );
				$asset_status = $has_image ? 'product-image' : ( !empty( $fallback_url ) ? 'figma-fallback' : 'WAITING_ON_OWNER' );

				if ( !$has_image && empty( $fallback_url ) ) {
					$item_class[] = 'f-product-configuration--no-media';
				}
				?>

				<article class="<?php echo esc_attr( implode( ' ', array_filter( $item_class ) ) ); ?>" data-asset-status="<?php echo esc_attr( $asset_status ); ?>">
					<?php if ( $has_image || !empty( $fallback_url ) ) { ?>
						<div class="f-product-configuration__thumb" data-asset-status="<?php echo esc_attr( $asset_status ); ?>">
							<?php if ( $has_image ) { ?>
								<?php echo wp_get_attachment_image( $image, 'medium' ); ?>
							<?php } else { ?>
								<img src="<?php echo esc_url( $fallback_url ); ?>" alt="" loading="lazy" decoding="async">
							<?php } ?>
						</div>
					<?php } ?>
					<div class="f-product-configuration__content">
						<?php if ( !empty( $name ) ) { ?>
							<h3><?php echo esc_html( $name ); ?></h3>
						<?php } ?>

						<?php if ( !empty( $price ) ) { ?>
							<p class="f-product-configuration__price"><?php echo esc_html( $price ); ?></p>
						<?php } ?>

						<dl>
							<?php if ( !empty( $seats ) ) { ?>
								<div><dt><?php echo esc_html_x( 'Počet míst', 'product configurations', 'baspa' ); ?></dt><dd><?php echo esc_html( $seats ); ?></dd></div>
							<?php } ?>
							<?php if ( !empty( $jets ) ) { ?>
								<div><dt><?php echo esc_html_x( 'Trysky', 'product configurations', 'baspa' ); ?></dt><dd><?php echo esc_html( $jets ); ?></dd></div>
							<?php } ?>
							<?php if ( !empty( $pumps ) ) { ?>
								<div><dt><?php echo esc_html_x( 'Čerpadla', 'product configurations', 'baspa' ); ?></dt><dd><?php echo esc_html( $pumps ); ?></dd></div>
							<?php } ?>
							<?php if ( !empty( $dimensions ) ) { ?>
								<div><dt><?php echo esc_html_x( 'Rozměry', 'product configurations', 'baspa' ); ?></dt><dd><?php echo esc_html( $dimensions ); ?></dd></div>
							<?php } ?>
						</dl>

						<?php if ( !empty( $description ) ) { ?>
							<p><?php echo esc_html( $description ); ?></p>
						<?php } ?>
					</div>
				</article>
			<?php } ?>
		</div>
	</div>
<?php }
