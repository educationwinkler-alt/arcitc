<?php
/*

    Updater Created on 4/6/2024 by craig@jucra.com
    
    Idea taken from: https://github.com/rudrastyh/misha-update-checker/blob/main/misha-update-checker.php
    
    When moving to new plugin, just update the class names in order to avoid conflict with other plugins.
    
    Our plugin repos are located at https://www.jucra.com/apps/plugins/
    
*/
if( ! class_exists( 'UpdateCheckerVisao3dViewer' ) ) {
  
	class UpdateCheckerVisao3dViewer{

		public $plugin_slug;
		public $version;
		public $cache_key;
		public $cache_allowed;
        public $updater_endpoint_url;

		public function __construct() {
            
            global $plugin_version; 
            //$plugin_version is grabbed from the plugin, make sure this exists in the plugin main file
            //$plugin_data = get_plugin_data(__FILE__);
            //$plugin_version = $plugin_data['Version'];
            
            $plugin_slug = plugin_basename( __DIR__ );
            $updater_endpoint_url = "https://www.jucra.com/apps/plugins/$plugin_slug/info.php";
            $plugin_slug_underscores = str_replace("-","_",$plugin_slug);
            
			$this->plugin_slug = $plugin_slug;
			$this->version = $plugin_version;
			$this->cache_key = "jucra_custom_updater_$plugin_slug_underscores";
			$this->cache_allowed = false;  
            $this->updater_endpoint_url = $updater_endpoint_url;
            
            add_filter( 'plugins_api', array( $this, 'info' ), 20, 3 );
			add_filter( 'site_transient_update_plugins', array( $this, 'update' ) );
			add_action( 'upgrader_process_complete', array( $this, 'purge' ), 10, 2 );

		}

		public function request(){

			$remote = get_transient( $this->cache_key );
            $updater_endpoint_url = $this->updater_endpoint_url;

			if( false === $remote || ! $this->cache_allowed ) {

				$remote = wp_remote_get(
					$updater_endpoint_url,
					array(
						'timeout' => 5,
						'headers' => array(
							'Accept' => 'application/json'
						)
					)
				);

				if(
					is_wp_error( $remote )
					|| 200 !== wp_remote_retrieve_response_code( $remote )
					|| empty( wp_remote_retrieve_body( $remote ) )
				) {
					return false;
				}

				set_transient( $this->cache_key, $remote, DAY_IN_SECONDS );

			}

			$remote = json_decode( wp_remote_retrieve_body( $remote ) );

			return $remote;

		}

		function info( $res, $action, $args ) {

			// do nothing if you're not getting plugin information right now
			if( 'plugin_information' !== $action ) {
				return $res;
			}

			// do nothing if it is not our plugin
			if( $this->plugin_slug !== $args->slug ) {
				return $res;
			}

			// get updates
			$remote = $this->request();

			if( ! $remote ) {
				return $res;
			}

			$res = new stdClass();

			$res->name = $remote->name;
			$res->slug = $remote->slug;
			$res->version = $remote->version;
			$res->tested = $remote->tested;
			$res->requires = $remote->requires;
			$res->author = $remote->author;
			$res->author_profile = $remote->author_profile;
			$res->download_link = $remote->download_url;
			$res->trunk = $remote->download_url;
			$res->requires_php = $remote->requires_php;
			$res->last_updated = $remote->last_updated;

			$res->sections = array(
				'description' => $remote->sections->description,
				'installation' => $remote->sections->installation,
				'changelog' => $remote->sections->changelog
			);

			if( ! empty( $remote->banners ) ) {
				$res->banners = array(
					'low' => $remote->banners->low,
					'high' => $remote->banners->high
				);
			}

			return $res;

		}

		public function update( $transient ) {
            
            $plugin_slug = plugin_basename( __DIR__ );
            
			if ( empty($transient->checked ) ) {
				return $transient;
			}

			$remote = $this->request();

			if(
				$remote
				&& version_compare( $this->version, $remote->version, '<' )
				&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<=' )
				&& version_compare( $remote->requires_php, PHP_VERSION, '<' )
			) {
				$res = new stdClass();
				$res->slug = $this->plugin_slug;
				$res->plugin = "$plugin_slug/$plugin_slug.php"; // misha-update-plugin/misha-update-plugin.php
				$res->new_version = $remote->version;
				$res->tested = $remote->tested;
				$res->package = $remote->download_url;

				$transient->response[ $res->plugin ] = $res;

	    }
            
            /*
            echo "<pre style='background-color:white;'>";
            echo "This is Line number: " . __LINE__ . " in " . basename(__FILE__)  . PHP_EOL;
            print_r($res);
            echo "</pre>";
            exit;
            */

			return $transient;

		}

		public function purge( $upgrader, $options ){

			if (
				$this->cache_allowed
				&& 'update' === $options['action']
				&& 'plugin' === $options[ 'type' ]
			) {
				// just clean the cache when new plugin version is installed
				delete_transient( $this->cache_key );
			}

		}

	}

	new UpdateCheckerVisao3dViewer();

}