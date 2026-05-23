<?php

/**
 * Post Single Sidebar
 */

$contacts = get_post_meta( get_the_ID(), 'product_contacts', true );
$cta_text = get_post_meta( get_the_ID(), 'product_cta_text', true );
?>

<aside class="f-sidebar f-sidebar--sticky a-stack a-gap--m">
	<div class="a-flex a-flex--align-center a-gap--xs">
		<div class="a-flex__item--100 a-flex__item--100:m">
			<?php get_template_part( 'modules/products/templates/post/common/price' ); ?>
		</div>
		<div class="a-flex__item--100 a-flex__item--100:m">
			<?php get_template_part( 'templates/button/contact', '', array(
				'text' => !empty( $cta_text ) ? $cta_text : __( 'Nezávazná konzultace', 'baspa' ),
			) ); ?>
		</div>
	</div>

	<?php
	if ( !empty( $contacts ) ) {
		if ( $contacts == 'pools' ) {
			block_template_part( 'contact-small-pools' );
		} else if ( $contacts == 'jacuzzis' ) {
			block_template_part( 'contact-small-jacuzzis' );
		}
	} else {
		if ( baspa_products_is_term_or_product( 'bazeny' ) ) {
			block_template_part( 'contact-small-pools' );
		} else if ( baspa_products_is_term_or_product( 'virivky' ) ) {
			block_template_part( 'contact-small-jacuzzis' );
		} else {
			block_template_part( 'contact-small' );
		}
	} ?>
</aside>
