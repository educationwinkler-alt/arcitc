<?php

/**
 * Heading Title
 */

$title = get_post_meta( get_the_ID(), 'page_title_text', true );

if ( get_post_meta( get_the_ID(), 'page_title', true ) != 0 ) { ?>
	<h1><?php if ( !empty( $title ) ) {
			echo wp_kses_post( strip_tags( $title, "<strong><em><br>" ) );
		} else {
			the_title();
		} ?></h1>
<?php }
