<?php
/*
Plugin Name: Visao 3D Viewer by JUCRA
Version: 1.26
Author: <a href='https://www.jucra.com'>JUCRA Digital SL</a>
Email: craig@jucra.com
Description: Insert the visao 3d viewer builder into pages on the arctic spas websites. Embed a shortcode on the page where you wish the builder to appear. EG: [visao_viewer model_name="Summit"]
*/

if (!defined('ABSPATH')) {
    exit;
}

################################### 
//GET PLUGIN VERSION
###################################
if ( !function_exists('get_plugin_data') ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Use the correct path to the main plugin file
$plugin_main_file = plugin_dir_path(__FILE__) . 'visao-3d-viewer.php';
$plugin_data = get_plugin_data($plugin_main_file);
$plugin_version = $plugin_data['Version'];

################################### 
//UNSINTALL OLD FILES AFTER UPDATE
###################################
function visao_cleanup_removed_files() {
    // List of files to be removed
    $files_to_remove = [
        plugin_dir_path(__FILE__) . 'settings.php',
        plugin_dir_path(__FILE__) . 'form.html',
    ];

    // Loop through and delete each file
    foreach ($files_to_remove as $file) {
        if (file_exists($file)) {
            @unlink($file); // Use @ to suppress errors if the file doesn't exist
        }
    }
}

// Hook into the plugin update process
add_action('upgrader_process_complete', 'visao_cleanup_on_update', 10, 2);

function visao_cleanup_on_update($upgrader_object, $options) {
    // Run only for this plugin
    if ($options['type'] === 'plugin' && isset($options['plugins'])) {
        if (in_array(plugin_basename(__FILE__), $options['plugins'], true)) {
            visao_cleanup_removed_files();
        }
    }
}

################################### 
//LOAD FUNCTIONS
###################################
include("functions.php");

################################### 
//UPDATER - added on 3/6/2024
###################################
include("update-checker.php");

################################### 
//OTHER SETTINGS
###################################
define('VISAO_VERSION'          , $plugin_version);
define('VISAO_PLUGIN_FILE'      , __FILE__);
define('VISAO_PLUGIN_PATH'      , plugin_dir_path(VISAO_PLUGIN_FILE));
define('VISAO_PLUGIN_URL'       , plugins_url(basename(VISAO_PLUGIN_PATH), basename(__FILE__)));
define('VISAO_ASSETS_URL'       , VISAO_PLUGIN_URL . '/assets');
define('VISAO_CHANGELOG_URL'    , VISAO_PLUGIN_URL . '/changelog.txt');
define('VISAO_FORM_ENDPOINT'    , '/'); //submit to root of wordpress kte

//this is needed to display the custom links on the plugin screen
add_filter('plugin_action_links', 'visao_plugin_action_links', 10, 2);

#########################
//shortcode for showing the builder
#########################
add_shortcode( 'visao_builder', 'sc_visao_builder' );
$visao_shortcode_added = false; //default to prevent nocache tags across header
function sc_visao_builder( $atts, $content = null ) {
    
    global $visao_shortcode_added; //this will be false
    $visao_shortcode_added = true; //now will be true
    
    /*--------------------------------enqueue scripts --------------------------------*/
    if ( function_exists( 'visao_enqueue_scripts' ) ) {
      visao_enqueue_scripts();
    }
    
     /*-------------------------------------------------------------------------*/
    //get the default tub that we know works 100%
    $model_name = $atts["model_name"];
    
     /*-------------------------------------------------------------------------*/
    //if there is no default tub defined, define it here
    if(!isset($model_name)) {
        $model_name = "Summit";
    }
    
     /*-------------------------------------------------------------------------*/
    //define all variables for the builder
    $model_name_escaped = str_replace(" ","%20", $model_name);
    $builder_json = "https://api.arcticspascore.com/live/jsons/visao-3d-viewer.php?model_name=$model_name_escaped";
    $builder_json = file_get_contents($builder_json);
    $builder_array = json_decode($builder_json,TRUE);
    $tub_exists = $builder_array["general_responses"]["tub_exists"];
    if($tub_exists == "true") { $tub_exists = 1; } else { $tub_exists = 0; }
    $api_time_secs = $builder_array["logging"]["time_elapsed"];
    $array_of_acrylic = $builder_array["options_acrylics"];
    $array_of_jets = $builder_array["options_jets"];
    $array_of_cabinets = $builder_array["options_cabinets"];
    $array_of_phrases = $builder_array["phrases"];
    $visao_iframe_url = $builder_array["tub_details"]["visao_link"];
    $guid = md5(uniqid(mt_rand(0,1000).time())); //random guid
    $server_ip = $_SERVER['SERVER_ADDR'];
    $cache_url_stripped = get_current_page_stripped_url();
    $browser_ip = $_SERVER['REMOTE_ADDR'];
    $source_url = get_current_page_url();
    $visao_iframe_link = '';
    $ar_button_html = '';
    $template_404_image = '';
    $variants_title_html = '';
	
	/*-------------------------------------------------------------------------*/
    //get plugin settings
	$plugin_settings = json_decode(get_option('visao_plugin_settings_json', '{}'), true);
	$plugin_settings = wp_parse_args($plugin_settings, [
		'hide_version_info' => 0, // Default to not hidden
		'hide_get_pricing' => 0, // Default to not hidden
		'form_page_url'    => '',
		'gravity_form_id'    => '',
		'gravity_form_html_field_id'    => '',
		
	]);
	$hide_version_info = $plugin_settings["hide_version_info"];
	$hide_get_pricing = $plugin_settings["hide_get_pricing"];
	$form_page_url = $plugin_settings["form_page_url"];
	$gravity_form_id = $plugin_settings["gravity_form_id"];
	$gravity_form_html_field_id = $plugin_settings["gravity_form_id"];
	
     /*-------------------------------------------------------------------------*/
    //options html
    if(is_array($array_of_acrylic)) {
        $array_of_acrylic_html = visao_output_variants($array_of_acrylic);
    }
    if(is_array($array_of_jets)) {    
        $array_of_jets_html = visao_output_variants($array_of_jets);
    }
    if(is_array($array_of_cabinets)) {
        $array_of_cabinets_html = visao_output_variants($array_of_cabinets);
    }
     /*-------------------------------------------------------------------------*/
    //csv of phrases
    $array_of_phrases_csv = array_map(function ($item) {
        return '"' . $item . '"';
    }, $array_of_phrases);

    $array_of_phrases_csv = implode(',', $array_of_phrases_csv);
    
     /*-------------------------------------------------------------------------*/
    //grab the last time this file was updated
    $last_update_date = gmdate("F d Y H:i:s.", filemtime(VISAO_PLUGIN_PATH. "visao-3d-viewer.php"));
    
    /*-------------------------------------------------------------------------*/
    //get the template for the 3d builder and form or show 404 error
	$template_html = "";
    if($tub_exists == true) {
        $template_html .= file_get_contents(VISAO_PLUGIN_PATH ."/template.html"); //this is the builder html template
    } else {
        $template_404_image = VISAO_ASSETS_URL . "template-404.png";
        $template_html = file_get_contents(VISAO_PLUGIN_PATH ."/template-404.html"); //this is when no tub exists
    }
	
	/*-------------------------------------------------------------------------*/
    //pricing now button
	$pricing_now_html = '
	<div class="container-fluid" id="visao-pricing-cta-container">
    <div class="row" id="pricing-cta-container">
        <div class="col-md-12">
            <div class="pricing-cta" id="pricing-cta">
			<a href="'.$form_page_url.'?model_name='.$model_name.'" id="pricing-link">
                <button id="request-pricing-button" class="cta-button">Request Pricing</button>
			</a>
            </div> 
        </div> 
     </div>
	 </div>';
	
	if($plugin_settings["hide_get_pricing"] == 1) {
		$pricing_now_html = "";
	}
	
	/*-------------------------------------------------------------------------*/
    //developers section
	
	$version_html = '
	<div class="container-fluid" id="visao-version-container" style="margin-top:25px;border:1px solid #CCC;padding:10px;">
    <div class="row">
			<div class="version-container" id="version-container">
				<p>
				<strong>Developers Tools:</strong> Plugin Version: '.VISAO_VERSION.' | Shortcode Model: '.$model_name.' | File Version: '.$last_update_date.' GMT | Api: '.$api_time_secs.' secs | <a href="'.$cache_url_stripped.'?cache='.$guid.'" rel="nofollow">Cache Refresh</a> | <span id="status-label">Viewer Status: Un-Mounted</span> | Server IP: '.$server_ip.' | <a href="'.VISAO_CHANGELOG_URL.'" target="_blank">Changelog</a>
				</p>
			</div> 
		 </div>
	</div>';
	
	if($plugin_settings["hide_version_info"] == 1) {
		$version_html = "";
	}
    
    /*-------------------------------------------------------------------------*/
    //do the search and replace part on the template to replce placeholders with variable data
    $template_html = str_replace("##VISAO_IFRAME_URL##", $visao_iframe_url, $template_html);
    $template_html = str_replace("##VISAO_ARRAY_OF_PHRASES##", $array_of_phrases_csv, $template_html);
    $template_html = str_replace("##VISAO_LINK##", $visao_iframe_link, $template_html);
    $template_html = str_replace("##VISAO_JETS_HTML##", $array_of_jets_html, $template_html);
    $template_html = str_replace("##VISAO_ACRYLICS_HTML##", $array_of_acrylic_html, $template_html);
    $template_html = str_replace("##VISAO_CABINETS_HTML##", $array_of_cabinets_html, $template_html);
    $template_html = str_replace("##VISAO_AR_BUTTON_HTML##", $ar_button_html, $template_html);
    $template_html = str_replace("##VISAO_BUILDER_VERSION_DATE##", $last_update_date, $template_html);
    $template_html = str_replace("##VISAO_API_TIME##", $api_time_secs, $template_html);
    $template_html = str_replace("##VISAO_CACHE_URL##", $cache_url_stripped, $template_html);
    $template_html = str_replace("##VISAO_GUID##", $guid, $template_html);
    $template_html = str_replace("##VISAO_SERVER_IP##", $server_ip, $template_html);
    $template_html = str_replace("##VISAO_MODEL_NAME##", $model_name, $template_html);
    $template_html = str_replace("##VISAO_URL_TO_ASSETS##", VISAO_ASSETS_URL, $template_html);
    $template_html = str_replace("##VISAO_CHANGELOG_URL##", VISAO_CHANGELOG_URL, $template_html);
    $template_html = str_replace("##VISAO_VERSION##", VISAO_VERSION, $template_html);
    $template_html = str_replace("##VISAO_IP_ADDRRESS##", $browser_ip, $template_html);
    $template_html = str_replace("##VISAO_FORM_ENDPOINT##", VISAO_FORM_ENDPOINT, $template_html);
    $template_html = str_replace("##VISAO_GET_SOURCE_URL##", $source_url, $template_html);
    $template_html = str_replace("##VISAO_404_IMAGE##", $template_404_image, $template_html);
    $template_html = str_replace("##VISAO_VARIANTS_TITLE##", $variants_title_html, $template_html);
    $template_html = str_replace("##VISAO_GUID##", $guid, $template_html);
	$template_html = str_replace("##VISAO_PRICING_NOW_BUTTON_HTML##", $pricing_now_html, $template_html);
	$template_html = str_replace("##VISAO_VERSION_HTML##", $version_html, $template_html);
    
    return $template_html;
    
}
