<?php

/**
 * Listing Image
 */

if ( has_post_thumbnail() ) { ?>
	<figure class="f-listing__image f-image a-image a-image--cover a-image--square">
		<?php the_post_thumbnail( get_template() . '-small-square' ); ?>
	</figure>
<?php } ?>
