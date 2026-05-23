<?php

/**
 * Accessory Listing Links
 */

$url = get_post_meta( get_the_ID(), 'product_url', true );

if ( !empty( $url ) ) { ?>
	<a href="<?php echo esc_url( $url ); ?>"
	   class="f-listing__link"
	   target="_blank"
	   rel="external"><?php echo esc_html__( 'Buy on Eshop', 'baspa' ); ?></a>
<?php }
