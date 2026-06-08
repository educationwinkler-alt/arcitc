<?php

/**
 * Functions
 *
 * @since 1.0.0
 */

/**
 * Config
 */
require_once get_theme_file_path( 'theme.php' );

/**
 * Components with Autoload
 */
require_once get_theme_file_path( 'vendor/autoload.php' );

/**
 * Components without Autoload
 */
// FORQY Function
require_once get_theme_file_path( 'vendor/forqys/function/function.php' );
// FORQY Font
require_once get_theme_file_path( 'vendor/forqys/font/font.php' );
// FORQY Icon
require_once get_theme_file_path( 'vendor/forqys/icon/icon.php' );
// FORQY Image
require_once get_theme_file_path( 'vendor/forqys/image/image.php' );
// FORQY Search
require_once get_theme_file_path( 'vendor/forqys/search/search.php' );
// FORQY Form
require_once get_theme_file_path( 'vendor/forqys/form/form.php' );
// FORQY Hours
require_once get_theme_file_path( 'vendor/forqys/hours/hours.php' );
// FORQY Tracking
require_once get_theme_file_path( 'vendor/forqys/tracking/tracking.php' );
// FORQY PhotoSwipe
require_once get_theme_file_path( 'vendor/forqys/pswp/pswp.php' );
// Metabox
require_once get_theme_file_path( 'vendor/wpmetabox/meta-box/meta-box.php' );

/**
 * Includes
 */
// Setup
require_once get_theme_file_path( 'inc/setup.php' );
// Updates
require_once get_theme_file_path( 'inc/updates.php' );
// Admin
require_once get_theme_file_path( 'inc/admin.php' );
// Styles
require_once get_theme_file_path( 'inc/styles.php' );
// Site Icon
require_once get_theme_file_path( 'inc/site-icon.php' );
// Mega Menu
require_once get_theme_file_path( 'inc/mega-menu.php' );
// Scripts
require_once get_theme_file_path( 'inc/scripts.php' );
// SEO
require_once get_theme_file_path( 'inc/seo.php' );
// Validation
require_once get_theme_file_path( 'inc/validation.php' );
// Functions
require_once get_theme_file_path( 'inc/functions.php' );
// Customize
require_once get_theme_file_path( 'inc/customize.php' );
// Images
require_once get_theme_file_path( 'inc/images.php' );
// Navigations
require_once get_theme_file_path( 'inc/navigations.php' );
// Translations
require_once get_theme_file_path( 'inc/translations.php' );
// Blocks
require_once get_theme_file_path( 'inc/blocks.php' );
// Shortcodes
require_once get_theme_file_path( 'inc/shortcodes.php' );

/**
 * Modules
 */
// Posts
require_once get_theme_file_path( 'modules/posts.php' );
// Pages
require_once get_theme_file_path( 'modules/pages.php' );
// Offers
require_once get_theme_file_path( 'modules/offers.php' ); // 35
// Slides
require_once get_theme_file_path( 'modules/slides.php' ); // 35
// Products
require_once get_theme_file_path( 'modules/products.php' ); // 36
// Accessories
require_once get_theme_file_path( 'modules/accessories.php' ); // 37
// References
require_once get_theme_file_path( 'modules/references.php' ); // 37
// Members
require_once get_theme_file_path( 'modules/members.php' ); // 37
// Features
require_once get_theme_file_path( 'modules/features.php' ); // 37
// Services
require_once get_theme_file_path( 'modules/services.php' ); // 37
// Partners
require_once get_theme_file_path( 'modules/partners.php' ); // 37
// Supports
require_once get_theme_file_path( 'modules/supports.php' ); // 38
// Downloads
require_once get_theme_file_path( 'modules/downloads.php' );
// FAQs
require_once get_theme_file_path( 'modules/faqs.php' ); // 38
// Jobs
require_once get_theme_file_path( 'modules/jobs.php' ); // 38
// Contacts
require_once get_theme_file_path( 'modules/contacts.php' ); // 40
