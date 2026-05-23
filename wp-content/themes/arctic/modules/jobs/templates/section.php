<?php

/**
 * Section
 */

// Query Arguments
$jobs_query_args = array(
	'post_type' => 'job',
	'orderby'   => array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	),
);

// Query
$jobs_query = new WP_Query( $jobs_query_args );

?>

<section id="<?php echo sanitize_title( esc_attr_x( 'career', 'anchor', 'baspa' ) ); ?>"
         class="f-section f-section--jobs js-links__section">

	<div class="f-section__container a-container">

		<div class="f-section__heading a-stack a-gap--s">
			<header class="f-section__header">
				<h2><?php if ( !empty( get_option( 'baspa_jobs_title' ) ) ) {
						echo wp_kses_post( get_option( 'baspa_jobs_title' ) );
					} else {
						echo wp_kses_post( __( 'Career in BASPA', 'baspa' ) );
					} ?></h2>
			</header>

			<?php if ( !empty( get_option( 'baspa_jobs_subtitle' ) ) ) { ?>
				<div class="f-section__subtitle a-container--75 a-container--align-start">
					<?php echo wp_kses_post( get_option( 'baspa_jobs_subtitle' ) ); ?>
				</div>
			<?php } ?>
		</div>

		<?php
		get_template_part( 'templates/loop', '', array(
			'query_args'           => $jobs_query_args,
			'query_module'         => 'jobs',
			'query_class'          => array(
				'f-listings',
				'a-grid',
				'a-grid--cols-1',
				'a-gap--xs',
			),
			'query_pagination'     => false,
			'query_posts_per_page' => -1,
			'query_empty'          => 'modules/jobs/templates/loop/empty',
		) );

		if ( !empty( forqy_get_page_by_template( 'template-jobs.php' ) ) ) { ?>

			<div class="f-section__actions f-section__actions--center">
				<a class="f-section__button f-button--secret a-button a-button--link"
				   href="<?php echo forqy_get_page_by_template( 'template-jobs.php' )[ 'permalink' ]; ?>">
					<?php echo wp_kses_post( __( 'View more <span class="screen-reader-text">jobs</span>', 'baspa' ) ); ?>
				</a>
			</div>

		<?php } ?>

	</div>

</section>
