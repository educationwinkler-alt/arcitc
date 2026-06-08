<?php

/**
 * Sidebar
 */

?>

<aside class="f-sidebar f-sidebar--sticky f-sidebar--sticky-no-links a-stack a-gap--m">
	<?php get_template_part( 'templates/component/quick-contact-card', '', array(
		'context'     => 'offer_sidebar',
		'class'       => array( 'f-quick-contact-card--sidebar' ),
		'button_text' => __( 'Napsat zprávu', 'baspa' ),
	) ); ?>
</aside>
