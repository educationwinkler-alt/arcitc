<?php

/**
 * About Hours
 */

// Hours
$hours = apply_filters( 'forqy_hours', '__return_empty_array' );

if ( !empty( $hours ) ) {
	get_template_part( 'vendor/forqys/hours/templates/status', '', array(
		'hours' => $hours,
	) );
}
