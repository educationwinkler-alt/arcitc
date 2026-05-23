<?php

/**
 * Contact Directory
 */

$phone = get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$email = get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' );

$contacts = array(
	array(
		'name'  => __( 'Lukáš Dušek', 'baspa' ),
		'role'  => __( 'Bazénový specialista', 'baspa' ),
		'scope' => __( 'Prodej vířivek, bazénů a konzultace realizací', 'baspa' ),
		'email' => $email,
		'phone' => $phone,
	),
	array(
		'name'  => __( 'Showroom Moravany', 'baspa' ),
		'role'  => __( 'Osobní výběr a ukázky', 'baspa' ),
		'scope' => __( 'Bohunická cesta 15, 664 48 Moravany u Brna', 'baspa' ),
		'email' => $email,
		'phone' => $phone,
	),
	array(
		'name'  => __( 'Servisní požadavky', 'baspa' ),
		'role'  => __( 'Podpora zákazníků', 'baspa' ),
		'scope' => __( 'Pomoc s provozem, údržbou a technickými dotazy', 'baspa' ),
		'email' => $email,
		'phone' => $phone,
	),
);
?>

<section class="f-section f-section--contact-directory">
	<div class="f-section__container a-container">
		<h2><?php echo esc_html__( 'Další důležité kontakty', 'baspa' ); ?></h2>
		<div class="f-contact-directory">
			<?php foreach ( $contacts as $contact ) { ?>
				<article class="f-contact-card">
					<div class="f-contact-card__avatar" aria-hidden="true">
						<?php
						$initial = function_exists( 'mb_substr' ) ? mb_substr( $contact['name'], 0, 1 ) : substr( $contact['name'], 0, 1 );
						echo esc_html( $initial );
						?>
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
				<strong><?php echo esc_html__( 'Arctic Spas CZ', 'baspa' ); ?></strong><br>
				<?php echo esc_html__( 'Bohunická cesta 15, 664 48 Moravany u Brna', 'baspa' ); ?><br>
				<?php echo esc_html__( 'IČ: bude doplněno před produkcí', 'baspa' ); ?>
			</p>
		</div>
	</div>
</section>
