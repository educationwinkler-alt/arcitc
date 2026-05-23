<?php

/**
 * Template Name: Pricelist
 */

get_header();
get_template_part( 'templates/heading' );
?>

	<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
	      class="f-main f-main--support f-main--pricelist f-main--top-0">

		<div class="f-listings__container a-container">

			<div class="a-flex a-gap--xl:m">
				<div class="a-flex__item--100 a-flex__item--66:m">

					<?php
					get_template_part( 'templates/section/content' );
					get_template_part( 'modules/supports/templates/section', 'by-category-pricelist' );
					?>

				</div>
				<div class="a-flex__item--100 a-flex__item--33:m">

					<?php
					get_template_part( 'modules/supports/templates/sidebar' );
					?>

				</div>
			</div>

		</div>

	</main>

<?php
get_footer();
