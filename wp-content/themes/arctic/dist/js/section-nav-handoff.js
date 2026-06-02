(function () {
  'use strict';

  var HEADER_SELECTOR = '.f-header';
  var HANDOFF_SELECTOR = '.js-section-nav-handoff';
  var NAV_SELECTOR = '.js-links__navigation';
  var SECTION_SELECTOR = '.js-links__section[id]';
  var HANDOFF_CLASS = 'is-section-nav-handoff';
  var ACTIVE_OFFSET = 340;

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
          link.classList.toggle('active', link === activeLink);
        });
      });
    }

    function updateHeaderHandoff() {
      if (!header || !handoffs.length) {
        return;
      }

      var headerHeight = header.getBoundingClientRect().height || 0;
      var shouldHide = handoffs.some(function (navigation) {
        var rect = navigation.getBoundingClientRect();

        return window.scrollY > 0 && rect.top <= headerHeight && rect.bottom > 0;
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
