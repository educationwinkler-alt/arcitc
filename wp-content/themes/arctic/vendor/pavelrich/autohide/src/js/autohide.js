/*jslint browser this:true long:true */
/*global global, window, define, module */

/**
 * Auto-hide
 *
 * @description     A component that auto-hides an element on scroll.
 * @version         1.0.0
 *
 * @author          Pavel Richter <pavelrich@gmail.com>
 * @link            https://pavelrichter.cz/
 */

(function (root, factory) {
    if (typeof define === "function" && define.amd) {
        define([], factory(root)); // AMD (Asynchronous Module Definition) - RequireJS
    } else if (typeof exports === "object") {
        module.exports = factory(root); // CommonJS (Node.js)
    } else {
        root.Autohide = factory(root); // Browser globals
    }
})(typeof global !== "undefined" ? global : this.window || this.global, function () {

    /**
     * Autohide
     *
     * @param options
     */
    window.Autohide = function (options) {
        "use strict";

        // Start
        let start;
        // Scroll position
        let scrollPositionCurrent = document.scrollingElement.scrollTop;

        /**
         * Defaults
         *
         * @type {{classHidden: string, classOnTop: string, selector: string, classVisible: string}}
         */
        const defaults = {
            selector: ".js-autohide",
            selectorZoneHide: ".js-autohide--hide",
            selectorZoneShow: ".js-autohide--show",
            class: "is-autohide",
            classHidden: "is-autohide--hidden",
            classVisible: "is-autohide--visible",
            classOnTop: "is-autohide--on-top",
            classZoneHidden: "is-autohide--zone-hidden",
            hideByScroll: false, // Hide element by scroll
        };

        /**
         * Settings
         *
         * @type {any}
         */
        const settings = Object.assign({}, defaults, options); // Merge 'options' with 'defaults'

        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        /**
         * Handle Focusable Elements
         *
         * @param element
         * @param isHidden
         */
        const handleFocus = function (element, isHidden) {
            const focusableElements = element.querySelectorAll(
                "a, button, input, select, textarea, [tabindex]:not([tabindex='-1'])"
            );
            focusableElements.forEach(focusableEl => {
                if (isHidden) {
                    focusableEl.setAttribute("tabindex", "-1");
                } else {
                    focusableEl.removeAttribute("tabindex");
                }
            });

            // Remove active focus
            if (!isHidden && document.activeElement.closest(settings.selector)) {
                document.activeElement.blur();
            }
        }

        /**
         * Detect Collision of Autohide Over Zone
         *
         * @param element
         * @param zone
         * @returns {boolean}
         */
        const detectCollision = function (element, zone) {
            const elementRect = element.getBoundingClientRect();
            const zoneRect = zone.getBoundingClientRect();
            const offset = elementRect.height;

            return (
                elementRect.bottom + offset >= zoneRect.top &&
                elementRect.top <= zoneRect.bottom
            );
        }

        /**
         * Autohide Visible
         *
         * @param element
         */
        const autohideVisible = function (element) {
            element.classList.add(settings.classVisible);
            element.classList.remove(settings.classOnTop);
            element.classList.remove(settings.classHidden);

            element.setAttribute("aria-hidden", "false");
            handleFocus(element, false);
        }

        /**
         * Autohide Hidden
         *
         * @param element
         */
        const autohideHidden = function (element) {
            element.classList.add(settings.classHidden);
            element.classList.remove(settings.classOnTop);
            element.classList.remove(settings.classVisible);

            element.setAttribute("aria-hidden", "true");
            handleFocus(element, true);
        }

        /**
         * Autohide on Top
         *
         * @param element
         */
        const autohideOnTop = function (element) {
            element.classList.add(settings.classOnTop);
            element.classList.remove(settings.classVisible);
            element.classList.remove(settings.classHidden);

            element.setAttribute("aria-hidden", "false");
            handleFocus(element, false);
        }

        /**
         * Autohide Reset
         *
         * @param element
         */
        const autohideReset = function (element) {
            element.classList.remove(settings.classOnTop);
            element.classList.remove(settings.classVisible);
            element.classList.remove(settings.classHidden);

            element.setAttribute("aria-hidden", "false");
            handleFocus(element, false);
        }

        /**
         * Init
         *
         * @type {NodeListOf<Element>}
         */
        const autohides = document.querySelectorAll(settings.selector);

        autohides.forEach(function (autohide) {

            let scrollPosition = 0;

            // Position
            const positionTop = 0;
            const positionBottom = autohide.clientHeight;

            // console.log("positionTop: " + positionTop);
            // console.log("positionBottom: " + positionBottom);

            // Start
            if (autohide.getAttribute("data-autohide-start")) {
                if (autohide.getAttribute("data-autohide-start") === "top") {
                    start = positionTop;
                } else if (autohide.getAttribute("data-autohide-start") === "bottom") {
                    start = positionBottom;
                } else {
                    start = autohide.getAttribute("data-autohide-start");
                }
            } else {
                // Default
                start = positionBottom;
            }

            // Hide by scroll
            const hideByScroll = autohide.getAttribute("data-autohide-by-scroll") === "true" || settings.hideByScroll;

            // Add event
            window.addEventListener("scroll", debounce(function () {
                onScroll(autohide, start, hideByScroll);
            }, 20));

            /**
             * On Scroll
             *
             * @param element
             * @param start
             * @param hideByScroll
             */
            const onScroll = function (element, start, hideByScroll) {

                const elementHeight = element.clientHeight;

                // Position - current
                scrollPositionCurrent = window.scrollY;

                // console.log("start: " + start);
                // console.log("scrollPosition: " + scrollPosition);
                // console.log("scrollPositionCurrent: " + scrollPositionCurrent);

                /**
                 * Auto-hide Active
                 */
                element.classList.add(settings.class);

                if (scrollPositionCurrent > start) {
                    // element.classList.add(settings.classVisible);
                    // element.setAttribute("aria-hidden", "false");
                    autohideVisible(element);

                    // console.log("scrollPosition: " + scrollPosition);
                    // console.log("scrollPositionCurrent: " + scrollPositionCurrent);

                    if (scrollPositionCurrent < scrollPosition) {
                        // Scrolling ↑ - Visible
                        // console.log("Scrolling ↑");
                        autohideVisible(element);
                    } else if (scrollPositionCurrent > scrollPosition) {
                        // Scrolling ↓ - Hidden
                        // console.log("Scrolling ↓");
                        autohideHidden(element);
                    }
                } else if (scrollPositionCurrent === 0) {
                    // On top
                    autohideOnTop(element);
                } else {
                    // Reset
                    autohideReset(element);
                }

                /**
                 * Hide by Hide Zone
                 */
                const hideZones = document.querySelectorAll(settings.selectorZoneHide);
                hideZones.forEach(function (hideZone) {
                    if (detectCollision(element, hideZone)) {
                        autohideHidden(element);
                        element.classList.add(settings.classZoneHidden);
                    } else {
                        element.classList.remove(settings.classZoneHidden);
                    }
                });

                /**
                 * Show by Show Zone
                 */
                const showZones = document.querySelectorAll(settings.selectorZoneHide);
                showZones.forEach(function (showZone) {
                    if (detectCollision(element, showZone)) {
                        autohideHidden(element);
                    }
                });

                // Hide by scroll
                if (hideByScroll) {

                    if (scrollPositionCurrent > scrollPosition) {
                        // Scrolling ↓
                        if (scrollPositionCurrent >= elementHeight) {
                            // Scroll position bigger or equal to element height - completely hidden
                            element.style.setProperty("--translate-y", "-100%");
                            element.style.transitionDuration = null;
                        } else {
                            element.style.setProperty("--translate-y", (-scrollPositionCurrent) + "px");
                            element.style.transitionDuration = "0s";
                        }
                    } else {
                        // Scrolling ↑
                        element.style.removeProperty("--translate-y");
                        element.style.transitionDuration = null;
                    }
                }

                scrollPosition = scrollPositionCurrent;

            }

        });

    }

    /**
     * Init Default Instance
     */
    Autohide();

    // Export
    if (typeof module !== "undefined" && typeof module.exports !== "undefined") {
        module.exports = Autohide;
    }

    return Autohide;

});