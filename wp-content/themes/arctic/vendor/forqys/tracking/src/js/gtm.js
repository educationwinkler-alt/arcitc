/*jslint browser */
/*global window */

/**
 * GTM
 *
 * @version 1.0.2
 */

document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    // console.log('✅ dataLayer is available: ', window.dataLayer);

    /**
     * Push to dataLayer
     *
     * @param event             "interaction", "submission", "purchase", ...
     * @param eventCategory     "click", "form", "video", ...
     * @param eventAction       "email", "phone", "social", ...
     * @param eventLabel        content of the event or URL
     */
    function dataLayerPush(event, eventCategory, eventAction, eventLabel) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: event,
            eventCategory: eventCategory,
            eventAction: eventAction,
            eventLabel: eventLabel,
        });
    }

    /**
     * Links
     * =====
     */
    const linksPhone = document.querySelectorAll("a[href^='tel:']");
    const linksEmail = document.querySelectorAll("a[href^='mailto:']");
    const linksX = document.querySelectorAll("a[href*='x.com']");
    const linksFacebook = document.querySelectorAll("a[href*='facebook.com']");
    const linksInstagram = document.querySelectorAll("a[href*='instagram.com']");
    const linksYouTube = document.querySelectorAll("a[href*='youtube.com']");
    const linksPDF = document.querySelectorAll("a[href$='.pdf']");

    // Click phone link
    linksPhone.forEach(function (linkPhone) {
        linkPhone.addEventListener("click", function (event) {
            // Push to dataLayer
            dataLayerPush("interaction", "click", "phone", linkPhone.innerText);
        });
    });

    // Click email link
    linksEmail.forEach(function (linkEmail) {
        linkEmail.addEventListener("click", function (event) {
            // Push to dataLayer
            dataLayerPush("interaction", "click", "email", linkEmail.innerText);
        });
    });

    // Click X link
    linksX.forEach(function (linkX) {
        linkX.addEventListener("click", function () {
            // Push to dataLayer
            dataLayerPush("interaction", "click", "social", linkX.getAttribute("href"));
        });
    });

    // Click Facebook link
    linksFacebook.forEach(function (linkFacebook) {
        linkFacebook.addEventListener("click", function () {
            // Push to dataLayer
            dataLayerPush("interaction", "click", "social", linkFacebook.getAttribute("href"));
        });
    });

    // Click Instagram link
    linksInstagram.forEach(function (linkInstagram) {
        linkInstagram.addEventListener("click", function () {
            // Push to dataLayer
            dataLayerPush("interaction", "click", "social", linkInstagram.getAttribute("href"));
        });
    });

    // Click YouTube link
    linksYouTube.forEach(function (linkYouTube) {
        linkYouTube.addEventListener("click", function () {
            // Push to dataLayer
            dataLayerPush("interaction", "click", "social", linkYouTube.getAttribute("href"));
        });
    });

    // Click PDF link
    linksPDF.forEach(function (linkPDF) {
        linkPDF.addEventListener("click", function (event) {
            // Push to dataLayer
            dataLayerPush("interaction", "click", "pdf", linkPDF.getAttribute("href"));
        });
    });


    /**
     * Forms
     * =====
     */
    const forms = document.querySelectorAll(".js-form");
    forms.forEach(function (form) {
        form.addEventListener("submit", function () {

            const formNameInput = form.querySelector("input[name='f-form']");
            const formName = formNameInput ? formNameInput.value : "contact";

            // Push to dataLayer
            dataLayerPush("submission", "form", formName + "Form", gtm_param.permalink);
        }, false);
    });

});