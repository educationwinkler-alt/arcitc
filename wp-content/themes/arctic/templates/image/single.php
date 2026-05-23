<?php

/**
 * Single Image
 */

?>

<figure class="f-single__image f-image a-image a-image--cover a-image--landscape-16-9">
	<?php the_post_thumbnail( get_template() . '-large', array(
		'data-lazy'     => false,
		'fetchpriority' => 'high',
	) ); ?>
</figure>
