/*jslint browser */
/*global window, LazyLoad */

/**
 * Lazy
 *
 * @version     1.0.2
 * @requires    LazyLoad
 * @url         https://github.com/verlok/vanilla-lazyload
 */

(function () {
    "use strict";

    const forqyImageLazy = new LazyLoad({
        elements_selector: ".js-lazy",
        class_loading: "lazy-loading",
        class_loaded: "lazy-loaded",
        class_error: "lazy-error",
        class_entered: "lazy-entered",
        class_exited: "lazy-exited",
        restore_on_error: true,
        use_native: false,

        callback_loaded: function (el) {
            const parent = el.parentNode;
            parent.classList.add("lazy-loaded");
        }
    });

}());