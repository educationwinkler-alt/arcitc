/*jslint browser */
/*global window */

/**
 * Form
 *
 * @version 1.1.2
 */

document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    /**
     * Forms
     *
     * @type {NodeListOf<Element>}
     */
    const forms = document.querySelectorAll(".js-form");

    forms.forEach(function (form) {

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            // Regenerate reCAPTCHA token
            regenerateReCAPTCHAToken(form).then(() => {
                // Get form data after reCAPTCHA token regeneration
                handleFormData(form);
            });
        });
    });

    /**
     * Regenerate ReCAPTCHA Token by the Form
     *
     * @param form
     * @returns {Promise<unknown>}
     */
    function regenerateReCAPTCHAToken(form) {

        return new Promise((resolve, reject) => {  // Returns a Promise
            const formReCAPTCHAInput = form.querySelector(".js-recaptcha");
            const formReCAPTCHAKey = form.querySelector(".js-recaptcha__k");
            const formReCAPTCHADebug = form.querySelector(".js-recaptcha__debug");

            if (!formReCAPTCHAInput || !formReCAPTCHAKey || typeof grecaptcha === "undefined") {
                resolve(null);
                return;
            }

            if (formReCAPTCHADebug) {
                console.log(formReCAPTCHAKey.value);
                console.log(formReCAPTCHAInput.value);
                console.log("grecaptcha exists:", typeof grecaptcha !== "undefined");
            }

            grecaptcha.ready(() => {
                grecaptcha.execute(formReCAPTCHAKey.value, {
                    action: "form"
                }).then((token) => {
                    if (token) {
                        formReCAPTCHAInput.value = token;
                        if (formReCAPTCHADebug) {
                            console.log("reCAPTCHA token regenerated!");
                        }
                        resolve(token); // Successfully completed
                    } else {
                        console.warn("reCAPTCHA token is not available, continuing anyway.");
                        resolve(null); // Resolve without stopping form submission
                    }
                }).catch((error) => {
                    console.error("reCAPTCHA error:", error);
                    resolve(null); // Resolve even if an error occurs
                });
            });
        });
    }

    /**
     * Classic Submit
     *
     * @param element
     */
    function submitFormClassic(element) {
        const submittedInput = document.createElement("input"); // Create hidden input 'f-form--submitted' and append to form
        submittedInput.type = "hidden";
        submittedInput.name = "f-form--submitted";
        submittedInput.value = "true";
        element.appendChild(submittedInput);

        element.submit();
    }

    /**
     * Hide Loading
     *
     * @param element
     */
    function hideLoading(element) {
        element.classList.add("closing");

        // Deactivate loading
        setTimeout(() => {
            element.classList.remove("active", "closing");
        }, 400);
    }

    /**
     * Handle Form Data
     *
     * @param form
     */
    function handleFormData(form) {

        const formAjaxURL = form.getAttribute("data-ajax");
        if (!formAjaxURL) {
            console.warn("No 'data-ajax' attribute set on the '<form>' element!");
            submitFormClassic(form);
            return;
        }
        const formResponse = form.querySelector(".js-form__response");
        const formLoading = form.querySelector(".js-form__loading");

        /**
         * Form Data
         * @type {FormData}
         */
        const formData = new FormData(form);
        formData.append("action", "form_processing"); // name of the function in action 'wp_ajax_form_processing' and 'wp_ajax_nopriv_form_processing
        formData.append("f-form--submitted", "true");

        /**
         * Form Request
         * @type {XMLHttpRequest}
         */
        if (formLoading) formLoading.classList.add("active"); // Activate loading

        fetch(formAjaxURL, {
            method: "POST",
            body: formData
        }).then(async (response) => {
            const text = await response.text();
            return {
                ok: response.ok,
                status: response.status,
                text: text
            };
        }).then(({ok, status, text}) => {
            if (!ok) {
                throw new Error(`HTTP error! Status: ${status}`);
            }

            if (formResponse) {
                formResponse.innerHTML = text;
                formResponse.scrollIntoView({behavior: "smooth"});
            }

            if (ok) {
                form.reset();
            }

            // Hide loading
            hideLoading(formLoading);

        }).catch(error => {
            console.error(error);

            if (formResponse) {
                formResponse.innerHTML = error;
                formResponse.scrollIntoView({behavior: "smooth"});
            }

            if (error.message.includes("403") || error.message.includes("404")) {
                console.warn("AJAX 403 Error! Falling back to classic form submission.");
                console.log("Classic form submitting ...");

                // Hide loading
                hideLoading(formLoading);
                submitFormClassic(form); // Classic submit
            }
        });

        // const formRequest = new XMLHttpRequest();
        // formRequest.open("POST", formAjaxURL, true);
        // // Set headers
        // formRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        //
        // // Response
        // formRequest.onreadystatechange = function () {
        //
        //     if (formRequest.status === 200) {
        //
        //         // console.log(formRequest.status);
        //         // console.log(formRequest.responseText);
        //         // for (const entry of formData.entries()) {
        //         //     console.log(entry[0] + ": " + entry[1]);
        //         // }
        //
        //         if (formResponse) {
        //             // Display Response
        //             formResponse.innerHTML = formRequest.response;
        //
        //             // Scroll to Response
        //             formResponse.scrollIntoView({
        //                 behavior: "smooth"
        //             });
        //         }
        //
        //         // Reset Form
        //         form.reset();
        //
        //         // Deactivate Loading
        //         if (formLoading) {
        //             setTimeout(() => {
        //                 formLoading.classList.add("closing");
        //
        //                 setTimeout(() => {
        //                     formLoading.classList.remove("active");
        //                     formLoading.classList.remove("closing");
        //                 }, 400);
        //             }, 200);
        //         }
        //
        //     } else if (formRequest.status === 403 || formRequest.status === 404) {
        //         console.warn("AJAX 403 error detected. Falling back to classic form submission.");
        //         console.log(formRequest.response);
        //         form.submit(); // Classic submit
        //     } else {
        //         console.error("Server Error:", formRequest.statusText);
        //         // console.log(formRequest.response);
        //         form.submit(); // Classic submit
        //     }
        // };
        //
        // // Error
        // formRequest.onerror = function () {
        //     console.error("AJAX Error: Form request failed. Falling back to classic submission.");
        //     // console.log(formRequest.response);
        //     form.submit(); // Classic submit
        // };
        //
        // // Send
        // formRequest.send(formData);

    }

    /**
     * Responses
     *
     * @type {Element}
     */
    const formResponses = document.querySelectorAll(".js-form__response");

    if (formResponses) {

        formResponses.forEach(function (formResponse) {

            formResponse.addEventListener("click", function (event) {
                const clickedEl = event.target;

                // Clicked child link element, return
                if (clickedEl.closest("a")) {
                    return;
                }

                // Clicked close element
                if (clickedEl.classList.contains("js-alert__close")) {
                    event.stopPropagation();

                    const alertEl = clickedEl.closest(".js-form__alert");
                    if (alertEl) {
                        alertEl.style.display = "none";
                    }
                }
                // Intentionally do not close the whole alert on generic click.
                // This prevents accidental dismiss when user misses a link.
            });

        });

    }

});
