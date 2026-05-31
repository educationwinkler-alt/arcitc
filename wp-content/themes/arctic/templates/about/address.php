<?php

/**
 * About Address
 */

$street = get_theme_mod( 'baspa_street', esc_html__( 'Bohunická cesta 15', 'baspa' ) );
$zip    = get_theme_mod( 'baspa_zip', esc_html__( '664 48', 'baspa' ) );
$city   = get_theme_mod( 'baspa_city', esc_html__( 'Moravany u Brna', 'baspa' ) );
$map    = function_exists( 'arctic_get_map_url' ) ? arctic_get_map_url() : get_theme_mod( 'baspa_map', 'https://maps.app.goo.gl/ZsYfoZ2aQGF1JnZG6' );

if ( !empty( $street ) ) { ?>
	<address class="f-address a-stack a-stack--row a-stack--align-center a-gap--s">
		<span><?php
			if ( !empty( $map ) ) {
				echo '<a href="' . esc_url( $map ) . '" target="_blank" rel="noopener" tabindex="-1">';
			}
			get_template_part( 'images/icon/location' );
			if ( !empty( $map ) ) {
				echo '</a>';
			} ?></span>
		<p class="a-stack a-gap--0"><?php
			if ( !empty( $map ) ) {
				echo '<a href="' . esc_url( $map ) . '" target="_blank" rel="noopener">';
			}
			echo esc_html( $street );
			if ( !empty( $zip ) || !empty( $city ) ) {
				echo '<br>';
//				echo !empty( $zip ) ? esc_html( $zip ) . '&nbsp;' : '';
				echo !empty( $city ) ? esc_html( $city ) : '';
			}
			if ( !empty( $map ) ) {
				echo '</a>';
			}
			?>
		</p>
	</address>
<?php }
