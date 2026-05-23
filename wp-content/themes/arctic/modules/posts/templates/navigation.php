<?php

/**
 * Navigation
 */

$categories = get_terms( array(
	'taxonomy' => 'category',
) );
if ( !empty( $categories ) && !is_wp_error( $categories ) ) { ?>

	<div class="f-links f-links--categories">
		<div class="f-links__container a-container">
			<nav class="f-links__navigation"
			     aria-label="<?php echo esc_attr_x( 'About Navigation', 'navigation', 'baspa' ); ?>">
				<ul>
					<li>
						<a href="<?php echo get_permalink( get_option( 'page_for_posts', true ) ); ?>">
							<?php echo esc_html__( 'All', 'baspa' ); ?>
						</a>
					</li>
					<?php foreach ( $categories as $category ) {
						$link_class = array( 'f-link' );
						if ( get_queried_object_id() == $category->term_id ) {
							$link_class = array( 'active' );
						}
						?>
						<li>
							<a href="<?php echo get_term_link( $category->term_id ); ?>" <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $link_class ); ?>>
								<?php echo esc_html( $category->name ); ?>
								<span class="f-count">(<?php echo esc_html( $category->count ); ?>)</span>
							</a>
						</li>
					<?php } ?>
				</ul>
			</nav>
		</div>
	</div>

<?php }
