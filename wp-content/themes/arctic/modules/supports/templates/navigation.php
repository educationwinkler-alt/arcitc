<?php

/**
 * Single Navigation
 */

$categories = get_terms( array(
	'taxonomy'   => 'support-category',
	'parent'     => 0,
	'hide_empty' => true,
	'meta_query' => array(
		'relation' => 'OR',
		array(
			'key'     => 'display_pricelist_only',
			'value'   => 'no',
			'compare' => '=',
		),
		array(
			'key'     => 'display_pricelist_only',
			'compare' => 'NOT EXISTS',
		),
	),
) );

if ( !empty( $categories ) && !is_wp_error( $categories ) ) { ?>

	<div class="f-links f-links--sticky f-links--support">
		<div class="f-links__container a-container">
			<nav class="f-links__navigation js-links__navigation"
			     aria-label="<?php echo esc_attr_x( 'Support Navigation', 'navigation', 'baspa' ); ?>">
				<ul>
					<?php foreach ( $categories as $category ) { ?>
						<li>
							<a href="#<?php echo sanitize_title( esc_attr( $category->slug ) ); ?>">
								<?php echo esc_html( $category->name ); ?>
							</a>
						</li>
					<?php } ?>
					<li>
						<a href="#<?php echo sanitize_title( esc_attr_x( 'service-form', 'anchor', 'baspa' ) ); ?>">
							<?php echo esc_html__( 'Servisní formulář', 'baspa' ); ?>
						</a>
					</li>
				</ul>
			</nav>
		</div>
	</div>

<?php }
