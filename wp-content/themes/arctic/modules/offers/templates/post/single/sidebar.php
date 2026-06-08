<?php

/**
 * Post Single Sidebar
 */

$contact = get_post_meta( get_the_ID(), 'offer_contact', true );
$contact_member_id = absint( get_post_meta( get_the_ID(), 'offer_contact_member_id', true ) );
?>

<aside class="f-sidebar f-sidebar--sticky a-stack a-gap--m">
	<?php get_template_part( 'templates/component/quick-contact-card', '', array(
		'context'     => 'offer_sidebar',
		'member_id'   => $contact_member_id,
		'class'       => array( 'f-quick-contact-card--offer' ),
		'button_text' => __( 'Napsat zprávu', 'baspa' ),
	) ); ?>
</aside>
