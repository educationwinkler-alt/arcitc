<?php

/**
 * Navigation List
 */

$parent      = get_queried_object();
$parent_id   = $args[ 'term_parent_id' ] ?? $parent->term_id;
$parent_type = get_term_meta( $parent_id, 'category_type', true );

// Terms
if ( isset( $args[ 'terms_args' ] ) && is_array( $args[ 'terms_args' ] ) ) {
	$terms_args = $args[ 'terms_args' ];
} else {
	$terms_args = array(
		'taxonomy'   => 'product-category',
		'hide_empty' => false,
		'parent'     => $parent_id,
	);
}
$terms = get_terms( $terms_args );

$categories_class = array( 'f-categories--sub', 'f-pages', 'a-grid', 'a-gap--xs' );
if ( $parent_type == 'accessories' ) {
	$categories_class[] = 'f-categories--accessories';
	$categories_class[] = 'a-grid--cols-4';
} else {
	$categories_class[] = 'a-grid--cols-3';
}

if ( !empty( $terms ) && !is_wp_error( $terms ) ) { ?>

	<ul <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $categories_class ); ?>>
		<?php foreach ( $terms as $term ) {
			$category_class = array( 'f-category', 'f-category--product', 'f-category--sub' );

			if ( !empty( $term->slug ) ) {
				$category_class[] = esc_attr( sanitize_title( $term->slug ) );
			}

			$category_image_id          = get_term_meta( $term->term_id, 'category_image', true );
			$category_description_short = get_term_meta( $term->term_id, 'category_description_short', true );
			$category_type              = get_term_meta( $term->term_id, 'category_type', true );
			if ( !empty( $category_type ) ) {
				$category_class[] = 'f-category--' . esc_attr( esc_attr( $category_type ) );
			} ?>
			<li <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $category_class ); ?>>
				<?php if ( !empty( $category_image_id ) ) { ?>
					<figure class="f-category__image">
						<?php echo wp_get_attachment_image( $category_image_id, get_template() . '-category', false, array(
							'fetchpriority' => 'low',
						) ); ?>
					</figure>
				<?php } ?>

				<a href="<?php echo get_term_link( $term->term_id ); ?>" class="f-category__container">
					<div class="a-flex a-flex--align-end a-flex--nowrap a-gap--m">
						<div class="a-flex__item--auto">
							<?php if ( isset( $args[ 'type' ] ) && $args[ 'type' ] == 'content' ) { ?>
								<h3 class="f-category__title"><?php echo esc_html( $term->name ); ?></h3>
							<?php } else { ?>
								<div class="f-category__title"><?php echo esc_html( $term->name ); ?></div>
							<?php } ?>
						</div>
						<div class="a-flex__item">
							<div class="f-icon"><?php get_template_part( 'images/icon/arrow-right', 'xs' ); ?></div>
						</div>
					</div>
				</a>
			</li>
		<?php } ?>
	</ul>

<?php }
