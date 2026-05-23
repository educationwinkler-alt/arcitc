<?php

/**
 * Section
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

if ( !empty( $categories ) && !is_wp_error( $categories ) ) {
	foreach ( $categories as $category ) {

		// Get Subcategories
		$subcategories = get_terms( array(
			'taxonomy'   => 'support-category',
			'parent'     => $category->term_id,
			'hide_empty' => true,
		) );

		/**
		 * Query
		 */
		// Meta Query Args
		$category_meta_query_args = array(
			'relation' => 'OR',
			array(
				'key'     => 'support_display_pricelist_only',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'   => 'support_display_pricelist_only',
				'value' => 0,
			),
		);
		// Query Args
		$category_query_args = array(
			'post_type'      => 'support',
			'post_status'    => 'publish',
			'tax_query'      => array(
				array(
					'taxonomy' => 'support-category',
					'field'    => 'slug',
					'terms'    => $category->slug,
				),
			),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'no_found_rows'  => true,
			'posts_per_page' => -1,
		);
		if ( !empty( $category_meta_query_args ) ) {
			$category_query_args[ 'meta_query' ] = $category_meta_query_args;
		}
		// Query
		$category_query = new WP_Query( $category_query_args );
		?>

		<section id="<?php echo sanitize_title( esc_attr( $category->slug ) ); ?>" class="f-section f-section--support-category js-links__section">

			<div class="f-section__container a-container">

				<div class="f-section__heading a-stack a-gap--s">
					<header class="f-section__header">
						<h2><?php echo esc_html( $category->name ); ?></h2>
					</header>

					<?php if ( !empty( $category->description ) ) { ?>
						<div class="f-section__subtitle"><?php echo wp_kses_post( $category->description ); ?></div>
					<?php } ?>
				</div>

				<?php if ( !empty( $subcategories ) && !is_wp_error( $subcategories ) ) { ?>
					<style>
						<?php foreach ( $subcategories as $subcategory ) {
							/** Filter Listings by CSS */
							echo '.f-support.support-category-'. esc_attr( $category->slug ) .'.support-category-' . esc_attr( $subcategory->slug ) . ' { display: none; }';

							echo '#support-category-'. esc_attr( $category->slug ) .':checked  ~ .f-listings .f-support.support-category-'. esc_attr( $category->slug ) .' {';
								echo 'display: block;';
							echo '}';
							echo '#support-category-'. esc_attr( $category->slug ) .'-' . esc_attr( $subcategory->slug ) . ':checked  ~ .f-listings .f-support.support-category-'. esc_attr( $category->slug ) .'.support-category-' . esc_attr( $subcategory->slug ) . ' {';
								echo 'display: block;';
							echo '}';
							echo '#support-category-'. esc_attr( $category->slug ) .':checked  ~ .f-terms .f-term[for="support-category-'. esc_attr( $category->slug ) .'"] {';
								echo 'color: var(--a--color--contrast);';
								echo 'background-color: var(--a--color);';
								echo 'border-color: transparent;';
							echo '}';
							echo '#support-category-'. esc_attr( $category->slug ) .'-' . esc_attr( $subcategory->slug ) . ':checked  ~ .f-terms .f-term[for="support-category-'. esc_attr( $category->slug ) . '-' . esc_attr( $subcategory->slug ) . '"] {';
								echo 'color: var(--a--color--contrast);';
								echo 'background-color: var(--a--color);';
								echo 'border-color: transparent;';
							echo '}';
						} ?>
					</style>
					<input type="radio" id="support-category-<?php echo esc_attr( $category->slug ); ?>" name="f-subcategory--<?php echo esc_attr( $category->slug ); ?>" class="f-term--radio" checked>
					<?php foreach ( $subcategories as $key => $subcategory ) {
						if ( $key === array_key_first( $subcategories ) ) { ?>
							<input type="radio" id="support-category-<?php echo esc_attr( $category->slug ) . '-' . esc_attr( $subcategory->slug ); ?>" name="f-subcategory--<?php echo esc_attr( $category->slug ); ?>" class="f-term--radio">
						<?php } else { ?>
							<input type="radio" id="support-category-<?php echo esc_attr( $category->slug ) . '-' . esc_attr( $subcategory->slug ); ?>" name="f-subcategory--<?php echo esc_attr( $category->slug ); ?>" class="f-term--radio">
						<?php }
					} ?>
					<ul class="f-terms f-terms--navigation">
						<li><label class="f-term"
						           for="support-category-<?php echo esc_attr( $category->slug ); ?>">
								<?php echo esc_html__( 'All', 'baspa' ); ?>
							</label></li>
						<?php foreach ( $subcategories as $subcategory ) {
							$posts_number = baspa_get_term_posts_number( $subcategory->term_id, 'support', 'support-category', $category_meta_query_args );
							if ( !empty( $posts_number ) ) { ?>
								<li><label class="f-term" for="support-category-<?php echo esc_attr( $category->slug ) . '-' . esc_attr( $subcategory->slug ); ?>">
										<?php echo esc_html( $subcategory->name ); ?>
										<span class="f-count">(<?php echo esc_html( $posts_number ); ?>)</span>
									</label></li>
							<?php }
						} ?>
					</ul>
				<?php }

				if ( $category_query->have_posts() ) {

					get_template_part( 'templates/loop', '', array(
						'query_args'           => $category_query_args,
						'query_module'         => 'supports',
						'query_class'          => array(
							'f-listings',
							'a-grid',
							'a-grid--cols-1',
							'a-gap--xs',
						),
						'query_pagination'     => false,
						'query_posts_per_page' => -1,
					) );

				} ?>

			</div>

		</section>

	<?php }
}
