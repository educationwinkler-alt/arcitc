(function () {
  'use strict';

  var HEADER_SELECTOR = '.f-header';
  var HANDOFF_SELECTOR = '.js-section-nav-handoff';
  var NAV_SELECTOR = '.js-links__navigation';
  var SECTION_SELECTOR = '.js-links__section[id]';
  var HANDOFF_CLASS = 'is-section-nav-handoff';
  var ACTIVE_OFFSET = 340;
  var HANDOFF_TOLERANCE = 2;

  function ready(callback) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback, { once: true });
      return;
    }

    callback();
  }

  function getTarget(link) {
    var href = link.getAttribute('href') || '';

    if (href.charAt(0) !== '#') {
      return null;
    }

    try {
      return document.getElementById(decodeURIComponent(href.slice(1)));
    } catch (error) {
      return document.getElementById(href.slice(1));
    }
  }

  function getStickyTop(element) {
    var top = window.getComputedStyle(element).top;
    var parsed = parseFloat(top);

    return Number.isFinite(parsed) ? parsed : 0;
  }

  ready(function () {
    var header = document.querySelector(HEADER_SELECTOR);
    var handoffs = Array.prototype.slice.call(document.querySelectorAll(HANDOFF_SELECTOR));
    var navigations = Array.prototype.slice.call(document.querySelectorAll(NAV_SELECTOR));
    var sections = Array.prototype.slice.call(document.querySelectorAll(SECTION_SELECTOR));
    var ticking = false;

    if (!handoffs.length && !navigations.length) {
      return;
    }

    function updateActiveLinks() {
      if (!sections.length || !navigations.length) {
        return;
      }

      navigations.forEach(function (navigation) {
        var links = Array.prototype.slice.call(navigation.querySelectorAll('a[href^="#"]'));
        var activeLink = null;
        var marker = window.scrollY + ACTIVE_OFFSET;

        links.forEach(function (link) {
          var target = getTarget(link);

          if (!target || sections.indexOf(target) === -1) {
            return;
          }

          var targetTop = target.getBoundingClientRect().top + window.scrollY;

          if (marker >= targetTop) {
            activeLink = link;
          }
        });

        if (!activeLink && links.length) {
          activeLink = links[0];
        }

        links.forEach(function (link) {
          var isActive = link === activeLink;

          link.classList.toggle('active', isActive);
          link.classList.toggle('is-active', isActive);
        });
      });
    }

    function updateHeaderHandoff() {
      if (!header || !handoffs.length) {
        return;
      }

      var shouldHide = handoffs.some(function (navigation) {
        var rect = navigation.getBoundingClientRect();
        var computedStyle = window.getComputedStyle(navigation);
        var stickyTop = getStickyTop(navigation);
        var documentTop = rect.top + window.scrollY;
        var reachedStickyPoint = window.scrollY >= documentTop - stickyTop - HANDOFF_TOLERANCE;
        var isAtStickyEdge = rect.top <= stickyTop + HANDOFF_TOLERANCE;
        var isStillVisible = rect.bottom > stickyTop + HANDOFF_TOLERANCE;

        if (computedStyle.position !== 'sticky') {
          return window.scrollY > 0 && reachedStickyPoint;
        }

        return window.scrollY > 0 && reachedStickyPoint && isAtStickyEdge && isStillVisible;
      });

      header.classList.toggle(HANDOFF_CLASS, shouldHide);
    }

    function update() {
      ticking = false;
      updateActiveLinks();
      updateHeaderHandoff();
    }

    function requestUpdate() {
      if (ticking) {
        return;
      }

      ticking = true;
      window.requestAnimationFrame(update);
    }

    update();
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate);
  });
}());
