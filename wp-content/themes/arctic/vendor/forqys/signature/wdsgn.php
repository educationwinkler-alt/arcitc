<?php

/**
 * PR Signature
 */

/*
get_template_part( 'vendor/forqys/signature/wdsgn', '', array(
	'text'  => _x( 'Developed by', 'signature', 'domain' ),
	'label' => _x( '(opens in a new tab)', 'signature', 'domain' ),
) );
*/

$label = !empty( $args[ 'label' ] ) ? $args[ 'label' ] : '(opens in a new tab)';
?>

<div class="f-signature f-signature--wdsgn a-sr-only">
	<?php echo isset( $args[ 'text' ] ) ? wp_kses_post( $args[ 'text' ] ) . '&nbsp;' : ''; ?> <a href="https://pavelrichter.cz/" target="_blank" tabindex="-1" title="Web Designer & Front-end/WordPress Developer" aria-label="Pavel Richter <?php echo esc_attr( $label ); ?>">Pavel Richter</a> @ <a href="https://wdsgn.agency/" target="_blank" tabindex="-1" title="Web Design & WordPress Development Agency" aria-label="WDSGN.Agency <?php echo esc_attr( $label ); ?>">WDSGN.Agency</a>
</div>
