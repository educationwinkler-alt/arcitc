<?php

/**
 * Contact Button
 */

if ( isset( $args[ 'text' ] ) ) {
	$text = strip_tags( $args[ 'text' ] );
} else {
	$text = __( 'Nezávazná konzultace', 'baspa' );
}

$button_class = array(
	'f-button',
	'a-button',
	'a-button--accent',
	'f-button--consultation',
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
		data-off="contact"
		aria-expanded="false"
		aria-controls="<?php echo sanitize_title( esc_attr_x( 'contact-us', 'anchor', 'baspa' ) ); ?>">
	<?php echo esc_html( $text ); ?>
</button>
