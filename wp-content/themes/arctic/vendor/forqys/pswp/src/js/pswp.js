/*jslint browser this:true long:true */
/*global global, window, define, module */

/**
 * PSWP
 *
 * @description     FORQY PhotoSwipe Component.
 * @version         1.0.2
 *
 * @author          Pavel Richter <pavelrich@gmail.com>
 * @link            https://pavelrichter.cz/
 *
 * @docs            https://photoswipe.com/getting-started/
 */

import PhotoSwipeLightbox from "photoswipe/lightbox";
import PhotoSwipe from "photoswipe";

const iconArrowPrevSVG = '<svg class="icon" width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M22 6L10 18L22 30" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
const iconArrowNextSVG = '<svg class="icon" width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M14 6L26 18L14 30" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
const iconCloseSVG = '<svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 18L18 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
const iconZoomSVG = '<svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><circle cx="12" cy="12" r="7" stroke="white" stroke-width="2"/><path d="M12 9V15M9 12H15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 17L21 21" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>';

const lightboxLinks = document.querySelectorAll("a");

const lightbox = new PhotoSwipeLightbox({
    gallery: ".js-images",
    children: ".js-image",
    showHideAnimationType: "fade",
    // showAnimationDuration: 0,
    // hideAnimationDuration: 0,
    bgOpacity: 1,
    pinchToClose: false,
    closeOnVerticalDrag: false,
    zoom: false, // disable zoom button

    arrowPrevSVG: iconArrowPrevSVG,
    arrowNextSVG: iconArrowNextSVG,
    closeSVG: iconCloseSVG,
    zoomSVG: iconZoomSVG,

    closeTitle: pswp_settings.text_close,
    arrowPrevTitle: pswp_settings.text_prev,
    arrowNextTitle: pswp_settings.text_next,
    zoomTitle: pswp_settings.text_zoom,
    errorMsg: pswp_settings.text_error,
    indexIndicatorSep: pswp_settings.text_separator,

    pswpModule: PhotoSwipe,

    // Set zoom levels using data attributes
    initialZoomLevel: (zoomLevel) => {
        const el = zoomLevel.itemData.element;
        if (!el) return;

        const val = el.getAttribute("data-pswp-zoom-initial");
        if (val === null) return;
        if (val === "fit" || val === "fill") return val;
        // parse number
        const num = parseFloat(val);
        if (isNaN(num)) return;

        const { elementSize, panAreaSize } = zoomLevel;
        const expectedWidth = elementSize.x * num;
        const maxAllowedWidth = panAreaSize.x;
        // check image not wider than viewport
        if (expectedWidth > maxAllowedWidth) {
            return maxAllowedWidth / elementSize.x;
        }
        return num;
    },
    secondaryZoomLevel: (zoomLevel) => {
        const el = zoomLevel.itemData.element;
        if (!el) return;

        const val = el.getAttribute("data-pswp-zoom-secondary");
        if (val === null) return;
        if (val === "fit" || val === "fill") return val;
        // parse number
        const num = parseFloat(val);
        return !isNaN(num) ? num : undefined;
    },
    maxZoomLevel: (zoomLevel) => {
        const el = zoomLevel.itemData.element;
        if (!el) return;

        const val = el.getAttribute("data-pswp-zoom-max");
        if (val === null) return;
        if (val === "fit" || val === "fill") return val;
        // parse number
        const num = parseFloat(val);
        return !isNaN(num) ? num : undefined;
    },
});

// Placeholder
lightbox.addFilter("useContentPlaceholder", (useContentPlaceholder, content) => {
    return false;
});

// Pan - Open zoomed image from top
lightbox.on("calcBounds", ({slide}) => {
    const el = slide.data.element;
    if (el?.getAttribute("data-pswp-zoom-pan") === "top") {
        slide.bounds.center.y = slide.bounds.min.y;
    }
});

lightbox.init();
