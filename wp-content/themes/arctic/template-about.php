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
				'image'        => content_url( 'uploads/import/figma/about-team-vladimir-portrait.png' ),
			),
			array(
				'name'         => 'Ing. Lukáš Dušek',
				'role'         => 'Jednatel společnosti',
				'description'  => 'Komunikace s dodavateli a prodej bazénů',
				'initials'     => 'LD',
				'asset_status' => 'figma-export',
				'image'        => content_url( 'uploads/import/figma/about-team-lukas-portrait.png' ),
			),
			array(
				'name'         => 'Helena Antonyová',
				'role'         => 'Prodej bazénů',
				'description'  => 'Prodej bazénů a jejich příslušenství',
				'initials'     => 'HA',
				'asset_status' => 'figma-export',
				'image'        => content_url( 'uploads/import/figma/about-team-helena-portrait.png' ),
			),
			array(
				'name'         => 'Alena Janulíková',
				'role'         => 'Logistika a fakturace',
				'description'  => 'Organizace dopravy a fakturace.',
				'initials'     => 'AJ',
				'asset_status' => 'figma-export',
				'image'        => content_url( 'uploads/import/figma/about-team-alena-portrait.png' ),
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
			return function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ? arctic_about_team_fallback() : array();
		}

		$query = baspa_members_query();
		$team  = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$name        = get_the_title();
				$member_data  = function_exists( 'baspa_member_data' ) ? baspa_member_data( get_the_ID(), 'full' ) : array();
				$image        = !empty( $member_data['image'] ) ? (string) $member_data['image'] : '';
				$asset_status = $image ? 'admin-member' : 'WAITING_ON_OWNER';

				if ( '' === $image && !empty( $member_data['avatar'] ) ) {
					$image        = (string) $member_data['avatar'];
					$asset_status = 'admin-member-avatar-fallback';
				}

				$team[] = array(
					'name'         => $name,
					'role'         => !empty( $member_data['position'] ) ? $member_data['position'] : get_post_meta( get_the_ID(), 'member_position', true ),
					'description'  => !empty( $member_data['scope'] ) ? $member_data['scope'] : get_post_meta( get_the_ID(), 'member_scope', true ),
					'initials'     => !empty( $member_data['initials'] ) ? $member_data['initials'] : arctic_about_initials( $name ),
					'asset_status' => $asset_status,
					'image'        => $image ?: '',
				);
			}

			wp_reset_postdata();
		}

		return $team ?: ( function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ? arctic_about_team_fallback() : array() );
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
				'source'  => 'static-fallback',
			),
			array(
				'title'   => 'Obchodník na prodejně v Moravanech',
				'content' => '',
				'source'  => 'static-fallback',
			),
			array(
				'title'   => 'Obchodník na prodejně v Moravanech',
				'content' => '',
				'source'  => 'static-fallback',
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
					'id'      => get_the_ID(),
					'title'   => get_the_title(),
					'content' => apply_filters( 'the_content', get_the_content() ),
					'source'  => 'job-cpt',
				);
			}

			wp_reset_postdata();
		}

		return $jobs ?: ( function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ? arctic_about_jobs_fallback() : array() );
	}
}

$team          = arctic_about_team_members();
$team_title    = get_option( 'baspa_members_title' ) ?: __( 'Náš tým', 'baspa' );
$team_subtitle = get_option( 'baspa_members_subtitle' ) ?: __( 'Naše současná sestava je vyladěna tak, aby byla schopna kompetentně a v dohodnutých termínech zajistit všechny fáze projektu. Od úvodní konzultace, přes návrh řešení, zpracování nabídky a realizaci, až po poprodejní služby a servis - tým BASPA je tu pro vás.', 'baspa' );
$jobs          = arctic_about_jobs();
$jobs_title    = get_option( 'baspa_jobs_title' ) ?: __( 'Kariéra v Arctic spas', 'baspa' );
$jobs_subtitle = get_option( 'baspa_jobs_subtitle' ) ?: __( 'Uplatnění u nás najdou šikovní lidé, kteří se nebojí komunikovat se zákazníky a odvádět dobrou práci každý den.', 'baspa' );
$jobs_source   = $jobs[0]['source'] ?? 'unknown';
$jobs_extra    = max( 0, count( $jobs ) - 3 ) * 116;

$post_id = get_queried_object_id();

$about_intro_title   = trim( wp_strip_all_tags( (string) get_post_meta( $post_id, 'about_intro_title', true ) ) );
$about_intro_title   = '' !== $about_intro_title ? $about_intro_title : __( 'Naše společnost', 'baspa' );
$about_intro_content = trim( apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ) );
$about_intro_source  = 'wp-editor';

if ( '' === $about_intro_content && function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ) {
	$about_intro_source  = 'static-fallback';
	$about_intro_content = wpautop( esc_html__( 'Prodejem vířivek Arctic Spas se zabýváme již od roku 2005 a základ našeho týmu se za tu dobu nezměnil. Máme více než 15 let osobních zkušeností s dovozem, prodejem, instalacemi a servisem vířivek Arctic Spas, které se zúročily při stovkách realizací v České republice i na Slovensku.', 'baspa' ) )
		. wpautop( esc_html__( 'Při naší práci se můžeme jako autorizovaný dealer spolehnout také na podporu kanadského výrobce s celosvětovou působností a tradicí od roku 1994. Veškeré získané know-how je plně k dispozici našim zákazníkům.', 'baspa' ) );
}

$default_stats = array(
	array(
		'value' => '21+',
		'label' => __( 'let zkušeností', 'baspa' ),
	),
	array(
		'value' => '1000+',
		'label' => __( 'spokojených klientů', 'baspa' ),
	),
	array(
		'value' => '11',
		'label' => __( 'členů týmu', 'baspa' ),
	),
);

$about_stats_raw  = get_post_meta( $post_id, 'about_stats' );
$about_stats_rows = array();

foreach ( $about_stats_raw as $raw_stat_row ) {
	if ( !is_array( $raw_stat_row ) ) {
		continue;
	}

	if ( array_key_exists( 'value', $raw_stat_row ) || array_key_exists( 'label', $raw_stat_row ) ) {
		$about_stats_rows[] = $raw_stat_row;
		continue;
	}

	foreach ( $raw_stat_row as $nested_stat_row ) {
		if ( is_array( $nested_stat_row ) ) {
			$about_stats_rows[] = $nested_stat_row;
		}
	}
}

$about_stats = array();
foreach ( $about_stats_rows as $stat_row ) {
	$value = trim( wp_strip_all_tags( (string) ( $stat_row['value'] ?? '' ) ) );
	$label = trim( wp_strip_all_tags( (string) ( $stat_row['label'] ?? '' ) ) );

	if ( '' === $value && '' === $label ) {
		continue;
	}

	$about_stats[] = array(
		'value' => $value,
		'label' => $label,
	);
}

if ( empty( $about_stats ) && function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks() ) {
	$about_stats = $default_stats;
}

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--about-figma js-autohide--hide" style="<?php echo esc_attr( sprintf( '--about-jobs-extra:%dpx;', $jobs_extra ) ); ?>">
	<?php get_template_part( 'templates/navigation/about' ); ?>

	<section class="f-section f-section--about-figma">
		<div class="f-section__container a-container">
			<div id="<?php echo sanitize_title( esc_attr_x( 'our-company', 'anchor', 'baspa' ) ); ?>" class="f-about-figma__intro js-links__section" data-content-source="<?php echo esc_attr( $about_intro_source ); ?>">
				<h2><?php echo esc_html( $about_intro_title ); ?></h2>
				<?php echo wp_kses_post( $about_intro_content ); ?>
			</div>
			<div class="f-about-figma__stats" data-content-source="about-meta" role="group" aria-label="<?php echo esc_attr__( 'Arctic Spas v číslech', 'baspa' ); ?>">
				<?php foreach ( $about_stats as $stat ) { ?>
					<div><strong><?php echo esc_html( $stat['value'] ); ?></strong><span><?php echo esc_html( $stat['label'] ); ?></span></div>
				<?php } ?>
			</div>
			<div class="f-about-figma__team-copy">
				<h2><?php echo wp_kses_post( $team_title ); ?></h2>
				<div class="f-about-figma__team-subtitle"><?php echo wpautop( wp_kses_post( $team_subtitle ) ); ?></div>
			</div>
			<div class="f-about-team-carousel js-about-team-carousel" data-team-count="<?php echo esc_attr( count( $team ) ); ?>">
				<button type="button"
				        class="f-about-team__control f-about-team__prev js-about-team-carousel__prev"
				        aria-label="<?php echo esc_attr__( 'Předchozí člen týmu', 'baspa' ); ?>"
				        hidden>
					<span aria-hidden="true">‹</span>
				</button>
				<div class="f-about-figma__team js-about-team-carousel__track" role="group" tabindex="0" aria-label="<?php echo esc_attr__( 'Členové týmu', 'baspa' ); ?>">
					<?php foreach ( $team as $person ) { ?>
						<article class="f-about-person">
							<?php if ( ! empty( $person['image'] ) ) { ?>
								<?php
								$media_classes = array( 'f-about-person__media' );

								if ( 'admin-member-avatar-fallback' === ( $person['asset_status'] ?? '' ) ) {
									$media_classes[] = 'f-about-person__media--avatar-fallback';
								}
								?>
								<div class="<?php echo esc_attr( implode( ' ', $media_classes ) ); ?>"
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
				        class="f-about-team__control f-about-team__next js-about-team-carousel__next"
				        aria-label="<?php echo esc_attr__( 'Další člen týmu', 'baspa' ); ?>">
					<span aria-hidden="true">›</span>
				</button>
			</div>
			<div id="career" class="f-about-figma__career js-links__section">
				<h2><?php echo esc_html( $jobs_title ); ?></h2>
				<div class="f-about-figma__career-copy"><?php echo wpautop( wp_kses_post( $jobs_subtitle ) ); ?></div>
			</div>
			<div class="f-about-figma__jobs"
			     data-content-source="<?php echo esc_attr( $jobs_source ); ?>"
			     data-job-count="<?php echo esc_attr( count( $jobs ) ); ?>">
				<?php foreach ( $jobs as $job_index => $job ) { ?>
					<?php
					$job_has_content = '' !== trim( wp_strip_all_tags( (string) ( $job['content'] ?? '' ) ) );
					$job_id          = isset( $job['id'] ) ? (int) $job['id'] : 0;
					?>
					<?php if ( $job_has_content ) { ?>
						<details class="f-about-job"
						         name="about-career"
						         data-content-source="<?php echo esc_attr( $job['source'] ?? $jobs_source ); ?>"
						         <?php echo $job_id > 0 ? 'data-job-id="' . esc_attr( (string) $job_id ) . '"' : ''; ?>
						         >
							<summary class="f-about-job__summary">
								<span class="f-about-job__icon" aria-hidden="true"></span>
								<h3><?php echo esc_html( $job['title'] ); ?></h3>
							</summary>
							<div class="f-about-job__content f-content">
								<?php echo wp_kses_post( $job['content'] ); ?>
							</div>
						</details>
					<?php } else { ?>
						<article class="f-about-job f-about-job--empty"
						         data-content-source="<?php echo esc_attr( $job['source'] ?? $jobs_source ); ?>">
							<div class="f-about-job__summary" aria-disabled="true">
								<span class="f-about-job__icon" aria-hidden="true"></span>
								<h3><?php echo esc_html( $job['title'] ); ?></h3>
							</div>
						</article>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
