<?php

/**
 * Contact Directory
 *
 * Contact data follows the current Figma contact frame. Person portraits are not
 * owner-verified yet, so avatars stay explicit WAITING_ON_OWNER placeholders.
 */

$contacts = array(
	array(
		'name'         => __( 'Vlastimil Zhoř', 'baspa' ),
		'role'         => __( 'Jednatel společnosti', 'baspa' ),
		'scope'        => __( 'Prodej vířivek', 'baspa' ),
		'email'        => 'vlastimil.zhor@baspa.cz',
		'phone'        => '+420 602 545 067',
		'initials'     => 'VZ',
		'asset_status' => 'WAITING_ON_OWNER',
	),
	array(
		'name'         => __( 'Ing. Lukáš Dušek', 'baspa' ),
		'role'         => __( 'Jednatel společnosti', 'baspa' ),
		'scope'        => __( 'Prodej bazénů', 'baspa' ),
		'email'        => 'lukas.dusek@baspa.cz',
		'phone'        => '+420 602 774 195',
		'initials'     => 'LD',
		'asset_status' => 'WAITING_ON_OWNER',
	),
	array(
		'name'         => __( 'Helena Antonyová', 'baspa' ),
		'role'         => __( 'Prodej bazénů', 'baspa' ),
		'scope'        => __( 'Prodej bazénů a příslušenství', 'baspa' ),
		'email'        => 'helena.antonyova@baspa.cz',
		'phone'        => '+420 777 099 687',
		'initials'     => 'HA',
		'asset_status' => 'WAITING_ON_OWNER',
	),
	array(
		'name'         => __( 'Alena Janulíková', 'baspa' ),
		'role'         => __( 'Logistika, fakturace', 'baspa' ),
		'scope'        => __( 'Logistika, fakturace, showroom Moravany', 'baspa' ),
		'email'        => 'alena.janulikova@baspa.cz',
		'phone'        => '+420 792 640 005',
		'initials'     => 'AJ',
		'asset_status' => 'WAITING_ON_OWNER',
	),
	array(
		'name'         => __( 'Bc. Tomáš Koutný', 'baspa' ),
		'role'         => __( 'Prodej vířivek', 'baspa' ),
		'scope'        => __( 'Prodej vířivek a koupacích sudů', 'baspa' ),
		'email'        => 'tomas.koutny@baspa.cz',
		'phone'        => '+420 602 149 106',
		'initials'     => 'TK',
		'asset_status' => 'WAITING_ON_OWNER',
	),
	array(
		'name'         => __( 'Pavel Nováček', 'baspa' ),
		'role'         => __( 'Vedoucí technického úseku', 'baspa' ),
		'scope'        => __( 'Organizace montáží a servisů', 'baspa' ),
		'email'        => 'pavel.novacek@baspa.cz',
		'phone'        => '+420 774 080 775',
		'initials'     => 'PN',
		'asset_status' => 'WAITING_ON_OWNER',
	),
);
?>

<section class="f-section f-section--contact-directory">
	<div class="f-section__container a-container">
		<h2><?php echo esc_html__( 'Další důležité kontakty', 'baspa' ); ?></h2>
		<div class="f-contact-directory">
			<?php foreach ( $contacts as $contact ) { ?>
				<article class="f-contact-card" data-content-source="figma-contact-frame">
					<div class="f-contact-card__avatar f-contact-card__avatar--waiting" data-asset-status="<?php echo esc_attr( $contact['asset_status'] ); ?>" aria-hidden="true">
						<?php echo esc_html( $contact['initials'] ); ?>
					</div>
					<h3><?php echo esc_html( $contact['name'] ); ?></h3>
					<p class="f-contact-card__role"><?php echo esc_html( $contact['role'] ); ?></p>
					<p><?php echo esc_html( $contact['scope'] ); ?></p>
					<a href="mailto:<?php echo antispambot( esc_attr( $contact['email'] ) ); ?>">
						<?php echo antispambot( esc_html( $contact['email'] ) ); ?>
					</a>
					<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $contact['phone'] ) ); ?>">
						<?php echo esc_html( $contact['phone'] ); ?>
					</a>
				</article>
			<?php } ?>
		</div>

		<div class="f-billing-box">
			<h2><?php echo esc_html__( 'Fakturační údaje', 'baspa' ); ?></h2>
			<p>
				<strong><?php echo esc_html__( 'BASPA s.r.o.', 'baspa' ); ?></strong><br>
				<?php echo esc_html__( 'Bohunická cesta 727/15, 664 48 Moravany', 'baspa' ); ?><br><br>
				<?php echo esc_html__( 'IČ 02257467', 'baspa' ); ?><br>
				<?php echo esc_html__( 'DIČ CZ02257467', 'baspa' ); ?><br><br>
				<?php echo esc_html__( 'Společnost je zapsána v obchodním rejstříku vedeném u Krajského soudu v Brně, oddíl C, vložka 80736.', 'baspa' ); ?>
			</p>
		</div>
	</div>
</section>
