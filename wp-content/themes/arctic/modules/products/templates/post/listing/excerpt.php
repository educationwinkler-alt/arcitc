<?php

/**
 * Listing Excerpt
 */

// Meta
$description = get_post_meta( get_the_ID(), 'product_description_short', true );
$dimensions  = get_post_meta( get_the_ID(), 'product_dimensions_external' );

if ( is_tax( 'product-category' ) && !empty( $dimensions ) ) {
	$description = (string) reset( $dimensions );
}

if ( !empty( $description ) ) { ?>
	<div class="f-listing__excerpt f-content">
		<?php echo esc_html( is_tax( 'product-category' ) ? $description : wp_trim_words( $description, 24, __( ' ...', 'baspa' ) ) ); ?>
	</div>
<?php }
