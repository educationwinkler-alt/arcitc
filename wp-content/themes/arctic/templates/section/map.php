<?php

/**
 * Map Section
 */

$map_embed = get_theme_mod( 'arctic_map_embed' );
$map_link  = get_theme_mod( 'baspa_map' );
$can_embed = !empty( $map_embed ) && ( !function_exists( 'wp_get_environment_type' ) || 'local' !== wp_get_environment_type() );

$figma_map = content_url( 'uploads/import/figma/contact-map-showroom.png' );
$hours_label = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_label', 'Úterý - Pátek' ) : get_theme_mod( 'arctic_showroom_hours_label', 'Úterý - Pátek' );
$hours_line_1 = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_line_1', '9:00 - 11:30' ) : get_theme_mod( 'arctic_showroom_hours_line_1', '9:00 - 11:30' );
$hours_line_2 = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_line_2', '12:30 - 16:00' ) : get_theme_mod( 'arctic_showroom_hours_line_2', '12:30 - 16:00' );
?>

<section class="f-section f-section--map">
	<div class="f-section__map <?php echo $can_embed ? '' : 'f-section__map--local'; ?>">
		<?php if ( $can_embed ) { ?>
			<iframe src="<?php echo esc_url( $map_embed ); ?>"
			        width="1920"
			        height="780"
			        style="border:0;"
			        allowfullscreen=""
			        loading="lazy"
			        tabindex="-1"
			        referrerpolicy="no-referrer-when-downgrade"></iframe>
		<?php } else { ?>
			<div class="f-local-map" aria-label="<?php echo esc_attr__( 'Mapa showroomu Moravany u Brna', 'baspa' ); ?>">
				<img class="f-local-map__image" src="<?php echo esc_url( $figma_map ); ?>" width="3110" height="782" alt="<?php echo esc_attr__( 'Kontaktní mapa a showroom podle grafiky', 'baspa' ); ?>" loading="lazy" decoding="async">
				<span class="f-local-map__pin" aria-hidden="true"></span>
				<div class="f-local-map__card">
					<h2><?php echo esc_html__( 'Kde nás najdete?', 'baspa' ); ?></h2>
					<div class="f-local-map__grid">
						<div>
							<h3 class="f-local-map__card-label f-local-map__card-label--address"><?php echo esc_html__( 'Kde nás najdete', 'baspa' ); ?></h3>
							<p><?php echo esc_html__( 'Moravany u Brna', 'baspa' ); ?><br><?php echo esc_html__( 'Bohunická cesta 15', 'baspa' ); ?></p>
						</div>
						<div>
							<h3 class="f-local-map__card-label f-local-map__card-label--hours"><?php echo esc_html__( 'Otevírací doba', 'baspa' ); ?></h3>
							<p><?php echo esc_html( $hours_label ); ?><br><?php echo esc_html( $hours_line_1 ); ?><br><?php echo esc_html( $hours_line_2 ); ?></p>
						</div>
					</div>
					<p><?php echo esc_html__( 'Chcete si osobně prohlédnout řešení a pobavit se o možnostech realizace? Rádi se vám budeme věnovat v showroomu.', 'baspa' ); ?></p>
					<?php if ( !empty( $map_link ) ) { ?>
						<a class="f-button a-button a-button--outline" href="<?php echo esc_url( $map_link ); ?>" target="_blank" rel="noopener">
							<?php echo esc_html__( 'Zobrazit na mapě', 'baspa' ); ?>
						</a>
					<?php } else { ?>
						<a class="f-button a-button a-button--outline" href="<?php echo esc_url( home_url( '/showroom/' ) ); ?>">
							<?php echo esc_html__( 'Více o showroomu', 'baspa' ); ?>
						</a>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
</section>
