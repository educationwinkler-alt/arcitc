<?php

/**
 * Products Module
 */

// Type
require_once get_theme_file_path( 'modules/products/type.php' );
require_once get_theme_file_path( 'modules/products/type/taxonomy.php' );
require_once get_theme_file_path( 'modules/products/type/metabox.php' );

// Includes
//require_once get_theme_file_path( 'modules/products/inc/rewrite.php' );
require_once get_theme_file_path( 'modules/products/inc/categories.php' );
require_once get_theme_file_path( 'modules/products/inc/configurations.php' );
require_once get_theme_file_path( 'modules/products/inc/query.php' );
require_once get_theme_file_path( 'modules/products/inc/product.php' );
// Admin
require_once get_theme_file_path( 'modules/products/inc/admin/scripts.php' );
require_once get_theme_file_path( 'modules/products/inc/admin/category.php' );
require_once get_theme_file_path( 'modules/products/inc/admin/list.php' );
require_once get_theme_file_path( 'modules/products/inc/admin/list/affiliate.php' );
