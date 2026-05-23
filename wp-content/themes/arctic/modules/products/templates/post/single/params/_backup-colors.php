<?php
$product_colors = baspa_products_get_product_colors( get_the_ID() );

if ( !empty( $product_colors ) ) { ?>
	<li class="a-flex__item--100 a-flex__item--50:xs a-flex__item--33:s a-flex__item--auto:m">
		<div class="f-param a-stack a-stack--justify-start a-gap--s">
			<div class="f-icon">
				<?php get_template_part( 'images/icon/param/color' ); ?>
			</div>
			<h4><?php echo esc_html__( 'Color', 'baspa' ); ?></h4>
			<ul>
				<?php foreach ( $product_colors as $key => $color ) { ?>
					<li class="f-color f-color--<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $color ); ?></li>
				<?php } ?>
			</ul>
		</div>
	</li>
<?php } ?>
