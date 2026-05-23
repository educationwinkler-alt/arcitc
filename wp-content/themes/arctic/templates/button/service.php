<?php

/**
 * Service Button
 */

if ( isset( $args[ 'text' ] ) ) {
	$text = strip_tags( $args[ 'text' ] );
} else {
	$text = __( 'Servisní formulář', 'baspa' );
}

$button_class = array(
	'f-button',
	'a-button',
	'a-button--accent',
	'f-off__trigger',
	'js-off__trigger',
);

if ( isset( $args[ 'class' ] ) && is_array( $args[ 'class' ] ) ) {
	$button_class = array_merge( $button_class, $args[ 'class' ] );
}

if ( isset( $args[ 'class_replace' ] ) && is_array( $args[ 'class_replace' ] ) ) {
	$button_class = $args[ 'class_replace' ];
}

// Page
$page_contact = !function_exists( 'forqy_get_page_by_template' ) ?: ( ( !empty( forqy_get_page_by_template( 'template-contact.php' ) ) ) ? forqy_get_page_by_template( 'template-contact.php' ) : null );
?>

<button <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $button_class ); ?>
		data-off="service"
		aria-expanded="false"
		aria-controls="<?php echo sanitize_title( esc_attr_x( 'service', 'anchor', 'baspa' ) ); ?>">
	<?php echo esc_html( $text ); ?>
</button>
