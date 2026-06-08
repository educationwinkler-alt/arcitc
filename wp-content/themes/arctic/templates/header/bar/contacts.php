<?php

/**
 * Bar Contacts
 */

// Header bar contact is intentionally pinned to Tomas Koutny.
$phone      = '+420 602 149 106';
$phone_href = function_exists( 'baspa_member_phone_href' ) ? baspa_member_phone_href( $phone ) : str_replace( ' ', '', $phone );
?>

<div class="f-bar__contacts a-stack a-stack--row a-stack--align-center a-gap--xxxs">
	<span class="f-bar__title"><?php echo esc_html_x( 'Máte dotaz? Volejte na', 'bar', 'baspa' ); ?></span>
	<a href="tel:<?php echo esc_attr( $phone_href ); ?>"
	   class="f-bar__phone"><?php echo esc_html( $phone ); ?></a>
	<?php $hours = apply_filters( 'forqy_hours', array() ); ?>
	<?php get_template_part( 'templates/about/hours', '', array(
		'class' => array( 'f-bar__hours' ),
		'label' => function_exists( 'baspa_hours_bar_label' ) ? baspa_hours_bar_label( $hours ) : '',
	) ); ?>
</div>
