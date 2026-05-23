<?php

/**
 * Listing Excerpt
 */

// Meta
$description = get_post_meta( get_the_ID(), 'product_description_short', true );

if ( !empty( $description ) ) { ?>
	<div class="f-listing__excerpt f-content">
		<?php echo wp_trim_words( $description, 24, __( ' ...', 'baspa' ) ); ?>
	</div>
<?php }
