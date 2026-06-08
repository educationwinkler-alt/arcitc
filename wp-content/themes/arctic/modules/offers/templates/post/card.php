<?php

/**
 * Figma offer archive card.
 */

$offer = function_exists( 'baspa_offer_card_data' ) ? baspa_offer_card_data( get_the_ID() ) : array();

if ( empty( $offer ) ) {
	return;
}

$post_id     = (int) ( $offer[ 'id' ] ?? 0 );
$title       = (string) ( $offer[ 'title' ] ?? get_the_title() );
$short_title = (string) ( $offer[ 'short_title' ] ?? $title );
$description = (string) ( $offer[ 'description' ] ?? '' );
$permalink   = (string) ( $offer[ 'permalink' ] ?? get_permalink() );
$image_id    = (int) ( $offer[ 'image_id' ] ?? 0 );
$label       = (string) ( $offer[ 'label' ] ?? '' );
$fallback_title = trim( wp_strip_all_tags( $short_title ) );
$fallback_mark  = '' !== $fallback_title ? ( function_exists( 'mb_substr' ) ? mb_substr( $fallback_title, 0, 1 ) : substr( $fallback_title, 0, 1 ) ) : '';
$fallback_mark  = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $fallback_mark ) : strtoupper( $fallback_mark );
$facts       = array(
	'status'      => array( __( 'Dostupnost', 'baspa' ), (string) ( $offer[ 'status' ] ?? '' ) ),
	'discount'    => array( __( 'Zvýhodnění', 'baspa' ), (string) ( $offer[ 'discount' ] ?? '' ) ),
	'price'       => array( __( 'Cena', 'baspa' ), (string) ( $offer[ 'price' ] ?? '' ) ),
	'valid_until' => array( __( 'Platnost', 'baspa' ), (string) ( $offer[ 'valid_until' ] ?? '' ) ),
);
?>

<article class="f-offer-card"
         data-content-source="offer-cpt"
         data-offer-id="<?php echo esc_attr( (string) $post_id ); ?>">
	<a class="f-offer-card__media" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $short_title ); ?>">
		<?php
		if ( $image_id > 0 ) {
			echo wp_get_attachment_image( $image_id, 'large', false, array(
				'alt'               => '',
				'loading'           => 'lazy',
				'decoding'          => 'async',
				'data-asset-status' => 'admin-offer',
			) );
		} else { ?>
			<span class="f-offer-card__media-placeholder" data-asset-status="admin-empty" aria-hidden="true">
				<?php echo esc_html( $fallback_mark ); ?>
			</span>
		<?php } ?>
	</a>

	<div class="f-offer-card__body">
		<?php if ( '' !== $label ) { ?>
			<span class="f-offer-card__badge"><?php echo esc_html( $label ); ?></span>
		<?php } ?>

		<h2>
			<a href="<?php echo esc_url( $permalink ); ?>">
				<?php echo wp_kses_post( strip_tags( $short_title, '<strong><em><br>' ) ); ?>
			</a>
		</h2>

		<?php if ( '' !== $description ) { ?>
			<div class="f-offer-card__description">
				<?php echo wp_kses_post( wp_trim_words( wp_strip_all_tags( $description ), 28, ' ...' ) ); ?>
			</div>
		<?php } ?>

		<?php
		$has_facts = false;
		foreach ( $facts as $fact ) {
			if ( '' !== trim( $fact[1] ) ) {
				$has_facts = true;
				break;
			}
		}

		if ( $has_facts ) { ?>
			<dl class="f-offer-card__facts">
				<?php foreach ( $facts as $fact ) {
					if ( '' === trim( $fact[1] ) ) {
						continue;
					} ?>
					<div>
						<dt><?php echo esc_html( $fact[0] ); ?></dt>
						<dd><?php echo esc_html( $fact[1] ); ?></dd>
					</div>
				<?php } ?>
			</dl>
		<?php } ?>

		<a class="f-offer-card__button a-button" href="<?php echo esc_url( $permalink ); ?>">
			<?php echo esc_html__( 'Zobrazit nabídku', 'baspa' ); ?>
		</a>
	</div>
</article>
