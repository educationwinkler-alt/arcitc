<?php

/**
 * Footer
 */

// Close #site
?>
</div>

<?php
if ( !is_single() || !is_page_template( 'template-contact.php' ) ) {
	get_template_part( 'templates/section/instagram' );
}
if ( !is_page_template( 'template-contact.php' ) ) {
	get_template_part( 'templates/section/contact' );
}
get_template_part( 'templates/footer' );

get_template_part( 'modules/contacts/templates/off', 'contact' );
get_template_part( 'modules/contacts/templates/off', 'service' );

get_template_part( 'templates/search' );
get_template_part( 'templates/overlays' );
get_template_part( 'templates/defs' );

/**
 * Hook: baspa_footer
 */
do_action( 'baspa_footer' );

wp_footer();
?>

<?php
$smartsupp_key = trim( (string) get_theme_mod( 'arctic_smartsupp_key', '' ) );
if ( function_exists( 'wp_get_environment_type' ) && 'production' === wp_get_environment_type() && '' !== $smartsupp_key ) {
	$smartsupp_color = '#a31f37';
	$smartsupp_offset_x = 22;
	$smartsupp_offset_y = 24;
	$privacy_url = function_exists( 'get_privacy_policy_url' ) ? (string) get_privacy_policy_url() : '';
	?>
	<script defer>
	    var _smartsupp = _smartsupp || {};
	    _smartsupp.key = <?php echo wp_json_encode( $smartsupp_key ); ?>;
	    _smartsupp.color = <?php echo wp_json_encode( $smartsupp_color ); ?>;
	    _smartsupp.offsetX = <?php echo (int) $smartsupp_offset_x; ?>;
	    _smartsupp.offsetY = <?php echo (int) $smartsupp_offset_y; ?>;
	    _smartsupp.ratingEnabled = true;
	    <?php if ( '' !== $privacy_url ) { ?>
	    _smartsupp.privacyNoticeEnabled = true;
	    _smartsupp.privacyNoticeUrl = <?php echo wp_json_encode( $privacy_url ); ?>;
	    <?php } ?>
	    window.smartsupp || (function (d) {
	        var s, c, o = smartsupp = function () {
	            o._.push(arguments)
	        };
	        o._ = [];
	        s = d.getElementsByTagName('script')[0];
	        c = d.createElement('script');
	        c.type = 'text/javascript';
	        c.charset = 'utf-8';
	        c.async = true;
	        c.src = 'https://www.smartsuppchat.com/loader.js?';
	        s.parentNode.insertBefore(c, s);
	    })(document);
	</script>
<?php } ?>
</body>
</html>
