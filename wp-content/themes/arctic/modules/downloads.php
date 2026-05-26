<?php

/**
 * Downloads Module
 */

require_once get_theme_file_path( 'modules/downloads/type.php' );
require_once get_theme_file_path( 'modules/downloads/type/taxonomy.php' );
require_once get_theme_file_path( 'modules/downloads/type/metabox.php' );
require_once get_theme_file_path( 'modules/downloads/inc/admin.php' );

if ( !function_exists( 'arctic_downloads_shortcode' ) ) {

	function arctic_downloads_shortcode(): string {
		ob_start();
		get_template_part( 'modules/downloads/templates/listing' );
		return (string) ob_get_clean();
	}

	add_shortcode( 'arctic-downloads', 'arctic_downloads_shortcode' );

}
