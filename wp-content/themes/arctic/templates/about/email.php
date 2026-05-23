<?php

/**
 * About Email
 */

// Email
$email = get_theme_mod( 'baspa_email', esc_html__( 'info@arctic-spas.cz', 'baspa' ) );

if ( !empty( $email ) ) { ?>
	<a href="mailto:<?php echo antispambot( esc_attr( $email ) ); ?>" class="f-contact">
		<?php echo antispambot( esc_html( $email ) ); ?>
	</a>
<?php }
