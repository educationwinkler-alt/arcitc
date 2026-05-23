/*jslint browser this:true long:true */
/*global window */

/**
 * Search
 *
 * @version         1.0.0
 */

(function () {
    "use strict";

    const searchForms = document.querySelectorAll(".js-search");

    /**
     * Fetch Results
     */
    function searchFetchResults(form, input, results, types, taxonomies) {

        const formAjaxURL = form.getAttribute("data-ajax");

        const searchInputValue = input.value ?? "";
        const searchTypeValue = types.value ?? "";
        const searchTaxonomyValue = taxonomies.value ?? "";

        /**
         * Form Data
         * @type {FormData}
         */
        const formData = new FormData(form);
        formData.append("action", "search_processing");
        formData.append("keyword", searchInputValue);
        formData.append("post_type", searchTypeValue);
        formData.append("post_taxonomy", searchTaxonomyValue);
        formData.append("f-form--submitted", "true");

        /**
         * Search Request
         * @type {XMLHttpRequest}
         */
        const searchRequest = new XMLHttpRequest();
        searchRequest.open("POST", formAjaxURL, true);
        // Set headers
        searchRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        // Activate loading
        results.classList.add("loading");

        // Response
        searchRequest.onreadystatechange = function () {

            // Deactivate loading
            results.classList.remove("loading");

            if (searchRequest.readyState === 4 && searchRequest.status === 200) {
                // Successful
                results.innerHTML = this.responseText;
            } else {
                // Unsuccessful
                console.log("Unsuccessful fetching search results.");
            }
        };

        // Error
        searchRequest.onerror = function () {
            console.log("Error when fetching search results.");
            console.log(searchRequest.response);
        };

        // Send
        searchRequest.send(formData);

    }

    /**
     * Destroy Results
     */
    function searchDestroyResults(results) {
        results.innerHTML = "";
    }

    searchForms.forEach(function (searchForm) {

        const searchInput = searchForm.querySelector("input[type=search]");
        const searchResults = searchForm.querySelector(".js-search__results");
        const searchClear = searchForm.querySelector(".js-search__clear");

        const searchTypes = searchForm.querySelector("[name='post_type']");
        const searchTaxonomies = searchForm.querySelector("[name='post_taxonomy']");

        if (searchInput) {
            // Disable autocomplete
            searchInput.setAttribute("autocomplete", "off");

            // Search
            searchInput.addEventListener("keyup", function () {
                if (searchInput.value !== 0 && searchInput.value.length > 1) {
                    searchFetchResults(searchForm, searchInput, searchResults, searchTypes, searchTaxonomies);
                }
                if (searchInput.value === 0 || searchInput.value.length < 2) {
                    searchDestroyResults(searchResults);
                }
            });
        }

        // Clear button
        if (searchClear) {
            searchClear.addEventListener("click", function (event) {
                event.preventDefault();
                searchInput.value = "";
                searchDestroyResults(searchResults);
            });
        }
    });

}());