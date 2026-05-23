<?php

/**
 * Single
 */

// Post
$post_class = array( 'f-offer--single', 'f-post', 'f-post--single' );

// Offer
$contact = get_post_meta( get_the_ID(), 'offer_contact', true );
?>

<article id="offer-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>
	<div class="a-container">

		<div class="a-flex a-gap--xl:m">
			<div class="a-flex__item--100 a-flex__item--auto:m">

				<?php get_template_part( 'templates/content' ); ?>

			</div>
			<?php if ( !empty( $contact ) && $contact != 'none' ) { ?>
				<div class="a-flex__item--100 a-flex__item--33:m">

					<aside class="f-sidebar f-sidebar--sticky a-stack a-gap--m">
						<?php
						if ( $contact == 'pools' ) {
							block_template_part( 'contact-small-pools' );
						} else if ( $contact == 'jacuzzis' ) {
							block_template_part( 'contact-small-jacuzzis' );
						} else if ( $contact == 'default' ) {
							block_template_part( 'contact-small' );
						} ?>
					</aside>

				</div>
			<?php } ?>
		</div>
		
	</div>
</article>
