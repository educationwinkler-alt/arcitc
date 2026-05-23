<?php

/**
 * About Phone
 */

// Phone
$phone = get_theme_mod( 'baspa_phone', esc_html__( '+420 602 774 195', 'baspa' ) );

if ( !empty( $phone ) ) { ?>
	<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>" class="f-contact">
		<?php echo esc_html( $phone ); ?>
	</a>
<?php }
