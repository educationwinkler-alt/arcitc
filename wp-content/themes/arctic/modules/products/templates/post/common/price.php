<?php

/**
 * Listing Price
 */

$price        = get_post_meta( get_the_ID(), 'product_price', true );
$price_text   = get_post_meta( get_the_ID(), 'product_price_text', true );
$price_suffix = get_post_meta( get_the_ID(), 'product_price_suffix', true );

if ( !empty( $price ) ) { ?>
	<div class="f-price">
		<ins>
			<?php
			echo forqy_price_czk( $price, 0 );
			if ( !empty( $price_suffix ) ) {
				echo '&nbsp;' ?>
				<span class="f-price__suffix"><?php echo esc_html( $price_suffix ); ?></span>
			<?php } ?>
		</ins>
	</div>
<?php } else if ( !empty( $price_text ) ) { ?>
	<div class="f-price f-price--text">
		<?php
		echo esc_html( $price_text );
		if ( !empty( $price_suffix ) ) {
			echo '&nbsp;' ?>
			<span class="f-price__suffix"><?php echo esc_html( $price_suffix ); ?></span>
		<?php } ?>
	</div>
<?php }
