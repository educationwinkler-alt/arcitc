<?php

/**
 * Map Section
 */

$map_embed = get_theme_mod( 'arctic_map_embed' );
$map_link  = get_theme_mod( 'baspa_map' );
$can_embed = !empty( $map_embed ) && ( !function_exists( 'wp_get_environment_type' ) || 'local' !== wp_get_environment_type() );

$figma_map = get_posts( array(
	'post_type'      => 'attachment',
	'post_status'    => 'inherit',
	'posts_per_page' => 1,
	'fields'         => 'ids',
	'meta_key'       => '_arctic_seed_key',
	'meta_value'     => 'figma-node-1-1069-contact-map-showroom',
) );
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
				<?php if ( !empty( $figma_map ) ) { ?>
					<?php echo wp_get_attachment_image( (int) $figma_map[0], 'huge', false, array(
						'class' => 'f-local-map__image',
					) ); ?>
				<?php } ?>
				<span class="f-local-map__label f-local-map__label--brno" aria-hidden="true">Brno</span>
				<span class="f-local-map__label f-local-map__label--moravany" aria-hidden="true">Moravany</span>
				<span class="f-local-map__pin" aria-hidden="true"></span>
				<div class="f-local-map__card">
					<h2><?php echo esc_html__( 'Kde nás najdete?', 'baspa' ); ?></h2>
					<div class="f-local-map__grid">
						<div>
							<h3><?php echo esc_html__( 'Adresa', 'baspa' ); ?></h3>
							<p><?php echo esc_html__( 'Bohunická cesta 15', 'baspa' ); ?><br><?php echo esc_html__( '664 48 Moravany u Brna', 'baspa' ); ?></p>
						</div>
						<div>
							<h3><?php echo esc_html__( 'Otevírací doba', 'baspa' ); ?></h3>
							<p><?php echo esc_html__( 'Po - Pá 8:00-17:00 h', 'baspa' ); ?></p>
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
