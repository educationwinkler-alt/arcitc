<?php

/**
 * Post Listing Categories
 */

$categories             = wp_get_post_terms( get_the_ID(), 'category' );
$category_questions_key = array_search( 'casto-se-ptate', array_column( $categories, 'slug' ) );

//do_action( 'qm/debug', $categories );
//do_action( 'qm/debug', $category_questions_key );

if ( $category_questions_key !== false && isset( $args[ 'questions' ] ) ) {
	if ( !$args[ 'questions' ] ) {
		unset( $categories[ $category_questions_key ] );
	} else {
		$categories = array( $categories[ $category_questions_key ] );
	}
} else {
	if ( isset( $args[ 'questions' ] ) && $args[ 'questions' ] ) {
		$categories = array();
	}
}

if ( !empty( $categories ) && !is_wp_error( $categories ) ) { ?>

	<ul class="f-terms">
		<?php foreach ( $categories as $category ) { ?>
			<li>
				<a href="<?php echo get_term_link( $category ); ?>"
				   class="f-term f-term--<?php echo esc_attr( $category->slug ); ?>"
				   aria-label="<?php /* translators: %s: term name */
				   echo sprintf( esc_attr__( 'View all posts in %s', 'baspa' ), esc_attr( $category->name ) ); ?>">
					<?php
					if ( $category->slug == 'casto-se-ptate' ) {
						get_template_part( 'images/icon/questions', 's' );
					}
					echo esc_html( $category->name );
					?></a>
			</li>
		<?php } ?>
	</ul>

<?php }
