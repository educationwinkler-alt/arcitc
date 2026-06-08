<?php

/**
 * Contact Directory
 */

$contacts       = array();
$contacts_query = function_exists( 'baspa_members_query_contacts' ) ? baspa_members_query_contacts() : null;

if ( $contacts_query instanceof WP_Query ) {
	foreach ( $contacts_query->posts as $member_post ) {
		$contact = function_exists( 'baspa_member_data' ) ? baspa_member_data( $member_post, get_template() . '-avatar' ) : array();

		if ( !empty( $contact ) ) {
			$contacts[] = $contact;
		}
	}
}

$billing_title    = get_theme_mod( 'baspa_billing_title', __( 'Fakturační údaje', 'baspa' ) );
$billing_company  = get_theme_mod( 'baspa_billing_company', __( 'BASPA s.r.o.', 'baspa' ) );
$billing_address  = get_theme_mod( 'baspa_billing_address', __( 'Bohunická cesta 727/15, 664 48 Moravany', 'baspa' ) );
$billing_ico      = get_theme_mod( 'baspa_billing_ico', __( 'IČ 02257467', 'baspa' ) );
$billing_dic      = get_theme_mod( 'baspa_billing_dic', __( 'DIČ CZ02257467', 'baspa' ) );
$billing_registry = get_theme_mod( 'baspa_billing_registry', __( 'Společnost je zapsána v obchodním rejstříku vedeném u Krajského soudu v Brně, oddíl C, vložka 80736.', 'baspa' ) );
?>

<section class="f-section f-section--contact-directory">
	<div class="f-section__container a-container">
		<?php if ( !empty( $contacts ) ) { ?>
			<h2><?php echo esc_html__( 'Další důležité kontakty', 'baspa' ); ?></h2>
			<div class="f-contact-directory">
				<?php foreach ( $contacts as $contact ) { ?>
					<article class="f-contact-card" data-content-source="<?php echo esc_attr( $contact['source'] ?? 'admin-member' ); ?>" data-member-id="<?php echo esc_attr( (int) ( $contact['id'] ?? 0 ) ); ?>">
						<?php if ( function_exists( 'baspa_member_avatar_html' ) ) {
							echo baspa_member_avatar_html( $contact, 'f-contact-card__avatar', get_template() . '-avatar' );
						} ?>
						<h3><?php echo esc_html( $contact['name'] ?? '' ); ?></h3>
						<?php if ( !empty( $contact['position'] ) ) { ?>
							<p class="f-contact-card__role"><?php echo esc_html( $contact['position'] ); ?></p>
						<?php } ?>
						<?php if ( !empty( $contact['scope'] ) ) { ?>
							<p><?php echo esc_html( $contact['scope'] ); ?></p>
						<?php } ?>
						<?php if ( !empty( $contact['email'] ) ) { ?>
							<a href="mailto:<?php echo antispambot( esc_attr( $contact['email'] ) ); ?>">
								<?php echo antispambot( esc_html( $contact['email'] ) ); ?>
							</a>
						<?php }
						if ( !empty( $contact['phone'] ) ) { ?>
							<a href="tel:<?php echo esc_attr( function_exists( 'baspa_member_phone_href' ) ? baspa_member_phone_href( $contact['phone'] ) : preg_replace( '/\s+/', '', $contact['phone'] ) ); ?>">
								<?php echo esc_html( $contact['phone'] ); ?>
							</a>
						<?php } ?>
					</article>
				<?php } ?>
			</div>
		<?php } ?>

		<div class="f-billing-box" data-content-source="customizer-about">
			<h2><?php echo esc_html( $billing_title ); ?></h2>
			<p>
				<?php if ( '' !== trim( (string) $billing_company ) ) { ?>
					<strong><?php echo esc_html( $billing_company ); ?></strong><br>
				<?php }
				if ( '' !== trim( (string) $billing_address ) ) { ?>
					<?php echo esc_html( $billing_address ); ?><br><br>
				<?php }
				if ( '' !== trim( (string) $billing_ico ) ) { ?>
					<?php echo esc_html( $billing_ico ); ?><br>
				<?php }
				if ( '' !== trim( (string) $billing_dic ) ) { ?>
					<?php echo esc_html( $billing_dic ); ?><br><br>
				<?php }
				if ( '' !== trim( (string) $billing_registry ) ) { ?>
					<?php echo nl2br( esc_html( $billing_registry ) ); ?>
				<?php } ?>
			</p>
		</div>
	</div>
</section>
