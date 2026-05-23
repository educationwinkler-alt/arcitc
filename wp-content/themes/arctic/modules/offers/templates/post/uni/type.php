<?php

/**
 * Post Type
 */

$type        = get_post_meta( get_the_ID(), 'offer_type', true );
$type_custom = get_post_meta( get_the_ID(), 'offer_type_custom', true );

if ( !empty( $type ) || !empty( $type_custom ) ) { ?>
	<div class="f-badge f-badge--<?php echo esc_attr( $type ); ?> a-badge">
		<?php
		if ( !empty( $type_custom ) ) {
			echo wp_kses_post( $type_custom );
		} else {
			if ( $type == 'spring' ) {
				echo esc_html__( 'Spring Offer', 'baspa' );
			} else if ( $type == 'summer' ) {
				get_template_part( 'images/icon/offer/summer' );
				echo esc_html__( 'Summer Offer', 'baspa' );
			} else if ( $type == 'autumn' ) {
				echo esc_html__( 'Autumn Offer', 'baspa' );
			} else if ( $type == 'winter' ) {
				get_template_part( 'images/icon/offer/winter' );
				echo esc_html__( 'Winter Offer', 'baspa' );
			}
		} ?>
	</div>
<?php }
