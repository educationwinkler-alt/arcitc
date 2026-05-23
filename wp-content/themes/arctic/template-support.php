<?php

/**
 * Template Name: Support
 */

get_header();
get_template_part( 'templates/heading/default' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
      class="f-main f-main--support f-main--top-0">

	<section class="f-section f-section--support-tabs">
		<div class="f-section__container a-container">
			<nav class="f-support-tabs" aria-label="<?php echo esc_attr__( 'Podpora', 'baspa' ); ?>">
				<a href="#caste-dotazy"><?php echo esc_html__( 'Časté dotazy', 'baspa' ); ?></a>
				<a href="#ke-stazeni"><?php echo esc_html__( 'Ke stažení', 'baspa' ); ?></a>
				<a href="#servisni-formular"><?php echo esc_html__( 'Servisní formulář', 'baspa' ); ?></a>
			</nav>
		</div>
	</section>

	<section id="caste-dotazy" class="f-section f-section--support-faq">
		<div class="f-section__container a-container">
			<div class="f-support-layout">
				<div class="f-support-layout__main">
					<h2><?php echo esc_html__( 'Časté dotazy', 'baspa' ); ?></h2>
					<div class="f-chip-list">
						<span class="is-active"><?php echo esc_html__( 'Všechny', 'baspa' ); ?></span>
						<span><?php echo esc_html__( 'Obchodní', 'baspa' ); ?></span>
						<span><?php echo esc_html__( 'Stavební příprava', 'baspa' ); ?></span>
						<span><?php echo esc_html__( 'Montáž', 'baspa' ); ?></span>
						<span><?php echo esc_html__( 'Provoz a údržba', 'baspa' ); ?></span>
					</div>

					<div class="f-support-accordion">
						<?php
						$questions = array(
							array(
								'title' => __( 'Jak probíhá výběr a objednávka vířivky?', 'baspa' ),
								'text'  => __( 'Nejprve společně ověříme velikost, umístění a požadovanou výbavu. Poté připravíme konkrétní konfiguraci a navazující technickou přípravu.', 'baspa' ),
								'tag'   => __( 'Obchodní', 'baspa' ),
							),
							array(
								'title' => __( 'Co je potřeba připravit před instalací?', 'baspa' ),
								'text'  => __( 'Důležitý je pevný podklad, přívod elektřiny a přístupová cesta pro usazení vířivky. Detaily řešíme podle konkrétního modelu.', 'baspa' ),
								'tag'   => __( 'Stavební příprava', 'baspa' ),
							),
							array(
								'title' => __( 'Zajišťujete dopravu a montáž?', 'baspa' ),
								'text'  => __( 'Ano, u nových realizací počítáme s dopravou, usazením a základním zaškolením obsluhy.', 'baspa' ),
								'tag'   => __( 'Montáž', 'baspa' ),
							),
							array(
								'title' => __( 'Jak náročná je běžná údržba vody?', 'baspa' ),
								'text'  => __( 'Údržba závisí na výbavě, četnosti používání a režimu filtrace. U Arctic Spas lze volit technologie, které péči výrazně zjednodušují.', 'baspa' ),
								'tag'   => __( 'Provoz', 'baspa' ),
							),
						);

						foreach ( $questions as $index => $question ) { ?>
							<article class="f-support-faq-card <?php echo 0 === $index ? 'is-open' : ''; ?>">
								<div class="f-support-faq-card__icon"><?php echo 0 === $index ? esc_html( '−' ) : esc_html( '+' ); ?></div>
								<div class="f-support-faq-card__body">
									<h3><?php echo esc_html( $question['title'] ); ?></h3>
									<?php if ( 0 === $index ) { ?>
										<p><?php echo esc_html( $question['text'] ); ?></p>
									<?php } ?>
								</div>
								<span class="f-support-faq-card__tag"><?php echo esc_html( $question['tag'] ); ?></span>
							</article>
						<?php } ?>
					</div>
				</div>

				<aside class="f-support-help-card">
					<h3><?php echo esc_html__( 'Potřebujete poradit?', 'baspa' ); ?></h3>
					<a href="mailto:<?php echo antispambot( esc_attr( get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' ) ) ); ?>">
						<?php echo antispambot( esc_html( get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' ) ) ); ?>
					</a>
					<a href="tel:<?php echo esc_attr( str_replace( ' ', '', get_theme_mod( 'baspa_phone', '+420 777 099 687' ) ) ); ?>">
						<?php echo esc_html( get_theme_mod( 'baspa_phone', '+420 777 099 687' ) ); ?>
					</a>
					<small><?php echo esc_html__( 'Po - Pá 8:00-17:00 h', 'baspa' ); ?></small>
					<strong><?php echo esc_html__( 'Lukáš Dušek', 'baspa' ); ?></strong>
					<span><?php echo esc_html__( 'Bazénový specialista', 'baspa' ); ?></span>
					<a class="f-button a-button a-button--outline" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">
						<?php echo esc_html__( 'Napsat zprávu', 'baspa' ); ?>
					</a>
				</aside>
			</div>
		</div>
	</section>

	<section id="ke-stazeni" class="f-section f-section--support-downloads">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html__( 'Ke stažení', 'baspa' ); ?></h2>
			<div class="f-chip-list">
				<span class="is-active"><?php echo esc_html__( 'Katalogy vířivek', 'baspa' ); ?></span>
				<span><?php echo esc_html__( 'Návody', 'baspa' ); ?></span>
				<span><?php echo esc_html__( 'Rozměry', 'baspa' ); ?></span>
				<span><?php echo esc_html__( 'Záruky', 'baspa' ); ?></span>
			</div>
			<?php echo do_shortcode( '[arctic-downloads]' ); ?>
		</div>
	</section>

	<section id="servisni-formular" class="f-section f-section--support-form">
		<div class="f-section__container a-container">
			<div class="f-support-form">
				<header>
					<h2><?php echo esc_html__( 'Servisní formulář', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Popište nám požadavek a ozveme se s dalším postupem. U servisního požadavku pomůže model vířivky, rok pořízení a krátký popis situace.', 'baspa' ); ?></p>
				</header>
				<form class="f-support-form__card" action="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>" method="get">
					<label>
						<span><?php echo esc_html__( 'Jméno', 'baspa' ); ?></span>
						<input type="text" name="name">
					</label>
					<label>
						<span><?php echo esc_html__( 'E-mail', 'baspa' ); ?></span>
						<input type="email" name="email">
					</label>
					<label>
						<span><?php echo esc_html__( 'Telefon', 'baspa' ); ?></span>
						<input type="tel" name="phone">
					</label>
					<label>
						<span><?php echo esc_html__( 'Dotaz nebo požadavek', 'baspa' ); ?></span>
						<textarea name="message" rows="5"></textarea>
					</label>
					<button class="f-button a-button a-button--accent" type="submit">
						<?php echo esc_html__( 'Odeslat požadavek', 'baspa' ); ?>
					</button>
				</form>
			</div>
		</div>
	</section>

	<?php get_template_part( 'templates/section/contact' ); ?>

</main>

<?php
get_footer();
