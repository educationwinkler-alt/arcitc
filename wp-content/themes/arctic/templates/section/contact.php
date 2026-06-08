<?php

/**
 * Contact CTA Section
 */

$contact_member = function_exists( 'baspa_members_get_selected_contact' ) ? baspa_members_get_selected_contact( 'contact_cta', get_template() . '-avatar' ) : array();
$email          = !empty( $contact_member['email'] ) ? $contact_member['email'] : get_theme_mod( 'baspa_email', 'info@arctic-spas.cz' );
$phone          = !empty( $contact_member['phone'] ) ? $contact_member['phone'] : get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$fallback_name  = trim( (string) get_theme_mod( 'baspa_name', get_bloginfo( 'name' ) ) );
$contact_name   = !empty( $contact_member['name'] ) ? $contact_member['name'] : ( $fallback_name !== '' ? $fallback_name : __( 'Arctic Spas', 'baspa' ) );
$contact_role   = !empty( $contact_member['position'] ) ? $contact_member['position'] : __( 'Rychly kontakt', 'baspa' );
$contact_source = !empty( $contact_member['source'] ) ? $contact_member['source'] : 'customizer-about';
$fallback_initial = function_exists( 'mb_substr' ) ? mb_substr( $contact_name, 0, 1 ) : substr( $contact_name, 0, 1 );
$fallback_initial = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $fallback_initial ) : strtoupper( $fallback_initial );
$avatar_member  = !empty( $contact_member ) ? $contact_member : array(
	'name'         => $contact_name,
	'initials'     => function_exists( 'baspa_member_initials' ) ? baspa_member_initials( $contact_name ) : $fallback_initial,
	'asset_status' => 'admin-empty',
);
$contact_title  = __( 'Potřebujete poradit s výběrem vhodné vířivky?', 'baspa' );
$hours          = apply_filters( 'forqy_hours', array() );
$hours_label    = function_exists( 'baspa_hours_bar_label' ) ? baspa_hours_bar_label( $hours ) : __( 'Po - Pá 8:00-17:00 h', 'baspa' );

if ( is_tax( 'product-category', 'swimspa' ) || ( is_singular( 'product' ) && has_term( 'swimspa', 'product-category', get_the_ID() ) ) ) {
	$contact_title = __( 'Potřebujete poradit s výběrem vhodného bazénu?', 'baspa' );
} elseif ( is_tax( 'product-category', 'dalsi-sortiment' ) || ( is_singular( 'product' ) && has_term( 'dalsi-sortiment', 'product-category', get_the_ID() ) ) ) {
	$contact_title = __( 'Potřebujete poradit s výběrem vhodného řešení?', 'baspa' );
}
?>

<section class="f-section f-section--contact f-section--component-contact">
	<div class="f-section__container a-container">
		<div class="f-contact-cta f-contact-cta--shared"
		     <?php echo !empty( $contact_member['id'] ) ? 'data-member-id="' . esc_attr( (string) $contact_member['id'] ) . '"' : ''; ?>
		     data-content-source="<?php echo esc_attr( $contact_source ); ?>">
			<h2><?php echo esc_html( $contact_title ); ?></h2>
			<p><?php echo esc_html__( 'Dejte nám vědět a naši specialisté na bazény se vám budou věnovat.', 'baspa' ); ?></p>
			<div class="f-contact-cta__bar">
				<div class="f-contact-cta__person">
					<?php if ( function_exists( 'baspa_member_avatar_html' ) ) {
						echo baspa_member_avatar_html( $avatar_member, 'f-contact-cta__avatar', get_template() . '-avatar', 'eager' );
					} else { ?>
						<span class="f-contact-cta__avatar f-contact-cta__avatar--waiting" data-asset-status="admin-empty" aria-hidden="true"><?php echo esc_html( $fallback_initial ); ?></span>
					<?php } ?>
					<div>
						<strong><?php echo esc_html( $contact_name ); ?></strong>
						<span><?php echo esc_html( $contact_role ); ?></span>
					</div>
				</div>
				<div class="f-contact-cta__details">
					<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
					<a href="tel:<?php echo esc_attr( function_exists( 'baspa_member_phone_href' ) ? baspa_member_phone_href( $phone ) : str_replace( ' ', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
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
