<?php

/**
 * GA4
 *
 * @package forqys/tracking
 * @since   1.0.0
 */

if ( !function_exists( 'forqy_tracking_ga4_head' ) ) {

	/**
	 * Google Analytics 4 Tracking Code in <head>
	 *
	 * @return void
	 */
	function forqy_tracking_ga4_head(): void {

		$config         = apply_filters( 'forqy_theme', array() ); // Theme config
		$config_consent = $config[ 'consent' ] ?? true;

		if ( !empty( $config[ 'ga4_id' ] ) ) {
			if ( $config_consent ) { ?>
				<!--noptimize--><!-- Google Consent V2 Default -->
				<script>
                    window.dataLayer = window.dataLayer || [];

                    function gtag() {
                        dataLayer.push(arguments);
                    }

                    gtag('consent', 'default', {
                        'ad_user_data': 'denied',
                        'ad_personalization': 'denied',
                        'ad_storage': 'denied',
                        'analytics_storage': 'denied',
                        'wait_for_update': 500,
                    });
                    gtag('js', new Date());
                    gtag('config', '<?php echo esc_js( $config[ 'ga4_id' ] ); ?>');
				</script><!-- End Google Consent V2 Default --><!--/noptimize-->
			<?php } ?>
			<!--noptimize--><!-- Global Site Tag -->
			<script src="<?php echo esc_url_raw( 'https://www.googletagmanager.com/gtag/js?id=' . $config[ 'ga4_id' ] ); ?>" async></script>
			<!-- End Global Site Tag --><!--/noptimize-->
		<?php }

	}

	add_filter( 'wp_head', 'forqy_tracking_ga4_head', 2 );

}
