<?php

/**
 * Post Listing Button
 */

?>

<a href="<?php the_permalink(); ?>"
   class="f-listing__button f-button a-button a-button--s a-button--accent a-button--icon" tabindex="-1">
	<span class="screen-reader-text"><?php echo sprintf( __( 'View %s', 'baspa' ), get_the_title() ); ?></span>
	<?php get_template_part( 'images/icon/arrow-right', 'xs' ); ?>
</a>
