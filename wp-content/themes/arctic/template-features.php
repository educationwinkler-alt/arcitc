<?php
/**
 * Template Name: Figma Vlastnosti
 */

$allow_seed_fallbacks = function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks();
$feature_image        = $allow_seed_fallbacks && function_exists( 'arctic_feature_fallback_image_url' ) ? arctic_feature_fallback_image_url() : '';
$fallback_features = array(
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
		'url'         => home_url( '/vlastnosti/zaruka-na-skorepinu/' ),
	),
	array(
		'id'          => 'termokryt',
		'title'       => 'Termokryt',
		'description' => 'Kryty s nejdelší životností. To jsou kryty Mylovac - Castcore®.',
		'url'         => home_url( '/vlastnosti/termokryt/' ),
	),
	array(
		'id'          => 'podlaha',
		'title'       => 'Podlaha vířivky',
		'description' => 'Vířivku s věčnou podlahou Forever Floor™ je možno umístit na jakýkoliv povrch.',
		'url'         => home_url( '/vlastnosti/podlaha-virivky/' ),
	),
	array(
		'id'          => 'servis',
		'title'       => 'Servisní přístup',
		'description' => 'Snadný přístup pro servis a upgrade vířivky.',
		'url'         => home_url( '/vlastnosti/servisni-pristup/' ),
	),
	array(
		'id'          => 'variabilita',
		'title'       => 'Variabilita',
		'description' => 'Nikde jinde nenaleznete tolik možností.',
		'url'         => home_url( '/vlastnosti/variabilita/' ),
	),
	array(
		'id'          => 'automaticka-dezinfekce',
		'title'       => 'Automatická dezinfekce',
		'description' => 'Čistá a bezpečná voda, volte systém Spa Boy.',
		'url'         => home_url( '/vlastnosti/automaticka-dezinfekce/' ),
	),
	array(
		'id'          => 'sluzby',
		'title'       => 'Služby',
		'description' => 'Nezávazná konzultace a osobní schůzka. Dovoz a instalace zdarma.',
		'url'         => home_url( '/sluzby/' ),
	),
);
$features          = function_exists( 'arctic_features_get_items' ) ? arctic_features_get_items() : array();

if ( empty( $features ) && $allow_seed_fallbacks ) {
	foreach ( $fallback_features as $index => $fallback_feature ) {
		$fallback_features[ $index ]['anchor']       = $fallback_feature['id'];
		$fallback_features[ $index ]['image_url']    = $feature_image;
		$fallback_features[ $index ]['asset_status'] = 'figma-export';
		$fallback_features[ $index ]['source']       = 'static-fallback';
	}

	$features = $fallback_features;
}

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--features">
	<section class="f-section f-section--feature-cards" aria-label="<?php echo esc_attr__( 'Vlastnosti vířivek Arctic Spas', 'baspa' ); ?>">
		<div class="f-section__container a-container">
			<div class="f-figma-card-grid f-figma-card-grid--features">
				<?php foreach ( $features as $feature ) {
					$feature_id   = (int) ( $feature['id'] ?? 0 );
					$anchor       = (string) ( $feature['anchor'] ?? ( $feature['id'] ?? sanitize_title( (string) ( $feature['title'] ?? '' ) ) ) );
					$image_url    = (string) ( $feature['image_url'] ?? $feature_image );
					$asset_status = (string) ( $feature['asset_status'] ?? 'WAITING_ON_OWNER' );
					$source       = (string) ( $feature['source'] ?? 'static-fallback' );
					$card_classes = array( 'f-figma-card', 'f-figma-card--feature' );
					if ( '' === $image_url ) {
						$card_classes[] = 'f-figma-card--missing-media';
					}
					?>
					<a id="<?php echo esc_attr( $anchor ); ?>"
					   class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>"
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
