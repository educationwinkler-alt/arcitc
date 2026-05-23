<?php

/**
 * Footer
 *
 * @since 1.0.0
 */

$phone = get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$email = get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );

$groups = array(
	__( 'Vířivky', 'baspa' ) => array(
		__( 'Série Core', 'baspa' )   => home_url( '/catalog/virivky/?series=core' ),
		__( 'Série Classic', 'baspa' ) => home_url( '/catalog/virivky/?series=classic' ),
		__( 'Série Custom', 'baspa' )  => home_url( '/catalog/virivky/?series=custom' ),
		__( 'Výprodej vířivek', 'baspa' ) => home_url( '/catalog/virivky/' ),
	),
	__( 'Vlastnosti vířivek', 'baspa' ) => array(
		__( 'Izolace vířivky', 'baspa' ) => home_url( '/vlastnosti/' ),
		__( 'Záruka', 'baspa' )          => home_url( '/zaruka/' ),
		__( 'Termokryt', 'baspa' )       => home_url( '/vlastnosti/' ),
		__( 'Servisní přístup', 'baspa' ) => home_url( '/podpora/' ),
	),
	__( 'Další informace', 'baspa' ) => array(
		__( 'Průběh realizace', 'baspa' ) => home_url( '/dalsi-informace/' ),
		__( 'Podpora', 'baspa' )          => home_url( '/podpora/' ),
		__( 'Ke stažení', 'baspa' )       => home_url( '/ke-stazeni/' ),
		__( 'Showroom', 'baspa' )         => home_url( '/showroom/' ),
		__( 'Kontakt', 'baspa' )          => home_url( '/kontakt/' ),
	),
);
?>

<footer id="<?php echo sanitize_title( esc_attr_x( 'footer', 'anchor', 'baspa' ) ); ?>" class="f-footer f-footer--arctic">
	<div class="f-footer__container a-container">
		<div class="f-footer__grid">
			<?php foreach ( $groups as $heading => $items ) { ?>
				<nav class="f-footer__group" aria-label="<?php echo esc_attr( $heading ); ?>">
					<h2><?php echo esc_html( $heading ); ?></h2>
					<ul>
						<?php foreach ( $items as $label => $url ) { ?>
							<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
						<?php } ?>
					</ul>
				</nav>
			<?php } ?>

			<aside class="f-footer__quick-contact">
				<div class="f-footer__quick-contact-body">
					<h2><?php echo esc_html__( 'Rychlý kontakt', 'baspa' ); ?></h2>
					<strong><?php echo esc_html__( 'Lukáš Dušek', 'baspa' ); ?></strong>
					<span><?php echo esc_html__( 'Bazénový specialista', 'baspa' ); ?></span>
					<a href="mailto:<?php echo antispambot( esc_attr( $email ) ); ?>"><?php echo antispambot( esc_html( $email ) ); ?></a>
					<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<?php get_template_part( 'templates/button/contact', '', array(
						'text' => __( 'Nezávazná konzultace', 'baspa' ),
					) ); ?>
				</div>
				<div class="f-footer__quick-map">
					<span><?php echo esc_html__( 'Bohunická cesta 15', 'baspa' ); ?></span>
					<strong><?php echo esc_html__( 'Moravany u Brna', 'baspa' ); ?></strong>
					<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php echo esc_html__( 'Zobrazit kontakt', 'baspa' ); ?></a>
				</div>
			</aside>
		</div>

		<div class="f-footer__bottom">
			<div class="f-footer__copyright">
				<?php echo '&copy;&nbsp;' . esc_html( date( 'Y' ) ) . '&nbsp;' . '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>. ' . esc_html__( 'Všechna práva vyhrazena.', 'baspa' ); ?>
			</div>
			<?php get_template_part( 'templates/logo' ); ?>
			<a href="<?php echo esc_url( home_url( '/ochrana-osobnich-udaju/' ) ); ?>"><?php echo esc_html__( 'Ochrana osobních údajů', 'baspa' ); ?></a>
		</div>
	</div>
</footer>
