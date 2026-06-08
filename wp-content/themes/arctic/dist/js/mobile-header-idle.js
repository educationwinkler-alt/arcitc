(function () {
  'use strict';

  var header = document.querySelector('.f-header');
  var mobileQuery = window.matchMedia('(max-width: 1399px)');
  var idleTimer = null;
  var idleDelay = 220;
  var focusableSelector = 'a, button, input, select, textarea, [tabindex]';

  if (!header) {
    return;
  }

  function restoreHeaderAccess() {
    header.classList.remove('is-autohide--hidden');
    header.classList.add('is-autohide--visible');
    header.setAttribute('aria-hidden', 'false');

    header.querySelectorAll(focusableSelector).forEach(function (element) {
      if (element.getAttribute('tabindex') === '-1') {
        element.removeAttribute('tabindex');
      }
    });
  }

  function clearMobileState() {
    window.clearTimeout(idleTimer);
    header.classList.remove('is-mobile-scroll-active');
    header.classList.add('is-mobile-scroll-idle');
  }

  function resetHeader() {
    window.clearTimeout(idleTimer);
    header.classList.remove('is-mobile-scroll-active');
    header.classList.remove('is-mobile-scroll-idle');
  }

  function showHeader() {
    clearMobileState();
    restoreHeaderAccess();
  }

  function hideHeader() {
    if (!mobileQuery.matches) {
      resetHeader();
      return;
    }

    if (document.body.classList.contains('off-active--navigation')) {
      showHeader();
      return;
    }

    header.classList.add('is-mobile-scroll-active');
    header.classList.remove('is-mobile-scroll-idle');
  }

  function handleScroll() {
    if (!mobileQuery.matches) {
      resetHeader();
      return;
    }

    if (window.scrollY <= 1) {
      showHeader();
      return;
    }

    hideHeader();
    window.clearTimeout(idleTimer);
    idleTimer = window.setTimeout(showHeader, idleDelay);
  }

  function handleMediaChange() {
    if (mobileQuery.matches) {
      showHeader();
    } else {
      resetHeader();
    }
  }

  window.addEventListener('scroll', handleScroll, { passive: true });

  if (mobileQuery.addEventListener) {
    mobileQuery.addEventListener('change', handleMediaChange);
  } else if (mobileQuery.addListener) {
    mobileQuery.addListener(handleMediaChange);
  }

  if (mobileQuery.matches) {
    showHeader();
  }
})();
