<?php

/**
 * Product detail contact card.
 */

?>

<aside class="f-sidebar f-sidebar--sticky a-stack a-gap--m">
	<?php get_template_part( 'templates/component/quick-contact-card', '', array(
		'context'     => 'product_sidebar',
		'class'       => array( 'f-product-contact-card', 'f-quick-contact-card--product' ),
		'button_text' => __( 'Napsat zprávu', 'baspa' ),
	) ); ?>
</aside>
