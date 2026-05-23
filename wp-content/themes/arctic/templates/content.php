<?php

/**
 * Content
 *
 * @since 1.0.0
 */

$content_class = array( 'f-content', 'a-content', 'js-images' );

if ( is_page() ) {
	$content_class[] = 'f-page__content';
} else if ( is_single() || is_singular() ) {
	$content_class[] = 'f-single__content';
}

if ( isset( $args[ 'container' ] ) && $args[ 'container' ] ) {
	$content_class[] = 'a-container';
}

if ( !empty( get_the_content() ) ) { ?>
	<div <?php forqy_class( $content_class ); ?>>
		<?php
		the_content();
		wp_link_pages();
		?>
	</div>
<?php } else {
	the_content();
}
