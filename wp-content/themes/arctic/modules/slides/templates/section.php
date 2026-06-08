<?php

/**
 * Section
 */

$slide_count = 1;

$is_homepage_slides          = is_front_page();
$allow_homepage_fallbacks    = $is_homepage_slides && function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks();
$homepage_fallback_slides    = array();

$slides_args = array(
	'post_type'      => 'slide',
	'post_status'    => 'publish',
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
	'posts_per_page' => -1,
);

$slides_query = new WP_Query( $slides_args );

if ( $allow_homepage_fallbacks ) {
	$existing_home_seed_keys = array();

	foreach ( $slides_query->posts as $slide_post ) {
		$seed_key = get_post_meta( $slide_post->ID, '_arctic_seed_key', true );

		if ( !empty( $seed_key ) ) {
			$existing_home_seed_keys[] = $seed_key;
		}
	}

	$homepage_fallback_candidates = array(
		array(
			'seed_key' => 'home-hero-arctic',
			'title'    => __( 'Kanadské luxusní vířivky', 'baspa' ),
			'content'  => __( 'Vířivky Arctic jsou vyrobeny tak, aby efektivně pracovaly v extrémních severských podmínkách a přinesly vám pohodovou relaxaci po celý rok.', 'baspa' ),
			'button'   => __( 'Vybrat vířivku', 'baspa' ),
			'url'      => home_url( '/virivky/' ),
			'image'    => content_url( 'uploads/import/figma/hp-hero-arctic-spas-07.jpg' ),
			'alt'      => __( 'Vířivka Arctic Spas u jezera', 'baspa' ),
			'eager'    => true,
		),
		array(
			'seed_key' => 'home-hero-garden',
			'title'    => __( 'Venkovní vířivky do každého počasí', 'baspa' ),
			'content'  => __( 'Vyberte si model Core, Classic nebo Custom. Arctic Spas jsou navržené pro celoroční provoz, nízkou spotřebu a pohodlnou relaxaci doma na zahradě.', 'baspa' ),
			'button'   => __( 'Zobrazit modely', 'baspa' ),
			'url'      => home_url( '/virivky/' ),
			'image'    => content_url( 'uploads/import/curator/01-header-virivky/virivky-hero-zahrada-01.jpg' ),
			'alt'      => __( 'Venkovní vířivka Arctic Spas v zahradě', 'baspa' ),
			'eager'    => false,
		),
		array(
			'seed_key' => 'home-hero-swimspa',
			'title'    => __( 'Celoroční bazény Arctic Spas', 'baspa' ),
			'content'  => __( 'Swimspa spojují prostor pro plavání, cvičení a relaxaci. Projděte si modely Classic a Custom a vyberte řešení pro celou rodinu.', 'baspa' ),
			'button'   => __( 'Zobrazit bazény', 'baspa' ),
			'url'      => home_url( '/swimspa/' ),
			'image'    => content_url( 'uploads/import/curator/02-header-celorocni-bazeny/celorocni-bazen-hero-hory-lifestyle-01-1920.jpg' ),
			'position' => 'center bottom',
			'alt'      => __( 'Celoroční bazén Arctic Spas v horském prostředí', 'baspa' ),
			'eager'    => false,
		),
	);

	foreach ( $homepage_fallback_candidates as $fallback_candidate ) {
		if ( (int) $slides_query->post_count + count( $homepage_fallback_slides ) >= 3 ) {
			break;
		}

		if ( in_array( $fallback_candidate['seed_key'], $existing_home_seed_keys, true ) ) {
			continue;
		}

		$homepage_fallback_slides[] = $fallback_candidate;
	}
}

$number = (int) $slides_query->post_count + count( $homepage_fallback_slides );
set_query_var( 'baspa_slides_number', $number );

if ( $slides_query->have_posts() || !empty( $homepage_fallback_slides ) ) {
	$slides_class = array( 'f-slides', 'swiper' );

	if ( $number >= 2 ) {
		$slides_class[] = 'js-slides';
	} else if ( 1 === (int) $number ) {
		$slides_class[] = 'f-slides--single-slide';
	}
	?>

	<section class="f-section f-section--slides" aria-label="<?php echo esc_attr_x( 'Slideshow', 'slideshow accessibility', 'baspa' ); ?>" data-content-source="slide-cpt">

		<?php if ( $is_homepage_slides ) { ?>
			<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
		<?php } ?>

		<div <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $slides_class ); ?>>

			<div id="slides" class="f-slides__wrapper swiper-wrapper">

				<?php while ( $slides_query->have_posts() ) {
					$slides_query->the_post();

					$slide_class   = array( 'f-slide', 'swiper-slide' );
					$slide_class[] = 'f-slide--' . esc_attr( (string) $slide_count );
					?>

					<div <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $slide_class ); ?> data-content-source="slide-cpt" data-slide-id="<?php echo esc_attr( (string) get_the_ID() ); ?>">
						<?php
						get_template_part( 'modules/slides/templates/slide/caption' );
						get_template_part( 'modules/slides/templates/slide/background', '', array(
							'slide_count' => $slide_count,
						) );
						?>
					</div>

					<?php
					$slide_count++;
				} ?>

				<?php foreach ( $homepage_fallback_slides as $homepage_fallback_slide ) {
					$slide_class   = array( 'f-slide', 'swiper-slide' );
					$slide_class[] = 'f-slide--' . esc_attr( (string) $slide_count );
					?>

					<div <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $slide_class ); ?> data-content-source="static-home-fallback" data-slide-id="<?php echo esc_attr( $homepage_fallback_slide['seed_key'] ); ?>">
						<div class="f-caption__container a-container">
							<div class="a-flex">
								<div class="a-flex__item--100 a-flex__item--60:m">
									<div class="f-caption a-stack a-stack--justify-start a-gap--s">
										<header class="f-caption__header">
											<h2><?php echo esc_html( $homepage_fallback_slide['title'] ); ?></h2>
										</header>

										<div class="f-content a-content">
											<p><?php echo esc_html( $homepage_fallback_slide['content'] ); ?></p>
										</div>

										<footer class="f-caption__footer">
											<a href="<?php echo esc_url( $homepage_fallback_slide['url'] ); ?>"
											   class="f-caption__button f-button f-button--outline a-button a-button--outline">
												<?php echo esc_html( $homepage_fallback_slide['button'] ); ?>
											</a>
										</footer>
									</div>
								</div>
								<div class="a-flex__item--100 a-flex__item--auto:m"></div>
							</div>
						</div>

						<figure class="f-slide__background a-image--cover">
							<img width="1920" height="1080"
							     src="<?php echo esc_url( $homepage_fallback_slide['image'] ); ?>"
							     alt="<?php echo esc_attr( $homepage_fallback_slide['alt'] ); ?>"
							     data-slide="<?php echo esc_attr( (string) $slide_count ); ?>"
							     <?php if ( !empty( $homepage_fallback_slide['position'] ) ) { ?>
								     style="object-position: <?php echo esc_attr( $homepage_fallback_slide['position'] ); ?>;"
							     <?php } ?>
							     <?php echo !empty( $homepage_fallback_slide['eager'] ) ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"'; ?>
							     decoding="async">
						</figure>
					</div>

					<?php
					$slide_count++;
				} ?>

			</div>

			<?php if ( $number >= 2 ) { ?>
				<div class="f-slides__controls f-slides__controls--navigation">
					<button type="button" class="f-slides__control f-slides__control--prev a-button a-button--icon a-button--accent js-slides__prev">
						<?php get_template_part( 'images/icon/arrow-left' ); ?></button>
					<button type="button" class="f-slides__control f-slides__control--next a-button a-button--icon a-button--accent js-slides__next">
						<?php get_template_part( 'images/icon/arrow-right' ); ?></button>
				</div>

				<div class="f-slides__controls f-slides__controls--pagination a-container">
					<div class="f-slides__pagination js-slides__pagination"></div>
				</div>
			<?php } ?>

		</div>

		<?php if ( $is_homepage_slides ) { ?>
			<?php get_template_part( 'templates/section/hero-promo' ); ?>
		<?php } ?>

	</section>

	<?php
	$slides_query->reset_postdata();
	wp_reset_postdata();
} else {
	get_template_part( 'templates/heading/hero' );
}
