<?php

/**
 * About Hours
 */

// Hours
$hours = apply_filters( 'forqy_hours', array() );
$class = array( 'f-about-hours' );
$label = isset( $args['label'] ) && is_string( $args['label'] ) ? $args['label'] : '';

if ( isset( $args['class'] ) && is_array( $args['class'] ) ) {
	$class = array_merge( $class, $args['class'] );
}

if ( !empty( $hours ) ) {
	if ( !empty( $label ) && !empty( array_filter( $hours, fn( $day ) => !empty( $day['value'] ) ) ) ) { ?>
		<span class="<?php echo esc_attr( implode( ' ', array_merge( array( 'f-hours__status', 'js-hours__status' ), $class ) ) ); ?>" role="status"><?php echo esc_html( $label ); ?></span>
		<?php return;
	}

	get_template_part( 'vendor/forqys/hours/templates/status', '', array(
		'hours' => $hours,
		'class' => $class,
	) );
}
