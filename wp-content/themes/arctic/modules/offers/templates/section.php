<?php

/**
 * Section
 */

// Query Arguments
$offers_query_args = array(
	'post_type'   => 'offer',
	'post_status' => 'publish',
);

// Query
$offer_query = new WP_Query( $offers_query_args );

if ( $offer_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'offers', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--offers">

		<div class="f-section__container a-container">

			<header class="f-section__header f-section__header--center">
				<h2><?php echo wp_kses_post( __( 'Offers', 'baspa' ) ); ?></h2>
			</header>

			<?php
			get_template_part( 'templates/loop', '', array(
				'query_module'         => 'offers',
				'query_args'           => $offers_query_args,
				'query_class'          => array(
					'f-listings',
					'a-grid',
					'a-grid--cols-1',
					'a-gap--xs',
				),
				'query_pagination'     => false,
				'query_posts_per_page' => 3,
			) );
			?>

			<?php if ( function_exists( 'forqy_get_page_by_template' ) && !empty( forqy_get_page_by_template( 'template-offers.php' ) ) ) { ?>
				<div class="f-section__actions a-buttons">
					<a class="f-section__button f-button a-button a-button--outline a-button--accent"
					   href="<?php echo forqy_get_page_by_template( 'template-offers.php' )[ 'permalink' ]; ?>">
						<?php
						echo wp_kses_post( __( 'View All Offers', 'baspa' ) );
						get_template_part( 'images/icon/arrow-right', 'xs' );
						?>
					</a>
				</div>
			<?php } ?>

		</div>

	</section>

<?php }
