<?php

/**
 * Section Template
 */

// Query
$members_query = baspa_members_query();

if ( $members_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'team', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--team">

		<div class="f-section__container a-container">

			<div class="f-section__heading a-stack a-gap--s">
				<header class="f-section__header">
					<h2>
						<?php if ( !empty( get_option( 'baspa_members_title' ) ) ) {
							echo wp_kses_post( get_option( 'baspa_members_title' ) );
						} else {
							echo wp_kses_post( __( 'Our Team', 'baspa' ) );
						} ?>
					</h2>
				</header>

				<?php if ( !empty( get_option( 'baspa_members_subtitle' ) ) ) { ?>
					<div class="f-section__subtitle a-container--50 a-container--align-start">
						<?php echo wp_kses_post( get_option( 'baspa_members_subtitle' ) ); ?>
					</div>
				<?php } ?>
			</div>

			<div class="f-carousel f-carousel--cols-4 f-carousel--members swiper js-carousel js-carousel--cols-4">

				<div class="f-carousel__wrapper swiper-wrapper">

					<?php while ( $members_query->have_posts() ) {
						$members_query->the_post(); ?>

						<div class="f-carousel__item swiper-slide">
							<?php
							get_template_part( 'modules/members/templates/post/listing' );
							?>
						</div>

					<?php } ?>

				</div>

				<button type="button"
				        class="f-carousel__control f-carousel__control--prev f-button f-button--accent a-button a-button--accent js-carousel__prev">
					<?php get_template_part( 'images/icon/arrow-left' ); ?></button>
				<button type="button"
				        class="f-carousel__control f-carousel__control--next f-button f-button--accent a-button a-button--accent js-carousel__next">
					<?php get_template_part( 'images/icon/arrow-right' ); ?></button>

			</div>

		</div>

	</section>

<?php }
