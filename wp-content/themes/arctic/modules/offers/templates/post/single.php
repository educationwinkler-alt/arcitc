<?php

/**
 * Single
 */

// Post
$post_class = array( 'f-offer--single', 'f-post', 'f-post--single' );

// Offer
$contact = get_post_meta( get_the_ID(), 'offer_contact', true );
$offer   = function_exists( 'baspa_offer_card_data' ) ? baspa_offer_card_data( get_the_ID() ) : array();
$contact_member_id = absint( $offer['contact_member_id'] ?? get_post_meta( get_the_ID(), 'offer_contact_member_id', true ) );
$facts   = array(
	'status'      => array( __( 'Dostupnost', 'baspa' ), (string) ( $offer[ 'status' ] ?? '' ) ),
	'discount'    => array( __( 'Zvýhodnění', 'baspa' ), (string) ( $offer[ 'discount' ] ?? '' ) ),
	'price'       => array( __( 'Cena', 'baspa' ), (string) ( $offer[ 'price' ] ?? '' ) ),
	'valid_until' => array( __( 'Platnost', 'baspa' ), (string) ( $offer[ 'valid_until' ] ?? '' ) ),
);
$has_facts = false;
foreach ( $facts as $fact ) {
	if ( '' !== trim( $fact[1] ) ) {
		$has_facts = true;
		break;
	}
}
?>

<article id="offer-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>
	<h2 class="screen-reader-text"><?php echo esc_html( get_the_title() ); ?></h2>
	<div class="a-container">

		<div class="a-flex a-gap--xl:m">
			<div class="a-flex__item--100 a-flex__item--auto:m">

				<?php if ( $has_facts ) { ?>
					<dl class="f-offer-facts f-offer-facts--single" data-content-source="offer-cpt" data-offer-id="<?php echo esc_attr( (string) get_the_ID() ); ?>">
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

				<?php get_template_part( 'templates/content' ); ?>

			</div>
			<?php if ( !empty( $contact ) && $contact != 'none' ) { ?>
				<div class="a-flex__item--100 a-flex__item--33:m">

					<aside class="f-sidebar f-sidebar--sticky a-stack a-gap--m">
						<?php get_template_part( 'templates/component/quick-contact-card', '', array(
							'context'     => 'offer_sidebar',
							'member_id'   => $contact_member_id,
							'class'       => array( 'f-quick-contact-card--offer' ),
							'button_text' => __( 'Napsat zprávu', 'baspa' ),
						) ); ?>
					</aside>

				</div>
			<?php } ?>
		</div>
		
	</div>
</article>
