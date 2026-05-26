<?php

/**
 * Single Navigation
 */

$product_id     = get_the_ID();
$price          = get_post_meta( $product_id, 'product_price_text', true );
$price_suffix   = get_post_meta( $product_id, 'product_price_suffix', true );
$is_hot_tub     = has_term( 'virivky', 'product-category', $product_id );
$is_wider_range = has_term( 'dalsi-sortiment', 'product-category', $product_id );
$nav_items      = array();

if ( function_exists( 'baspa_products_product_has_configurations' ) && baspa_products_product_has_configurations( $product_id ) ) {
	$nav_items['#konfigurace'] = __( 'Konfigurace', 'baspa' );
}

if ( !empty( get_post_meta( $product_id, 'product_acrylic_colors' ) ) || !empty( get_post_meta( $product_id, 'product_acrylic_color_options' ) ) ) {
	$nav_items['#barvy'] = __( 'Barvy', 'baspa' );
}

if ( $is_hot_tub ) {
	$nav_items['#vyhody']            = __( 'Výhody', 'baspa' );
	$nav_items['#volitelna-vybava'] = __( 'Volitelná výbava', 'baspa' );
}

if ( !$is_wider_range ) {
	$nav_items['#' . sanitize_title( esc_attr_x( 'references', 'anchor', 'baspa' ) )] = __( 'Příklady realizací', 'baspa' );
}
?>

<div class="f-links f-links--sticky f-links--product">
	<div class="f-links__container a-container">
		<?php if ( !empty( $nav_items ) ) { ?>
			<nav class="f-links__navigation js-links__navigation" aria-label="<?php echo esc_attr_x( 'Product Navigation', 'navigation', 'baspa' ); ?>">
				<ul>
					<?php foreach ( $nav_items as $href => $label ) { ?>
						<li><a href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $label ); ?></a></li>
					<?php } ?>
				</ul>
			</nav>
		<?php } ?>

		<div class="f-links__cta">
			<?php if ( !empty( $price ) ) { ?>
				<div class="f-links__price">
					<strong><?php echo esc_html( $price ); ?></strong>
					<?php if ( !empty( $price_suffix ) ) { ?>
						<span><?php echo esc_html( $price_suffix ); ?></span>
					<?php } ?>
				</div>
			<?php } ?>
			<?php get_template_part( 'templates/button/contact', '', array(
				'text' => __( 'Nezávazná konzultace', 'baspa' ),
			) ); ?>
		</div>
	</div>
</div>
