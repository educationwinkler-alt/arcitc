(function () {
  'use strict';

  var widgetSelector = '#smartsupp-widget-container [data-testid="widgetButtonFrame"], #smartsupp-widget-container [data-testid="widgetMessengerFrame"]';
  var promoSelector = '.template--homepage .f-section--slides > .f-hero-promo, .template--homepage .f-hero-promo';
  var baseOffset = 24;
  var edgeOffset = 22;
  var promoGap = 16;
  var forceAboveScroll = 80;
  var frameHandle = 0;

  function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
  }

  function isVisible(element) {
    if (!element) {
      return false;
    }

    var rect = element.getBoundingClientRect();
    var style = window.getComputedStyle(element);

    return rect.width > 1 && rect.height > 1 && style.display !== 'none' && style.visibility !== 'hidden';
  }

  function getPromo() {
    var promo = document.querySelector(promoSelector);

    return isVisible(promo) ? promo : null;
  }

  function getWidgetHeight(widget) {
    var rect = widget.getBoundingClientRect();
    var styleHeight = parseFloat(window.getComputedStyle(widget).height);

    return rect.height || styleHeight || 56;
  }

  function getTargetBottom(widget, promo) {
    if (!promo || !document.body.classList.contains('template--homepage')) {
      return baseOffset;
    }

    var promoRect = promo.getBoundingClientRect();
    var widgetHeight = getWidgetHeight(widget);
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    var viewportWidth = window.innerWidth || document.documentElement.clientWidth || 0;
    var baseTop = viewportHeight - baseOffset - widgetHeight;
    var promoInViewport = promoRect.bottom > 0 && promoRect.top < viewportHeight;
    var promoNearWidgetColumn = promoRect.right > viewportWidth - 520;
    var wouldCollideAtBase = promoInViewport && promoNearWidgetColumn && promoRect.bottom > baseTop - promoGap && promoRect.top < viewportHeight - baseOffset;
    var keepAboveAtTop = window.scrollY <= forceAboveScroll && promoInViewport && promoNearWidgetColumn;

    if (!wouldCollideAtBase && !keepAboveAtTop) {
      return baseOffset;
    }

    return clamp(
      Math.ceil(viewportHeight - promoRect.top + promoGap),
      baseOffset,
      Math.max(baseOffset, viewportHeight - widgetHeight - promoGap)
    );
  }

  function adjustMessengerHeight(widget, promo, bottom) {
    if (widget.getAttribute('data-testid') !== 'widgetMessengerFrame') {
      return;
    }

    if (!widget.dataset.arcticSmartsuppMaxHeight) {
      widget.dataset.arcticSmartsuppMaxHeight = widget.style.maxHeight || '';
    }

    if (!promo || bottom <= baseOffset) {
      if (widget.dataset.arcticSmartsuppMaxHeight) {
        widget.style.setProperty('max-height', widget.dataset.arcticSmartsuppMaxHeight);
      } else {
        widget.style.removeProperty('max-height');
      }
      return;
    }

    var promoRect = promo.getBoundingClientRect();
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    var topGap = 16;
    var availableAbovePromo = Math.floor(promoRect.top - promoGap - topGap);
    var availableInViewport = Math.floor(viewportHeight - bottom - topGap);
    var targetHeight = Math.max(320, Math.min(availableAbovePromo, availableInViewport));

    widget.style.setProperty('max-height', targetHeight + 'px', 'important');
  }

  function prepareTransition(widget) {
    if (widget.dataset.arcticSmartsuppOffsetReady) {
      return;
    }

    widget.dataset.arcticSmartsuppOffsetReady = 'true';
    widget.style.transition = widget.style.transition
      ? widget.style.transition + ', bottom 240ms ease, right 240ms ease'
      : 'bottom 240ms ease, right 240ms ease';
  }

  function applyOffset() {
    frameHandle = 0;

    var promo = getPromo();
    var widgets = document.querySelectorAll(widgetSelector);

    widgets.forEach(function (widget) {
      var bottom = getTargetBottom(widget, promo);
      var bottomValue = bottom + 'px';
      var rightValue = edgeOffset + 'px';

      prepareTransition(widget);

      if (widget.dataset.arcticSmartsuppBottom !== bottomValue) {
        widget.style.setProperty('bottom', bottomValue, 'important');
        widget.dataset.arcticSmartsuppBottom = bottomValue;
      }

      if (widget.dataset.arcticSmartsuppRight !== rightValue) {
        widget.style.setProperty('right', rightValue, 'important');
        widget.dataset.arcticSmartsuppRight = rightValue;
      }

      adjustMessengerHeight(widget, promo, bottom);
    });
  }

  function scheduleApply() {
    if (frameHandle) {
      return;
    }

    frameHandle = window.requestAnimationFrame(applyOffset);
  }

  window.addEventListener('scroll', scheduleApply, { passive: true });
  window.addEventListener('resize', scheduleApply);
  window.addEventListener('orientationchange', scheduleApply);

  if (document.body) {
    new MutationObserver(scheduleApply).observe(document.body, {
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ['style', 'data-testid'],
    });
  }

  scheduleApply();
  window.setTimeout(scheduleApply, 600);
  window.setTimeout(scheduleApply, 1800);
  window.setTimeout(scheduleApply, 4200);
})();
