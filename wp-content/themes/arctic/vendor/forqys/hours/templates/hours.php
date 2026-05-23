<?php

/**
 * Hours
 *
 * @since 1.0.0
 */

/*
get_template_part( 'vendor/forqys/hours/templates/hours', '', array(
	'hours' => $hours,
) );
*/

if ( !isset( $args[ 'hours' ] ) || !is_array( $args[ 'hours' ] ) ) {
	return;
}

if ( !empty( array_filter( $args[ 'hours' ], fn( $day ) => !empty( $day[ 'value' ] ) ) ) ) { // check if all 'value' aren't empty ?>
	<ul class="f-hours">
		<?php foreach ( $args[ 'hours' ] as $day => $data ) {
			$label     = esc_html( $data[ 'label' ] );
			$intervals = implode( ', ', function_exists( 'forqy_hours_parse' ) ? forqy_hours_parse( $data[ 'value' ] ) : $data[ 'value' ] );
			if ( !empty( $intervals ) ) { ?>
				<li><?php echo esc_html( $data[ 'label' ] . ': ' . $intervals ); ?></li>
			<?php }
		} ?>
	</ul>
<?php }
