<?php

/**
 * Navigation
 */

$references_page = forqy_get_page_by_template( 'template-references.php' );

$categories = get_terms( array(
	'taxonomy'   => 'reference-category',
	'hide_empty' => false,
) );

if ( !empty( $categories ) && !is_wp_error( $categories ) ) { ?>

	<div class="f-links f-links--stack f-links--sticky f-links--references js-section-nav-handoff">
		<div class="f-links__container a-container">
			<nav class="f-links__navigation"
				 aria-label="<?php echo esc_attr_x( 'References Navigation', 'navigation', 'baspa' ); ?>">
				<ul>
					<?php if ( !empty( $references_page ) ) { ?>
						<li>
							<a href="<?php echo esc_url( $references_page[ 'permalink' ] ); ?>"
							   class="<?php echo get_the_ID() == $references_page[ 'id' ] || is_front_page() ? 'active' : ''; ?>">
								<?php echo esc_html__( 'All', 'baspa' ); ?>
							</a>
						</li>
					<?php } ?>
					<?php foreach ( $categories as $category ) { ?>
						<li>
							<a href="<?php echo get_term_link( $category ); ?>"
							   class="<?php echo get_queried_object()->term_id == $category->term_id ? 'active' : ''; ?>">
								<?php echo esc_html( $category->name ); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			</nav>
		</div>
	</div>

<?php }
