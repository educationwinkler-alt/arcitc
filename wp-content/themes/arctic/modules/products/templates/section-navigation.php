<?php

/**
 * Section Template
 */

$parent      = get_queried_object();
$parent_id   = $args[ 'parent_id' ] ?? $parent->term_id;
$parent_type = get_term_meta( $parent_id, 'category_type', true );

// Terms
$terms_args = array(
	'taxonomy'   => 'product-category',
	'hide_empty' => false,
	'parent'     => $parent_id,
);

$terms = get_terms( $terms_args );

if ( !empty( $terms ) && !is_wp_error( $terms ) ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'categories', 'anchor', 'baspa' ) ); ?>" class="f-section f-section--soft f-section--product-subcategories">
		<div class="f-section__container a-container">

			<header class="f-section__header f-section__header--center screen-reader-text">
				<h2><?php esc_html_e( 'Sub-categories', 'baspa' ); ?></h2>
			</header>

			<?php get_template_part( 'modules/products/templates/navigation/list', '', array(
				'type'           => 'content',
				'term_parent_id' => $parent_id,
				'terms_args'     => $terms_args,
			) ); ?>

		</div>
	</section>

<?php }
