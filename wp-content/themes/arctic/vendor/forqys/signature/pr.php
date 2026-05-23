<?php

/**
 * PR Signature
 */

/*
get_template_part( 'vendor/forqys/signature/pr', '', array(
	'text'  => _x( 'Developed by', 'signature', 'domain' ),
	'label' => _x( '(opens in a new tab)', 'signature', 'domain' ),
) );
*/

$label = !empty( $args[ 'label' ] ) ? $args[ 'label' ] : '(otevře se v nové záložce)';
?>

<div class="f-signature f-signature--pr a-sr-only">
	<?php echo isset( $args[ 'text' ] ) ? wp_kses_post( $args[ 'text' ] ) . '&nbsp;' : ''; ?> <a href="https://pavelrichter.cz/" target="_blank" tabindex="-1" title="Web Designer & Front-end/WordPress Developer" aria-label="Pavel Richter <?php echo esc_attr( $label ); ?>">Pavel Richter</a>
</div>
