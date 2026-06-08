<?php

/**
 * Shared quick contact card.
 */

$context      = !empty( $args['context'] ) ? (string) $args['context'] : 'contact_cta';
$title        = !empty( $args['title'] ) ? (string) $args['title'] : __( 'Potřebujete poradit?', 'baspa' );
$button_text  = !empty( $args['button_text'] ) ? (string) $args['button_text'] : __( 'Napsat zprávu', 'baspa' );
$extra_class  = !empty( $args['class'] ) && is_array( $args['class'] ) ? $args['class'] : array();
$member_id    = !empty( $args['member_id'] ) ? absint( $args['member_id'] ) : 0;
$card_classes = array_merge( array( 'f-quick-contact-card' ), $extra_class );
$is_product_card = in_array( 'f-product-contact-card', $card_classes, true );
$hours_classes   = array( 'f-quick-contact-card__hours' );
$avatar_class    = 'f-quick-contact-card__avatar';

if ( $is_product_card ) {
	$hours_classes[] = 'f-product-contact-card__hours';
	$avatar_class   .= ' f-product-contact-card__avatar';
}

$contact_member = array();
$settings       = function_exists( 'baspa_members_contact_settings' ) ? baspa_members_contact_settings() : array();
$option         = $settings[ $context ]['option'] ?? '';
$selected_id    = $option ? (int) get_option( $option, 0 ) : 0;

if ( $member_id > 0 && function_exists( 'baspa_member_data' ) ) {
	$contact_member = baspa_member_data( $member_id, get_template() . '-avatar' );
}

if ( empty( $contact_member ) && function_exists( 'baspa_members_get_selected_contact' ) ) {
	if ( $selected_id || 'contact_cta' === $context ) {
		$contact_member = baspa_members_get_selected_contact( $context, get_template() . '-avatar' );
	}

	if ( empty( $contact_member ) && 'contact_cta' !== $context ) {
		$contact_member = baspa_members_get_selected_contact( 'contact_cta', get_template() . '-avatar' );
	}
}

$email        = !empty( $contact_member['email'] ) ? $contact_member['email'] : get_theme_mod( 'baspa_email', 'info@arctic-spas.cz' );
$phone        = !empty( $contact_member['phone'] ) ? $contact_member['phone'] : get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$fallback_name = trim( (string) get_theme_mod( 'baspa_name', get_bloginfo( 'name' ) ) );
$contact_name = !empty( $contact_member['name'] ) ? $contact_member['name'] : ( $fallback_name !== '' ? $fallback_name : __( 'Arctic Spas', 'baspa' ) );
$contact_role = !empty( $contact_member['position'] ) ? $contact_member['position'] : __( 'Rychly kontakt', 'baspa' );
$contact_source = !empty( $contact_member['source'] ) ? $contact_member['source'] : 'customizer-about';
$fallback_initial = function_exists( 'mb_substr' ) ? mb_substr( $contact_name, 0, 1 ) : substr( $contact_name, 0, 1 );
$fallback_initial = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $fallback_initial ) : strtoupper( $fallback_initial );
$avatar_member = !empty( $contact_member ) ? $contact_member : array(
	'name'         => $contact_name,
	'initials'     => function_exists( 'baspa_member_initials' ) ? baspa_member_initials( $contact_name ) : $fallback_initial,
	'asset_status' => 'admin-empty',
);
$hours        = apply_filters( 'forqy_hours', array() );
$hours_label  = function_exists( 'baspa_hours_bar_label' ) ? baspa_hours_bar_label( $hours ) : __( 'Po - Pá 8:00-17:00 h', 'baspa' );
$phone_href   = function_exists( 'baspa_member_phone_href' ) ? baspa_member_phone_href( $phone ) : str_replace( ' ', '', $phone );
?>

<div class="<?php echo esc_attr( implode( ' ', array_filter( $card_classes ) ) ); ?>"
     data-content-source="<?php echo esc_attr( $contact_source ); ?>"
     data-contact-context="<?php echo esc_attr( $context ); ?>"
     <?php echo !empty( $contact_member['id'] ) ? 'data-member-id="' . esc_attr( (string) $contact_member['id'] ) . '"' : ''; ?>>
	<h3><?php echo esc_html( $title ); ?></h3>

	<div class="f-quick-contact-card__details">
		<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
		<a href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a>
		<?php get_template_part( 'templates/about/hours', '', array(
			'class' => $hours_classes,
			'label' => $hours_label,
		) ); ?>
	</div>

	<div class="f-quick-contact-card__person">
		<?php if ( function_exists( 'baspa_member_avatar_html' ) ) {
			echo baspa_member_avatar_html( $avatar_member, $avatar_class, get_template() . '-avatar', 'eager' );
		} else { ?>
			<span class="<?php echo esc_attr( $avatar_class . ' ' . $avatar_class . '--waiting' ); ?>" data-asset-status="admin-empty" aria-hidden="true"><?php echo esc_html( $fallback_initial ); ?></span>
		<?php } ?>
		<div class="f-quick-contact-card__identity">
			<strong><?php echo esc_html( $contact_name ); ?></strong>
			<span><?php echo esc_html( $contact_role ); ?></span>
		</div>
	</div>

	<?php get_template_part( 'templates/button/contact', '', array(
		'text'          => $button_text,
		'class_replace' => array(
			'f-quick-contact-card__button',
			'f-button',
			'a-button',
			'a-button--outline',
			'f-off__trigger',
			'js-off__trigger',
		),
	) ); ?>
</div>
