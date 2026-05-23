<?php

/**
 * Listing Footer
 */

?>

<footer class="f-listing__footer a-flex a-flex--align-center a-gap--s">
	<div class="f-listing__buttons a-buttons">
		<a href="<?php the_permalink(); ?>" class="f-listing__button f-button f-button--outline f-button--reversed a-button a-button--s a-button--outline">
			<?php echo esc_html__( 'View Detail', 'baspa' ); ?>
		</a>
	</div>
</footer>
