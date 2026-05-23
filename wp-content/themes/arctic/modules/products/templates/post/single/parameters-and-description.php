<?php

/**
 * Post Single Parameters
 */

$product_models              = get_post_meta( get_the_ID(), 'product_model', false );
$product_seats               = get_post_meta( get_the_ID(), 'product_seats', false );
$product_nozzles             = get_post_meta( get_the_ID(), 'product_nozzles', false );
$product_dimensions_internal = get_post_meta( get_the_ID(), 'product_dimensions_internal', false );
$product_dimensions_external = get_post_meta( get_the_ID(), 'product_dimensions_external', false );
$product_water_depth         = get_post_meta( get_the_ID(), 'product_water_depth', false );
$product_water_volume        = get_post_meta( get_the_ID(), 'product_water_volume', false );

if (
	!empty( $product_models ) ||
	!empty( $product_seats ) ||
	!empty( $product_nozzles ) ||
	!empty( $product_dimensions_internal ) ||
	!empty( $product_dimensions_external ) ||
	!empty( $product_water_depth ) ||
	!empty( $product_water_volume ) ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'parameters-and-description', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--single f-section--description js-links__section">

		<div class="f-section__container a-container">
			<header class="f-section__header screen-reader-text">
				<h2><?php echo esc_html__( 'Konfigurace a parametry', 'baspa' ); ?></h2>
			</header>

			<div class="a-flex a-gap--xl:m">
				<div class="a-flex__item--100 a-flex__item--66:m">

					<?php
					get_template_part( 'modules/products/templates/post/single/configurations' );
					?>

					<h3><?php echo esc_html__( 'Parametry', 'baspa' ); ?></h3>

					<ul class="f-params a-flex a-gap--xxs">
						<?php if ( !empty( $product_models ) ) { ?>
							<li class="a-flex__item--100 a-flex__item--50:xs a-flex__item--33:m a-flex__item--25:l">
								<div class="f-param a-stack a-stack--justify-start a-gap--s">
									<div class="f-icon">
										<?php get_template_part( 'images/icon/param/model' ); ?>
									</div>
									<h4><?php echo esc_html__( 'Model', 'baspa' ); ?></h4>
									<ul>
										<?php foreach ( $product_models as $model ) { ?>
											<li><?php echo esc_html( $model ); ?></li>
										<?php } ?>
									</ul>
								</div>
							</li>
						<?php } ?>
						<?php if ( !empty( $product_seats ) ) { ?>
							<li class="a-flex__item--100 a-flex__item--50:xs a-flex__item--33:m a-flex__item--25:l">
								<div class="f-param a-stack a-stack--justify-start a-gap--s">
									<div class="f-icon">
										<?php get_template_part( 'images/icon/param/seats' ); ?>
									</div>
									<h4><?php echo esc_html__( 'Počet míst', 'baspa' ); ?></h4>
									<ul>
										<?php foreach ( $product_seats as $seat ) { ?>
											<li><?php echo esc_html( $seat ); ?></li>
										<?php } ?>
									</ul>
								</div>
							</li>
						<?php } ?>
						<?php if ( !empty( $product_nozzles ) ) { ?>
							<li class="a-flex__item--100 a-flex__item--50:xs a-flex__item--33:m a-flex__item--25:l">
								<div class="f-param a-stack a-stack--justify-start a-gap--s">
									<div class="f-icon">
										<?php get_template_part( 'images/icon/param/nozzles' ); ?>
									</div>
									<h4><?php echo esc_html__( 'Trysky/čerpadla', 'baspa' ); ?></h4>
									<ul>
										<?php foreach ( $product_nozzles as $nozzle ) { ?>
											<li><?php echo esc_html( $nozzle ); ?></li>
										<?php } ?>
									</ul>
								</div>
							</li>
						<?php } ?>
						<?php if ( !empty( $product_dimensions_internal ) ) { ?>
							<li class="a-flex__item--100 a-flex__item--50:xs a-flex__item--33:m a-flex__item--25:l">
								<div class="f-param a-stack a-stack--justify-start a-gap--s">
									<div class="f-icon">
										<?php get_template_part( 'images/icon/param/dimensions-internal' ); ?>
									</div>
									<h4><?php echo esc_html__( 'Vnitřní rozměry', 'baspa' ); ?></h4>
									<ul>
										<?php foreach ( $product_dimensions_internal as $dimension_internal ) { ?>
											<li><?php echo esc_html( $dimension_internal ); ?></li>
										<?php } ?>
									</ul>
								</div>
							</li>
						<?php } ?>
						<?php if ( !empty( $product_dimensions_external ) ) { ?>
							<li class="a-flex__item--100 a-flex__item--50:xs a-flex__item--33:m a-flex__item--25:l">
								<div class="f-param a-stack a-stack--justify-start a-gap--s">
									<div class="f-icon">
										<?php get_template_part( 'images/icon/param/dimensions-external' ); ?>
									</div>
									<h4><?php echo esc_html__( 'Vnější rozměry', 'baspa' ); ?></h4>
									<ul>
										<?php foreach ( $product_dimensions_external as $dimension_external ) { ?>
											<li><?php echo esc_html( $dimension_external ); ?></li>
										<?php } ?>
									</ul>
								</div>
							</li>
						<?php } ?>
						<?php if ( !empty( $product_water_depth ) ) { ?>
							<li class="a-flex__item--100 a-flex__item--50:xs a-flex__item--33:m a-flex__item--25:l">
								<div class="f-param a-stack a-stack--justify-start a-gap--s">
									<div class="f-icon">
										<?php get_template_part( 'images/icon/param/water-depth' ); ?>
									</div>
									<h4><?php echo esc_html__( 'Hloubka vody', 'baspa' ); ?></h4>
									<ul>
										<?php foreach ( $product_water_depth as $water_depth ) { ?>
											<li><?php echo esc_html( $water_depth ); ?></li>
										<?php } ?>
									</ul>
								</div>
							</li>
						<?php } ?>
						<?php if ( !empty( $product_water_volume ) ) { ?>
							<li class="a-flex__item--100 a-flex__item--50:xs a-flex__item--33:m a-flex__item--25:l">
								<div class="f-param a-stack a-stack--justify-start a-gap--s">
									<div class="f-icon">
										<?php get_template_part( 'images/icon/param/water-volume' ); ?>
									</div>
									<h4><?php echo esc_html__( 'Objem vody', 'baspa' ); ?></h4>
									<ul>
										<?php foreach ( $product_water_volume as $water_volume ) { ?>
											<li><?php echo esc_html( $water_volume ); ?></li>
										<?php } ?>
									</ul>
								</div>
							</li>
						<?php } ?>
					</ul>

					<?php if ( get_the_content( get_the_ID() ) ) { ?>
						<h3><?php echo esc_html__( 'Popis', 'baspa' ); ?></h3>
						<?php
						get_template_part( 'templates/content' );
					} ?>

				</div>
				<div class="a-flex__item--100 a-flex__item--33:m">

					<?php get_template_part( 'modules/products/templates/post/single/sidebar' ); ?>

				</div>
			</div>

		</div>

	</section>

<?php }
