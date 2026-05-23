<?php

/**
 * Post Single Tags
 */

if ( has_tag() ) { ?>
	<nav class="f-single__tags f-tags">
		<h3 class="screen-reader-text"><?php echo esc_html__( 'Tags', 'baspa' ); ?></h3>
		<?php the_tags( '<ul><li>', '</li><li>', '</li></ul>' ); ?>
	</nav>
<?php }
