<?php
if(isset($_GET["model_name"])) {
    
    
    
    /*
        
        EXAMPLEs
        
        https://www.arcticspasdev.com/visao/?model_name=Tundra
        https://www.arcticspasdev.com/visao/?model_name=Summit XL
    
    */
    
    $model_name = $_GET["model_name"];
    
} else {
    
    $model_name = "Summit";
    
}

$model_name_escaped = str_replace(" ","%20", $model_name);
$url = "https://api.arcticspascore.com/live/jsons/visao-3d-viewer.php?model_name=$model_name_escaped";
$builder_json = file_get_contents($url);
$builder_array = json_decode($builder_json,TRUE);
$json_time = $builder_array["logging"]["time_elapsed"];
$array_of_acrylic = $builder_array["options_acrylics"];
$array_of_jets = $builder_array["options_jets"];
$array_of_cabinets = $builder_array["options_cabinets"];
$array_of_phrases = $builder_array["phrases"];
$visao_link = $builder_array["tub_details"]["visao_link"];
$acrylic_html = output_variants($array_of_acrylic);
$jets_html = output_variants($array_of_jets);
$cabinets_html = output_variants($array_of_cabinets);
$cache_refresh = md5(uniqid(mt_rand(0,1000).time())); //random guid
$google_maps_key = "AIzaSyBQZf1m2w9r5J7Mvt8XaU3W1vWelD2RS1A";

//csv of phrases
$array_of_phrases_csv = array_map(function ($item) {
    return '"' . $item . '"';
}, $array_of_phrases);

$array_of_phrases_csv = implode(',', $array_of_phrases_csv);

####################################
//grab the last time this file was updated
####################################
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$last_update_date = gmdate("F d Y H:i:s.", filemtime("index.php"));

########################
#OUTPUT VARIANTS HTML
########################
function output_variants($array) {
    
    $count = 0;
    foreach($array as $X) {
        
        $name = $array[$count]["option_name"];
        $id = $array[$count]["option_id"];
        $icon_url = $array[$count]["icon_url"];
        
        ########################
        //define the option name in the fist li
        ########################
        if($count == 0 && stripos($id, "dd-Acrylic") !== false) {
            $html .= "<li class='titles-options'>Shell<br>Color</li>"; 
        }
        
        if($count == 0 && stripos($id, "dd-Jets") !== false) {
            $html .= "<li class='titles-options'>Jets</li>"; 
        }
        
        if($count == 0 && stripos($id, "dd-Cabinet") !== false) {
            $html .= "<li class='titles-options'>Cabinet</li>"; 
        }
        
        ########################
        //output the rest of the html for images
        ########################
        $html .= "<li><img id='$id' class='clickable-image' src='$icon_url'></li>"; 
        $count = $count + 1;
        
    }
    
    $output = "<ul>$html</ul>";
    return $output;
}

########################
#CHECK IF THE USER IS ON MOBILE
########################
function isMobileDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $mobileKeywords = array('android', 'webos', 'iphone', 'ipod', 'blackberry', 'iemobile', 'opera mini');
    
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
$currentURL = 'http';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $currentURL .= 's';
}
$currentURL .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Parse the URL
$parsedURL = parse_url($currentURL);

// Rebuild the URL without the query string
$strippedURL = $parsedURL['scheme'] . '://' . $parsedURL['host'] . $parsedURL['path'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $model_name ?></title>
    <meta charset="UTF-8" />
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
      
    <!--google maps-->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_key ?>&libraries=places"></script>

      
    <script src="assets/index.umd.js"></script>
    <link rel="stylesheet" href="assets/styles.css">
    <link href="assets/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600" rel="stylesheet">
    
 </head>
<body>
    
    <script>
    const phrases = [<?php echo $array_of_phrases_csv ?>];
    </script>
    
    <script>
  // Initialize the Google Places Autocomplete service
  const input = document.getElementById('zip');
  const autocomplete = new google.maps.places.Autocomplete(input);

  // When a place is selected from the autocomplete, populate the address field
  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    if (!place.geometry || !place.formatted_address) {
      return;
    }
    document.getElementById('address').value = place.formatted_address;
  });

  // Handle form submission
  document.getElementById('addressForm').addEventListener('submit', (event) => {
    event.preventDefault();
    const zipCode = document.getElementById('zip').value;
    // Send only the zip code to your server for processing
    console.log('Zip code to be sent to the server:', zipCode);
    // Perform your AJAX request to send the data to the server
  });
</script>

<div id="app">
    
<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-6">        
        <!--<h2 id="title-left">The <?php echo $model_name ?> 3D Tour</h2>-->
        </div>
    
        <div class="col-md-6" id="title-right">
            <div id="arrow-container">
<!--            <img src="src/images/cta-arrow.png" alt="Arrow pointing to the button">
-->            <!--<button id="request-pricing-button" class="cta-button">Request Pricing</button>-->
            </div>
        </div>
    
    </div>
</div>
        
<div class="container-fluid">
	<div class="row">
	
         <?php
        ####################################################
        //VIEWER
        ####################################################
        ?>
        <div class="col-md-5">
	
            <div id="viewer-iframe">
                
                <iframe
                  id="visao-viewer-id"
                  src="<?php echo $visao_link ?>"
                  title="Arctic Spas 3d Hot Tubs Viewer"
                  style="border: none; width: 100%; height: 500px;"
                  allow="autoplay; fullscreen; vr"
                  allowfullscreen="true"
                  allowtransparency="true"
                >
                </iframe>
                
                <div class="buttons-div" id="camera-lock-buttons-div" style="width:100%;text-align:center;">
                  <button id="lock-camera-button" class="redbtn" disabled>Lock</button>
                  <button id="unlock-camera-button" class="greybtn" disabled>Unlock</button>  
                  <button id="reset-camera-button" class="darkgreybtn" disabled>Reset</button>
                <button id="show-help-button" class="blackbtn" disabled>Show Help</button>
                </div> 
            
            </div>
        
        </div>
		
        <?php
        ####################################################
        //Controls/Variants
        ####################################################
        ?>
        <div class="col-md-7">

            <div id="variants">
                
                <div id="loading-overlay">
                    <div class="spinner"></div>
                    <div id="loading-overlay-container">
                        <p>
                            Please wait whilst the Arctic Spas 3d viewer is loaded....<br>
                            <small id="loading-sub-text" class="loading-sub-text"></small>
                        </p>
                     </div>
                </div>
                
                <div id="variants-title">
                    <h2>Build Your Spa</h2>
                    <p>Explore a diverse range of shell colors, jets, and cabinet finishes.</p>
                </div>
                
                <div id="variantListJets">
                    <?php echo $jets_html; ?>
                </div>
                
                <div id="variantListAcrylics">
                    <?php echo $acrylic_html; ?>
                </div>
                
                <div id="variantListCabinets">
                    <?php echo $cabinets_html; ?> 
                </div>

                <div id="extraFeatures">
                    <ul>
                        <div id="augmented-reality-title">
                        <h2>See Your Spa in Your Space</h2>
                        </div>
                        
                        <div id="augmented-reality-instructions">
                        <?php
                        if (isMobileDevice()) {
                        ?>
                        <p>Envision your perfect hot tub in your own backyard using our AR tools. On a mobile device easily scan the generated QR code to see what your spa will look like in your space. </u></p>
                        <li><button id="start-ar-button" class="redbtn" disabled>Start AR</button></li>
                        <?php
                        } else {
                        ?>
                        <p>Envision your perfect hot tub in your own backyard using our AR tools. Using a computer, select “Show QR” code below. </u></p>
                        <li><button id="show-qr-button" class="redbtn" disabled>Show QR</button></li>
                        <?php
                        }
                        ?>
                         </div>
                        
                       <!-- <li><button id="close-qr-button" style= "margin-top:0px;" class="greybtn" disabled>Close QR</button> --></li>
                        
                    </ul>
                </div>
            
		    </div>
            
	    </div><!-- /col-md-7 -->
        
    </div><!-- /row -->
</div><!-- /container-fluid -->

 <?php
####################################################
//CTA BUTTON
####################################################
?>
<div class="container-fluid">
	<div class="row">
        
                <div class="pricing-cta" id="pricing-cta">
                    <button id="request-pricing-button" class="cta-button">Request Pricing</button>
                </div> 
        
     </div><!-- /row -->
</div><!-- /container-fluid -->

 <?php
####################################################
//VERSION
####################################################
?>
<div class="container-fluid">
	<div class="row">
        
                <div class="version-container" id="version-container">
                    <?php echo $model_name ?> | Builder Version: <?php echo $last_update_date ?> GMT | Api: <?php echo $json_time ?> secs | <a href="<?php echo $strippedURL ?>?cache=<?php echo $cache_refresh ?>">cache refresh</a> | <span id="status-label">Viewer Status: Un-Mounted</span> | Server IP: <?php echo $_SERVER['SERVER_ADDR']; ?>
                </div> 
        
     </div><!-- /row -->
</div><!-- /container-fluid -->       
        
<?php
####################################################
//Modal Code
####################################################
?>   
 <!-- The modal -->
    <div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestModalLabel">Request Pricing</h5>
                    <button type="button" id="close-button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- The form fields -->
                    <form>
                        <div class="form-group">
                            <label for="zipCode">Zip Code</label>
                            <input type="text" class="form-control" id="zipCode">
                            <input type="text" class="form-control" id="address" placeholder="Address" disabled>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" class="form-control" id="phone">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Request Pricing Now</button>
                </div>
            </div>
        </div>
    </div>
    
     <!-- Add the Bootstrap JavaScript and jQuery links -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
     <script>
        // JavaScript to trigger the modal when the button is clicked
        document.getElementById("request-pricing-button").addEventListener("click", function () {
            $('#requestModal').modal('show');
        });
         
          document.getElementById("close-button").addEventListener("click", function () {
        $('#requestModal').modal('hide');
    });
    </script> 
    

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    // Click event handler for Acrylics section images
    $("#variantListAcrylics .clickable-image").click(function() {
      var clickedImageId = $(this).attr("id");
      var clickedImageSrc = $(this).attr("src");
      $("#selectedAcrylicImageId").val(clickedImageId);
      $("#selectedAcrylicImageSrc").val(clickedImageSrc);
    });

    // Click event handler for Jets section images
    $("#variantListJets .clickable-image").click(function() {
      var clickedImageId = $(this).attr("id");
      var clickedImageSrc = $(this).attr("src");
      $("#selectedJetsImageId").val(clickedImageId);
      $("#selectedJetsImageSrc").val(clickedImageSrc);
    });

    // Click event handler for Cabinets section images
    $("#variantListCabinets .clickable-image").click(function() {
      var clickedImageId = $(this).attr("id");
      var clickedImageSrc = $(this).attr("src");
      $("#selectedCabinetImageId").val(clickedImageId);
      $("#selectedCabinetImageSrc").val(clickedImageSrc);
    });
  });
</script>
    
</div> <!-- /app --> 













 
<div style="display:none;"> 


      <div>
          <h5 class="labels" id="status-label">Viewer Status: Un-Mounted</div>
        <div>
        <div class="control-container">
          <h5 class="labels">Change Language</h5>
          <h5 class="current-labels" id="current-language">Language: </h5>
          <div id="language-div">
            <div class="dropdown">
              <button class="dropbtn">Languages</button>
              <div id="language-dropdown" class="dropdown-content">
              </div>
            </div>
          </div>
        </div>
        <div class="control-container">
          <h5 class="labels">Change Model Variant/Configuration</h5>
          <h5 class="current-labels" id="current-variant">Variant: </h5>
          <div id="variant-div">
            <div class="dropdown">
              <button class="dropbtn">Variants</button>
              <div id="variant-dropdown" class="dropdown-content">
            </div>
          </div>
        </div>
        </div>
        <div id="viewer-buttons-div">
          <div class="control-container">
            <h5 class="labels">Camera</h5>
            <div class="buttons-div" id="camera-lock-buttons-div">
              <button id="lock-camera-button" class="redbtn" disabled>Lock</button>
              <button id="unlock-camera-button" class="greybtn" disabled>Unlock</button>  
              <button id="reset-camera-button" class="darkgreybtn" disabled>Reset</button>
            </div> 
          </div>
          <div class="control-container">
            <h5 class="labels">Augmented Reality</h5>
            <div class="buttons-div" id="ar-buttons-div">
              <button id="start-ar-button" class="dropbtn" disabled>Start AR</button>
            </div>
          </div>
          <div class="control-container">
            <h5 class="labels">Steps</div>
            <h5 class="current-labels" id="current-step">Step: </h5>
            <div class="buttons-div" id="close-steps-buttons-div">
              <div class="dropdown">
                <button class="dropbtn">Steps</button>
              <div id="steps-dropdown" class="dropdown-content">
            </div>
            <button id="close-steps-button" class="greybtn" disabled>Close Steps</button>
          </div>
        </div>
          </div>
          <div class="control-container">
            <h5 class="labels">Step Player</h5>
            <div class="buttons-div" id="steps-buttons-div">
              <button id="play-button" class="greenbtn" disabled>Play</button>
              <button id="pause-button" class="redbtn" disabled>Pause</button>
              <button id="previous-step-button" class="dropbtn" disabled>Previous</button>
              <button id="next-step-button" class="dropbtn" disabled>Next</button>
            </div>
          </div>
          <div class="control-container">
            <h5 class="labels">QR Panel</h5>
            <div class="buttons-div" id="qr-buttons-div">
              <button id="show-qr-button" class="dropbtn" disabled>Show QR</button>
              <button id="close-qr-button" class="greybtn" disabled>Close QR</button>  
            </div>
          </div>
          <div class="control-container">
            <h5 class="labels">Help Panel</h5>
            <div class="buttons-div" id="help-buttons-div">
              <button id="show-help-button" class="dropbtn" disabled>Show Help</button>
              <button id="close-help-button" class="greybtn" disabled>Close Help</button>  
            </div>
          </div>
        </div>
    </div>
          
 
          
    <script src="assets/index.js"></script>
  </body>
</html>
