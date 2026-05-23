<?php

/**
 * Excerpt
 */

if ( has_excerpt() ) { ?>
	<div class="f-single__excerpt">
		<?php the_excerpt(); ?>
	</div>
<?php }
