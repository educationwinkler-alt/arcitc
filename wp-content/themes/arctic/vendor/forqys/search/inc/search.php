<?php

/**
 * Search
 *
 * @package     forqys/search
 * @since       1.0.0
 */

if ( !function_exists( 'forqy_search_processing' ) ) {

	/**
	 * Search Processing
	 *
	 * @return void
	 */
	function forqy_search_processing(): void {

		$keyword    = $_POST[ 'keyword' ];
		$types      = isset( $_POST[ 'post_type' ] ) ? explode( ',', $_POST[ 'post_type' ] ) : 'post';
		$taxonomies = isset( $_POST[ 'post_taxonomy' ] ) ? explode( ',', $_POST[ 'post_taxonomy' ] ) : 'category';

		/**
		 * Posts
		 */
		$query = new WP_Query( array(
			'post_type'      => $types,
			'post_status'    => 'publish',
			's'              => esc_attr( $keyword ),
			'posts_per_page' => -1,
		) );

		if ( $query->have_posts() ) { ?>
			<ul>
				<?php while ( $query->have_posts() ) {
					$query->the_post(); ?>
					<li>
						<a href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) { ?>
								<figure><?php the_post_thumbnail( 'thumbnail' ); ?></figure>
							<?php }

							the_title(); ?>
						</a>
					</li>
				<?php }
				wp_reset_postdata(); ?>
			</ul>
		<?php }

		/**
		 * Taxonomies
		 */
		if ( !empty( $taxonomies ) ) {

			// Arguments
			$taxonomies_args = array(
				'name__like' => esc_attr( $keyword ),
				'hide_empty' => true,
				'fields'     => 'all',
				'orderby'    => 'menu_order',
				'order'      => 'ASC',
			);

			if ( is_array( $taxonomies ) ) {

				foreach ( $taxonomies as $taxonomy ) {
					$tax = get_taxonomy( $taxonomy );

					$tax_terms       = get_terms( array_merge( $taxonomies_args, array(
						'taxonomy' => $taxonomy,
					) ) );
					$tax_terms_count = count( $tax_terms );

					if ( !empty( $tax_terms_count ) ) {

						echo '<section>';
						echo '<header>';
						echo '<h3>' . esc_html( $tax->label ) . '</h3>';
						echo '</header>';

						echo "<ul>";
						foreach ( $tax_terms as $term ) {
							echo "<li><a href='" . get_term_link( $term ) . "'>" . esc_html( $term->name ) . "</a></li>";
						}
						echo "</ul>";
						echo '</section>';
					}
				}
			}
		}

		wp_die();

	}

	add_action( 'wp_ajax_search_processing', 'forqy_search_processing' );
	add_action( 'wp_ajax_nopriv_search_processing', 'forqy_search_processing' );

}
