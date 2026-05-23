/*jslint browser this:true long:true */
/*global window, jQuery, PhotoSwipe, PhotoSwipeUI_Default */

/**
 * Images
 *
 * @version     1.0.3
 * @requires    jQuery, PhotoSwipe, PhotoSwipeUI_Default
 */

(function ($) {
    "use strict";

    // Selectors
    const pswp = $(".js-pswp");
    const pswpContainerSelector = ".js-images, .js-pswp__items";
    const pswpImageSelector = ".js-image:not([target='_blank']), .js-pswp__item:not([target='_blank'])";
    const pswpImage = $(pswpImageSelector);

    /**
     * forqyGalleryGetImages
     * @param gallery
     * @param el
     * @returns {*[]}
     */
    function forqyGalleryGetImages(gallery, el) {

        let galleryItem;
        let index;

        const elements = $(gallery).find(pswpImageSelector);
        const galleryItems = [];

        elements.each(function (i) {
            const $el = $(this);
            const size = $el.data("size").split("x");
            const $title = $el.parent("figure").find("figcaption").html();

            if ($el.data("type") === "embed") {

                const galleryItemHTML = "<div class='pswp__container pswp__container--video'><div class='pswp__item pswp__item--video'><iframe src='" + $el.data("video") + "' width='" + parseInt(size[0], 10) + "' height='" + parseInt(size[1], 10) + "' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share' allowfullscreen></iframe></div></div>"

                // Video
                galleryItem = {
                    html: galleryItemHTML,
                    title: $title,
                };
            } else {

                // Image
                galleryItem = {
                    src: $el.attr("href"),
                    w: parseInt(size[0], 10),
                    h: parseInt(size[1], 10),
                    title: $title,
                    el: $el
                };
            }

            galleryItems.push(galleryItem);

            if (el === $el.get(0)) {
                index = i;
            }
        });

        return [galleryItems, parseInt(index, 10)];

    }

    /**
     * forqyOpenPhotoSwipe
     * @param element
     */
    function forqyOpenPhotoSwipe(element) {

        let options;
        let items;
        let index;

        const pswpEl = pswp.get(0);
        const galleryEl = $(element).parents(pswpContainerSelector).first();

        items = forqyGalleryGetImages(galleryEl, element);
        index = items[1];
        items = items[0];

        // PhotoSwipe Options
        options = {
            index: index,
            loop: true,
            focus: true,
            bgOpacity: 1,
            shareEl: false,
            closeOnScroll: false,
            closeOnVerticalDrag: false,
            pinchToClose: false,
            showHideOpacity: true,
            showAnimationDuration: 300,
            hideAnimationDuration: 300,
            preload: true,
            history: false,
            loadingIndicatorDelay: 0,
            barsSize: {
                top: 60,
                bottom: "auto"
            }
        };

        // Pass Data to PhotoSwipe, and Initialize It
        const gallery = new PhotoSwipe(pswpEl, PhotoSwipeUI_Default, items, options);
        gallery.init();

    }

    /**
     * Open PhotoSwipe
     */
    pswpImage.on("click", function (e) {
        e.preventDefault();

        forqyOpenPhotoSwipe(this);
    });

}(jQuery));