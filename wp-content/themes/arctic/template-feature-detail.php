<?php
/**
 * Template Name: Figma Detail Vlastnosti
 */

$hero_image    = content_url( 'uploads/import/legacy-categories/virivky.jpg' );
$diagram_image = content_url( 'uploads/import/figma/feature-freeheat-diagram.png' );
$feature_image = content_url( 'uploads/import/figma/category-hero-virivky.jpg' );
$features      = array(
	array(
		'title'       => 'Izolace vířivky',
		'description' => 'Obvodová izolace FreeHeat™ zaručí nízké náklady na provoz i v kruté zimě.',
		'url'         => home_url( '/vlastnosti/izolace-virivky/' ),
	),
	array(
		'title'       => 'Záruka na skořepinu',
		'description' => 'Doživotní záruka na vodotěsnost skořepiny, nejdůležitější součást vířivky.',
		'url'         => home_url( '/zaruka/' ),
	),
	array(
		'title'       => 'Termokryt',
		'description' => 'Kryty s nejdelší životností. To jsou kryty Mylovac - Castcore®.',
		'url'         => home_url( '/vlastnosti/#termokryt' ),
	),
	array(
		'title'       => 'Podlaha vířivky',
		'description' => 'Vířivku s věčnou podlahou Forever Floor™ je možno umístit na jakýkoliv povrch.',
		'url'         => home_url( '/vlastnosti/#podlaha' ),
	),
	array(
		'title'       => 'Servis',
		'description' => 'Snadný přístup pro servis a upgrade vířivky.',
		'url'         => home_url( '/podpora/#servis' ),
	),
	array(
		'title'       => 'Variabilita',
		'description' => 'Nikde jinde nenaleznete tolik možností.',
		'url'         => home_url( '/vlastnosti/#variabilita' ),
	),
	array(
		'title'       => 'Automatická dezinfekce',
		'description' => 'Čistá a bezpečná voda, volte systém Spa Boy.',
		'url'         => home_url( '/vlastnosti/#automaticka-dezinfekce' ),
	),
	array(
		'title'       => 'Služby',
		'description' => 'Nezávazná konzultace a osobní schůzka. Dovoz a instalace zdarma.',
		'url'         => home_url( '/sluzby/' ),
	),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--feature-detail">
	<section class="f-section f-section--figma-article">
		<div class="f-section__container a-container">
			<article class="f-figma-article f-figma-article--feature-detail">
				<img class="f-figma-article__hero" src="<?php echo esc_url( $hero_image ); ?>" alt="" loading="eager" decoding="async">
				<section>
					<h2><?php echo esc_html__( 'Izolace na správném místě šetří vaše peníze každý den!', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Izolujeme vnější stěny, podlahu, a především strop či střechu. Vše důležité se přitom nachází uvnitř tohoto izolovaného prostoru. Vzduch uvnitř kabinetu se ohřívá zbytkovým teplem čerpadel a pomáhá udržovat vodu na požadované teplotě.', 'baspa' ); ?></p>
				</section>
				<img class="f-figma-article__diagram" src="<?php echo esc_url( $diagram_image ); ?>" alt="" loading="lazy" decoding="async">
				<section>
					<h2><?php echo esc_html__( 'Další inovace', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Jakákoliv překážka mezi elektrickým zařízením a skořepinou omezí přenos zbytkového tepla a znamená zvýšené náklady pro uživatele. Arctic Spas proto drží technologii v izolovaném, ale servisně přístupném prostoru.', 'baspa' ); ?></p>
				</section>
				<section>
					<h2><?php echo esc_html__( 'Nejnižší provozní náklady', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Zbytkové teplo z provozu zařízení, především čerpadel, může prostupovat přes sklolaminátovou skořepinu a přispívá k přihřívání vody. Výsledkem je méně ohřevových cyklů a nižší spotřeba energie.', 'baspa' ); ?></p>
				</section>
				<section>
					<h2><?php echo esc_html__( 'Skutečná ochrana proti mrazu', 'baspa' ); ?></h2>
					<p><?php echo esc_html__( 'Obvodová izolace FreeHeat® umí využít teplo kumulované ve vodě vířivky k ochraně proti mrazu při výpadku proudu. Právě proto je vhodná pro celoroční provoz v náročném klimatu.', 'baspa' ); ?></p>
				</section>
			</article>
		</div>
	</section>
	<section class="f-section f-section--feature-related">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html__( 'Další vlastnosti', 'baspa' ); ?></h2>
			<div class="f-figma-card-grid f-figma-card-grid--features">
				<?php foreach ( $features as $feature ) { ?>
					<a class="f-figma-card f-figma-card--feature"
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
