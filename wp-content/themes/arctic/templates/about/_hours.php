<?php

/**
 * About Hours
 */

// Hours
$hours       = get_theme_mod( 'baspa_hours', esc_html__( 'Po-Pá: 8:00-16:00', 'baspa' ) );
$hours_open  = date( 'H:i', strtotime( get_theme_mod( 'baspa_hours_open', esc_html__( '8:00', 'baspa' ) ) ) );
$hours_close = date( 'H:i', strtotime( get_theme_mod( 'baspa_hours_close', esc_html__( '16:00', 'baspa' ) ) ) );

$status = forqy_hours_status( $hours_open, $hours_close );

if ( !empty( $hours ) && !empty( $status ) ) { ?>

	<span class="f-hours f-badge a-stack a-stack--row a-stack--align-center a-gap--xxxs"><span class="f-status f-status--pin f-status--<?php echo esc_attr( $status ); ?>"></span><?php echo esc_html( $hours ); ?></span>

<?php }
