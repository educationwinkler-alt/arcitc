<?php
/**
 * Template Name: Figma Další informace
 */

$info_image = content_url( 'uploads/import/figma/category-product-card-1.png' );
$items      = array(
	array(
		'id'          => 'sluzby',
		'title'       => 'Služby',
		'description' => 'Konzultace, osobní schůzka, katalog, doprava, instalace a servis.',
		'url'         => home_url( '/sluzby/' ),
	),
	array(
		'id'          => 'certifikaty',
		'title'       => 'Certifikáty',
		'description' => 'Technologie, bezpečnostní standardy a certifikace výrobce Arctic Spas.',
		'url'         => home_url( '/certifikaty/' ),
	),
	array(
		'id'          => 'zaruka',
		'title'       => 'Záruka',
		'description' => 'Přehled prodloužených záruk pro řady Custom, Classic a Core.',
		'url'         => home_url( '/zaruka/' ),
	),
	array(
		'id'          => 'kolik-stoji-provoz',
		'title'       => 'Kolik stojí provoz a údržba',
		'description' => 'Provozní náklady, izolace FreeHeat, údržba vody a dlouhodobá péče.',
		'url'         => home_url( '/kolik-stoji-udrzba/' ),
	),
	array(
		'id'          => 'faq',
		'title'       => 'Časté otázky',
		'description' => 'Odpovědi k výběru, přípravě místa, montáži, provozu a servisu.',
		'url'         => home_url( '/podpora/#faq' ),
	),
	array(
		'id'          => 'reference',
		'title'       => 'Reference',
		'description' => 'Ukázky realizací a instalací vířivek i celoročních bazénů.',
		'url'         => home_url( '/reference/' ),
	),
	array(
		'id'          => 'o-nas',
		'title'       => 'O nás',
		'description' => 'Specializovaný prodejce Arctic Spas pro Česko a Slovensko.',
		'url'         => home_url( '/o-nas/' ),
	),
	array(
		'id'          => 'showroom',
		'title'       => 'Showroom',
		'description' => 'Prezentační plocha v Moravanech u Brna pro osobní výběr.',
		'url'         => home_url( '/showroom/' ),
	),
	array(
		'id'          => 'servis',
		'title'       => 'Servis',
		'description' => 'Záruční i pozáruční servis, zazimování a servisní požadavky.',
		'url'         => home_url( '/servis/' ),
	),
	array(
		'id'          => 'kontakt',
		'title'       => 'Kontakt',
		'description' => 'Telefon, e-mail, showroom a fakturační údaje Arctic Spas CZ.',
		'url'         => home_url( '/kontakt/' ),
	),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--more-info">
	<section class="f-section f-section--info-cards" aria-label="<?php echo esc_attr__( 'Další informace Arctic Spas', 'baspa' ); ?>">
		<div class="f-section__container a-container">
			<div class="f-figma-card-grid f-figma-card-grid--info">
				<?php foreach ( $items as $item ) { ?>
					<a id="<?php echo esc_attr( $item['id'] ); ?>"
					   class="f-figma-card f-figma-card--info"
					   href="<?php echo esc_url( $item['url'] ); ?>"
					   style="--figma-card-image: url('<?php echo esc_url( $info_image ); ?>');">
						<span class="f-figma-card__content">
							<strong><?php echo esc_html( $item['title'] ); ?></strong>
							<span><?php echo esc_html( $item['description'] ); ?></span>
						</span>
						<span class="f-figma-card__arrow" aria-hidden="true"></span>
					</a>
				<?php } ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
