<?php

/**
 * Bar Contacts
 */

// Contacts
$phone = get_theme_mod( 'baspa_phone', esc_html__( '+420 777 099 687', 'baspa' ) );
$email = get_theme_mod( 'baspa_email', esc_html__( 'info@arctic-spas.cz', 'baspa' ) );
?>

<div class="f-bar__contacts a-stack a-stack--row a-stack--align-center a-gap--xxxs">
	<span class="f-bar__title"><?php echo esc_html_x( 'Máte dotaz? Volejte', 'bar', 'baspa' ); ?></span>
	<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>"
	   class="f-bar__phone"><?php echo esc_html( $phone ); ?></a>
	<span class="f-bar__hours"><?php echo esc_html_x( 'Po - Pá 8:00-17:00 h', 'bar', 'baspa' ); ?></span>
	<?php get_template_part( 'templates/about/hours' ); ?>
</div>
