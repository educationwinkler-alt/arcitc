<?php

/**
 * Post Listing Description
 */

$description = get_post_meta( get_the_ID(), 'reference_description', true );

if ( !empty( $description ) ) { ?>
	<p class="f-listing__description">
		<?php echo wp_kses_post( strip_tags( $description, "<strong><em><br>" ) ); ?>
	</p>
<?php }
