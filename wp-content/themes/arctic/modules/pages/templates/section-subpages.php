<?php

/**
 * Subpages Section Template
 */

$section_class = array( 'f-section' );
$header_class  = array( 'f-section__header' );

// Query Args
$subpages_query_args = array(
	'post_type'      => 'page',
	'post_parent'    => get_the_ID(),
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
	'no_found_rows'  => -1,
	'posts_per_page' => -1,
);

if ( isset( $args[ 'related' ] ) && $args[ 'related' ] ) {
	$current_post = get_post( get_the_ID() );
	if ( $current_post->post_parent == 0 ) {
		return;
	}
	$subpages_query_args[ 'post_parent' ]  = $current_post->post_parent;
	$subpages_query_args[ 'post__not_in' ] = array( get_the_ID() );
	$section_class[]                       = 'f-section--subpages-related';
} else {
	$section_class[] = 'f-section--subpages';
	$header_class[]  = 'screen-reader-text';
}

// Query
$subpages_query = new WP_Query( $subpages_query_args );

if ( $subpages_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'pages', 'anchor', 'baspa' ) ); ?>"
		<?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $section_class ); ?>>
		<div class="f-section__container a-container">

			<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $header_class ); ?>>
				<h2><?php if ( isset( $args[ 'related' ] ) && $args[ 'related' ] ) {
						esc_html_e( 'You may be interested in', 'baspa' );
					} else {
						esc_html_e( 'Subpages', 'baspa' );
					} ?></h2>
			</header>

			<ul class="f-subpages f-pages a-grid a-grid--cols-3 a-gap--xs">
				<?php while ( $subpages_query->have_posts() ) {
					$subpages_query->the_post();

					$page_class = array( 'f-page' );
					?>
					<li <?php forqy_class( $page_class ); ?>>
						<?php if ( has_post_thumbnail() ) { ?>
							<figure class="f-page__image">
								<a href="<?php the_permalink(); ?>" tabindex="-1">
									<?php the_post_thumbnail( get_template() . '-category' ); ?>
								</a>
							</figure>
						<?php } ?>

						<a href="<?php the_permalink(); ?>" class="f-page__container">
							<div class="a-flex a-flex--align-end a-flex--nowrap a-gap--m">
								<div class="a-flex__item--auto">
									<h3><?php the_title(); ?></h3>
								</div>
								<div class="a-flex__item">
									<div class="f-icon"><?php get_template_part( 'images/icon/arrow-right', 'xs' ); ?></div>
								</div>
							</div>
						</a>
					</li>
				<?php } ?>
			</ul>

		</div>
	</section>

	<?php
	$subpages_query->reset_postdata();
	wp_reset_query();
}
