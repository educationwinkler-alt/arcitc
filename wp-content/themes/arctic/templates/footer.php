<?php

/**
 * Footer
 *
 * @since 1.0.0
 */

$phone = get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$email = get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );

$groups = array(
	array(
		'heading' => __( 'Vířivky', 'baspa' ),
		'items'   => array(
			__( 'Série Core', 'baspa' )      => home_url( '/virivky/?series=core' ),
			__( 'Série Classic', 'baspa' )   => home_url( '/virivky/?series=classic' ),
			__( 'Série Custom', 'baspa' )    => home_url( '/virivky/?series=custom' ),
			__( 'Skladove virivky', 'baspa' ) => home_url( '/virivky/' ),
		),
		'sections' => array(
			array(
				'heading' => __( 'Celoroční bazény', 'baspa' ),
				'items'   => array(
					__( 'Série Core', 'baspa' )    => home_url( '/swimspa/?series=core' ),
					__( 'Série Classic', 'baspa' ) => home_url( '/swimspa/?series=classic' ),
				),
			),
		),
	),
	array(
		'heading' => __( 'Vlastnosti vířivek', 'baspa' ),
		'items'   => array(
			__( 'Izolace vířivky', 'baspa' )        => home_url( '/vlastnosti/izolace-virivky/' ),
			__( 'Záruka na skořepinu', 'baspa' )    => home_url( '/zaruka/' ),
			__( 'Termokryt', 'baspa' )              => home_url( '/vlastnosti/#termokryt' ),
			__( 'Podlaha vířivky', 'baspa' )        => home_url( '/vlastnosti/#podlaha' ),
			__( 'Servisní přístup', 'baspa' )       => home_url( '/podpora/#servis' ),
			__( 'Variabilita', 'baspa' )            => home_url( '/vlastnosti/#variabilita' ),
			__( 'Automatická dezinfekce', 'baspa' ) => home_url( '/vlastnosti/#automaticka-dezinfekce' ),
		),
	),
	array(
		'heading' => __( 'Další informace', 'baspa' ),
		'items'   => array(
			__( 'Průběh realizace', 'baspa' )            => home_url( '/#order-progress' ),
			__( 'Podpora', 'baspa' )                     => home_url( '/podpora/' ),
			__( 'Služby', 'baspa' )                      => home_url( '/sluzby/' ),
			__( 'Kolik stojí provoz a údržba', 'baspa' ) => home_url( '/kolik-stoji-udrzba/' ),
			__( 'Časté otázky', 'baspa' )                => home_url( '/podpora/#faq' ),
			__( 'Reference', 'baspa' )                   => home_url( '/reference/' ),
			__( 'O nás', 'baspa' )                       => home_url( '/o-nas/' ),
			__( 'Showroom', 'baspa' )                    => home_url( '/showroom/' ),
			__( 'Servis', 'baspa' )                      => home_url( '/servis/' ),
			__( 'Kontakt', 'baspa' )                     => home_url( '/kontakt/' ),
		),
	),
);
?>

<footer id="<?php echo sanitize_title( esc_attr_x( 'footer', 'anchor', 'baspa' ) ); ?>" class="f-footer f-footer--arctic">
	<div class="f-footer__container a-container">
		<div class="f-footer__grid">
			<?php foreach ( $groups as $group ) { ?>
				<nav class="f-footer__group" aria-label="<?php echo esc_attr( $group['heading'] ); ?>">
					<h2><?php echo esc_html( $group['heading'] ); ?></h2>
					<ul>
						<?php foreach ( $group['items'] as $label => $url ) { ?>
							<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
						<?php } ?>
					</ul>
					<?php if ( !empty( $group['sections'] ) ) {
						foreach ( $group['sections'] as $section ) { ?>
							<h2><?php echo esc_html( $section['heading'] ); ?></h2>
							<ul>
								<?php foreach ( $section['items'] as $label => $url ) { ?>
									<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
								<?php } ?>
							</ul>
						<?php }
					} ?>
				</nav>
			<?php } ?>

			<aside class="f-footer__quick-contact">
				<div class="f-footer__quick-contact-body">
					<h2><?php echo esc_html__( 'Rychlý kontakt', 'baspa' ); ?></h2>
					<div class="f-footer__quick-person">
						<span class="f-footer__quick-avatar" aria-hidden="true"></span>
						<div>
							<strong><?php echo esc_html__( 'Lukáš Dušek', 'baspa' ); ?></strong>
							<span><?php echo esc_html__( 'Bazénový specialista', 'baspa' ); ?></span>
						</div>
					</div>
					<a href="mailto:<?php echo antispambot( esc_attr( $email ) ); ?>"><?php echo antispambot( esc_html( $email ) ); ?></a>
					<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<span class="f-footer__quick-hours"><?php echo esc_html__( 'Po - Pá 8:00-17:00 h', 'baspa' ); ?></span>
					<?php get_template_part( 'templates/button/contact', '', array(
						'text' => __( 'Nezávazná konzultace', 'baspa' ),
					) ); ?>
				</div>
				<div class="f-footer__quick-map">
					<span><?php echo esc_html__( 'Bohunická cesta 15', 'baspa' ); ?></span>
					<strong><?php echo esc_html__( 'Moravany u Brna', 'baspa' ); ?></strong>
					<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php echo esc_html__( 'Zobrazit na mapě', 'baspa' ); ?></a>
				</div>
			</aside>
		</div>

		<div class="f-footer__bottom">
			<div class="f-footer__copyright">
				<?php echo esc_html__( 'Copyright © 2024 Arctic Spas. Všechna práva vyhrazena.', 'baspa' ); ?>
			</div>
			<?php get_template_part( 'templates/logo' ); ?>
			<a href="<?php echo esc_url( home_url( '/ochrana-osobnich-udaju/' ) ); ?>"><?php echo esc_html__( 'Ochrana osobních údajů', 'baspa' ); ?></a>
		</div>
	</div>
</footer>
