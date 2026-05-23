<?php

/**
 * Metabox
 */

add_filter( 'rwmb_media_add_string', function () {
	return esc_html_x( '+ Add Media', 'admin', 'baspa' );
} );
