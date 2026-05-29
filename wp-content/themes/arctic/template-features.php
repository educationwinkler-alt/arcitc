<?php
/**
 * Template Name: Figma Vlastnosti
 */

$feature_image = content_url( 'uploads/import/figma/category-hero-virivky.jpg' );
$features      = array(
	array(
		'id'          => 'izolace',
		'title'       => 'Izolace vířivky',
		'description' => 'Obvodová izolace FreeHeat™ zaručí nízké náklady na provoz i v kruté zimě.',
		'url'         => home_url( '/vlastnosti/izolace-virivky/' ),
	),
	array(
		'id'          => 'skorepina',
		'title'       => 'Záruka na skořepinu',
		'description' => 'Doživotní záruka na vodotěsnost skořepiny, nejdůležitější součást vířivky.',
		'url'         => home_url( '/zaruka/' ),
	),
	array(
		'id'          => 'termokryt',
		'title'       => 'Termokryt',
		'description' => 'Kryty s nejdelší životností. To jsou kryty Mylovac - Castcore®.',
		'url'         => home_url( '/vlastnosti/#termokryt' ),
	),
	array(
		'id'          => 'podlaha',
		'title'       => 'Podlaha vířivky',
		'description' => 'Vířivku s věčnou podlahou Forever Floor™ je možno umístit na jakýkoliv povrch.',
		'url'         => home_url( '/vlastnosti/#podlaha' ),
	),
	array(
		'id'          => 'servis',
		'title'       => 'Servis',
		'description' => 'Snadný přístup pro servis a upgrade vířivky.',
		'url'         => home_url( '/podpora/#servisni-formular' ),
	),
	array(
		'id'          => 'variabilita',
		'title'       => 'Variabilita',
		'description' => 'Nikde jinde nenaleznete tolik možností.',
		'url'         => home_url( '/vlastnosti/#variabilita' ),
	),
	array(
		'id'          => 'automaticka-dezinfekce',
		'title'       => 'Automatická dezinfekce',
		'description' => 'Čistá a bezpečná voda, volte systém Spa Boy.',
		'url'         => home_url( '/vlastnosti/#automaticka-dezinfekce' ),
	),
	array(
		'id'          => 'sluzby',
		'title'       => 'Služby',
		'description' => 'Nezávazná konzultace a osobní schůzka. Dovoz a instalace zdarma.',
		'url'         => home_url( '/sluzby/' ),
	),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--features">
	<section class="f-section f-section--feature-cards" aria-label="<?php echo esc_attr__( 'Vlastnosti vířivek Arctic Spas', 'baspa' ); ?>">
		<div class="f-section__container a-container">
			<div class="f-figma-card-grid f-figma-card-grid--features">
				<?php foreach ( $features as $feature ) { ?>
					<a id="<?php echo esc_attr( $feature['id'] ); ?>"
					   class="f-figma-card f-figma-card--feature"
					   href="<?php echo esc_url( $feature['url'] ); ?>"
					   style="--figma-card-image: url('<?php echo esc_url( $feature_image ); ?>');">
						<span class="f-figma-card__content">
							<strong><?php echo esc_html( $feature['title'] ); ?></strong>
							<span><?php echo esc_html( $feature['description'] ); ?></span>
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
