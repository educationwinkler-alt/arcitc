(function () {
  function initCarousel(carousel) {
    var track = carousel.querySelector('.js-about-team-carousel__track');
    var prev = carousel.querySelector('.js-about-team-carousel__prev');
    var next = carousel.querySelector('.js-about-team-carousel__next');

    if (!track || !prev || !next) {
      return;
    }

    function getStep() {
      var firstCard = track.querySelector('.f-about-person');
      var gap = parseFloat(window.getComputedStyle(track).columnGap || window.getComputedStyle(track).gap || '0') || 0;

      return firstCard ? firstCard.getBoundingClientRect().width + gap : track.clientWidth;
    }

    function setButtonState(button, isHidden) {
      button.hidden = isHidden;
      button.setAttribute('aria-disabled', isHidden ? 'true' : 'false');
      button.tabIndex = isHidden ? -1 : 0;
    }

    function updateControls() {
      var maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      var isAtStart = track.scrollLeft <= 4;
      var isAtEnd = track.scrollLeft >= maxScroll - 4;
      var hasOverflow = maxScroll > 4;

      setButtonState(prev, !hasOverflow || isAtStart);
      setButtonState(next, !hasOverflow || isAtEnd);
    }

    function scrollByStep(direction) {
      var maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      var target = Math.max(0, Math.min(maxScroll, track.scrollLeft + (getStep() * direction)));

      track.scrollTo({
        left: target,
        behavior: 'smooth'
      });

      window.setTimeout(updateControls, 260);
    }

    var ticking = false;

    track.addEventListener('scroll', function () {
      if (ticking) {
        return;
      }

      ticking = true;
      window.requestAnimationFrame(function () {
        updateControls();
        ticking = false;
      });
    }, { passive: true });

    prev.addEventListener('click', function () {
      scrollByStep(-1);
    });

    next.addEventListener('click', function () {
      scrollByStep(1);
    });

    window.addEventListener('resize', updateControls);
    updateControls();
  }

  function initCareerAccordion(container) {
    var details = Array.prototype.slice.call(container.querySelectorAll('.f-about-job'));
    var expandableDetails = details.filter(function (item) {
      return item.tagName.toLowerCase() === 'details' && item.querySelector('.f-about-job__content');
    });

    expandableDetails.forEach(function (item) {
      item.open = false;

      item.addEventListener('toggle', function () {
        if (!item.open) {
          return;
        }

        expandableDetails.forEach(function (other) {
          if (other !== item) {
            other.open = false;
          }
        });
      });
    });
  }

  function init() {
    document.querySelectorAll('.js-about-team-carousel').forEach(initCarousel);
    document.querySelectorAll('.f-about-figma__jobs').forEach(initCareerAccordion);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
