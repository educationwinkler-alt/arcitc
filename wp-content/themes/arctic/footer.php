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

<?php if ( function_exists( 'wp_get_environment_type' ) && 'local' !== wp_get_environment_type() ) { ?>
	<script defer>
	    var _smartsupp = _smartsupp || {};
	    _smartsupp.key = '8bb1ce83465608fcc3a4e6a22ac74583b3508b1d';
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
