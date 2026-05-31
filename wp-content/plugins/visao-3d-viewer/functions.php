<?php
add_filter( 'plugin_row_meta', 'add_changelog_link_to_plugin_meta', 10, 4 );
function add_changelog_link_to_plugin_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
    // Replace 'your-plugin-directory/your-main-plugin-file.php' with the relative path to your main plugin file.
    if ( strpos( $plugin_file, 'visao-3d-viewer/visao-3d-viewer.php' ) !== false ) {
        $changelog_link = '<a href="https://www.arcticspasdev.com/wordpress/wp-content/plugins/visao-3d-viewer/changelog.txt" target="_blank">Changelog</a>';
        $plugin_meta[] = $changelog_link;
    }

    return $plugin_meta;
}

function __ADMIN_OPTIONS_PAGES__() {}

add_action( 'admin_menu', 'visao_register_custom_options_pages' );
function visao_register_custom_options_pages() {
    // Register the Main Options page under Settings
    add_submenu_page(
        'options-general.php',  // Parent slug (Settings menu)
        'Main Options',         // Page title
        'Visao 3D Builder Settings',         // Menu title
        'manage_options',       // Capability
        'visao_main_options',   // Menu slug
        'visao_main_options_page'     // Callback function
    );
}

add_action( 'admin_init', 'visao_register_plugin_settings' );
function visao_register_plugin_settings() {
    // Register the setting
    register_setting(
        'plugin_settings_group', // Settings group
        'visao_plugin_settings_json'   // Option name
    );
}

// Callback function for the Main Options page
function visao_main_options_page() {
    ?>
    <div class="wrap">
        <h1>Visao 3D Buider Main Settings</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=visao_main_options&tab=plugin_settings" class="nav-tab <?php echo visao_get_active_tab() === 'plugin_settings' ? 'nav-tab-active' : ''; ?>">Plugin Settings</a>
            <a href="?page=visao_main_options&tab=css_editor" class="nav-tab <?php echo visao_get_active_tab() === 'css_editor' ? 'nav-tab-active' : ''; ?>">CSS Editor</a>
        </h2>
        
        <div class="tab-content">
            <?php
            $current_tab = visao_get_active_tab();
            if ( $current_tab === 'plugin_settings' ) {
                visao_render_plugin_settings_tab();
            } elseif ( $current_tab === 'css_editor' ) {
                visao_render_css_editor_tab();
            } else {
                render_plugin_settings_tab(); // Default to Plugin Settings
            }
            ?>
        </div>
    </div>
    <?php
}

// Utility function to get the current active tab
function visao_get_active_tab() {
    return isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'plugin_settings';
}

function visao_render_plugin_settings_tab() {
	
	global $plugin_version;
	
    // Get the current settings (JSON-decoded into an array)
    $settings = json_decode( get_option( 'visao_plugin_settings_json', '{}' ), true );

    // Default values
    $settings = wp_parse_args( $settings, [
		'hide_version_info' => 0,
        'hide_get_pricing' => 0,
        'form_page_url'    => '',
		'gravity_form_id'    => '',
		'gravity_form_html_field_id'    => '',
	    ]);

    ?>
    <h2>Plugin Settings</h2>
    <p>Here you can manage your plugin settings.</p>
	<p>Current loaded plugin version is: <?php echo $plugin_version ?>.
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="visao_save_plugin_settings">
        <?php wp_nonce_field( 'visao_plugin_settings_save', 'visao_plugin_settings_nonce' ); ?>
        <table class="form-table">
			<tr>
                <th scope="row">
                    <label for="hide_version_info">Hide Version Section?</label>
                </th>
                <td>
                    <input type="checkbox" id="hide_version_info" name="plugin_settings[hide_version_info]" value="1" <?php checked( $settings['hide_version_info'], 1 ); ?> />
					<small>If you want to hide the version box under the builder, check this box</small>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="hide_get_pricing">Hide "Get Pricing Now" Button?</label>
                </th>
                <td>
                    <input type="checkbox" id="hide_get_pricing" name="plugin_settings[hide_get_pricing]" value="1" <?php checked( $settings['hide_get_pricing'], 1 ); ?> />
					<small>If you want to hide the pricing now form, check this box.</small>
                </td>
            </tr>
			<tr>
                <th scope="row">
                    <label for="gravity_form_id">Gravity Forms ID</label>
                </th>
                <td>
                    <input type="text" id="gravity_form_id" name="plugin_settings[gravity_form_id]" value="<?php echo esc_attr( $settings['gravity_form_id'] ); ?>" class="regular-text" />
					<small>If using gravity forms, enter the form id to display special functions in top of the form</small>
                </td>
            </tr>
			<tr>
                <th scope="row">
                    <label for="gravity_form_html_field_id">Gravity Forms Field ID</label>
                </th>
                <td>
                    <input type="text" id="gravity_form_html_field_id" name="plugin_settings[gravity_form_html_field_id]" value="<?php echo esc_attr( $settings['gravity_form_html_field_id'] ); ?>" class="regular-text" />
					<small>If using gravity forms, enter the field id of the html field inside the form</small>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="form_page_url">Form Page URL</label>
                </th>
                <td>
                    <input type="text" id="form_page_url" name="plugin_settings[form_page_url]" value="<?php echo esc_attr( $settings['form_page_url'] ); ?>" class="regular-text" />
					<small>This will be the url where the pricing form is located (this can be relative or absolute)</small>
                </td>
            </tr>
        </table>
        <?php
        // Submit button
        submit_button();
        ?>
    </form>
    <?php
}

add_action( 'admin_post_visao_save_plugin_settings', 'visao_save_plugin_settings' );

function visao_save_plugin_settings() {
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }

    // Verify nonce for security
    check_admin_referer( 'visao_plugin_settings_save', 'visao_plugin_settings_nonce' );

    // Prepare settings array with fallback defaults
    $settings = [
		'hide_version_info' => isset( $_POST['plugin_settings']['hide_version_info'] ) ? 1 : 0,
        'hide_get_pricing' => isset( $_POST['plugin_settings']['hide_get_pricing'] ) ? 1 : 0,
        'form_page_url'    => sanitize_text_field( $_POST['plugin_settings']['form_page_url'] ?? '' ),
		'gravity_form_id'    => sanitize_text_field( $_POST['plugin_settings']['gravity_form_id'] ?? '' ),
		'gravity_form_html_field_id'    => sanitize_text_field( $_POST['plugin_settings']['gravity_form_html_field_id'] ?? '' ),
    ];

    // Save settings as JSON
    update_option( 'visao_plugin_settings_json', wp_json_encode( $settings ) );

    // Redirect back to the settings page
    wp_redirect( admin_url( 'options-general.php?page=visao_main_options&tab=plugin_settings&saved=true' ) );
    exit;
}

// Enqueue CodeMirror scripts and styles
add_action('admin_enqueue_scripts', 'visao_enqueue_codemirror_assets');
function visao_enqueue_codemirror_assets($hook) {
    // Only load on the specific admin page
    if (isset($_GET['page']) && $_GET['page'] === 'visao_main_options') {
        wp_enqueue_script('code-editor');
        wp_enqueue_style('code-editor');
        wp_enqueue_script('custom-css-editor', plugin_dir_url(__FILE__) . 'assets/custom-css-editor.js', ['jquery', 'code-editor'], false, true);
    }
}

// Render CSS Editor Tab
function visao_render_css_editor_tab() {
    // Path to the CSS file
    $css_file_path = plugin_dir_path(__FILE__) . 'assets/visao-styles.css';

    // Save the CSS if the form is submitted
    if (isset($_POST['visao_css'])) {
        // Security check using nonce and capability
        if (check_admin_referer('save_visao_css', 'visao_css_nonce') && current_user_can('manage_options')) {
            $new_css = stripslashes($_POST['visao_css']);
            file_put_contents($css_file_path, $new_css);
            echo '<div id="message" class="updated notice is-dismissible"><p>' . __('CSS updated successfully.', 'visao-3d-viewer') . '</p></div>';
        }
    }

    // Load the current CSS content
    $css_content = '';
    if (file_exists($css_file_path)) {
        $css_content = file_get_contents($css_file_path);
    } else {
        $css_content = '/* CSS file not found. */';
    }

    ?>
    <h2>3D Builder CSS Editor</h2>
    <p>Here you can edit your custom CSS with syntax highlighting.</p>
    <form method="post">
        <?php wp_nonce_field('save_visao_css', 'visao_css_nonce'); ?>
        <textarea id="visao-css-editor" name="visao_css" rows="20" style="width:100%;"><?php echo esc_textarea($css_content); ?></textarea>
        <?php submit_button(__('Save CSS', 'visao-3d-viewer')); ?>
    </form>
    <?php
}

function __GRAVITY_FORMS__() {}

// Helper function to validate and retrieve `icon_url`
function visao_get_option_icon_url($options_array, $option_id) {
    foreach ($options_array as $option) {
        if (isset($option['option_id']) && $option['option_id'] === $option_id) {
            return isset($option['icon_url']) ? $option['icon_url'] : '';
        }
    }
    return false; // Return false if not found
}

// Retrieve plugin settings
$plugin_settings = json_decode(get_option('visao_plugin_settings_json', '{}'), true);
$plugin_settings = wp_parse_args($plugin_settings, [
    'gravity_form_id'           => '',
    'gravity_form_html_field_id' => '',
]);

$visao_gravity_form_id = $plugin_settings['gravity_form_id'];
$visao_gravity_form_html_field_id = $plugin_settings['gravity_form_html_field_id'];

// Check if $gravity_form_id and $gravity_form_html_field_id are valid
if (is_numeric($visao_gravity_form_id) && is_numeric($visao_gravity_form_html_field_id)) {

    // Dynamically add the filters for the specific form
    add_filter("gform_pre_render_{$visao_gravity_form_id}", 'visao_populate_custom_html_field');
    add_filter("gform_pre_process_{$visao_gravity_form_id}", 'visao_populate_custom_html_field');

    function visao_populate_custom_html_field($form) {
        global $visao_gravity_form_id, $visao_gravity_form_html_field_id;

        // Retrieve URL parameters from the 3D Builder
        $model_name = isset($_GET['model_name']) ? sanitize_text_field($_GET['model_name']) : '';
        $options_shell = isset($_GET['option_acrylic']) ? sanitize_text_field($_GET['option_acrylic']) : '';
        $options_jets = isset($_GET['option_jets']) ? sanitize_text_field($_GET['option_jets']) : '';
        $options_cabinet = isset($_GET['option_cabinet']) ? sanitize_text_field($_GET['option_cabinet']) : '';

        // If any parameter is empty, stop processing
        if (empty($model_name) || empty($options_shell) || empty($options_jets) || empty($options_cabinet)) {
            return $form;
        }

        // Retrieve the 3D Builder JSON
        $model_name_escaped = str_replace(' ', '%20', $model_name);
        $builder_json_url = "https://api.arcticspascore.com/live/jsons/visao-3d-viewer.php?model_name=$model_name_escaped";
        $builder_json = file_get_contents($builder_json_url);
        $builder_array = json_decode($builder_json, true);

        // Ensure the tub exists
        if (!$builder_array['general_responses']['tub_exists']) {
            return $form;
        }

        // Validate and retrieve the icon URLs for each option
        $shell_icon_url = visao_get_option_icon_url($builder_array['options_acrylics'], $options_shell);
        $jets_icon_url = visao_get_option_icon_url($builder_array['options_jets'], $options_jets);
        $cabinet_icon_url = visao_get_option_icon_url($builder_array['options_cabinets'], $options_cabinet);

        // If any icon URL is invalid (not found), stop processing
        if (!$shell_icon_url || !$jets_icon_url || !$cabinet_icon_url) {
            return $form;
        }

        // Build the output
        $html_output = "
		<style>
		.custom-options {
			text-align: center;
			margin-bottom:35px;
		}
		.custom-options h2 {
			margin-bottom: 20px;
		}
		.custom-options .option-container {
			display: flex;
			justify-content: center;
			gap: 20px;
		}
		.custom-options .option-container div {
			text-align: center;
		}
		.custom-options img {
			width: 80px;
			height: 80px;
			border-radius: 50%;
			display: block;
			margin: 0 auto;
		}
		.custom-options p {
			margin-top: 10px;
		}
		.custom-options h2 {
			font-size: 33px;
		}
		</style>";
		$html_output .= '<div class="custom-options">';
		$html_output .= '<h2>Model Name: ' . esc_html($model_name) . '</h2>';
		$html_output .= '<div class="option-container">';

		$html_output .= '<div>';
		$html_output .= '<img src="' . esc_url($shell_icon_url) . '" alt="Shell Icon">';
		$html_output .= '<p><strong>Shell Option</strong></p>';
		$html_output .= '</div>';

		$html_output .= '<div>';
		$html_output .= '<img src="' . esc_url($jets_icon_url) . '" alt="Jets Icon">';
		$html_output .= '<p><strong>Jets Option</strong></p>';
		$html_output .= '</div>';

		$html_output .= '<div>';
		$html_output .= '<img src="' . esc_url($cabinet_icon_url) . '" alt="Cabinet Icon">';
		$html_output .= '<p><strong>Cabinet Option</strong></p>';
		$html_output .= '</div>';

		$html_output .= '</div>'; // Closing option-container
		$html_output .= '</div>'; // Closing custom-options

        // Insert the output into the HTML field with ID of $gravity_form_html_field_id
        foreach ($form['fields'] as &$field) {
            if ($field->id == $visao_gravity_form_html_field_id) {
                $field->content = $html_output;
            }
        }

        return $form;
    }
}

function __OTHER_FUNCTIONS__() {}

########################
#LOAD STYLES AND JS IN HEADER
########################
function visao_enqueue_scripts() {

    //css
    wp_enqueue_style('visao-css-custom-styles', VISAO_ASSETS_URL . '/visao-styles.css', array(), '1.0', 'all');
    wp_enqueue_style('visao-css-custom-styles-mobile', VISAO_ASSETS_URL . '/visao-styles-mobile.css', array(), '1.0', 'all');
    wp_enqueue_style('visao-css-boostrap-styles', VISAO_ASSETS_URL . '/bootstrap.min.css', array(), '1.0', 'all');
    wp_enqueue_style('visao-css-poppins-font', 'https://fonts.googleapis.com/css?family=Poppins:400,600', array(), '1.0', 'all');

    //js
    wp_enqueue_script('visao-scripts-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js', array(), '1.0', true);
    wp_enqueue_script('visao-scripts-umd', 'https://cdn.jsdelivr.net/npm/@visao/viewer-api@0.0.1-beta.21', array(), '1.0', true);

}

//this will only be added to header when plugin is run.
function visao_add_no_cache_tags() {
    
        global $visao_shortcode_added;
        if ($visao_shortcode_added == true) {

            echo PHP_EOL;
            echo '<!-- Cache Control Added by Visao 3D Builder Plugin-->' . PHP_EOL;
            echo '<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />'. PHP_EOL;
            echo '<meta http-equiv="Pragma" content="no-cache" />'. PHP_EOL;
            echo '<meta http-equiv="Expires" content="0" />';
            echo PHP_EOL . PHP_EOL;

    }

}

add_action('wp_head', 'visao_add_no_cache_tags');

/*-------------------------------- Links  in plugin page--------------------------------*/
function visao_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<span style="background-color:#ec0950;color:#fff;font-weight:bold;padding:0px 8px 2px">' .
                          'Custom Plugin by JUCRA' .
                             '</span>
                           <span><a href="'.VISAO_PLUGIN_URL.'/changelog.txt" target="_blank">Changelog</a></span>  
                             
                             ';
        array_unshift($links, $settings_link);
    }
    return $links;
}

########################
#OUTPUT VARIANTS HTML
########################
function visao_output_variants($array) {
    
    $html = '';
    
    $count = 0;
    foreach($array as $X) {
        
        $name = $array[$count]["option_name"];
        $id = $array[$count]["option_id"];
        $icon_url = $array[$count]["icon_url"];
        $option_type = $array[$count]["option_type"];
        
        ########################
        //define the option name in the fist li
        ########################
        if($count == 0 && $option_type == "acrylic") {
            $html .= "<li class='titles-options'>Shell<br>Colour</li>"; 
        }
        
        if($count == 0 && $option_type == "jet") {
            $html .= "<li class='titles-options'>Jets</li>"; 
        }
        
        if($count == 0 && $option_type == "cabinet") {
            $html .= "<li class='titles-options'>Cabinet<br>Colour</li>"; 
        }
        
        ########################
        //output the rest of the html for images
        ########################
        $html .= "<li><img id='$id' class='clickable-image' src='$icon_url'>";
        $html .= "<div class='option-name-text'>$name</div>";
        $html .= "</li>"; 
        
        $count = $count + 1;
        
    }
    
    $output = "<ul>$html</ul>";
    return $output;
}

########################
#CHECK IF THE USER IS ON MOBILE
########################
function is_mobile_device() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $mobileKeywords = array(
        'android', 
        'webos', 
        'iphone', 
        'ipod', 
        'blackberry', 
        'iemobile', 
        'opera mini',
        'ios'
    );
    
    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return true;
        }
    }
    
    return false;
}

########################
#GET CURRENT PAGE URL FOR THE CACHING
########################
function get_current_page_stripped_url() {
    
    $currentURL = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $currentURL .= 's';
    }
    $currentURL .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Parse the URL
    $parsedURL = parse_url($currentURL);

    // Rebuild the URL without the query string
    $strippedURL = $parsedURL['scheme'] . '://' . $parsedURL['host'] . $parsedURL['path'];
    
    return $strippedURL;

}

########################
#GET CURRENT PAGE URL
########################
function get_current_page_url() {
    
    $currentURL = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $currentURL .= 's';
    }
    $currentURL .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    return $currentURL;

}

//end functions file