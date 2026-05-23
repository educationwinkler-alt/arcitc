<?php

/**
 * Listing Header
 */

$title_alter = get_post_meta( get_the_ID(), 'product_title_short', true );
if ( !empty( $title_alter ) ) {
	$title = $title_alter;
} else {
	$title = get_the_title();
} ?>
<header class="f-listing__header">
	<h2><?php echo wp_kses_post( $title ); ?></h2>
</header>
