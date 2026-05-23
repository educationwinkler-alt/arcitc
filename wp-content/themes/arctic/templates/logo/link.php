<?php

/**
 * Logo Link
 */

?>

<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<?php if ( file_exists( get_theme_file_path( 'images/logo.svg' ) ) ) {
		/**
		 * SVG Logo
		 */
		$logo        = simplexml_load_file( get_theme_file_path( 'images/logo.svg' ) );
		$logo_width  = (string) $logo->attributes()->width;
		$logo_height = (string) $logo->attributes()->height;
		?>

        <img class="f-logo__img"
             width="<?php echo ! empty( $logo_width ) ? esc_attr( $logo_width ) : '200'; ?>"
             height="<?php echo ! empty( $logo_height ) ? esc_attr( $logo_height ) : '50'; ?>"
             src="<?php echo get_theme_file_uri( 'images/logo.svg' ); ?>"
             alt="<?php echo esc_attr( get_bloginfo( 'name' ) ) . ' &mdash; ' . esc_attr( get_bloginfo( 'description' ) ); ?>"
             fetchpriority="high" decoding="async">

	<?php } else if ( ! empty( locate_template( 'images/logo.php' ) ) ) {
		/**
		 * Inline Logo
		 */
		get_template_part( 'images/logo' );
	} else {
		/**
		 * Text Logo
		 */
		echo esc_html( get_bloginfo( 'name' ) );
	} ?>
</a>
