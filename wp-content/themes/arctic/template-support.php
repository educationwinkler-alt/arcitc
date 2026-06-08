<?php

/**
 * Template Name: Support
 */

get_header();
get_template_part( 'templates/heading/default' );

$support_member = function_exists( 'baspa_members_get_selected_contact' ) ? baspa_members_get_selected_contact( 'support_help', get_template() . '-avatar' ) : array();
$support_defaults = function_exists( 'arctic_support_option_defaults' ) ? arctic_support_option_defaults() : array();
$downloads_defaults = function_exists( 'arctic_downloads_option_defaults' ) ? arctic_downloads_option_defaults() : array();
$support_help_email = !empty( $support_member['email'] ) ? $support_member['email'] : get_theme_mod( 'baspa_email', 'info@arctic-spas.cz' );
$support_help_phone = !empty( $support_member['phone'] ) ? $support_member['phone'] : get_theme_mod( 'baspa_phone', '+420 777 099 687' );

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
$support_help_name = !empty( $support_member['name'] )
	? $support_member['name']
	: ( function_exists( 'arctic_support_get_option' )
		? arctic_support_get_option( 'arctic_support_help_name', $support_defaults['arctic_support_help_name'] ?? 'Bc. Tomáš Koutný' )
		: 'Bc. Tomáš Koutný' );
$support_help_role = !empty( $support_member['position'] )
	? $support_member['position']
	: ( function_exists( 'arctic_support_get_option' )
		? arctic_support_get_option( 'arctic_support_help_role', $support_defaults['arctic_support_help_role'] ?? 'Prodej vířivek' )
		: 'Prodej vířivek' );
$support_help_hours = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_help_hours', $support_defaults['arctic_support_help_hours'] ?? 'Po - Pá 8:00-17:00 h' )
	: 'Po - Pá 8:00-17:00 h';
$support_help_button = function_exists( 'arctic_support_get_option' )
	? arctic_support_get_option( 'arctic_support_help_button', $support_defaults['arctic_support_help_button'] ?? 'Napsat zprávu' )
	: 'Napsat zprávu';
$support_help_button_url = function_exists( 'arctic_support_get_option' ) ? arctic_support_get_option( 'arctic_support_help_button_url', '/kontakt/' ) : '/kontakt/';
$support_help_button_url = function_exists( 'arctic_sections_url' ) ? arctic_sections_url( $support_help_button_url, '/kontakt/' ) : home_url( '/kontakt/' );
$support_help_source = !empty( $support_member['source'] ) ? $support_member['source'] : 'support-settings';
$download_filter_definitions = function_exists( 'arctic_downloads_filter_definitions' )
	? arctic_downloads_filter_definitions()
	: array(
		array( 'key' => 'catalog', 'label' => __( 'Katalogy vířivek', 'baspa' ) ),
		array( 'key' => 'manual', 'label' => __( 'Návody', 'baspa' ) ),
		array( 'key' => 'dimensions', 'label' => __( 'Rozměry', 'baspa' ) ),
		array( 'key' => 'warranty', 'label' => __( 'Záruky', 'baspa' ) ),
	);

$questions = array();
$allow_support_fallbacks = function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks();

if ( $allow_support_fallbacks ) {
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
}

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
      class="f-main f-main--support f-main--support-contract f-main--top-0 js-autohide--hide">

	<section class="f-section f-section--support-tabs f-section--support-tabs-contract f-links--sticky f-links--support js-section-nav-handoff">
		<div class="f-section__container a-container">
			<h2 class="screen-reader-text"><?php echo esc_html__( 'Podpora', 'baspa' ); ?></h2>
			<nav class="f-support-tabs f-support-tabs--contract js-links__navigation" aria-label="<?php echo esc_attr__( 'Podpora', 'baspa' ); ?>">
				<a class="active" href="#caste-dotazy"><?php echo esc_html( $support_faq_title ); ?></a>
				<a href="#ke-stazeni"><?php echo esc_html( $support_downloads_title ); ?></a>
				<a href="#servisni-formular"><?php echo esc_html( $support_form_title ); ?></a>
			</nav>
		</div>
	</section>

	<section id="caste-dotazy" class="f-section f-section--support-faq f-section--support-faq-contract js-links__section">
		<div class="f-section__container a-container">
			<div class="f-support-layout">
				<div class="f-support-layout__main">
					<h2><?php echo esc_html( $support_faq_title ); ?></h2>
					<?php if ( !empty( $questions ) ) { ?>
						<div class="f-chip-list f-chip-list--interactive f-chip-list--contract" role="group" aria-label="<?php echo esc_attr__( 'Kategorie dotazů', 'baspa' ); ?>">
							<?php foreach ( $faq_filters as $filter_index => $filter ) { ?>
							<button type="button"
							        class="<?php echo $filter_index === 0 ? 'is-active' : ''; ?>"
							        data-support-filter="<?php echo esc_attr( $filter['key'] ); ?>"
							        aria-pressed="<?php echo $filter_index === 0 ? 'true' : 'false'; ?>">
								<?php echo esc_html( $filter['label'] ); ?>
							</button>
							<?php } ?>
						</div>
					<?php } ?>

					<div class="f-support-accordion f-support-accordion--contract">
						<?php if ( empty( $questions ) && ( current_user_can( 'edit_posts' ) || $allow_support_fallbacks ) ) { ?>
							<p class="f-support-empty" data-content-source="admin-empty"><?php echo esc_html__( 'FAQ zatím nemá publikované položky. Přidejte je ve WordPress administraci přes FAQ.', 'baspa' ); ?></p>
						<?php }

						foreach ( $questions as $index => $question ) {
							$panel_id = 'support-faq-panel-' . $index;
							$is_open  = 0 === $index;
							?>
							<article class="f-support-faq-card f-support-faq-card--contract <?php echo $is_open ? 'is-open' : ''; ?>"
							         data-support-faq-card
							         data-support-category="<?php echo esc_attr( $question['tag_slug'] ?? 'podpora' ); ?>">
								<button type="button"
								        class="f-support-faq-card__icon"
								        data-support-faq-card-toggle
								        aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
								        aria-controls="<?php echo esc_attr( $panel_id ); ?>">
									<span aria-hidden="true"><?php echo $is_open ? '−' : '+'; ?></span>
									<span class="screen-reader-text"><?php echo esc_html( $question['title'] ); ?></span>
								</button>
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

				<aside class="f-support-help-card f-support-help-card--contract"
				       <?php echo !empty( $support_member['id'] ) ? 'data-member-id="' . esc_attr( (string) $support_member['id'] ) . '"' : ''; ?>
				       data-content-source="<?php echo esc_attr( $support_help_source ); ?>">
					<h3><?php echo esc_html( $support_help_title ); ?></h3>
					<a href="mailto:<?php echo antispambot( esc_attr( $support_help_email ) ); ?>">
						<?php echo antispambot( esc_html( $support_help_email ) ); ?>
					</a>
					<a href="tel:<?php echo esc_attr( function_exists( 'baspa_member_phone_href' ) ? baspa_member_phone_href( $support_help_phone ) : str_replace( ' ', '', $support_help_phone ) ); ?>">
						<?php echo esc_html( $support_help_phone ); ?>
					</a>
					<small><?php echo esc_html( $support_help_hours ); ?></small>
					<div class="f-support-help-card__person">
						<?php if ( !empty( $support_member ) && function_exists( 'baspa_member_avatar_html' ) ) {
							echo baspa_member_avatar_html( $support_member, 'f-support-help-card__avatar', get_template() . '-avatar' );
						} else if ( function_exists( 'baspa_member_avatar_html' ) && function_exists( 'baspa_member_initials' ) ) {
							echo baspa_member_avatar_html( array(
								'name'         => $support_help_name,
								'initials'     => baspa_member_initials( $support_help_name ),
								'asset_status' => 'admin-empty',
							), 'f-support-help-card__avatar', get_template() . '-avatar' );
						} else {
							$support_initial = wp_strip_all_tags( $support_help_name );
							$support_initial = function_exists( 'mb_substr' ) ? mb_substr( $support_initial, 0, 1 ) : substr( $support_initial, 0, 1 );
							?>
							<span class="f-support-help-card__avatar" data-asset-status="admin-empty" aria-hidden="true"><?php echo esc_html( $support_initial ); ?></span>
						<?php } ?>
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

	<section id="ke-stazeni" class="f-section f-section--support-downloads f-section--support-downloads-contract js-links__section">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html( $support_downloads_title ); ?></h2>
			<div class="f-chip-list f-chip-list--interactive f-chip-list--contract" role="group" aria-label="<?php echo esc_attr__( 'Kategorie ke stažení', 'baspa' ); ?>">
				<?php foreach ( $download_filter_definitions as $definition ) { ?>
					<button type="button"
					        class=""
					        data-download-filter="<?php echo esc_attr( $definition['key'] ?? 'catalog' ); ?>"
					        aria-pressed="false">
						<?php echo esc_html( $definition['label'] ?? '' ); ?>
					</button>
				<?php } ?>
			</div><?php echo trim( (string) do_shortcode( '[arctic-downloads]' ) ); ?>
		</div>
	</section>

	<section id="servisni-formular" class="f-section f-section--support-form f-section--support-form-contract js-links__section">
		<div class="f-section__container a-container">
			<div class="f-support-form f-support-form--contract">
				<header>
					<h2><?php echo esc_html( $support_form_title ); ?></h2>
					<p><?php echo wp_kses_post( $support_form_content ); ?></p>
				</header>
				<form class="f-support-form__card f-support-form__card--contract" action="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>" method="get">
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
