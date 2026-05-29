<?php
/**
 * Template Name: Figma O nás
 */

$team = array(
	array(
		'name'  => 'Vladimír Zajíč',
		'role'  => 'Vedoucí showroomu',
		'initials'     => 'VZ',
		'asset_status' => 'WAITING_ON_OWNER',
	),
	array(
		'name'  => 'Lukáš Dušek',
		'role'  => 'Obchodní konzultant',
		'initials'     => 'LD',
		'asset_status' => 'WAITING_ON_OWNER',
	),
	array(
		'name'  => 'Helena Antonyová',
		'role'  => 'Pečuje o zákazníky',
		'initials'     => 'HA',
		'asset_status' => 'WAITING_ON_OWNER',
	),
	array(
		'name'  => 'Servisní tým',
		'role'  => 'Montáž a servis',
		'initials'     => 'ST',
		'asset_status' => 'WAITING_ON_OWNER',
	),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--about-figma">
	<section class="f-section f-section--about-figma">
		<div class="f-section__container a-container">
			<div class="f-about-figma__intro">
				<h2><?php echo esc_html__( 'Naše společnost', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Prodejem vířivek Arctic Spas se zabýváme již od roku 2005 a základ našeho týmu se za tu dobu nezměnil. Máme více než 15 let osobních zkušeností s dovozem, prodejem, instalacemi a servisem vířivek Arctic Spas, které se zúročily při stovkách realizací v České republice i na Slovensku.', 'baspa' ); ?></p>
				<p><?php echo esc_html__( 'Při naší práci se můžeme jako autorizovaný dealer spolehnout také na podporu kanadského výrobce s celosvětovou působností a tradicí od roku 1994. Veškeré získané know-how je plně k dispozici našim zákazníkům.', 'baspa' ); ?></p>
			</div>
			<div class="f-about-figma__stats" aria-label="<?php echo esc_attr__( 'Arctic Spas v číslech', 'baspa' ); ?>">
				<div><strong>21+</strong><span><?php echo esc_html__( 'let zkušeností', 'baspa' ); ?></span></div>
				<div><strong>1000+</strong><span><?php echo esc_html__( 'spokojených klientů', 'baspa' ); ?></span></div>
				<div><strong>11</strong><span><?php echo esc_html__( 'členů týmu', 'baspa' ); ?></span></div>
			</div>
			<div class="f-about-figma__team-copy">
				<h2><?php echo esc_html__( 'Náš tým', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Naše současná sestava je vyladěna tak, aby byla schopna kompetentně a v dohodnutých termínech zajistit všechny fáze projektu. Od úvodní konzultace, přes návrh řešení, zpracování nabídky a realizaci, až po poprodejní služby a servis.', 'baspa' ); ?></p>
			</div>
			<div class="f-about-figma__team">
				<?php foreach ( $team as $person ) { ?>
					<article class="f-about-person">
						<div class="f-about-person__media f-about-person__media--waiting"
						     data-asset-status="<?php echo esc_attr( $person['asset_status'] ); ?>"
						     aria-hidden="true">
							<span><?php echo esc_html( $person['initials'] ); ?></span>
						</div>
						<h3><?php echo esc_html( $person['name'] ); ?></h3>
						<p><?php echo esc_html( $person['role'] ); ?></p>
						<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php echo esc_html__( 'Kontaktovat', 'baspa' ); ?></a>
					</article>
				<?php } ?>
			</div>
			<div class="f-about-figma__career">
				<h2><?php echo esc_html__( 'Kariéra v Arctic Spas', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Uplatnění u nás najdou šikovní lidé, kteří se nebojí komunikovat se zákazníky a odvádět dobrou práci každý den.', 'baspa' ); ?></p>
			</div>
			<div class="f-about-figma__jobs">
				<article class="f-about-job f-about-job--open">
					<h3><?php echo esc_html__( 'Montážní technik', 'baspa' ); ?></h3>
					<p><?php echo esc_html__( 'Hledáme spolehlivého technika pro montáže, servisní výjezdy a péči o zákazníky po instalaci.', 'baspa' ); ?></p>
					<div>
						<strong><?php echo esc_html__( 'Požadujeme', 'baspa' ); ?></strong>
						<ul>
							<li><?php echo esc_html__( 'technickou zručnost a pečlivost', 'baspa' ); ?></li>
							<li><?php echo esc_html__( 'řidičský průkaz skupiny B', 'baspa' ); ?></li>
							<li><?php echo esc_html__( 'slušné jednání se zákazníky', 'baspa' ); ?></li>
						</ul>
					</div>
					<div>
						<strong><?php echo esc_html__( 'Nabízíme', 'baspa' ); ?></strong>
						<ul>
							<li><?php echo esc_html__( 'stabilní práci v malém týmu', 'baspa' ); ?></li>
							<li><?php echo esc_html__( 'zaškolení na produktech Arctic Spas', 'baspa' ); ?></li>
							<li><?php echo esc_html__( 'férové jednání a dlouhodobou spolupráci', 'baspa' ); ?></li>
						</ul>
					</div>
					<a class="a-button a-button--accent" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php echo esc_html__( 'Mám zájem', 'baspa' ); ?></a>
				</article>
				<article class="f-about-job">
					<h3><?php echo esc_html__( 'Obchodník na prodejně v Moravanech', 'baspa' ); ?></h3>
				</article>
				<article class="f-about-job">
					<h3><?php echo esc_html__( 'Obchodník na prodejně v Moravanech', 'baspa' ); ?></h3>
				</article>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
