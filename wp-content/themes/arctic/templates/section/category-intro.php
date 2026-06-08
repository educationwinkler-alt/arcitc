<?php

/**
 * Category Intro
 */

$term_id = get_queried_object_id();
$term    = get_queried_object();
$blocks  = array();
$source  = 'term-meta';

if ( !function_exists( 'arctic_category_intro_resolve_url' ) ) {
	function arctic_category_intro_resolve_url( string $url ): string {
		$url = trim( $url );

		if ( '' === $url ) {
			return '';
		}

		if ( 0 === strpos( $url, '/' ) ) {
			return home_url( $url );
		}

		return $url;
	}
}

if ( !function_exists( 'arctic_category_intro_fallback_blocks' ) ) {
	function arctic_category_intro_fallback_blocks( bool $is_swimspa ): array {
		if ( $is_swimspa ) {
			return array(
				array(
					'title'       => 'Výhody celoročních bazénů Arctic',
					'text'        => 'Rodinný bazén na zahradě je snem řady domácností. Swimspa Arctic přivezeme kompletní, včetně filtrace, automatické dezinfekce, elektroohřevu, obvodové izolace FreeHeat™ a bezpečného termokrytu. Stačí ji postavit na rovnou plochu, připojit k elektřině a napustit vodou.',
					'button_text' => 'Více o vlastnostech',
					'url'         => home_url( '/vlastnosti/' ),
					'image_url'   => content_url( 'uploads/import/figma-category-celorocni-bazeny.jpg' ),
					'alt'         => 'Celoroční bazén Arctic Spas',
				),
				array(
					'title'       => 'Celoroční provoz bez stavebních prací',
					'text'        => 'Celoroční bazén Arctic vám přinese zábavu, relaxaci i sportovní vyžití bez výkopů a složitých stavebních prací. Díky kvalitní konstrukci, izolaci a termokrytu je připravený pro pohodlné používání po celý rok.',
					'button_text' => 'Více o záruce',
					'url'         => home_url( '/zaruka/' ),
					'image_url'   => content_url( 'uploads/import/legacy-categories/swimspa.jpg' ),
					'alt'         => 'Venkovní swimspa Arctic Spas',
				),
			);
		}

		return array(
			array(
				'title'       => 'Vlastnosti vířivek',
				'text'        => 'Venkovní vířivky Arctic Spas jsou navrženy a vyrobeny pro drsné podnebí severní Kanady tak, aby dlouhé roky spolehlivě sloužily, byly jednoduché na obsluhu a pro svůj provoz spotřebovaly minimum energie.',
				'button_text' => 'Více o vlastnostech',
				'url'         => home_url( '/vlastnosti/' ),
				'image_url'   => content_url( 'uploads/import/figma/category-vlastnosti.jpg' ),
				'alt'         => 'Vlastnosti vířivek Arctic Spas',
			),
			array(
				'title'       => 'Záruka',
				'text'        => 'Za kvalitou našich výrobků si stojíme, což dokládá doživotní záruka Arctic Spas na vodotěsnost skořepiny a pětiletá záruka na většinu komponentů včetně ohřevu.',
				'button_text' => 'Více o záruce',
				'url'         => home_url( '/zaruka/' ),
				'image_url'   => content_url( 'uploads/import/figma/category-zaruka.jpg' ),
				'alt'         => 'Záruka Arctic Spas',
			),
		);
	}
}

if ( !function_exists( 'arctic_category_intro_render_image' ) ) {
	function arctic_category_intro_render_image( array $block ): void {
		$image_id = isset( $block['image_id'] ) ? absint( $block['image_id'] ) : 0;
		$alt      = isset( $block['alt'] ) ? (string) $block['alt'] : '';

		if ( $image_id > 0 ) {
			echo wp_get_attachment_image( $image_id, 'large', false, array(
				'alt'               => $alt,
				'loading'           => 'lazy',
				'decoding'          => 'async',
				'data-asset-status' => 'admin-category-intro',
			) );
			return;
		}

		if ( !empty( $block['image_url'] ) ) {
			?>
			<img src="<?php echo esc_url( $block['image_url'] ); ?>"
			     width="674"
			     height="424"
			     alt="<?php echo esc_attr( $alt ); ?>"
			     loading="lazy"
			     decoding="async"
			     data-asset-status="seed-fallback">
			<?php
		}
	}
}

for ( $index = 1; $index <= 2; $index++ ) {
	$prefix = 'category_intro_' . $index;
	$block  = array(
		'title'       => (string) get_term_meta( $term_id, $prefix . '_title', true ),
		'text'        => (string) get_term_meta( $term_id, $prefix . '_text', true ),
		'button_text' => (string) get_term_meta( $term_id, $prefix . '_button_text', true ),
		'url'         => arctic_category_intro_resolve_url( (string) get_term_meta( $term_id, $prefix . '_button_url', true ) ),
		'image_id'    => absint( get_term_meta( $term_id, $prefix . '_image', true ) ),
		'alt'         => (string) get_term_meta( $term_id, $prefix . '_alt', true ),
	);

	if ( '' !== $block['title'] || '' !== $block['text'] || $block['image_id'] > 0 ) {
		$blocks[] = $block;
	}
}

if ( empty( $blocks ) ) {
	if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() ) {
		return;
	}

	$blocks = arctic_category_intro_fallback_blocks( is_tax( 'product-category', 'swimspa' ) );
	$source = 'seed-fallback';
}

if ( empty( $blocks ) ) {
	return;
}
?>

<section class="f-section f-section--category-intro"
         data-content-source="<?php echo esc_attr( $source ); ?>"
         data-term-id="<?php echo esc_attr( $term instanceof WP_Term ? $term->term_id : $term_id ); ?>">
	<div class="f-section__container a-container">
		<?php foreach ( $blocks as $index => $block ) {
			$is_reverse = 1 === $index;
			$classes    = array( 'f-category-intro', $is_reverse ? 'f-category-intro--reverse' : 'f-category-intro--split' );
			?>
			<div <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $classes ); ?>>
				<?php if ( $is_reverse ) { ?>
					<figure class="f-category-intro__image">
						<?php arctic_category_intro_render_image( $block ); ?>
					</figure>
				<?php } ?>

				<div class="f-category-intro__content">
					<?php if ( !empty( $block['title'] ) ) { ?>
						<h2><?php echo esc_html( $block['title'] ); ?></h2>
					<?php } ?>
					<?php if ( !empty( $block['text'] ) ) { ?>
						<?php echo wp_kses_post( wpautop( $block['text'] ) ); ?>
					<?php } ?>
					<?php if ( !empty( $block['button_text'] ) && !empty( $block['url'] ) ) { ?>
						<a class="f-button a-button a-button--accent" href="<?php echo esc_url( $block['url'] ); ?>">
							<?php echo esc_html( $block['button_text'] ); ?>
						</a>
					<?php } ?>
				</div>

				<?php if ( !$is_reverse ) { ?>
					<figure class="f-category-intro__image">
						<?php arctic_category_intro_render_image( $block ); ?>
					</figure>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>
