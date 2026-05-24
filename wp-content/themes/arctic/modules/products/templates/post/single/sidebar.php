<?php

/**
 * Product detail Figma contact card.
 */

$email  = get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );
$phone  = get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$avatar = content_url( 'uploads/import/figma/contact-lukas-dusek.png' );
?>

<aside class="f-sidebar f-sidebar--sticky a-stack a-gap--m">
	<div class="f-product-contact-card">
		<h3><?php echo esc_html__( 'Potřebujete poradit?', 'baspa' ); ?></h3>

		<div class="f-product-contact-card__details">
			<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
			<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
			<span><?php echo esc_html__( 'Po - Pá 8:00-17:00 h', 'baspa' ); ?></span>
		</div>

		<div class="f-product-contact-card__person">
			<span class="f-product-contact-card__avatar" aria-hidden="true">
				<img src="<?php echo esc_url( $avatar ); ?>" alt="" loading="eager" decoding="async">
			</span>
			<div>
				<strong><?php echo esc_html__( 'Lukáš Dušek', 'baspa' ); ?></strong>
				<span><?php echo esc_html__( 'Bazénový specialista', 'baspa' ); ?></span>
			</div>
		</div>

		<a class="f-product-contact-card__button f-button a-button a-button--outline" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">
			<?php echo esc_html__( 'Napsat zprávu', 'baspa' ); ?>
		</a>
	</div>
</aside>
