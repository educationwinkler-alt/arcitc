/*jslint browser */
/*global window */

/**
 * Consent
 *
 * @version 1.0.1
 */

(function () {
    "use strict";

    /**
     * GTag
     *
     * @url https://developers.google.com/tag-platform/gtagjs/reference
     */
    function gtag() {
        window.dataLayer.push(arguments);
    }

    /**
     * Update Google Consent V2
     *
     * @url https://developers.google.com/tag-platform/security/guides/consent?consentmode=basic
     */
    function updateConsentAndPush(event, eventCategory, eventAction, eventLabel) {
        window.dataLayer = window.dataLayer || [];

        gtag("consent", "update", {
            ad_storage: "granted",
            ad_user_data: "granted",
            ad_personalization: "granted",
            analytics_storage: "granted",
            functionality_storage: "granted",
            personalization_storage: "granted",
        });

        // Push to dataLayer
        window.dataLayer.push({
            event: event,
            eventCategory: eventCategory,
            eventAction: eventAction,
            eventLabel: eventLabel,
        });
    }

    /**
     * Handle Consent
     */
    document.addEventListener("DOMContentLoaded", function () {

        /**
         * Update Consent on Click Accept Button
         */
        const buttonsSelectors = [
            "button.cky-btn-accept", // CookieYes banner & preferences (modal) accept button
            // "button.cky-banner-btn-close" // CookieYes banner close button
        ];
        const buttons = document.querySelectorAll(buttonsSelectors.join(', '));

        buttons.forEach(function (button) {
            button.addEventListener("click", function () {

                localStorage.setItem("consentGranted", "true"); // Save to localStorage
                updateConsentAndPush("update_consent", "consent", "cookies", button.innerText);
            });
        });

        /**
         * Update Consent Based on Previous Consent Saved in localStorage
         */
        if (localStorage.getItem("consentGranted") === "true") {
            updateConsentAndPush("update_consent", "consent", "cookies", "localStorage");
        }

    });

}());