<?php
/**
 * Template Name: Figma Detail Vlastnosti
 */

$post_id = get_queried_object_id();
$allow_seed_fallbacks = function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks();

$asset = static function ( string $filename ): string {
	return content_url( 'uploads/import/' . ltrim( $filename, '/' ) );
};

$first_meta_image_id = static function ( string $key ) use ( $post_id ): int {
	$values = array_values( array_filter( array_map( 'absint', get_post_meta( $post_id, $key ) ) ) );

	return !empty( $values ) ? (int) $values[0] : 0;
};

$hero_image_id    = $first_meta_image_id( 'feature_detail_hero_images' );
$diagram_image_id = $first_meta_image_id( 'feature_detail_diagram_images' );
$detail_slug      = (string) get_post_field( 'post_name', $post_id );
$is_freeheat_page = 'izolace-virivky' === $detail_slug;
$hero_fallback    = $allow_seed_fallbacks ? $asset( 'legacy-categories/virivky.jpg' ) : '';
$diagram_fallback = ( $allow_seed_fallbacks && $is_freeheat_page ) ? $asset( 'figma/feature-freeheat-diagram.png' ) : '';
$feature_image    = $allow_seed_fallbacks && function_exists( 'arctic_feature_fallback_image_url' ) ? arctic_feature_fallback_image_url() : '';
$related_heading  = trim( wp_strip_all_tags( (string) get_post_meta( $post_id, 'feature_detail_related_heading', true ) ) );
$linked_feature   = function_exists( 'arctic_feature_get_by_detail_page' ) ? arctic_feature_get_by_detail_page( $post_id ) : array();
$features         = function_exists( 'arctic_features_get_items' ) ? arctic_features_get_items() : array();

$fallback_features = array(
	array(
		'title'       => 'Izolace vířivky',
		'description' => 'Obvodová izolace FreeHeat™ zaručí nízké náklady na provoz i v kruté zimě.',
		'url'         => home_url( '/vlastnosti/izolace-virivky/' ),
	),
	array(
		'title'       => 'Záruka na skořepinu',
		'description' => 'Doživotní záruka na vodotěsnost skořepiny, nejdůležitější součást vířivky.',
		'url'         => home_url( '/vlastnosti/zaruka-na-skorepinu/' ),
	),
	array(
		'title'       => 'Termokryt',
		'description' => 'Kryty s nejdelší životností. To jsou kryty Mylovac - Castcore®.',
		'url'         => home_url( '/vlastnosti/termokryt/' ),
	),
	array(
		'title'       => 'Podlaha vířivky',
		'description' => 'Vířivku s věčnou podlahou Forever Floor™ je možno umístit na jakýkoliv povrch.',
		'url'         => home_url( '/vlastnosti/podlaha-virivky/' ),
	),
	array(
		'title'       => 'Servisní přístup',
		'description' => 'Snadný přístup pro servis a upgrade vířivky.',
		'url'         => home_url( '/vlastnosti/servisni-pristup/' ),
	),
	array(
		'title'       => 'Variabilita',
		'description' => 'Nikde jinde nenaleznete tolik možností.',
		'url'         => home_url( '/vlastnosti/variabilita/' ),
	),
	array(
		'title'       => 'Automatická dezinfekce',
		'description' => 'Čistá a bezpečná voda, volte systém Spa Boy.',
		'url'         => home_url( '/vlastnosti/automaticka-dezinfekce/' ),
	),
	array(
		'title'       => 'Služby',
		'description' => 'Nezávazná konzultace a osobní schůzka. Dovoz a instalace zdarma.',
		'url'         => home_url( '/sluzby/' ),
	),
);

if ( empty( $features ) && $allow_seed_fallbacks ) {
	foreach ( $fallback_features as $index => $fallback_feature ) {
		$fallback_features[ $index ]['id']           = 0;
		$fallback_features[ $index ]['anchor']       = sanitize_title( $fallback_feature['title'] );
		$fallback_features[ $index ]['image_url']    = $feature_image;
		$fallback_features[ $index ]['asset_status'] = 'figma-export';
		$fallback_features[ $index ]['source']       = 'static-fallback';
	}

	$features = $fallback_features;
}

$article_content = trim( apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ) );
$article_source  = 'wp-editor';

if ( '' === $article_content ) {
	if ( $allow_seed_fallbacks ) {
		$article_source  = 'static-fallback';
		$article_content = '
			<section>
				<h2>' . esc_html__( 'Izolace na správném místě šetří vaše peníze každý den!', 'baspa' ) . '</h2>
				<p>' . esc_html__( 'Izolujeme vnější stěny, podlahu, a především strop či střechu. Vše důležité se přitom nachází uvnitř tohoto izolovaného prostoru. Vzduch uvnitř kabinetu se ohřívá zbytkovým teplem čerpadel a pomáhá udržovat vodu na požadované teplotě.', 'baspa' ) . '</p>
			</section>
			<section>
				<h2>' . esc_html__( 'Další inovace', 'baspa' ) . '</h2>
				<p>' . esc_html__( 'Jakákoliv překážka mezi elektrickým zařízením a skořepinou omezí přenos zbytkového tepla a znamená zvýšené náklady pro uživatele. Arctic Spas proto drží technologii v izolovaném, ale servisně přístupném prostoru.', 'baspa' ) . '</p>
			</section>
			<section>
				<h2>' . esc_html__( 'Nejnižší provozní náklady', 'baspa' ) . '</h2>
				<p>' . esc_html__( 'Zbytkové teplo z provozu zařízení, především čerpadel, může prostupovat přes sklolaminátovou skořepinu a přispívá k přihřívání vody. Výsledkem je méně ohřevových cyklů a nižší spotřeba energie.', 'baspa' ) . '</p>
			</section>
			<section>
				<h2>' . esc_html__( 'Skutečná ochrana proti mrazu', 'baspa' ) . '</h2>
				<p>' . esc_html__( 'Obvodová izolace FreeHeat® umí využít teplo kumulované ve vodě vířivky k ochraně proti mrazu při výpadku proudu. Právě proto je vhodná pro celoroční provoz v náročném klimatu.', 'baspa' ) . '</p>
			</section>';
	} else {
		$article_source = 'admin-empty';
	}
}

if ( '' === $related_heading ) {
	$related_heading = __( 'Další vlastnosti', 'baspa' );
}

$article_before_diagram = $article_content;
$article_after_diagram  = '';
$first_section_end      = stripos( $article_content, '</section>' );
$has_diagram_media      = $diagram_image_id || '' !== $diagram_fallback;

if ( $has_diagram_media && false !== $first_section_end ) {
	$split_at               = $first_section_end + strlen( '</section>' );
	$article_before_diagram = substr( $article_content, 0, $split_at );
	$article_after_diagram  = substr( $article_content, $split_at );
}

$article_classes = array( 'f-figma-article', 'f-figma-article--feature-detail' );
if ( $has_diagram_media ) {
	$article_classes[] = 'f-figma-article--has-diagram';
}

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--feature-detail">
	<section class="f-section f-section--figma-article">
		<div class="f-section__container a-container">
			<h2 class="screen-reader-text"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
			<div class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>" data-content-source="<?php echo esc_attr( $article_source ); ?>" data-feature-id="<?php echo esc_attr( (string) ( $linked_feature['id'] ?? 0 ) ); ?>">
				<?php
				if ( $hero_image_id ) {
					echo wp_get_attachment_image( $hero_image_id, 'full', false, array(
						'class'             => 'f-figma-article__hero',
						'alt'               => '',
						'loading'           => 'eager',
						'decoding'          => 'async',
						'data-asset-status' => 'admin-feature-detail',
					) );
				} elseif ( '' !== $hero_fallback ) {
					?>
					<img class="f-figma-article__hero" src="<?php echo esc_url( $hero_fallback ); ?>" alt="" loading="eager" decoding="async" data-asset-status="figma-export">
				<?php } else { ?>
					<div class="f-figma-article__hero f-figma-article__media-placeholder" data-asset-status="admin-empty" aria-hidden="true"></div>
				<?php } ?>

				<?php echo wp_kses_post( $article_before_diagram ); ?>

				<?php if ( $has_diagram_media ) {
					if ( $diagram_image_id ) {
						echo wp_get_attachment_image( $diagram_image_id, 'full', false, array(
							'class'             => 'f-figma-article__diagram',
							'alt'               => '',
							'loading'           => 'lazy',
							'decoding'          => 'async',
							'data-asset-status' => 'admin-feature-detail',
						) );
					} else {
						?>
						<img class="f-figma-article__diagram" src="<?php echo esc_url( $diagram_fallback ); ?>" alt="" loading="lazy" decoding="async" data-asset-status="figma-export">
					<?php }
				} ?>

				<?php echo wp_kses_post( $article_after_diagram ); ?>
			</div>
		</div>
	</section>
	<section class="f-section f-section--feature-related">
		<div class="f-section__container a-container">
			<h2><?php echo esc_html( $related_heading ); ?></h2>
			<div class="f-figma-card-grid f-figma-card-grid--features">
				<?php foreach ( $features as $feature ) {
					$feature_id   = (int) ( $feature['id'] ?? 0 );
					$image_url    = (string) ( $feature['image_url'] ?? $feature_image );
					$asset_status = (string) ( $feature['asset_status'] ?? 'WAITING_ON_OWNER' );
					$source       = (string) ( $feature['source'] ?? 'static-fallback' );
					$card_classes = array( 'f-figma-card', 'f-figma-card--feature' );
					if ( '' === $image_url ) {
						$card_classes[] = 'f-figma-card--missing-media';
					}
					?>
					<a class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>"
					   href="<?php echo esc_url( $feature['url'] ); ?>"
					   data-content-source="<?php echo esc_attr( $source ); ?>"
					   data-feature-id="<?php echo esc_attr( (string) $feature_id ); ?>"
					   data-asset-status="<?php echo esc_attr( $asset_status ); ?>"
					   <?php echo '' !== $image_url ? 'style="--figma-card-image: url(\'' . esc_url( $image_url ) . '\');"' : ''; ?>>
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
