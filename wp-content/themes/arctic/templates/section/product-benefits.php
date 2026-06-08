<?php

/**
 * Product Benefits
 */

$product_id = get_the_ID();
$section    = function_exists( 'arctic_product_get_benefit_section' )
	? arctic_product_get_benefit_section( $product_id )
	: array(
		'heading' => __( 'Výhody vířivek Arctic Spas - série Classic', 'baspa' ),
		'text'    => __( 'Vířivky Arctic Spas série Classic jsou standardně dodávány s řadou funkcí. Kliknutím na vybrané funkce se o nich dozvíte více.', 'baspa' ),
		'cards'   => array(),
	);
$benefits   = $section['cards'] ?? array();
?>

<?php if ( !empty( $benefits ) ) { ?>
<section id="vyhody" class="f-section f-section--product-benefits js-links__section" data-content-source="<?php echo esc_attr( (string) ( $benefits[0]['source'] ?? 'static-fallback' ) ); ?>">
	<div class="f-section__container a-container">
		<header class="f-section__header">
			<h2><?php echo esc_html( (string) $section['heading'] ); ?></h2>
			<p><?php echo esc_html( (string) $section['text'] ); ?></p>
		</header>

		<div class="f-product-benefits">
			<?php foreach ( $benefits as $index => $benefit ) {
				$has_popup    = !empty( $benefit['interactive'] );
				$icon         = (string) ( $benefit['icon'] ?? 'feature' );
				$media_status = (string) ( $benefit['media_status'] ?? 'WAITING_ON_FIGMA_EXPORT' );
				$media_url    = (string) ( $benefit['media_url'] ?? '' );
				$figma_node   = (string) ( $benefit['figma_node'] ?? '100:662' );
				$popup_id     = sanitize_title( (string) ( $benefit['popup_id'] ?? 'benefit-' . $index ) );
				?>
				<article class="f-product-benefit f-product-benefit--media-<?php echo esc_attr( $icon ); ?> <?php echo $has_popup ? 'f-product-benefit--has-popup f-product-benefit--interactive' : 'f-product-benefit--static'; ?>" data-content-source="<?php echo esc_attr( (string) ( $benefit['source'] ?? 'static-fallback' ) ); ?>">
					<?php if ( '' !== $media_url ) { ?>
						<span class="f-product-benefit__media f-product-benefit__media--<?php echo esc_attr( $icon ); ?>"
							data-asset-status="<?php echo esc_attr( $media_status ); ?>"
							data-asset-source="<?php echo esc_attr( (string) ( $benefit['media_source'] ?? 'product-media' ) ); ?>"
							data-figma-node="<?php echo esc_attr( $figma_node ); ?>"
							aria-hidden="true">
							<img src="<?php echo esc_url( $media_url ); ?>" alt="" loading="lazy" decoding="async">
						</span>
					<?php } ?>
					<h3><?php echo esc_html( (string) ( $benefit['title'] ?? '' ) ); ?></h3>
					<p><?php echo esc_html( (string) ( $benefit['summary'] ?? '' ) ); ?></p>
					<?php if ( $has_popup ) { ?>
						<span class="f-product-benefit__more" aria-hidden="true">+</span>
						<button type="button"
							class="f-product-benefit__trigger f-off__trigger js-off__trigger"
							data-off="<?php echo esc_attr( $popup_id ); ?>"
							aria-controls="<?php echo esc_attr( $popup_id ); ?>">
							<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Zobrazit detail: %s', 'baspa' ), (string) ( $benefit['title'] ?? '' ) ) ); ?></span>
						</button>
					<?php } ?>
				</article>
			<?php } ?>
		</div>
	</div>
</section>

<?php foreach ( $benefits as $index => $benefit ) {
	if ( empty( $benefit['interactive'] ) ) {
		continue;
	}

	$popup_id      = sanitize_title( (string) ( $benefit['popup_id'] ?? 'benefit-' . $index ) );
	$popup_title   = (string) ( $benefit['popup_title'] ?? $benefit['title'] ?? '' );
	$popup_content = trim( (string) ( $benefit['popup_content'] ?? '' ) );
	$popup_image   = (string) ( $benefit['popup_image_url'] ?? '' );

	if ( '' === $popup_content ) {
		$popup_content = '<p>' . esc_html( (string) ( $benefit['summary'] ?? '' ) ) . '</p>';
	}
	?>
	<aside id="<?php echo esc_attr( $popup_id ); ?>"
		class="f-off f-off--benefit-popup f-off--dialog a-off a-off--dialog js-off"
		data-off="<?php echo esc_attr( $popup_id ); ?>"
		data-off-breakpoint="all"
		data-off-relocate="false"
		aria-labelledby="<?php echo esc_attr( $popup_id . '-title' ); ?>"
		aria-hidden="true">

		<div class="f-benefit-popup f-off__container a-off__container">
			<button class="f-benefit-popup__close f-off__close a-off__close js-off__close"
				data-off="<?php echo esc_attr( $popup_id ); ?>"
				aria-controls="<?php echo esc_attr( $popup_id ); ?>">
				<?php if ( function_exists( 'forqy_get_icon' ) ) {
					forqy_get_icon( 'close' );
				} ?>
				<span class="screen-reader-text"><?php echo esc_html__( 'Zavřít okno', 'baspa' ); ?></span>
			</button>

			<h2 id="<?php echo esc_attr( $popup_id . '-title' ); ?>"><?php echo esc_html( $popup_title ); ?></h2>

			<?php if ( '' !== $popup_image ) { ?>
				<figure class="f-benefit-popup__media">
					<img src="<?php echo esc_url( $popup_image ); ?>" width="697" height="364" alt="" loading="lazy" decoding="async" data-asset-status="<?php echo esc_attr( (string) ( $benefit['popup_image_status'] ?? 'available' ) ); ?>">
				</figure>
			<?php } ?>

			<div class="f-benefit-popup__content">
				<?php echo wp_kses_post( wpautop( $popup_content ) ); ?>
			</div>

			<button class="f-benefit-popup__button js-off__close" type="button" data-off="<?php echo esc_attr( $popup_id ); ?>" aria-controls="<?php echo esc_attr( $popup_id ); ?>">
				<?php echo esc_html__( 'Zavřít okno', 'baspa' ); ?>
			</button>
		</div>
	</aside>
<?php } ?>
<?php } ?>
