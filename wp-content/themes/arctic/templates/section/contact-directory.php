<?php

/**
 * Contact Directory
 */

$phone_lukas   = '+420 777 099 687';
$email_lukas   = get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );
$phone_vlastik = '+420 602 545 067';
$email_vlastik = 'vlastimil.zhor@arctic-spas.cz';
$phone_servis  = get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$email_servis  = get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );

$contacts = array(
	array(
		'name'     => __( 'Lukáš Dušek', 'baspa' ),
		'role'     => __( 'Obchodní konzultant', 'baspa' ),
		'scope'    => __( 'Prodej vířivek, bazénů a konzultace realizací', 'baspa' ),
		'email'    => $email_lukas,
		'phone'    => $phone_lukas,
		'initials' => 'LD',
	),
	array(
		'name'     => __( 'Vlastimil Zhoř', 'baspa' ),
		'role'     => __( 'Spolumajitel', 'baspa' ),
		'scope'    => __( 'Maloobchodní prodej a showroom', 'baspa' ),
		'email'    => $email_vlastik,
		'phone'    => $phone_vlastik,
		'initials' => 'VZ',
	),
	array(
		'name'     => __( 'Servisní tým', 'baspa' ),
		'role'     => __( 'Podpora zákazníků', 'baspa' ),
		'scope'    => __( 'Pomoc s provozem, montáž a technické dotazy', 'baspa' ),
		'email'    => $email_servis,
		'phone'    => $phone_servis,
		'initials' => 'ST',
	),
);
?>

<section class="f-section f-section--contact-directory">
	<div class="f-section__container a-container">
		<h2><?php echo esc_html__( 'Kontaktní osoby', 'baspa' ); ?></h2>
		<div class="f-contact-directory">
			<?php foreach ( $contacts as $contact ) { ?>
				<article class="f-contact-card">
					<div class="f-contact-card__avatar" aria-hidden="true">
						<?php echo esc_html( $contact['initials'] ); ?>
					</div>
					<h3><?php echo esc_html( $contact['name'] ); ?></h3>
					<p class="f-contact-card__role"><?php echo esc_html( $contact['role'] ); ?></p>
					<p><?php echo esc_html( $contact['scope'] ); ?></p>
					<a href="mailto:<?php echo antispambot( esc_attr( $contact['email'] ) ); ?>">
						<?php echo antispambot( esc_html( $contact['email'] ) ); ?>
					</a>
					<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $contact['phone'] ) ); ?>">
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
