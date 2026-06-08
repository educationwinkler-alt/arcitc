<?php

/**
 * Slide Button
 */

$button_text         = get_post_meta( get_the_ID(), 'button_text', true );
$button_url_post     = get_post_meta( get_the_ID(), 'button_url_post', true );
$button_url_category = get_post_meta( get_the_ID(), 'button_url_category', true );
$button_url          = get_post_meta( get_the_ID(), 'button_url', true );
$seed_key            = (string) get_post_meta( get_the_ID(), '_arctic_seed_key', true );
$homepage_defaults   = array(
	'home-hero-arctic' => array(
		'text' => __( 'Vybrat vířivku', 'baspa' ),
		'url'  => home_url( '/virivky/' ),
	),
	'home-hero-garden' => array(
		'text' => __( 'Zobrazit modely', 'baspa' ),
		'url'  => home_url( '/virivky/' ),
	),
	'home-hero-swimspa' => array(
		'text' => __( 'Zobrazit bazény', 'baspa' ),
		'url'  => home_url( '/swimspa/' ),
	),
);

if ( is_front_page() && '' === (string) $button_text && isset( $homepage_defaults[ $seed_key ] ) ) {
	$button_text = $homepage_defaults[ $seed_key ]['text'];
	$button_url  = $homepage_defaults[ $seed_key ]['url'];
}

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
