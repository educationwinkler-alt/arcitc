<?php

/**
 * Listing Image
 */

$url = get_post_meta( get_the_ID(), 'partner_url', true );

if ( has_post_thumbnail() ) {
	if ( !empty( $url ) ) { ?>
		<figure class="f-listing__image">
			<a href="<?php echo esc_url( $url ); ?>" class="f-image a-image a-image--contain a-image--landscape" target="_blank" rel="nofollow noreferrer" tabindex="-1">
				<?php the_post_thumbnail( get_template() . '-logo' ); ?>
			</a>
		</figure>
	<?php } else { ?>
		<figure class="f-listing__image f-image a-image a-image--contain a-image--landscape">
			<?php the_post_thumbnail( get_template() . '-logo' ); ?>
		</figure>
	<?php }
}
