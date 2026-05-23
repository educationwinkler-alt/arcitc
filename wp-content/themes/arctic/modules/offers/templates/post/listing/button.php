<?php

/**
 * Post Listing Button
 */

?>

<div class="f-buttons a-buttons a-buttons--center">
	<a href="<?php the_permalink(); ?>"
	   class="f-listing__button f-button a-button a-button--s a-button--outline" tabindex="-1">
		<?php echo sprintf( __( 'More About Offer', 'baspa' ), get_the_title() ); ?>
	</a>
</div>
