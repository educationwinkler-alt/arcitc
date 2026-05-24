<?php

/**
 * Single Navigation
 */

$price        = get_post_meta( get_the_ID(), 'product_price_text', true );
$price_suffix = get_post_meta( get_the_ID(), 'product_price_suffix', true );
?>

<div class="f-links f-links--sticky f-links--product">
	<div class="f-links__container a-container">
		<nav class="f-links__navigation js-links__navigation" aria-label="<?php echo esc_attr_x( 'Product Navigation', 'navigation', 'baspa' ); ?>">
			<ul>
				<li><a href="#konfigurace"><?php echo esc_html__( 'Konfigurace', 'baspa' ); ?></a></li>
				<li><a href="#barvy"><?php echo esc_html__( 'Barvy', 'baspa' ); ?></a></li>
				<li><a href="#vyhody"><?php echo esc_html__( 'Výhody', 'baspa' ); ?></a></li>
				<li><a href="#volitelna-vybava"><?php echo esc_html__( 'Volitelná výbava', 'baspa' ); ?></a></li>
					<li><a href="#<?php echo sanitize_title( esc_attr_x( 'references', 'anchor', 'baspa' ) ); ?>"><?php echo esc_html__( 'Příklady realizací', 'baspa' ); ?></a></li>
			</ul>
		</nav>

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
