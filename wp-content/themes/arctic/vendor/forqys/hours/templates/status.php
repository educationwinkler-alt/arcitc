<?php

/**
 * Status
 */

/*
get_template_part( 'vendor/forqys/hours/templates/status', '', array(
	'hours'         => $hours,
	'short'         => true,
	'short_length'  => 2,
	'class'         => array(),
) );
*/

if ( !isset( $args[ 'hours' ] ) || !is_array( $args[ 'hours' ] ) ) {
	return;
}

$short        = !( isset( $args[ 'short' ] ) && is_bool( $args[ 'short' ] ) ) || $args[ 'short' ]; // true as default
$short_length = isset( $args[ 'short_length' ] ) && is_int( $args[ 'short_length' ] ) ? $args[ 'short_length' ] : 2;

$class = array( 'f-hours__status', 'js-hours__status' );
if (isset( $args[ 'class' ] ) && is_array( $args[ 'class' ] ) ) {
	$class = array_merge( $class, $args[ 'class' ] );
}

if ( !empty( array_filter( $args[ 'hours' ], fn( $day ) => !empty( $day[ 'value' ] ) ) ) ) { // check if all 'value' aren't empty
	if ( function_exists( 'forqy_hours' ) ) { ?>
		<span <?php echo 'class="' . join( ' ', $class ) . '"'; ?> role="status"><?php echo forqy_hours( $args[ 'hours' ], $short, $short_length ); ?></span>
	<?php }
}
