var viewerAPI, visao, language;

function enableButton(button, onClick) {
  if (!button) {
    return;
  }

  button.removeAttribute("disabled");
  button.onclick = onClick;
}

//Languages
function setSelectedLanguage(newLanguage) {
  language = newLanguage;
  const currentLanguage = document.getElementById("current-language");
  if (!currentLanguage) return;
  currentLanguage.innerHTML =
    "Language: " + newLanguage.toUpperCase();
}

function handleChangeLanguage(newLanguage) {
  visao.changeLanguage(newLanguage);
  setSelectedLanguage(newLanguage);
}

function showLanguageInformation() {
  visao.getLanguageInformation(function (languageInformation) {
    var languages = languageInformation["languages"];
    var currentLanguage = languageInformation["language"];
    var languageDropdown = document.getElementById("language-dropdown");

    setSelectedLanguage(currentLanguage);
    
    if (languageDropdown) {
    	languageDropdown.innerHTML = "";
    }
    
    languages.forEach((language) => {
      var languageTag = document.createElement("a");
      languageTag.setAttribute("id", "dd-" + language);
      languageTag.innerHTML = language.toUpperCase();
      languageTag.onclick = function () {
        handleChangeLanguage(language);
      };
      languageDropdown?.appendChild(languageTag);
    });
  });
}

// Variants
function setSelectedVariant(newVariant) {
	const currentVariant = document.getElementById("current-variant");
	if (!currentVariant) return;
	currentVariant.innerHTML = "Variant: " + newVariant.toUpperCase();
}

function showVariantInformation() {
  visao.getVariantInformation((variantInformation) => {
    var variantDropdown = document.getElementById("variant-dropdown");
    var variants = variantInformation["modelVariants"];
    var currentVariant = variantInformation["modelVariant"];

    setSelectedVariant(currentVariant);
    
    if (variantDropdown) { 
    	variantDropdown.innerHTML = "";
    }
    
    variants.forEach((variant) => {
      var variantTag = document.createElement("a");
      variantTag.setAttribute("id", "dd-" + variant);
      variantTag.innerHTML = variant.toUpperCase();
      variantTag.onclick = function () {
        handleVariantChange(variant);
      };
      variantDropdown?.appendChild(variantTag);
    });
  });
}

function handleVariantChange(newVariant) {
  visao.showModelVariant(newVariant);
  //console.log("Variant changed to:", newVariant);
  setSelectedVariant(newVariant);
}

//####################################
// CUSTOM BY CRAIG
//####################################
// Object to store selected links for each variantList
const selectedImages = {};
const clickableImages = document.querySelectorAll(".clickable-image");

// Add click event listeners to each image
clickableImages.forEach(image => {
    
      image.addEventListener("click", function() {
      // Get the variantList ID
      const variantListId = this.closest("div").id;

      // Remove the red border from all images in the same variantList
      //const imagesInVariantList = document.querySelectorAll('#${variantListId} .clickable-image');
      //const imagesInVariantList = document.querySelectorAll("#${variantListId} .clickable-image");
      const imagesInVariantList = document.querySelectorAll(`#${variantListId} .clickable-image`);
      imagesInVariantList.forEach(image => image.classList.remove("selected-image"));

      // Add the red border to the clicked image
      this.classList.add("selected-image");

      // Get the new variant from the image's ID
      const newVariant = this.id.replace("dd-", "");

      // Update the selected image for this variantList
      selectedImages[variantListId] = newVariant;

      // Call the handleVariantChange function with the new variant
      handleVariantChange(newVariant);

      //log to console
      console.log("Variant changed to:", newVariant);

    });
});

//loading phrases for the builder
const loadingSubText = document.querySelector('#loading-sub-text');

let index = 0;

function changePhrase() {
loadingSubText.style.opacity = 0;

setTimeout(() => {
  index = (index + 1) % phrases.length;
  loadingSubText.textContent = phrases[index];
  loadingSubText.style.opacity = 1;
}, 500);

setTimeout(changePhrase, 5000); // Call the function again after 5 seconds
}

// Start the initial cycle
changePhrase();

//####################################

// Camera
function showLockCameraButton() {
  enableButton(document.getElementById("lock-camera-button"), function () {
    visao.lockCamera();
  });
}

function showUnlockCameraButton() {
  enableButton(document.getElementById("unlock-camera-button"), function () {
    visao.unlockCamera();
  });
}

function showResetCameraButton() {
  enableButton(document.getElementById("reset-camera-button"), function () {
    visao.resetCamera();
  });
}

// AR
function showARButton() {
  enableButton(document.getElementById("start-ar-button"), function () {
    visao.startAR();
  });
}

// Steps

function setSelectedStep(newStep) {
  document.getElementById("current-step").innerHTML =
    "Step: " + newStep.toUpperCase();
}

function showSteps() {
  visao.getDemos(function (payload) {
    var demos = payload["demos"];
    var demo = demos[0];
    if (!demo) {
      return;
    }

    var steps = demo.steps;
    var stepsDropdown = document.getElementById("steps-dropdown");
    setSelectedStep(steps[0].name);
    stepsDropdown.innedHTML = "";
    // stepsDropdown.children = [];

    steps.forEach(function (step) {
      var localizeTitle = step.localizations.find(
        (l) => l.language === language
      );
      var stepTitle = localizeTitle ? localizeTitle.content : step.name;
      var stepTag = document.createElement("a");
      stepTag.setAttribute("id", "dd-" + step);
      stepTag.innerHTML = stepTitle;
      stepTag.onclick = function () {
        visao.showStep(step.name);
        setSelectedStep(step.name);
      };
      stepsDropdown.appendChild(stepTag);
    });
  });
}

function showCloseStepButton() {
  enableButton(document.getElementById("close-steps-button"), function () {
    visao.closeStep();
  });
}

function showPlayStepsButton() {
  enableButton(document.getElementById("play-button"), function () {
    visao.play();
  });
}

function showPauseStepsButton() {
  enableButton(document.getElementById("pause-button"), function () {
    visao.pause();
  });
}

function showPreviousStepButton() {
  enableButton(document.getElementById("previous-step-button"), function () {
    visao.previousStep();
  });
}

function showNextStepButton() {
  enableButton(document.getElementById("next-step-button"), function () {
    visao.nextStep();
  });
}

function showOpenQRButton() {
  enableButton(document.getElementById("show-qr-button"), function () {
    visao.showQR();
  });
}

function showCloseQRButton() {
  enableButton(document.getElementById("close-qr-button"), function () {
    visao.closeQR();
  });
}

function showHelpPanelButton() {
  enableButton(document.getElementById("show-help-button"), function () {
    visao.showHelp();
  });
}

function showCloseHelpPanelButton() {
  enableButton(document.getElementById("close-help-button"), function () {
    visao.closeHelp();
  });
}

function setStatusLabel(status) {
  document.getElementById("status-label").innerHTML =
    "Viewer Status: " + status;
}

function hanldeMountedState() {
  setStatusLabel("Mounted");
  showLanguageInformation();
}

function handleLoadingState() {
  setStatusLabel("Loading");
}

function handleLoadedState() {
    
  //added by craig@jucra.com on 9/8/2023
  const loadingOverlay = document.querySelector('#loading-overlay');
  loadingOverlay.style.display = 'none'; // Hide the loading overlay when the iframe is loaded
    
  setStatusLabel("Loaded");    
  showVariantInformation();
  showLockCameraButton();
  showUnlockCameraButton();
  showResetCameraButton();
  showARButton();
  showSteps();
  showCloseStepButton();
  showPlayStepsButton();
  showPauseStepsButton();
  showPreviousStepButton();
  showNextStepButton();
  showOpenQRButton();
  showCloseQRButton();
  showHelpPanelButton();
  showCloseHelpPanelButton();
}

function listenToViewerState() {
  visao.listenToViewerStatus(function (status) {
      
    if (status === viewerAPI.ViewerStatus.MOUNTED) {
      hanldeMountedState();
    
    }

    if (status === viewerAPI.ViewerStatus.LOADING) {
      handleLoadingState();
        
    }

    if (status === viewerAPI.ViewerStatus.LOADED) {
      handleLoadedState();
        
    }
  });
    
}

/*
function initializeVisaoViewer() {
  viewerAPI = window.VisaoAPI;
  console.log("INIT", viewerAPI);
  visao = new viewerAPI.Visao("visao-viewer-id");
  listenToViewerState();
}

if (document.readyState === "complete") {
  console.log("complete", document.getElementById("visao-viewer-id"));
  initializeVisaoViewer();
} else {
  document.onreadystatechange = function () {
    if (document.readyState === "complete") {
      console.log(
        "ready state callback",
        document.getElementById("visao-viewer-id")
      );
      initializeVisaoViewer();
    }
  };
}
*/


 
function initializeVisaoViewer() {
  try {
    viewerAPI = window.VisaoAPI;
    if (!viewerAPI) {
      console.log("Visao ViewerAPI is not available");
      //throw new Error("Visao ViewerAPI is not available");
    }
    console.log("INIT", viewerAPI);
    visao = new viewerAPI.Visao("visao-viewer-id");
    if (!visao) {
      console.log("Failed to initialize Visao");
      //throw new Error("Failed to initialize Visao");
    }
    listenToViewerState();
  } catch (error) {
    console.error("Error in initializeVisaoViewer:", error);
  }
}

if (document.readyState === "complete") {
  console.log("complete", document.getElementById("visao-viewer-id"));
  initializeVisaoViewer();
} else {
  document.onreadystatechange = function () {
    if (document.readyState === "complete") {
      console.log(
        "ready state callback",
        document.getElementById("visao-viewer-id")
      );
      initializeVisaoViewer();
    }
  };
}


