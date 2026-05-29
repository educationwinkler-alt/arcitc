<?php

/**
 * Listing Header
 */

$header_class = array( 'f-listing__header' );
?>

<header <?php forqy_class( $header_class ); ?>>
	<?php if ( is_home() || is_front_page() ) {
		?>
		<h3><?php the_title(); ?></h3>
	<?php
	} else {
		?>
		<h2><?php the_title(); ?></h2>
	<?php } ?>
</header>
