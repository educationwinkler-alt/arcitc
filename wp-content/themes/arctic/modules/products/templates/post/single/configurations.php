<?php

/**
 * Product Configurations
 */

$configurations = get_post_meta( get_the_ID(), 'product_configurations' );
$configurations = array_filter( $configurations, static function ( $configuration ) {
	return is_array( $configuration ) && !empty( array_filter( $configuration ) );
} );

if ( !empty( $configurations ) ) { ?>
	<div id="konfigurace" class="f-product-configurations a-stack a-gap--s">
		<h2><?php echo esc_html_x( 'Konfigurace', 'product configurations', 'baspa' ); ?></h2>

		<div class="f-product-configurations__grid">
			<?php foreach ( $configurations as $configuration ) {
				$name        = $configuration['name'] ?? '';
				$image       = isset( $configuration['image'] ) ? absint( $configuration['image'] ) : 0;
				$price       = $configuration['price'] ?? '';
				$seats       = $configuration['seats'] ?? '';
				$jets        = $configuration['jets'] ?? '';
				$pumps       = $configuration['pumps'] ?? '';
				$dimensions  = $configuration['dimensions'] ?? '';
				$description = $configuration['description'] ?? '';
				?>

				<article class="f-product-configuration">
					<div class="f-product-configuration__thumb" aria-hidden="<?php echo empty( $image ) ? 'true' : 'false'; ?>">
						<?php
						if ( !empty( $image ) ) {
							echo wp_get_attachment_image( $image, 'medium' );
						}
						?>
					</div>
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
