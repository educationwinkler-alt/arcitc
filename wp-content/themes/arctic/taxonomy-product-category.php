<?php

/**
 * Product Category Taxonomy
 */

// Page
$page_id = baspa_pages_get_page_by_product_category( get_queried_object_id() );

// Term
$term = get_queried_object();
$term_slug = ( $term instanceof WP_Term ) ? $term->slug : '';
$category_context = 'default';

if ( $term_slug === 'virivky' ) {
	$category_context = 'hot-tub';
} elseif ( $term_slug === 'swimspa' ) {
	$category_context = 'swimspa';
} elseif ( $term_slug === 'dalsi-sortiment' ) {
	$category_context = 'wider-range';
}

get_header();
get_template_part( 'templates/heading/term' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--term f-main--category f-main--category-<?php echo esc_attr( $category_context ); ?> f-main--top-0"
	      data-category-flow="<?php echo esc_attr( $category_context ); ?>">

		<?php
		if ( is_tax( 'product-category', array( 'virivky', 'swimspa' ) ) ) {
		get_template_part( 'templates/section/category-intro' );
			get_template_part( 'templates/section/product-series-nav' );
		}

		get_template_part( 'modules/products/templates/section', 'navigation' );
		get_template_part( 'modules/products/templates/section', 'products' );

		if ( is_tax( 'product-category', array( 'virivky', 'swimspa' ) ) ) {
			get_template_part( 'modules/contacts/templates/section', 'catalog' );
		}

		if ( is_tax( 'product-category', 'virivky' ) ) {
			get_template_part( 'templates/section/configurator' );
		}

		if ( !is_tax( 'product-category', 'virivky' ) && !empty( $page_id ) ) {
			$page = get_post( $page_id );
			?>
			<div class="f-section f-section--content">
				<div class="f-section__container a-container">
					<div class="f-page__content f-content a-content"><?php echo apply_filters( 'the_content', $page->post_content ); ?></div>
				</div>
			</div>
		<?php }

		get_template_part( 'templates/section/showroom' );
		get_template_part( 'templates/section/progress' );
		get_template_part( 'modules/references/templates/section', 'recent' );
		?>

	</main>

<?php
get_footer();
