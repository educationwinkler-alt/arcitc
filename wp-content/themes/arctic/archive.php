<?php

/**
 * Archive
 */

get_header();
get_template_part( 'templates/heading/archive' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--listings f-main--archive f-main--top-0">

		<?php get_template_part( 'modules/posts/templates/navigation' ); ?>

		<div class="f-listings__container a-container">

			<div class="a-flex a-gap--xl:m">
				<div class="a-flex__item--100 a-flex__item--66:m">

					<?php
					get_template_part( 'templates/loop', '', array(
						'query_module'     => 'posts',
						'query_class'      => array(
							'f-listings',
							'a-grid',
							'a-grid--cols-1',
							'a-gap--m',
						),
						'query_listing'    => 'list',
						'query_pagination' => true,
					) );
					?>

				</div>
				<div class="a-flex__item--100 a-flex__item--33:m">

					<?php
					get_template_part( 'modules/posts/templates/sidebar' );
					?>

				</div>
			</div>

		</div>

	</main>

<?php
get_footer();

