<!DOCTYPE html>
<html id="top" <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fff">

	<?php
	/**
	 * Hook: forqy_head_pre
	 *
	 * @hooked forqy_fonts_pre - 1
	 * @hooked forqy_tracking_pre - 2
	 * @hooked forqy_form_pre - 3
	 */
	do_action( 'forqy_head_pre' );
	?>

	<?php if ( function_exists( 'wp_get_environment_type' ) && 'local' !== wp_get_environment_type() ) { ?>
		<link rel='preconnect' href='https://connect.facebook.net'>
		<link rel='preconnect' href='https://c.seznam.cz'>
		<link rel='preconnect' href='https://www.clarity.ms'>
		<link rel='preconnect' href='https://static.hotjar.com'>
		<link rel='preconnect' href='https://www.smartsuppchat.com'>
	<?php } ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php
/**
 * Hook: baspa_body_open
 *
 * @hooked baspa_gtm_body - 1
 */
do_action( 'baspa_body_open' );

wp_body_open();

// Skip Links
get_template_part( 'templates/skiplinks' );
?>

<div id="site" class="f-site">
<?php
get_template_part( 'templates/header' );
