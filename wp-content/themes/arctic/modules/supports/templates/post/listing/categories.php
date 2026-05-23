<?php

/**
 * Post Listing Categories
 */

$categories = wp_get_post_terms( get_the_ID(), 'support-category', array(
	'parent' => get_queried_object()->parent,
) );

if ( !empty( $categories ) && !is_wp_error( $categories ) ) { ?>

	<ul class="f-terms">
		<?php foreach ( $categories as $category ) {
			if ( $category->parent != 0 ) { ?>
				<li class="f-term">
					<?php echo esc_html( $category->name ); ?>
				</li>
			<?php }
		} ?>
	</ul>

<?php }
