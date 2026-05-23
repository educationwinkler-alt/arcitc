<?php

/**
 * Section Template
 */

// Query Arguments
$partners_query_args = array(
	'post_type'      => 'partner',
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	),
	'no_found_rows'  => -1,
	'posts_per_page' => -1,
);

// Query
$partners_query = baspa_partners_query();

if ( $partners_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'suppliers-and-partners', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--partners f-section--soft js-links__section">

		<div class="f-section__container a-container">

			<div class="f-section__heading a-stack a-gap--s">
				<header class="f-section__header">
					<h2>
						<?php if ( !empty( get_option( 'baspa_partners_title' ) ) ) {
							echo wp_kses_post( get_option( 'baspa_partners_title' ) );
						} else {
							echo wp_kses_post( __( 'Suppliers and Partners', 'baspa' ) );
						} ?>
					</h2>
				</header>

				<?php if ( !empty( get_option( 'baspa_partners_subtitle' ) ) ) { ?>
					<div class="f-section__subtitle a-container--50 a-container--align-start">
						<?php echo wp_kses_post( get_option( 'baspa_partners_subtitle' ) ); ?>
					</div>
				<?php } ?>

				<?php if ( function_exists( 'forqy_get_page_by_template' ) && !empty( forqy_get_page_by_template( 'template-partners.php' ) ) ) { ?>

					<div class="f-section__actions a-buttons">
						<?php echo function_exists( 'baspa_button_next' ) ? baspa_button_next( __( 'View Next Partners', 'baspa' ), forqy_get_page_by_template( 'template-partners.php' )[ 'permalink' ] ) : ''; ?>
					</div>

				<?php } ?>
			</div>

			<?php
			get_template_part( 'templates/loop', '', array(
				'query_args'           => $partners_query->query,
				'query_module'         => 'partners',
				'query_class'          => array( 'f-listings', 'a-grid', 'a-gap--xxs', 'a-gap--xs:m' ),
				'query_pagination'     => false,
				'query_posts_per_page' => -1,
			) );
			?>

		</div>

	</section>

<?php }
