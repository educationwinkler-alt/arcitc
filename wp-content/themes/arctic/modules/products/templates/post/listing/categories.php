<?php

/**
 * Listing Categories
 */

$categories = wp_get_post_terms( get_the_ID(), 'product-category' );

if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) { ?>

    <ul class="f-listing__categories">
		<?php foreach ( $categories as $category ) { ?>
            <li>
                <a href="<?php echo get_term_link( $category ); ?>"
                   aria-label="<?php /* translators: %s: term name */
				   echo sprintf( esc_attr__( 'View all products in %s', 'aidea' ), $category->name ); ?>">
					<?php echo esc_html( $category->name ); ?></a>
            </li>
		<?php } ?>
    </ul>

<?php }
