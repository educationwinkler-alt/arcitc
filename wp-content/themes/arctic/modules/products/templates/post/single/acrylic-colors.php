<?php

/**
 * Product Acrylic Colors
 */

$product_id     = get_the_ID();
$colors         = array_values( array_filter( get_post_meta( $product_id, 'product_acrylic_colors' ) ) );
$cabinet_colors = array_values( array_filter( get_post_meta( $product_id, 'product_cabinet_colors' ) ) );

if ( empty( $colors ) && !has_term( 'dalsi-sortiment', 'product-category', $product_id ) ) {
	$colors = array( 'Dakota', 'Kalahari', 'Odyssey', 'Platinum Swirl', 'Espresso' );
}

$resolve_import_asset = static function ( array $candidates ): array {
	foreach ( $candidates as $candidate ) {
		$relative = trim( (string) ( $candidate['path'] ?? '' ), '/\\' );
		$status   = (string) ( $candidate['status'] ?? 'available' );

		if ( '' === $relative ) {
			continue;
		}

		$absolute = WP_CONTENT_DIR . '/uploads/import/' . $relative;
		if ( file_exists( $absolute ) ) {
			return array(
				'image_url'     => content_url( 'uploads/import/' . $relative ),
				'asset_source'   => 'uploads/import/' . $relative,
				'asset_status'   => $status,
			);
		}
	}

	return array(
		'image_url'     => '',
		'asset_source'   => '',
		'asset_status'   => 'WAITING_ON_OWNER',
	);
};

$resolve_acrylic_asset = static function ( string $name ) use ( $resolve_import_asset ): array {
	$slug = sanitize_title( remove_accents( $name ) );
	$map  = array(
		'dakota' => array(
			array( 'path' => 'owner-swatches/acrylic-dakota.jpg', 'status' => 'owner-supplied' ),
			array( 'path' => 'figma/color-dakota.png', 'status' => 'figma-fallback' ),
		),
		'espresso' => array(
			array( 'path' => 'owner-swatches/acrylic-espresso.jpg', 'status' => 'owner-supplied' ),
			array( 'path' => 'figma/color-espresso.png', 'status' => 'figma-fallback' ),
		),
		'kalahari' => array(
			array( 'path' => 'owner-swatches/acrylic-kalahari.jpg', 'status' => 'owner-supplied' ),
			array( 'path' => 'figma/color-kalahari.png', 'status' => 'figma-fallback' ),
		),
		'odyssey' => array(
			array( 'path' => 'owner-swatches/acrylic-odyssey.jpg', 'status' => 'owner-supplied' ),
			array( 'path' => 'figma/color-odyssey.png', 'status' => 'figma-fallback' ),
		),
		'platinum-swirl' => array(
			array( 'path' => 'figma/color-platinum-swirl.png', 'status' => 'figma-fallback' ),
		),
	);

	return $resolve_import_asset( $map[ $slug ] ?? array() );
};

$resolve_cabinet_asset = static function ( string $name ) use ( $resolve_import_asset ): array {
	$slug = sanitize_title( remove_accents( $name ) );

	if ( false !== strpos( $slug, 'cedr' ) || false !== strpos( $slug, 'cedar' ) ) {
		return $resolve_import_asset( array(
			array( 'path' => 'figma/cabinet-cedar.png', 'status' => 'figma-fallback' ),
		) );
	}

	if ( false !== strpos( $slug, 'bezudrzb' ) || false !== strpos( $slug, 'maintenance' ) ) {
		return $resolve_import_asset( array(
			array( 'path' => 'figma/cabinet-maintenance-free.png', 'status' => 'figma-fallback' ),
		) );
	}

	return array(
		'image_url'     => '',
		'asset_source'   => '',
		'asset_status'   => 'WAITING_ON_OWNER',
	);
};

$normalize_image_options = static function ( array $options, callable $fallback_resolver ): array {
	$normalized = array();

	foreach ( $options as $option ) {
		if ( !is_array( $option ) ) {
			continue;
		}

		$name  = trim( (string) ( $option['name'] ?? '' ) );
		$image = isset( $option['image'] ) ? absint( $option['image'] ) : 0;

		if ( '' === $name ) {
			continue;
		}

		$asset = array(
			'image_url'     => '',
			'asset_source'   => '',
			'asset_status'   => 'owner-supplied',
		);

		if ( 0 === $image || !wp_attachment_is_image( $image ) ) {
			$image = 0;
			$asset = $fallback_resolver( $name );
		}

		$normalized[] = array(
			'name'         => $name,
			'image'        => $image,
			'image_url'    => $asset['image_url'] ?? '',
			'asset_source' => $asset['asset_source'] ?? '',
			'asset_status' => $asset['asset_status'] ?? 'available',
		);
	}

	return $normalized;
};

$merge_named_options = static function ( array $image_options, array $names, callable $fallback_resolver ): array {
	if ( empty( $names ) ) {
		return $image_options;
	}

	$by_slug = array();
	foreach ( $image_options as $option ) {
		$slug = sanitize_title( remove_accents( (string) ( $option['name'] ?? '' ) ) );
		if ( '' !== $slug ) {
			$by_slug[ $slug ] = $option;
		}
	}

	$merged = array();
	$used   = array();
	foreach ( $names as $name ) {
		$name = trim( (string) $name );
		$slug = sanitize_title( remove_accents( $name ) );

		if ( '' === $slug ) {
			continue;
		}

		if ( isset( $by_slug[ $slug ] ) ) {
			$merged[]      = $by_slug[ $slug ];
			$used[ $slug ] = true;
			continue;
		}

		$asset = $fallback_resolver( $name );
		$merged[] = array(
			'name'         => $name,
			'image'        => 0,
			'image_url'    => $asset['image_url'] ?? '',
			'asset_source' => $asset['asset_source'] ?? '',
			'asset_status' => $asset['asset_status'] ?? 'WAITING_ON_OWNER',
		);
		$used[ $slug ] = true;
	}

	foreach ( $image_options as $option ) {
		$slug = sanitize_title( remove_accents( (string) ( $option['name'] ?? '' ) ) );
		if ( '' !== $slug && empty( $used[ $slug ] ) ) {
			$merged[] = $option;
		}
	}

	return $merged;
};

$color_options   = $merge_named_options( $normalize_image_options( get_post_meta( get_the_ID(), 'product_acrylic_color_options' ), $resolve_acrylic_asset ), $colors, $resolve_acrylic_asset );
$cabinet_options = $merge_named_options( $normalize_image_options( get_post_meta( get_the_ID(), 'product_cabinet_color_options' ), $resolve_cabinet_asset ), $cabinet_colors, $resolve_cabinet_asset );
$section_title   = !empty( $cabinet_options )
	? _x( 'Vyberte si barvu skořepiny a kabinetu', 'product colors', 'baspa' )
	: _x( 'Vyberte si barvu skořepiny', 'product colors', 'baspa' );

$render_color_list = static function ( array $items ): void {
	?>
	<ul class="f-product-colors__list">
		<?php foreach ( $items as $color ) {
			$name         = $color['name'] ?? '';
			$image        = isset( $color['image'] ) ? absint( $color['image'] ) : 0;
			$image_url    = (string) ( $color['image_url'] ?? '' );
			$asset_source = (string) ( $color['asset_source'] ?? '' );
			$asset_status = (string) ( $color['asset_status'] ?? 'available' );
			$color_slug   = sanitize_title( remove_accents( (string) $name ) );
			$has_visual   = 0 !== $image || '' !== $image_url;
			$classes      = array( 'f-product-colors__item' );

			if ( !$has_visual ) {
				$classes[] = 'f-product-colors__item--missing';
			}
			?>
			<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-color-slug="<?php echo esc_attr( $color_slug ); ?>" data-asset-status="<?php echo esc_attr( $asset_status ); ?>" data-asset-source="<?php echo esc_attr( $asset_source ); ?>">
				<?php if ( !empty( $image ) ) { ?>
					<?php echo wp_get_attachment_image( $image, 'thumbnail', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
				<?php } elseif ( '' !== $image_url ) { ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async">
				<?php } else {
					$initial = function_exists( 'mb_substr' ) ? mb_substr( (string) $name, 0, 1, 'UTF-8' ) : substr( (string) $name, 0, 1 );
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
