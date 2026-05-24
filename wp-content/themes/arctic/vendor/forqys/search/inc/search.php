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

		$post_value = static function ( string $key, string $default = '' ): string {
			return isset( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : $default;
		};

		$keyword    = sanitize_text_field( $post_value( 'keyword' ) );
		$types      = array_filter( array_map( 'sanitize_key', explode( ',', $post_value( 'post_type', 'post' ) ) ), 'post_type_exists' );
		$taxonomies = array_filter( array_map( 'sanitize_key', explode( ',', $post_value( 'post_taxonomy', 'category' ) ) ), 'taxonomy_exists' );
		$nonce      = $post_value( 'search_nonce' );

		if ( empty( $nonce ) || !wp_verify_nonce( $nonce, 'forqy_search_processing' ) ) {
			wp_die( '', '', array( 'response' => 403 ) );
		}

		if ( empty( $keyword ) ) {
			wp_die();
		}

		if ( empty( $types ) ) {
			$types = array( 'post' );
		}

		$remote_addr = isset( $_SERVER[ 'REMOTE_ADDR' ] ) ? sanitize_text_field( wp_unslash( $_SERVER[ 'REMOTE_ADDR' ] ) ) : 'unknown';
		$rate_key    = 'forqy_search_' . md5( $remote_addr );
		$rate_count  = (int) get_transient( $rate_key );

		if ( $rate_count >= 60 ) {
			wp_die( '', '', array( 'response' => 429 ) );
		}

		set_transient( $rate_key, $rate_count + 1, MINUTE_IN_SECONDS );

		/**
		 * Posts
		 */
		$query = new WP_Query( array(
			'post_type'      => $types,
			'post_status'    => 'publish',
			's'              => $keyword,
			'posts_per_page' => 10,
		) );

		if ( $query->have_posts() ) { ?>
			<ul>
				<?php while ( $query->have_posts() ) {
					$query->the_post(); ?>
					<li>
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php if ( has_post_thumbnail() ) { ?>
								<figure><?php the_post_thumbnail( 'thumbnail' ); ?></figure>
							<?php }

							echo esc_html( get_the_title() ); ?>
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
				'name__like' => $keyword,
				'hide_empty' => true,
				'fields'     => 'all',
				'orderby'    => 'menu_order',
				'order'      => 'ASC',
				'number'     => 10,
			);

			if ( is_array( $taxonomies ) ) {

				foreach ( $taxonomies as $taxonomy ) {
					$tax = get_taxonomy( $taxonomy );
					if ( !$tax ) {
						continue;
					}

					$tax_terms       = get_terms( array_merge( $taxonomies_args, array(
						'taxonomy' => $taxonomy,
					) ) );
					if ( is_wp_error( $tax_terms ) ) {
						continue;
					}

					$tax_terms_count = count( $tax_terms );

					if ( !empty( $tax_terms_count ) ) {

						echo '<section>';
						echo '<header>';
						echo '<h3>' . esc_html( $tax->label ) . '</h3>';
						echo '</header>';

						echo "<ul>";
						foreach ( $tax_terms as $term ) {
							$term_link = get_term_link( $term );
							if ( is_wp_error( $term_link ) ) {
								continue;
							}
							echo "<li><a href='" . esc_url( $term_link ) . "'>" . esc_html( $term->name ) . "</a></li>";
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
