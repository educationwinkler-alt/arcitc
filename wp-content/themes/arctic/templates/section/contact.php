<?php

/**
 * Contact CTA Section
 */

$email         = get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );
$phone         = get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$avatar        = content_url( 'uploads/import/figma/contact-lukas-dusek.png' );
$contact_title = __( 'Potřebujete poradit s výběrem vhodné vířivky?', 'baspa' );
$hours         = apply_filters( 'forqy_hours', array() );
$hours_label   = function_exists( 'baspa_hours_bar_label' ) ? baspa_hours_bar_label( $hours ) : __( 'Po - Pá 8:00-17:00 h', 'baspa' );

if ( is_tax( 'product-category', 'swimspa' ) || ( is_singular( 'product' ) && has_term( 'swimspa', 'product-category', get_the_ID() ) ) ) {
	$contact_title = __( 'Potřebujete poradit s výběrem vhodného bazénu?', 'baspa' );
} elseif ( is_tax( 'product-category', 'dalsi-sortiment' ) || ( is_singular( 'product' ) && has_term( 'dalsi-sortiment', 'product-category', get_the_ID() ) ) ) {
	$contact_title = __( 'Potřebujete poradit s výběrem vhodného řešení?', 'baspa' );
}
?>

<section class="f-section f-section--contact f-section--component-contact">
	<div class="f-section__container a-container">
		<div class="f-contact-cta f-contact-cta--shared">
			<h2><?php echo esc_html( $contact_title ); ?></h2>
			<p><?php echo esc_html__( 'Dejte nám vědět a naši specialisté na bazény se vám budou věnovat.', 'baspa' ); ?></p>
			<div class="f-contact-cta__bar">
				<div class="f-contact-cta__person">
					<span class="f-contact-cta__avatar" aria-hidden="true">
						<img src="<?php echo esc_url( $avatar ); ?>" alt="" loading="eager" decoding="async">
					</span>
					<div>
						<strong><?php echo esc_html__( 'Lukáš Dušek', 'baspa' ); ?></strong>
						<span><?php echo esc_html__( 'Bazénový specialista', 'baspa' ); ?></span>
					</div>
				</div>
				<div class="f-contact-cta__details">
					<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
					<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<?php get_template_part( 'templates/about/hours', '', array(
						'class' => array( 'f-contact-cta__hours' ),
						'label' => $hours_label,
					) ); ?>
				</div>
				<?php get_template_part(
					'templates/button/contact',
					'',
					array(
						'text'          => __( 'Napsat zprávu', 'baspa' ),
						'class_replace' => array(
							'f-button',
							'f-button--outline',
							'f-button--reversed',
							'a-button',
							'a-button--outline',
							'f-off__trigger',
							'js-off__trigger',
						),
					)
				); ?>
			</div>
		</div>
	</div>
</section>
