(function () {
  function initCarousel(carousel) {
    var track = carousel.querySelector('.js-about-team-carousel__track');
    var next = carousel.querySelector('.js-about-team-carousel__next');

    if (!track || !next) {
      return;
    }

    next.addEventListener('click', function () {
      var firstCard = track.querySelector('.f-about-person');
      var gap = parseFloat(window.getComputedStyle(track).columnGap || window.getComputedStyle(track).gap || '0') || 0;
      var step = firstCard ? firstCard.getBoundingClientRect().width + gap : track.clientWidth;
      var isAtEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;

      track.scrollTo({
        left: isAtEnd ? 0 : track.scrollLeft + step,
        behavior: 'smooth'
      });
    });
  }

  function init() {
    document.querySelectorAll('.js-about-team-carousel').forEach(initCarousel);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
