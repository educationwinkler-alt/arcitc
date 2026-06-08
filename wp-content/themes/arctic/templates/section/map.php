<?php

/**
 * Map Section
 */

$map_embed = function_exists( 'arctic_get_map_embed_url' ) ? arctic_get_map_embed_url() : get_theme_mod( 'arctic_map_embed', '' );
$map_link  = function_exists( 'arctic_get_map_url' ) ? arctic_get_map_url() : get_theme_mod( 'baspa_map', 'https://maps.app.goo.gl/ZsYfoZ2aQGF1JnZG6' );
$can_embed = !empty( $map_embed );

$figma_map    = content_url( 'uploads/import/figma/contact-map-showroom.png' );
$hours_label  = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_label', 'Úterý - Pátek' ) : get_theme_mod( 'arctic_showroom_hours_label', 'Úterý - Pátek' );
$hours_line_1 = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_line_1', '9:00 - 11:30' ) : get_theme_mod( 'arctic_showroom_hours_line_1', '9:00 - 11:30' );
$hours_line_2 = function_exists( 'arctic_sections_get_theme_mod' ) ? arctic_sections_get_theme_mod( 'arctic_showroom_hours_line_2', '12:30 - 16:00' ) : get_theme_mod( 'arctic_showroom_hours_line_2', '12:30 - 16:00' );
?>

<section class="f-section f-section--map">
	<div class="f-section__map <?php echo esc_attr( $can_embed ? 'f-section__map--embed' : 'f-section__map--local' ); ?>">
		<div class="f-local-map <?php echo esc_attr( $can_embed ? 'f-local-map--embed' : 'f-local-map--fallback' ); ?>"
		     data-content-source="<?php echo esc_attr( $can_embed ? 'customizer-map-embed' : 'figma-map-fallback' ); ?>"
		     role="region"
		     aria-label="<?php echo esc_attr__( 'Mapa showroomu Moravany u Brna', 'baspa' ); ?>">
			<?php if ( $can_embed ) { ?>
				<iframe class="f-local-map__iframe"
				        src="<?php echo esc_url( $map_embed ); ?>"
				        width="1920"
				        height="782"
				        style="border:0;"
				        allowfullscreen=""
				        loading="lazy"
				        title="<?php echo esc_attr__( 'Interaktivní mapa showroomu Moravany u Brna', 'baspa' ); ?>"
				        referrerpolicy="no-referrer-when-downgrade"></iframe>
			<?php } else { ?>
				<img class="f-local-map__image" src="<?php echo esc_url( $figma_map ); ?>" width="3110" height="782" alt="<?php echo esc_attr__( 'Kontaktní mapa a showroom podle grafiky', 'baspa' ); ?>" loading="lazy" decoding="async">
			<?php } ?>

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
				<a class="f-button a-button a-button--outline" href="<?php echo esc_url( $map_link ); ?>" target="_blank" rel="noopener">
					<?php echo esc_html__( 'Zobrazit na mapě', 'baspa' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
