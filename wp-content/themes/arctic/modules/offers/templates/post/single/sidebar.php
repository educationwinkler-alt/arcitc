<?php

/**
 * Post Single Sidebar
 */

$contact = get_post_meta(get_the_ID(), 'offer_contact', true);
?>

<aside class="f-sidebar f-sidebar--sticky a-stack a-gap--m">
	<?php
	if ( baspa_products_is_term_or_product( 'bazeny' ) ) {
		block_template_part( 'contact-small-pools' );
	} else if ( baspa_products_is_term_or_product( 'virivky' ) ) {
		block_template_part( 'contact-small-jacuzzis' );
	} else {
		block_template_part( 'contact-small' );
	} ?>
</aside>
