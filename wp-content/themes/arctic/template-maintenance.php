<?php
/**
 * Template Name: Figma Provoz a údržba
 */

$sections = array(
	array(
		'title' => 'Náklady na vlastnictví a provozování vířivky',
		'text'  => 'Stejně jako zvyklost používání vířivky, náklady na pořízení a provozování vířivky se během posledních několika let výrazně změnily. Pokroky u výrobců jako Arctic Spas zlepšily vývoj a technologie, čímž se snížily počáteční náklady a hlavně provozní náklady spojené s vlastnictvím přenosné vířivky.',
	),
	array(
		'title' => 'Další inovace',
		'text'  => 'Jakákoliv překážka mezi elektrickým zařízením a skořepinou omezí přenos zbytkového tepla a znamená zvýšené náklady pro uživatele. Arctic Spas přenáší izolaci na vyšší úroveň a pracuje se zbytkovým teplem uvnitř kabinetu.',
	),
	array(
		'title' => 'Nejnižší provozní náklady',
		'text'  => 'Zbytkové teplo z provozu zařízení, především čerpadel, může prostupovat přes sklolaminátovou skořepinu. To přispívá k přihřívání vody a snižuje počet ohřevových cyklů i spotřebu energie.',
	),
	array(
		'title' => 'Skutečná ochrana proti mrazu',
		'text'  => 'Obvodová izolace FreeHeat® má schopnost využít teplo kumulované ve vodě vířivky k ochraně proti mrazu při výpadku proudu. Protože mezi vodou a kabinetem není pěnová bariéra, zůstává technologie přístupná a servisovatelná.',
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
						<p><?php echo esc_html( $section['text'] ); ?></p>
					</section>
				<?php } ?>
			</article>
		</div>
	</section>
</main>

<?php
get_footer();
