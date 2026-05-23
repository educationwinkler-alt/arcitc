<?php

/**
 * Slide Button
 */

$button_text         = get_post_meta( get_the_ID(), 'button_text', true );
$button_url_post     = get_post_meta( get_the_ID(), 'button_url_post', true );
$button_url_category = get_post_meta( get_the_ID(), 'button_url_category', true );
$button_url          = get_post_meta( get_the_ID(), 'button_url', true );

if ( !empty( $button_text ) && ( !empty( $button_url_post ) || !empty( $button_url_category ) || !empty( $button_url ) ) ) {
	if ( !empty( $button_url_post ) ) {
		$url = get_the_permalink( $button_url_post );
	} else if ( !empty( $button_url_category ) && term_exists( $button_url_category ) ) {
		$url = get_term_link( get_term( $button_url_category ) );
	} else if ( !empty( $button_url ) ) {
		$url = $button_url;
	}
	if ( !empty( $url ) ) { ?>
		<a href="<?php echo esc_url( $url ); ?>"
		   class="f-caption__button f-button f-button--outline a-button a-button--outline">
			<?php echo wp_kses_post( $button_text ); ?>
		</a>
	<?php }
} else if ( !empty( $button_text ) ) {
	get_template_part( 'templates/button/contact', '', array(
		'text'          => $button_text,
		'class_replace' => array(
			'f-caption__button',
			'f-button',
			'f-button--outline',
			'a-button',
			'a-button--outline',
			'f-off__trigger',
			'js-off__trigger',
		),
	) );
}
