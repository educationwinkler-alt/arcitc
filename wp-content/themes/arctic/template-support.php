<?php

/**
 * Template Name: Support
 */

get_header();
get_template_part( 'templates/heading/default' );

$support_avatar = content_url( 'uploads/import/figma/contact-lukas-dusek.png' );
$support_defaults = function_exists( 'arctic_support_option_defaults' ) ? arctic_support_option_defaults() : array();
$downloads_defaults = function_exists( 'arctic_downloads_option_defaults' ) ? arctic_downloads_option_defaults() : array();

$support_faq_title = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_faq_title', $support_defaults['arctic_support_faq_title'] ?? 'Časté dotazy' )
	: 'Časté dotazy';
$support_downloads_title = function_exists( 'arctic_downloads_get_option' )
	? arctic_downloads_get_option( 'arctic_downloads_support_title', $downloads_defaults['arctic_downloads_support_title'] ?? 'Ke stažení' )
	: 'Ke stažení';
$support_form_title = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_form_title', $support_defaults['arctic_support_form_title'] ?? 'Servisní formulář' )
	: 'Servisní formulář';
$support_form_content = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_form_content', $support_defaults['arctic_support_form_content'] ?? 'Samozřejmostí je pro nás záruční i pozáruční servis u zákazníka, k dispozici je Vám formulář servisního požadavku, na který budeme co nejdříve reagovat. Objednat si u nás můžete odborné zazimování bazénu či vířivky stejně jako jarní zprovoznění.' )
	: 'Samozřejmostí je pro nás záruční i pozáruční servis u zákazníka, k dispozici je Vám formulář servisního požadavku, na který budeme co nejdříve reagovat. Objednat si u nás můžete odborné zazimování bazénu či vířivky stejně jako jarní zprovoznění.';
$support_help_title = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_help_title', $support_defaults['arctic_support_help_title'] ?? 'Potřebujete poradit?' )
	: 'Potřebujete poradit?';
$support_help_name = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_help_name', $support_defaults['arctic_support_help_name'] ?? 'Lukáš Dušek' )
	: 'Lukáš Dušek';
$support_help_role = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_help_role', $support_defaults['arctic_support_help_role'] ?? 'Bazénový specialista' )
	: 'Bazénový specialista';
$support_help_hours = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_help_hours', $support_defaults['arctic_support_help_hours'] ?? 'Po - Pá 8:00-17:00 h' )
	: 'Po - Pá 8:00-17:00 h';
$support_help_button = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_help_button', $support_defaults['arctic_support_help_button'] ?? 'Napsat zprávu' )
	: 'Napsat zprávu';
$support_help_button_url = function_exists( 'arctic_support_get_option' ) ? arctic_support_get_option( 'arctic_support_help_button_url', '/kontakt/' ) : '/kontakt/';
$support_help_button_url = function_exists( 'arctic_sections_url' ) ? arctic_sections_url( $support_help_button_url, '/kontakt/' ) : home_url( '/kontakt/' );
$download_filter_labels = function_exists( 'arctic_downloads_filter_labels' )
	? arctic_downloads_filter_labels()
	: array( 'Katalogy vířivek', 'Návody', 'Rozměry', 'Záruky' );
$download_filter_keys = array( 'catalog', 'manual', 'dimensions', 'warranty' );

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
		'tag'   => __( 'Provoz a údržba', 'baspa' ),
	),
	array(
		'title' => __( 'Pomůžete s výběrem vhodné konfigurace?', 'baspa' ),
		'text'  => __( 'Ano. Společně projdeme počet osob, umístění, izolaci, masážní trysky a volitelnou výbavu tak, aby model odpovídal reálnému používání.', 'baspa' ),
		'tag'   => __( 'Obchodní', 'baspa' ),
	),
	array(
		'title' => __( 'Lze si vířivku prohlédnout osobně?', 'baspa' ),
		'text'  => __( 'Vybrané modely a technologie si můžete projít v showroomu v Moravanech u Brna. Návštěvu doporučujeme domluvit předem.', 'baspa' ),
		'tag'   => __( 'Obchodní', 'baspa' ),
	),
	array(
		'title' => __( 'Jak se řeší stavební připravenost?', 'baspa' ),
		'text'  => __( 'Po výběru modelu připravíme podklady pro podkladovou desku, elektrický přívod, manipulační prostor a případné zapuštění.', 'baspa' ),
		'tag'   => __( 'Stavební příprava', 'baspa' ),
	),
	array(
		'title' => __( 'Umíte zajistit servis po instalaci?', 'baspa' ),
		'text'  => __( 'Servisní požadavky řešíme přes kontaktní formulář nebo telefonicky. Pomůže model, rok pořízení a stručný popis problému.', 'baspa' ),
		'tag'   => __( 'Servis', 'baspa' ),
	),
	array(
		'title' => __( 'Jak rychle dostanu cenovou nabídku?', 'baspa' ),
		'text'  => __( 'Po upřesnění modelu, konfigurace a montážních podmínek připravíme nezávaznou kalkulaci včetně navazující přípravy.', 'baspa' ),
		'tag'   => __( 'Obchodní', 'baspa' ),
	),
);

$faq_query = new WP_Query( array(
	'post_type'      => 'faq',
	'post_status'    => 'publish',
	'posts_per_page' => 9,
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	),
) );

$editable_questions = array();
if ( $faq_query->have_posts() ) {
	while ( $faq_query->have_posts() ) {
		$faq_query->the_post();
		$faq_terms = wp_get_post_terms( get_the_ID(), 'faq-category' );
		$faq_tag   = !empty( $faq_terms ) && !is_wp_error( $faq_terms ) ? $faq_terms[0]->name : __( 'Podpora', 'baspa' );

		$editable_questions[] = array(
			'title' => get_the_title(),
			'text'  => wp_strip_all_tags( apply_filters( 'the_content', get_the_content() ) ),
			'tag'   => $faq_tag,
		);
	}
	wp_reset_postdata();
}

if ( !empty( $editable_questions ) ) {
	$questions = $editable_questions;
}

$faq_filters = array(
	array(
		'key'   => 'all',
		'label' => __( 'Všechny', 'baspa' ),
	),
);
$faq_filter_seen = array( 'all' => true );

foreach ( $questions as $index => $question ) {
	$tag_slug = sanitize_title( (string) ( $question['tag'] ?? '' ) );
	if ( $tag_slug === '' ) {
		$tag_slug = 'podpora';
	}

	$questions[ $index ]['tag_slug'] = $tag_slug;

	if ( isset( $faq_filter_seen[ $tag_slug ] ) ) {
		continue;
	}

	$faq_filters[] = array(
		'key'   => $tag_slug,
		'label' => (string) ( $question['tag'] ?? __( 'Podpora', 'baspa' ) ),
	);
	$faq_filter_seen[ $tag_slug ] = true;
}
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
      class="f-main f-main--support f-main--top-0">

	<section class="f-section f-section--support-tabs">
		<div class="f-section__container a-container">
			<nav class="f-support-tabs" aria-label="<?php echo esc_attr__( 'Podpora', 'baspa' ); ?>">
				<a href="#caste-dotazy"><?php echo esc_html( $support_faq_title ); ?></a>
				<a href="#ke-stazeni"><?php echo esc_html( $support_downloads_title ); ?></a>
				<a href="#servisni-formular"><?php echo esc_html( $support_form_title ); ?></a>
			</nav>
		</div>
	</section>

	<section id="caste-dotazy" class="f-section f-section--support-faq">
		<div class="f-section__container a-container">
			<div class="f-support-layout">
				<div class="f-support-layout__main">
					<h2><?php echo esc_html( $support_faq_title ); ?></h2>
					<div class="f-chip-list f-chip-list--interactive" role="tablist" aria-label="<?php echo esc_attr__( 'Kategorie dotazů', 'baspa' ); ?>">
						<?php foreach ( $faq_filters as $filter_index => $filter ) { ?>
							<button type="button"
							        class="<?php echo $filter_index === 0 ? 'is-active' : ''; ?>"
							        data-support-filter="<?php echo esc_attr( $filter['key'] ); ?>"
							        role="tab"
							        aria-selected="<?php echo $filter_index === 0 ? 'true' : 'false'; ?>">
								<?php echo esc_html( $filter['label'] ); ?>
							</button>
						<?php } ?>
					</div>

					<div class="f-support-accordion">
						<?php foreach ( $questions as $index => $question ) {
							$panel_id = 'support-faq-panel-' . $index;
							$is_open  = 0 === $index;
							?>
							<article class="f-support-faq-card <?php echo $is_open ? 'is-open' : ''; ?>"
							         data-support-faq-card
							         data-support-category="<?php echo esc_attr( $question['tag_slug'] ?? 'podpora' ); ?>"
							         role="button"
							         tabindex="0"
							         aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
							         aria-controls="<?php echo esc_attr( $panel_id ); ?>">
								<div class="f-support-faq-card__icon" aria-hidden="true"><?php echo $is_open ? '−' : '+'; ?></div>
								<div class="f-support-faq-card__body">
									<h3><?php echo esc_html( $question['title'] ); ?></h3>
									<p id="<?php echo esc_attr( $panel_id ); ?>" <?php echo $is_open ? '' : 'hidden'; ?>>
										<?php echo esc_html( $question['text'] ); ?>
									</p>
								</div>
								<span class="f-support-faq-card__tag"><?php echo esc_html( $question['tag'] ); ?></span>
							</article>
						<?php } ?>
					</div>
				</div>

				<aside class="f-support-help-card">
					<h3><?php echo esc_html( $support_help_title ); ?></h3>
					<a href="mailto:<?php echo antispambot( esc_attr( get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' ) ) ); ?>">
						<?php echo antispambot( esc_html( get_theme_mod( 'baspa_email', 'lukas.dusek@arctic-spas.cz' ) ) ); ?>
					</a>
					<a href="tel:<?php echo esc_attr( str_replace( ' ', '', get_theme_mod( 'baspa_phone', '+420 777 099 687' ) ) ); ?>">
						<?php echo esc_html( get_theme_mod( 'baspa_phone', '+420 777 099 687' ) ); ?>
					</a>
					<small><?php echo esc_html( $support_help_hours ); ?></small>
					<div class="f-support-help-card__person">
						<span class="f-support-help-card__avatar" aria-hidden="true">
							<img src="<?php echo esc_url( $support_avatar ); ?>" alt="" loading="lazy" decoding="async">
						</span>
						<div>
							<strong><?php echo esc_html( $support_help_name ); ?></strong>
							<span><?php echo esc_html( $support_help_role ); ?></span>
						</div>
					</div>
					<a class="f-button a-button a-button--outline" href="<?php echo esc_url( $support_help_button_url ); ?>"><?php echo esc_html( $support_help_button ); ?></a>
				</aside>
			</div>
		</div>
	</section>

	<section id="ke-stazeni" class="f-section f-section--support-downloads">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html( $support_downloads_title ); ?></h2>
			<div class="f-chip-list f-chip-list--interactive" role="tablist" aria-label="<?php echo esc_attr__( 'Kategorie ke stažení', 'baspa' ); ?>">
				<?php foreach ( $download_filter_labels as $index => $label ) { ?>
					<button type="button"
					        class=""
					        data-download-filter="<?php echo esc_attr( $download_filter_keys[ $index ] ?? 'catalog' ); ?>"
					        role="tab"
					        aria-selected="false">
						<?php echo esc_html( $label ); ?>
					</button>
				<?php } ?>
			</div><?php echo trim( (string) do_shortcode( '[arctic-downloads]' ) ); ?>
		</div>
	</section>

	<section id="servisni-formular" class="f-section f-section--support-form">
		<div class="f-section__container a-container">
			<div class="f-support-form">
				<header>
					<h2><?php echo esc_html( $support_form_title ); ?></h2>
					<p><?php echo wp_kses_post( $support_form_content ); ?></p>
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
					<p class="f-support-form__consent"><?php echo esc_html__( 'Odesláním souhlasíte se zpracováním osobních údajů.', 'baspa' ); ?></p>
					<button class="f-button a-button a-button--accent" type="submit">
						<?php echo esc_html__( 'Odeslat požadavek', 'baspa' ); ?>
					</button>
				</form>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
