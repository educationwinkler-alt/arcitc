<?php
/**
 * Template Name: Figma Provoz a údržba
 */

$sections = array(
	array(
		'title' => __( 'Náklady na vlastnictví a provozování vířivky', 'baspa' ),
		'text'  => array(
			__( 'Stejně jako zvyklost používání vířivky, náklady na pořízení a provozování vířivky se během posledních několika let drasticky změnily. Pokroky u výrobců jako je Arctic Spas výrazně zlepšily vývoj a technologie, čímž se snížily počáteční náklady a hlavně provozní náklady spojené s vlastnictvím přenosné vířivky.', 'baspa' ),
			__( 'Jednou z takových inovací od Arctic Spas je technologie FreeHeat, při níž teplo generované vnitřními čerpadly a motory slouží k udržení teploty vody na optimální úrovni.', 'baspa' ),
		),
		'list'  => array(
			__( 'Izolované termokryty — zachycují a udržují stoupající teplo ve vířivce.', 'baspa' ),
			__( 'Vynikající filtrační systémy — čistá voda s minimální spotřebou chemikálií.', 'baspa' ),
			__( 'Lepší chemikálie — produkty Arctic Pure pro čistší vodu a méně práce s údržbou.', 'baspa' ),
		),
	),
	array(
		'title' => __( 'Jaký je skutečný provozní náklad vířivky?', 'baspa' ),
		'text'  => array(
			__( 'Pokud si koupíte vysoce kvalitní vířivku s ekonomickým provozním systémem, Arctic Spas nabízí zákazníkům takový komplexní nízkorozpočtový produkt provozovaný po celý rok — pak očekávejte celkové náklady kolem 1 dolaru za den.', 'baspa' ),
			__( 'Rozsáhlá studie společnosti Arctic Spas ukázala, že vířivky Arctic Spas vykazovaly denní provozní náklady nižší než 60 centů při průměrné venkovní teplotě −13,8 °C, zatímco srovnávané vířivky s pěnovou výplní spotřebovaly přes 75 centů za den. Vířivky Arctic Spas byly více než o 25 % účinnější.', 'baspa' ),
		),
	),
	array(
		'title' => __( 'Náklady na údržbu vířivky: Oddělte mýtus od reality', 'baspa' ),
		'text'  => array(
			__( 'Provoz a spotřeba vířivky jsou nedílnou součástí vlastnictví; dalším velmi diskutovaným faktorem je údržba. Rozdíl mezi mýtem o údržbě vířivky a skutečností je dnes širší než kdy předtím, ale nemělo by to být překážkou pro pořízení vířivky.', 'baspa' ),
			__( 'Renomovaní výrobci vířivek nabízejí ohromující paletu nástrojů a možností údržby, které drží náklady na nízké úrovni. Díky pokročilé technologii monitorování dnešní majitelé přesně vědí, kolik energie je zapotřebí pro provoz jejich vířivky.', 'baspa' ),
		),
	),
	array(
		'title' => __( 'Spa Boy: Údržba vířivky s technologií nové generace', 'baspa' ),
		'text'  => array(
			__( 'Nejmodernější systém péče o vodu na světě, systém SpaBoy, je špičkovým zařízením pro úpravu a údržbu kvality vody s možností ovládání odkudkoliv na světě.', 'baspa' ),
			__( 'Spa Boy používá technologie využívané ve zdravotnictví, které snižují provozní náklady. Monitorovaná voda je čistá, bezpečná a účinná, protože se neustále kontrolují hladiny dezinfekce a pH vody. Mimo příležitostné úpravy vody a výměny filtrů provádí všechny operace systém Spa Boy.', 'baspa' ),
		),
	),
	array(
		'title' => __( 'Proč jsou záruky tak důležité', 'baspa' ),
		'text'  => array(
			__( 'Dobrá záruka v dlouhodobém horizontu ušetří spoustu peněz. Vířivky Arctic Spas mají dobře vydobytou pověst jako jedny z nejpevnějších a nejvíce odolných venkovních vířivek, které jsou k dispozici na trhu.', 'baspa' ),
			__( 'Navíc standardní záruka Arctic Spas řadí společnost mezi jednu z nejvýhodnějších na trhu. Pro vodotěsnost skořepiny poskytuje Arctic Spas doživotní záruku. Vnitřní vybavení a komponenty technologie mají záruku po dobu pěti let od nákupu.', 'baspa' ),
		),
	),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--maintenance">
	<section class="f-section f-section--figma-article">
		<div class="f-section__container a-container">
			<article class="f-figma-article">
				<?php foreach ( $sections as $section ) { ?>
					<section>
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
						<?php foreach ( $section['text'] as $paragraph ) { ?>
							<p><?php echo esc_html( $paragraph ); ?></p>
						<?php } ?>
						<?php if ( !empty( $section['list'] ) ) { ?>
							<ul>
								<?php foreach ( $section['list'] as $item ) { ?>
									<li><?php echo esc_html( $item ); ?></li>
								<?php } ?>
							</ul>
						<?php } ?>
					</section>
				<?php } ?>
			</article>
		</div>
	</section>
</main>

<?php
get_footer();
