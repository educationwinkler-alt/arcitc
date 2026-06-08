(function () {
  'use strict';

  function initHomepageMobileSlider() {
    if (!document.body.classList.contains('template--homepage')) {
      return;
    }

    var slider = document.querySelector('.template--homepage .js-slides');
    var wrapper = slider ? slider.querySelector('.f-slides__wrapper') : null;

    if (!slider || !wrapper) {
      return;
    }

    var lastTransform = '';
    var lastDuration = '';
    var scheduled = false;

    function syncWrapperTransform() {
      scheduled = false;

      var transform = wrapper.style.transform && wrapper.style.transform !== 'none'
        ? wrapper.style.transform
        : 'translate3d(0, 0, 0)';
      var duration = wrapper.style.transitionDuration || '0ms';

      if (transform !== lastTransform) {
        wrapper.style.setProperty('--arctic-home-slider-transform', transform);
        lastTransform = transform;
      }

      if (duration !== lastDuration) {
        wrapper.style.setProperty('--arctic-home-slider-transition-duration', duration);
        lastDuration = duration;
      }
    }

    function scheduleSync() {
      if (scheduled) {
        return;
      }

      scheduled = true;
      window.requestAnimationFrame(syncWrapperTransform);
    }

    scheduleSync();

    if ('MutationObserver' in window) {
      var observer = new MutationObserver(scheduleSync);
      observer.observe(wrapper, {
        attributes: true,
        attributeFilter: ['style'],
      });
    }

    slider.addEventListener('transitionrun', scheduleSync, true);
    slider.addEventListener('transitionstart', scheduleSync, true);
    slider.addEventListener('transitionend', scheduleSync, true);
    window.addEventListener('resize', scheduleSync, { passive: true });
    window.setInterval(scheduleSync, 750);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHomepageMobileSlider);
  } else {
    initHomepageMobileSlider();
  }
})();
