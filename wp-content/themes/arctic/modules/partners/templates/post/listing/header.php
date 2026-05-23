<?php

/**
 * Listing Header
 */

$url = get_post_meta( get_the_ID(), 'partner_url', true );

$header_class = array( 'f-listing__header' );
?>

<header <?php forqy_class( $header_class ); ?>>
	<h3>
		<?php if ( !empty( $url ) ) { ?>
			<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="nofollow noreferrer"><?php the_title(); ?></a>
		<?php } else {
			the_title();
		} ?>
	</h3>
</header>
