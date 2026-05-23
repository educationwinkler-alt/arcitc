/*jslint browser this:true long:true */
/*global global, window, define, module */

/**
 * Off
 *
 * @description     An interactive component that expands/collapses an off-canvas panel.
 * @version         1.1.0
 *
 * @author          Pavel Richter <pavelrich@gmail.com>
 * @link            https://pavelrichter.cz/
 */

(function (root, factory) {
    if (typeof define === "function" && define.amd) {
        define([], factory(root));
    } else if (typeof exports === "object") {
        module.exports = factory(root);
    } else {
        root.Off = factory(root);
    }
})(typeof global !== "undefined" ? global : this.window || this.global, function (root) {

    "use strict";

    /**
     * Defaults
     */
    const defaults = {
        name: "off",
        breakpoint: "1024",
        selector: ".js-off",
        selectorContainer: ".js-off__container", // Container around the off to relocate
        selectorTrigger: ".js-off__trigger", // Trigger to open the off
        selectorCloser: ".js-off__close", // Closer to close the off
        selectorLocation: ".js-off__location", // Location of the relocated off element
        selectorFocusables: "a[href]:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), button:not([disabled])", // Focusable elements within the off
        transitionDuration: 500 // ms
    };

    /**
     * Variables
     */
    const supports = !!document.querySelector && !!root.addEventListener; // Support Check

    // Body
    const body = document.getElementsByTagName("body")[0];

    // Settings
    let settings;
    // Off
    let isOffOpen = false;
    let activeOffs = [];
    // Touch
    let touchstartX = 0;
    let touchstartY = 0;


    /**
     * Extend
     * @private
     *
     * @param {Object} defaults
     * @param {Object} options
     *
     * @returns {Object}
     */
    const extend = function (defaults, options) {
        const extended = {};

        for (const dflt in defaults) {
            if (defaults.hasOwnProperty(dflt)) {
                extended[dflt] = defaults[dflt];
            }
        }

        for (const optn in options) {
            if (options.hasOwnProperty(optn)) {
                extended[optn] = options[optn];
            }
        }

        return extended;
    };

    /**
     * Get Focusable Elements
     * @private
     *
     * @param element
     * @param focusables
     *
     * @returns {*[]}
     */
    const getFocusableElements = function (element, focusables) {
        return [...element.querySelectorAll(focusables)].filter(el => !el.hasAttribute("disabled") && !el.getAttribute("aria-hidden"));
    }

    /**
     * Focus Trap
     * @private
     *
     * @param element
     * @param {Object} settings
     */
    const focusTrap = function (element, settings) {

        // All focusable elements within element
        const focusables = getFocusableElements(element, settings.selectorFocusables);
        // First/Last focusable elements
        const focusableFirst = focusables[0];
        const focusableLast = focusables[focusables.length - 1];

        // Focus the first element immediately
        focusableFirst.focus();

        element.addEventListener("keydown", function (event) {

            if (!(event.key === "Tab" || event.keyCode === 9)) {
                return;
            }

            if (event.shiftKey) { // Shift + Tab
                if (document.activeElement === focusableFirst) {
                    focusableLast.focus();
                    event.preventDefault();
                }
            } else { // Tab
                if (document.activeElement === focusableLast) {
                    focusableFirst.focus();
                    event.preventDefault();
                }
            }
        });
    }

    /**
     * Relocate
     * @private
     *
     * @param element
     * @param container
     * @param location
     * @param {Object} settings
     */
    const relocate = function (element, container, location, settings) {

        if (!element) return;

        const viewportWidth = window.innerWidth;
        const viewportBreakpoint = element.getAttribute("data-" + settings.name + "-breakpoint") ? element.getAttribute("data-" + settings.name + "-breakpoint") : settings.breakpoint;

        if (viewportWidth < viewportBreakpoint || viewportBreakpoint === "all" || body.classList.contains(settings.name + "-all--enabled")) {

            element.classList.add("initialized");
            // Set 'inert' attribute if inert active
            if (element.hasAttribute("data-" + settings.name + "-inert")) {
                element.setAttribute("inert", "");
            }

            /**
             * Relocate to the Target Location If Not Disabled by Attribute 'data-off-relocate=false'
             */
            if (location && !element.hasAttribute("data-" + settings.name + "-relocate")) {
                location.insertAdjacentElement("afterbegin", element);
                element.classList.add("relocated");
            }
        } else {

            element.classList.remove("initialized");
            // Set 'inert' attribute if inert active
            if (element.hasAttribute("data-" + settings.name + "-inert")) {
                element.setAttribute("inert", "");
            }

            /**
             * Append Back to the Original Container
             */
            if (container && !element.hasAttribute("data-" + settings.name + "-relocate")) {
                container.appendChild(element);
                element.classList.remove("relocated");
            }
        }
    }

    /**
     * Open
     * @private
     *
     * @param trigger
     * @param element
     * @param {Object} settings
     */
    const open = function (trigger, element, settings) {
        isOffOpen = true;

        if (!element) return;

        const offID = element.getAttribute("data-" + settings.name);

        // TRIGGER
        // Add 'expanded' class to the trigger
        trigger.classList.add("expanded");
        // Change 'aria-expanded' attribute to 'true'
        trigger.setAttribute("aria-expanded", "true");

        // CLOSER
        const closer = element.querySelector(settings.selectorCloser);
        const closersAll = document.querySelectorAll(settings.selectorCloser); // Unassigned closers like overlay
        // Remove 'tabindex' attribute of the closer to enable access by keyboard
        if (closer) {
            closer.removeAttribute("tabindex");
        }
        if (closersAll.length) {
            closersAll.forEach(function (closer) {
                if (!closer.hasAttribute("data-" + settings.name)) {
                    closer.removeAttribute("tabindex");
                    closer.setAttribute("aria-hidden", "false");
                }
            });
        }

        // OFF
        // Add current off to "activeOffs" array
        if (!activeOffs.includes(element)) {
            activeOffs.push(element);
        }
        // Add 'active' class to the off
        element.classList.add("active");
        // Remove 'inert' attribute if inert active
        if (element.hasAttribute("data-" + settings.name + "-inert")) {
            element.removeAttribute("inert");
        }
        // Change 'aria-hidden' attribute to 'false' if is set
        element.setAttribute("aria-hidden", "false");

        // BODY
        // Add 'active' class to the <body>
        body.classList.add(settings.name + "-active", settings.name + "-active--" + offID);

        // Add hash
        let hash = element.getAttribute("data-" + settings.name + "-hash");
        if (hash) {
            hash = hash.trim().toLowerCase().replace(/[^a-z0-9-_]/g, ''); // hash check (allowed letters, numbers, hyphens, and underscores)
            history.pushState(null, document.title, "#" + hash);
        }

        // FOCUSER
        if (isOffOpen) {
            // Focus trap within opened off
            focusTrap(element, settings);
        }
    }

    /**
     * Close
     * @private
     *
     * @param elements              off(s) to close
     * @param {Object} settings     settings
     * @param focus                 focus back to trigger
     */
    const close = function (elements, settings, focus = true) {
        isOffOpen = false;

        if (!elements) return;
        if (!elements.forEach) {
            elements = [elements];
        }

        elements.forEach(element => {

            const offID = element.getAttribute("data-" + settings.name);
            const offTransition = parseInt(element.getAttribute("data-" + settings.name + "-duration"), 10); // ms

            // TRIGGER
            // Remove 'expanded' class of the trigger
            const offTriggersExpanded = document.querySelectorAll(settings.selectorTrigger + "[data-" + settings.name + "=" + offID + "].expanded");

            // CLOSER
            const closer = element.querySelector(settings.selectorCloser);
            const closersAll = document.querySelectorAll(settings.selectorCloser); // Unassigned closers like overlay
            // Set 'tabindex' attribute of the closer to disable assess by keyboard
            if (closer) {
                closer.setAttribute("tabindex", "-1");
            }
            if (closersAll.length) {
                closersAll.forEach(function (closer) {
                    if (!closer.hasAttribute("data-" + settings.name)) {
                        closer.setAttribute("tabindex", "-1");
                        closer.setAttribute("aria-hidden", "true");
                    }
                });
            }

            offTriggersExpanded.forEach(function (triggerExpanded) {

                // Remove 'expanded' class on trigger
                triggerExpanded.classList.remove("expanded");
                // Change 'aria-expanded' attribute to 'false'
                triggerExpanded.setAttribute("aria-expanded", "false");

                // Focus back to trigger
                if (focus) {
                    window.requestAnimationFrame(() => {
                        Promise.resolve(1).then(function resolve() {
                            triggerExpanded.focus();
                        });
                    });
                }
            });

            // OFF
            // Remove current off from "activeOffs" array
            const index = activeOffs.indexOf(element);
            if (index !== -1) {
                activeOffs.splice(index, 1);
            }
            // Closing
            if (element.classList.contains("active")) {
                element.classList.add("closing");
                body.classList.add(settings.name + "-closing", settings.name + "-closing--" + offID);

                // Set 'inert' attribute if inert active
                if (element.hasAttribute("data-" + settings.name + "-inert")) {
                    element.setAttribute("inert", "");
                }
                // Change 'aria-hidden' attribute to 'true'
                element.setAttribute("aria-hidden", "true");

                setTimeout(function () {
                    element.classList.remove("closing", "active");

                    // BODY
                    // Remove 'off-active' class of the <body> with 'off-closing' delay
                    if (body.classList.contains(settings.name + "-active--" + offID)) {
                        const offsActive = document.querySelectorAll(settings.selector + ".active");

                        body.classList.remove(settings.name + "-closing", settings.name + "-closing--" + offID);
                        body.classList.remove(settings.name + "-active--" + offID);

                        if (!offsActive.length || !activeOffs.length) {
                            body.classList.remove(settings.name + "-active");
                        }
                    }

                }, offTransition && !isNaN(offTransition) ? offTransition : parseInt(settings.transitionDuration, 10));
            }

            // Remove hash
            if (element.hasAttribute("data-" + settings.name + "-hash")) {
                history.replaceState(null, document.title, window.location.pathname + window.location.search);
            }
        });
    }

    /**
     * Initialize
     * @public
     *
     * @param {Object} options
     */
    const Off = function (options = {}) {

        // Feature check
        if (!supports) return;

        /**
         * Settings
         * @type {any}
         */
        settings = extend(defaults, options);

        /**
         * Off
         */
        const offs = document.querySelectorAll(settings.selector);
        const offsByHash = document.querySelectorAll(settings.selector + "[data-" + settings.name + "-hash]");
        const offTriggers = document.querySelectorAll(settings.selectorTrigger);
        const offClosers = document.querySelectorAll(settings.selectorCloser);

        offs.forEach(function (off) {

            const container = off.closest(settings.selectorContainer);
            const location = document.querySelectorAll(settings.selectorLocation)[0] ? document.querySelectorAll(settings.selectorLocation)[0] : body;

            const linksAnchor = off.querySelectorAll("a[href*='#']");
            const linksAnchorEmpty = off.querySelectorAll("a[href='#']");

            /**
             * Off Relocation
             */
            relocate(off, container, location, settings);
            // Relocate off on resize
            window.addEventListener("resize", function () {
                relocate(off, container, location, settings);
            });

            /**
             * Close Off on Click to Any Anchor Link
             */
            linksAnchor.forEach(function (linkAnchor) {

                linkAnchor.addEventListener("click", function (event) {
                    const offToClose = this.closest(settings.selector);
                    const offToCloseAnchors = offToClose ? (offToClose.getAttribute("data-" + settings.name + "-anchors") ? offToClose.getAttribute("data-" + settings.name + "-anchors") === "true" : false) : true;

                    if (offToCloseAnchors) {
                        if (linkAnchor.getAttribute("href") !== "#") {
                            close(offToClose, settings, false);
                        }
                    } else {
                        event.preventDefault();

                        const anchor = linkAnchor.getAttribute("href").substring(1);
                        const anchorElement = document.getElementById(anchor);

                        // Scroll to element
                        if (anchorElement) {
                            anchorElement.scrollIntoView({
                                behavior: "smooth",
                                block: "start",
                            });
                        }
                    }
                });
            });

            /**
             * Prevent Default on Empty Anchor Link
             */
            linksAnchorEmpty.forEach(function (linkAnchorEmpty) {

                linkAnchorEmpty.addEventListener("click", function (event) {
                    event.preventDefault();
                });
            });

            /**
             * Close Off on Swipe
             */
            off.addEventListener("touchstart", function (event) {
                touchstartX = event.changedTouches[0].screenX;
                touchstartY = event.changedTouches[0].screenY;
            }, {passive: true});

            off.addEventListener("touchend", function (event) {

                const diffX = event.changedTouches[0].screenX - touchstartX;
                const diffY = event.changedTouches[0].screenY - touchstartY;

                const ratioX = Math.abs(diffX / diffY);
                const ratioY = Math.abs(diffY / diffX);

                // Off
                const offID = this.getAttribute("data-" + settings.name);
                const offsToClose = document.querySelectorAll(settings.selector + "[data-" + settings.name + "=" + offID + "]");

                offsToClose.forEach(function (off) {

                    const offPosition = off.getAttribute("data-" + settings.name + "-position");

                    if (ratioX > ratioY) {

                        // Position on right
                        if (offPosition === "right" && diffX >= 0) {
                            close(off, settings);
                        }

                        // Position on left
                        if (offPosition === "left" && diffX <= 0) {
                            close(off, settings);
                        }
                    }

                    // Y Direction // COLLISION WITH SCROLLING!!! --- DISABLED
                    // if (ratioY > ratioX) {

                    // Position on top
                    // if (offPosition === "top" && diffY <= 0) {
                    //     close(off, settings);
                    // }

                    // Position on bottom
                    // if (offPosition === "bottom" && diffY >= 0) {
                    //     close(off, settings);
                    // }
                    // }
                });
            }, {passive: true});
        });

        /**
         * Open Off by Hash in an URL
         */
        for (const offByHash of offsByHash) {
            const offByHashName = offByHash.getAttribute("data-" + settings.name);
            const trigger = document.querySelector(`${settings.selectorTrigger}[data-${settings.name}="${offByHashName}"]`);
            const hash = window.location.hash.substring(1);

            if (trigger && hash && offByHash.getAttribute("data-off-hash") === hash) {
                open(trigger, offByHash, settings);
                break;
            }
        }

        /**
         * Open Off on Click to Trigger
         */
        offTriggers.forEach(function (offTrigger) {

            const offID = offTrigger.getAttribute("data-" + settings.name);
            const offToOpen = document.querySelector(settings.selector + "[data-" + settings.name + "=" + offID + "]");

            // Find clicked trigger in a document - due to event delegation of cloned elements like cloned header
            document.addEventListener("click", function (event) {

                if (event.target && event.target.matches(settings.selectorTrigger + "[data-" + settings.name + "=" + offID + "]")) {
                    event.preventDefault();

                    open(event.target, offToOpen, settings);
                }
            });

        });

        /**
         * Close Off on Click to Closer
         */
        offClosers.forEach(function (offCloser) {

            // Close
            offCloser.addEventListener("click", function (event) {
                event.preventDefault();

                const offID = offCloser.getAttribute("data-" + settings.name);
                const offFocus = offCloser ? (offCloser.getAttribute("data-" + settings.name + "-focus") ? offCloser.getAttribute("data-" + settings.name + "-focus") === "true" : false) : true;
                const offToClose = document.querySelector(settings.selector + "[data-" + settings.name + "=" + offID + "]");

                if (offToClose) {
                    close(offToClose, settings, offFocus);
                } else {
                    const offsToClose = document.querySelectorAll(settings.selector + ".active");
                    close(offsToClose, settings, offFocus);
                }
            });

        });

        /**
         * Close Last Off on Escape Key
         */
        document.addEventListener("keydown", function (event) {

            const key = event.key;
            const offLastActive = activeOffs.pop();
            const offLastActiveFocus = offLastActive ? (offLastActive.getAttribute("data-" + settings.name + "-focus") ? offLastActive.getAttribute("data-" + settings.name + "-focus") === "true" : false) : true;

            if (key === "Escape" || key === "Esc") {
                close(offLastActive, settings, offLastActiveFocus);
            }
        });
    }

    /**
     * Init Default Instance
     */
    document.addEventListener("DOMContentLoaded", function () {
        new Off();
    });

    return Off;

});