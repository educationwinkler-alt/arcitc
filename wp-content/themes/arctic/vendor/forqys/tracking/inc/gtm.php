<?php

/**
 * GTM
 *
 * @package forqys/tracking
 * @since   1.0.0
 */

if ( !function_exists( 'forqy_tracking_gtm_head' ) ) {

	/**
	 * Google Tag Manager Tracking Code in <head>
	 *
	 * @return void
	 */
	function forqy_tracking_gtm_head(): void {

		$config         = apply_filters( 'forqy_theme', array() ); // Theme config
		$config_consent = $config[ 'consent' ] ?? true;

		if ( !empty( $config[ 'gtm_id' ] ) ) {
			if ( $config_consent ) { ?>
<!--noptimize--><!-- Google Consent V2 Default -->
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag("consent", "default", {
        ad_storage: "denied",
        ad_user_data: "denied",
        ad_personalization: "denied",
        analytics_storage: "denied",
        functionality_storage: "denied",
        personalization_storage: "denied",
        security_storage: "granted",
        wait_for_update: 500,
    });
    gtag("set", "ads_data_redaction", false);
    gtag("set", "url_passthrough", false);
</script><!-- End Google Consent V2 Default --><!--/noptimize-->
			<?php } ?>
<!--noptimize--><!-- Google Tag Manager -->
<script>
    (function (w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
        const f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l !== 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '<?php echo esc_js( $config[ 'gtm_id' ] ); ?>');
</script><!-- End Google Tag Manager --><!--/noptimize-->
		<?php }

	}

	add_filter( 'wp_head', 'forqy_tracking_gtm_head', 2 );

}

if ( !function_exists( 'forqy_tracking_gtm_scripts' ) ) {

	/**
	 * GTM Scripts
	 *
	 * @return void
	 */
	function forqy_tracking_gtm_scripts(): void {

		$config = apply_filters( 'forqy_theme', array() ); // Theme config

		if ( !empty( $config[ 'gtm_id' ] ) ) {

			wp_register_script(
				get_template() . '-gtm',
				str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/dist/js/gtm.js' ),
				array(),
				'1.0.2',
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				),
			);

			wp_localize_script( get_template() . '-gtm', 'gtm_param', array(
				'permalink' => get_the_permalink(),
			) );

			wp_enqueue_script( get_template() . '-gtm' );

		}

	}

	add_action( 'wp_enqueue_scripts', 'forqy_tracking_gtm_scripts', 2 ); // 1 == CookieYes init script

}

if ( !function_exists( 'forqy_tracking_gtm_body' ) ) {

	/**
	 * Google Tag Manager <noscript> in <body>
	 *
	 * @return void
	 */
	function forqy_tracking_gtm_body(): void {

		$config = apply_filters( 'forqy_theme', array() ); // Theme config

		if ( !empty( $config[ 'gtm_id' ] ) ) { ?>
			<!-- Google Tag Manager (noscript) --><noscript><iframe src="<?php echo esc_url_raw( 'https://www.googletagmanager.com/ns.html?id=' . esc_attr( $config[ 'gtm_id' ] ) ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript><!-- End Google Tag Manager (noscript) -->
		<?php }

	}

	add_filter( 'wp_body_open', 'forqy_tracking_gtm_body', 1 );

}
