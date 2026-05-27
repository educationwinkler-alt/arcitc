<?php

/**
 * Figma product series navigation.
 */

if ( is_tax( 'product-category', 'swimspa' ) ) {
	$links = array(
		array( '#serie-swimspa', __( 'Modely bazénů', 'baspa' ), true ),
		array( '#konfigurator', __( 'Vlastní konfigurace', 'baspa' ), false ),
		array( '#showroom', __( 'Showroom', 'baspa' ), false ),
		array( '#references', __( 'Reference', 'baspa' ), false ),
	);
} else {
	$links = array(
		array( '#serie-custom', __( 'Série Custom', 'baspa' ), true ),
		array( '#serie-classic', __( 'Série Classic', 'baspa' ), false ),
		array( '#serie-core', __( 'Série Core', 'baspa' ), false ),
		array( '#konfigurator', __( 'Vlastní konfigurace', 'baspa' ), false ),
	);
}
?>

<section class="f-section f-section--series-nav" aria-label="<?php echo esc_attr__( 'Produktové série', 'baspa' ); ?>">
	<div class="f-section__container a-container">
		<nav class="f-series-nav">
			<?php foreach ( $links as $link ) { ?>
				<a<?php echo $link[2] ? ' class="is-active"' : ''; ?> href="<?php echo esc_url( $link[0] ); ?>"><?php echo esc_html( $link[1] ); ?></a>
			<?php } ?>
		</nav>
	</div>
</section>
