<?php

/**
 * Listing Header
 */

$header_class = array( 'f-listing__header' );
//if ( get_post_meta( get_the_ID(), 'reference_single', true ) == 0 ) {
//	$header_class[] = 'screen-reader-text';
//}
?>

<header <?php forqy_class( $header_class ); ?>>
	<?php if ( is_home() || is_front_page() ) {
		if ( get_post_meta( get_the_ID(), 'reference_single', true ) == 0 ) { ?>
			<h3><?php the_title(); ?></h3>
		<?php } else { ?>
			<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php }
	} else {
		if ( get_post_meta( get_the_ID(), 'reference_single', true ) == 0 ) { ?>
			<h2><?php the_title(); ?></h2>
		<?php } else { ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php } ?>
	<?php } ?>
</header>
