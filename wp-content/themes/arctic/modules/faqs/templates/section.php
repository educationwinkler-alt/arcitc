<?php

/**
 * Section
 */

// Query Arguments
$faqs_query_args = array(
	'post_type' => 'faq',
	'orderby'   => array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	),
);

// Query
$faqs_query = new WP_Query( $faqs_query_args );

if ( $faqs_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'faq', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--faqs js-links__section">

		<div class="f-section__container a-container">

			<div class="f-section__heading a-stack a-gap--s">
				<header class="f-section__header">
					<h2><?php if ( !empty( get_option( 'baspa_faqs_title' ) ) ) {
							echo wp_kses_post( get_option( 'baspa_faqs_title' ) );
						} else {
							echo wp_kses_post( __( 'Frequently Asked Questions', 'baspa' ) );
						} ?></h2>
				</header>

				<?php if ( !empty( get_option( 'baspa_faqs_subtitle' ) ) ) { ?>
					<div class="f-section__subtitle">
						<?php echo wp_kses_post( get_option( 'baspa_faqs_subtitle' ) ); ?>
					</div>
				<?php } ?>
			</div>

			<?php
			get_template_part( 'templates/loop', '', array(
				'query_args'           => $faqs_query_args,
				'query_module'         => 'faqs',
				'query_class'          => array(
					'f-listings',
					'a-grid',
					'a-grid--cols-1',
					'a-gap--xs',
				),
				'query_pagination'     => false,
				'query_posts_per_page' => -1,
			) );

			if ( !empty( forqy_get_page_by_template( 'template-faqs.php' ) ) ) { ?>

				<div class="f-section__actions f-section__actions--center">
					<a class="f-section__button f-button--secret a-button a-button--link"
					   href="<?php echo forqy_get_page_by_template( 'template-faqs.php' )[ 'permalink' ]; ?>">
						<?php echo wp_kses_post( __( 'View more <span class="screen-reader-text">FAQs</span>', 'baspa' ) ); ?>
					</a>
				</div>

			<?php } ?>

		</div>

	</section>

<?php }
