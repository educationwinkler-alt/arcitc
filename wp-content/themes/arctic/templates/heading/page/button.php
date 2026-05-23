<?php

/**
 * Heading Button
 */

$button_text = get_post_meta( get_the_ID(), 'page_button_text', true );
$button_url  = get_post_meta( get_the_ID(), 'page_button_url', true );

if ( !empty( $button_text ) && !empty( $button_url ) ) { ?>
	<a href="<?php echo esc_url( $button_url ); ?>"
	   class="f-heading__button f-heading__button--accent f-button a-button a-button--accent">
		<?php echo wp_kses_post( $button_text ); ?>
	</a>
<?php } else if ( !empty( $button_text ) ) {
	get_template_part( 'templates/button/contact', '', array(
		'text'  => $button_text,
		'class' => array(
			'f-heading__button',
			'f-heading__button--accent',
		),
	) );
}
