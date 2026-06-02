<?php
/**
 * Template Name: Figma O nás
 */

if ( ! function_exists( 'arctic_about_team_fallback' ) ) {
	function arctic_about_team_fallback(): array {
		return array(
			array(
				'name'         => 'Vlastimil Zhoř',
				'role'         => 'Jednatel společnosti',
				'description'  => 'Prodej vířivek',
				'initials'     => 'VZ',
				'asset_status' => 'figma-export',
				'image'        => content_url( 'uploads/import/figma/about-team-vladimir.png' ),
			),
			array(
				'name'         => 'Ing. Lukáš Dušek',
				'role'         => 'Jednatel společnosti',
				'description'  => 'Komunikace s dodavateli a prodej bazénů',
				'initials'     => 'LD',
				'asset_status' => 'figma-export',
				'image'        => content_url( 'uploads/import/figma/about-team-lukas.png' ),
			),
			array(
				'name'         => 'Helena Antonyová',
				'role'         => 'Prodej bazénů',
				'description'  => 'Prodej bazénů a jejich příslušenství',
				'initials'     => 'HA',
				'asset_status' => 'figma-export',
				'image'        => content_url( 'uploads/import/figma/about-team-helena.png' ),
			),
			array(
				'name'         => 'Alena Janulíková',
				'role'         => 'Logistika a fakturace',
				'description'  => 'Organizace dopravy a fakturace.',
				'initials'     => 'AJ',
				'asset_status' => 'figma-export',
				'image'        => content_url( 'uploads/import/figma/about-team-alena.png' ),
			),
		);
	}
}

if ( ! function_exists( 'arctic_about_initials' ) ) {
	function arctic_about_initials( string $name ): string {
		$parts    = preg_split( '/\s+/u', trim( wp_strip_all_tags( $name ) ) );
		$initials = '';

		foreach ( array_slice( array_filter( $parts ), 0, 2 ) as $part ) {
			$initials .= function_exists( 'mb_substr' ) ? mb_substr( $part, 0, 1, 'UTF-8' ) : substr( $part, 0, 1 );
		}

		return $initials ?: '?';
	}
}

if ( ! function_exists( 'arctic_about_team_members' ) ) {
	function arctic_about_team_members(): array {
		if ( ! function_exists( 'baspa_members_query' ) ) {
			return arctic_about_team_fallback();
		}

		$query = baspa_members_query();
		$team  = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$name  = get_the_title();
				$image = get_the_post_thumbnail_url( get_the_ID(), 'full' );

				$team[] = array(
					'name'         => $name,
					'role'         => get_post_meta( get_the_ID(), 'member_position', true ),
					'description'  => get_post_meta( get_the_ID(), 'member_scope', true ),
					'initials'     => arctic_about_initials( $name ),
					'asset_status' => $image ? 'admin-member' : 'WAITING_ON_OWNER',
					'image'        => $image ?: '',
				);
			}

			wp_reset_postdata();
		}

		return $team ?: arctic_about_team_fallback();
	}
}

if ( ! function_exists( 'arctic_about_jobs_fallback' ) ) {
	function arctic_about_jobs_fallback(): array {
		$contact_url = esc_url( home_url( '/kontakt/' ) );
		$portal_url  = esc_url( home_url( '/o-nas/#career' ) );

		$open_content = sprintf(
			'<!-- wp:paragraph -->
<p>Hledáme spolehlivého technika pro montáže, servisní výjezdy a péči o zákazníky po instalaci.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading">Požadujeme</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>technickou zručnost a pečlivost</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>řidičský průkaz skupiny B</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>slušné jednání se zákazníky</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>samostatnost při montážích a servisu</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>chuť učit se produkty Arctic Spas</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading">Nabízíme</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>stabilní práci v malém týmu</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>zaškolení na produktech Arctic Spas</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>férové jednání a dlouhodobou spolupráci</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>zázemí showroomu v Moravanech u Brna</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>podmínky podle zkušeností a domluvy</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="%1$s">Kontaktujte nás</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="%2$s">Více na pracovním portále</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->',
			$contact_url,
			$portal_url
		);

		return array(
			array(
				'title'   => 'Montážní technik',
				'content' => $open_content,
			),
			array(
				'title'   => 'Obchodník na prodejně v Moravanech',
				'content' => '',
			),
			array(
				'title'   => 'Obchodník na prodejně v Moravanech',
				'content' => '',
			),
		);
	}
}

if ( ! function_exists( 'arctic_about_jobs' ) ) {
	function arctic_about_jobs(): array {
		$query = new WP_Query(
			array(
				'post_type'      => 'job',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
			)
		);

		$jobs = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$jobs[] = array(
					'title'   => get_the_title(),
					'content' => apply_filters( 'the_content', get_the_content() ),
				);
			}

			wp_reset_postdata();
		}

		return $jobs ?: arctic_about_jobs_fallback();
	}
}

$team          = arctic_about_team_members();
$team_title    = get_option( 'baspa_members_title' ) ?: __( 'Náš tým', 'baspa' );
$team_subtitle = get_option( 'baspa_members_subtitle' ) ?: __( 'Naše současná sestava je vyladěna tak, aby byla schopna kompetentně a v dohodnutých termínech zajistit všechny fáze projektu. Od úvodní konzultace, přes návrh řešení, zpracování nabídky a realizaci, až po poprodejní služby a servis - tým BASPA je tu pro vás.', 'baspa' );
$jobs          = arctic_about_jobs();
$jobs_title    = get_option( 'baspa_jobs_title' ) ?: __( 'Kariéra v Arctic spas', 'baspa' );
$jobs_subtitle = get_option( 'baspa_jobs_subtitle' ) ?: __( 'Uplatnění u nás najdou šikovní lidé, kteří se nebojí komunikovat se zákazníky a odvádět dobrou práci každý den.', 'baspa' );
$jobs_extra    = max( 0, count( $jobs ) - 3 ) * 116;

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--about-figma js-autohide--hide" style="<?php echo esc_attr( sprintf( '--about-jobs-extra:%dpx;', $jobs_extra ) ); ?>">
	<?php get_template_part( 'templates/navigation/about' ); ?>

	<section class="f-section f-section--about-figma">
		<div class="f-section__container a-container">
			<div id="<?php echo sanitize_title( esc_attr_x( 'our-company', 'anchor', 'baspa' ) ); ?>" class="f-about-figma__intro js-links__section">
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
				<h2><?php echo wp_kses_post( $team_title ); ?></h2>
				<div class="f-about-figma__team-subtitle"><?php echo wpautop( wp_kses_post( $team_subtitle ) ); ?></div>
			</div>
			<div class="f-about-team-carousel js-about-team-carousel" data-team-count="<?php echo esc_attr( count( $team ) ); ?>">
				<div class="f-about-figma__team js-about-team-carousel__track" tabindex="0" aria-label="<?php echo esc_attr__( 'Členové týmu', 'baspa' ); ?>">
					<?php foreach ( $team as $person ) { ?>
						<article class="f-about-person">
							<?php if ( ! empty( $person['image'] ) ) { ?>
								<div class="f-about-person__media"
								     data-asset-status="<?php echo esc_attr( $person['asset_status'] ); ?>">
									<img src="<?php echo esc_url( $person['image'] ); ?>"
									     alt="<?php echo esc_attr( $person['name'] ); ?>"
									     loading="lazy"
									     decoding="async">
								</div>
							<?php } else { ?>
								<div class="f-about-person__media f-about-person__media--waiting"
								     data-asset-status="<?php echo esc_attr( $person['asset_status'] ); ?>"
								     aria-hidden="true">
									<span><?php echo esc_html( $person['initials'] ); ?></span>
								</div>
							<?php } ?>
							<h3><?php echo esc_html( $person['name'] ); ?></h3>
							<?php if ( ! empty( $person['role'] ) ) { ?>
								<p class="f-about-person__role"><?php echo esc_html( $person['role'] ); ?></p>
							<?php } ?>
							<?php if ( ! empty( $person['description'] ) ) { ?>
								<p class="f-about-person__description"><?php echo esc_html( $person['description'] ); ?></p>
							<?php } ?>
						</article>
					<?php } ?>
				</div>
				<button type="button"
				        class="f-about-team__next js-about-team-carousel__next"
				        aria-label="<?php echo esc_attr__( 'Další člen týmu', 'baspa' ); ?>">
					<span aria-hidden="true">›</span>
				</button>
			</div>
			<div id="<?php echo sanitize_title( esc_attr_x( 'career', 'anchor', 'baspa' ) ); ?>" class="f-about-figma__career js-links__section">
				<h2><?php echo esc_html( $jobs_title ); ?></h2>
				<div class="f-about-figma__career-copy"><?php echo wpautop( wp_kses_post( $jobs_subtitle ) ); ?></div>
			</div>
			<div class="f-about-figma__jobs" data-job-count="<?php echo esc_attr( count( $jobs ) ); ?>">
				<?php foreach ( $jobs as $job_index => $job ) { ?>
					<details class="f-about-job<?php echo 0 === $job_index ? ' f-about-job--open' : ''; ?>" <?php echo 0 === $job_index ? 'open' : ''; ?>>
						<summary class="f-about-job__summary">
							<span class="f-about-job__icon" aria-hidden="true"></span>
							<h3><?php echo esc_html( $job['title'] ); ?></h3>
						</summary>
						<?php if ( ! empty( $job['content'] ) ) { ?>
							<div class="f-about-job__content f-content">
								<?php echo wp_kses_post( $job['content'] ); ?>
							</div>
						<?php } ?>
					</details>
				<?php } ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();