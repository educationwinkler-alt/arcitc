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

	<?php
	$smartsupp_key = trim( (string) get_theme_mod( 'arctic_smartsupp_key', '' ) );
	if ( function_exists( 'wp_get_environment_type' ) && 'production' === wp_get_environment_type() && '' !== $smartsupp_key ) {
		?>
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
