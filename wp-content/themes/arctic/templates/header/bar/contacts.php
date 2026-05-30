<?php

/**
 * Bar Contacts
 */

// Contacts
$phone = get_theme_mod( 'baspa_phone', esc_html__( '+420 777 099 687', 'baspa' ) );
$email = get_theme_mod( 'baspa_email', esc_html__( 'info@arctic-spas.cz', 'baspa' ) );
?>

<div class="f-bar__contacts a-stack a-stack--row a-stack--align-center a-gap--xxxs">
	<span class="f-bar__title"><?php echo esc_html_x( 'Máte dotaz? Volejte na', 'bar', 'baspa' ); ?></span>
	<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>"
	   class="f-bar__phone"><?php echo esc_html( $phone ); ?></a>
	<?php $hours = apply_filters( 'forqy_hours', array() ); ?>
	<?php get_template_part( 'templates/about/hours', '', array(
		'class' => array( 'f-bar__hours' ),
		'label' => function_exists( 'baspa_hours_bar_label' ) ? baspa_hours_bar_label( $hours ) : '',
	) ); ?>
</div>
