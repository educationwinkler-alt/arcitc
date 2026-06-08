<?php

/**
 * Sidebar
 */

?>

<aside class="f-sidebar f-sidebar--sticky a-stack a-gap--m">
	<?php get_template_part( 'templates/component/quick-contact-card', '', array(
		'context'     => 'support_help',
		'class'       => array( 'f-quick-contact-card--support' ),
		'button_text' => __( 'Napsat zprávu', 'baspa' ),
	) ); ?>
</aside>
