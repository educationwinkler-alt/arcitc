<?php

/**
 * Post Listing Button
 */

if ( get_post_meta( get_the_ID(), 'reference_single', true ) != 0 && apply_filters( 'arctic_reference_single_public_enabled', false, get_the_ID() ) ) { ?>
	<div class="f-buttons a-buttons">
		<a href="<?php the_permalink(); ?>" class="f-button--next a-button a-button--xs" tabindex="-1">
			<?php echo esc_html__( 'Read More', 'baspa' ); ?>
			<span class="f-next__icon" aria-hidden="true"></span>
		</a>
	</div>
<?php }
