<?php

/**
 * Post Listing Categories
 */

$categories = wp_get_post_terms( get_the_ID(), 'reference-category' );

if ( !empty( $categories ) && !is_wp_error( $categories ) ) { ?>

	<ul class="f-reference__categories f-listing__categories f-terms f-terms--down">
		<?php foreach ( $categories as $category ) { ?>
			<li class="f-term"><?php echo esc_html( $category->name ); ?></li>
		<?php } ?>
	</ul>

<?php }
