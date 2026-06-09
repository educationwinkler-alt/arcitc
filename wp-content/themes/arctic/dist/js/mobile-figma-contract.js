(function () {
  'use strict';

  var media = window.matchMedia('(max-width: 767px)');

  function getDirectChildren(element, selector) {
    return Array.prototype.filter.call(element.children || [], function (child) {
      return child.matches(selector);
    });
  }

  function setPanelOpen(item, trigger, panel, isOpen) {
    item.classList.toggle('is-open', isOpen);
    trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

    if (media.matches) {
      panel.hidden = !isOpen;
    } else {
      panel.hidden = false;
    }
  }

  function normalisePrimaryNavigation() {
    var list = document.querySelector('.f-off--navigation .f-navigation__list');

    if (!list) {
      return;
    }

    getDirectChildren(list, 'li.has-sub').forEach(function (item, index) {
      var trigger = item.querySelector(':scope > a');
      var panel = item.querySelector(':scope > .f-navigation-sub, :scope > .sub-menu');

      if (!trigger || !panel) {
        return;
      }

      item.classList.add('is-mobile-accordion');
      panel.id = panel.id || 'mobile-menu-panel-' + index;
      trigger.setAttribute('aria-controls', panel.id);

      if (!trigger.dataset.originalLabel) {
        trigger.dataset.originalLabel = trigger.textContent.trim();
      }

      if (trigger.dataset.originalLabel === 'Vlastnosti') {
        trigger.textContent = media.matches ? 'Vlastnosti v\u00ed\u0159ivek' : trigger.dataset.originalLabel;
      }

      var firstSubLink = panel.querySelector('.f-navigation-sub__list > li:first-child > a, .sub-menu > li:first-child > a');
      if (firstSubLink && firstSubLink.textContent.trim() === 'Vybrat podle parametr\u016f') {
        firstSubLink.closest('li').classList.toggle('is-mobile-hidden-figma', media.matches);
      }

      if (media.matches) {
        trigger.setAttribute('role', 'button');
        setPanelOpen(item, trigger, panel, index === 0);
      } else {
        trigger.removeAttribute('role');
        setPanelOpen(item, trigger, panel, true);
      }

      if (!trigger.dataset.mobileFigmaBound) {
        trigger.dataset.mobileFigmaBound = '1';
        trigger.addEventListener('click', function (event) {
          if (!media.matches) {
            return;
          }

          event.preventDefault();
          setPanelOpen(item, trigger, panel, !item.classList.contains('is-open'));
        });
      }
    });
  }

  function setFooterSectionOpen(heading, list, isOpen) {
    heading.classList.toggle('is-open', isOpen);
    heading.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

    if (media.matches) {
      list.hidden = !isOpen;
    } else {
      list.hidden = false;
    }
  }

  function normaliseFooterNavigation() {
    var footer = document.querySelector('.f-footer--arctic');

    if (!footer) {
      return;
    }

    var groups = Array.prototype.slice.call(footer.querySelectorAll('.f-footer__group'));

    groups.forEach(function (group, groupIndex) {
      group.classList.add('is-mobile-footer-accordion');

      getDirectChildren(group, 'h2').forEach(function (heading, headingIndex) {
        var list = heading.nextElementSibling && heading.nextElementSibling.matches('ul') ? heading.nextElementSibling : null;

        if (!list) {
          return;
        }

        heading.classList.add('is-mobile-footer-heading');
        list.id = list.id || 'mobile-footer-panel-' + groupIndex + '-' + headingIndex;
        heading.setAttribute('aria-controls', list.id);

        if (media.matches) {
          heading.setAttribute('role', 'button');
          heading.setAttribute('tabindex', '0');
          setFooterSectionOpen(heading, list, groupIndex === 0 && headingIndex === 0);
        } else {
          heading.removeAttribute('role');
          heading.removeAttribute('tabindex');
          setFooterSectionOpen(heading, list, true);
        }

        if (!heading.dataset.mobileFigmaBound) {
          heading.dataset.mobileFigmaBound = '1';
          heading.addEventListener('click', function () {
            if (!media.matches) {
              return;
            }

            setFooterSectionOpen(heading, list, !heading.classList.contains('is-open'));
          });

          heading.addEventListener('keydown', function (event) {
            if (!media.matches || (event.key !== 'Enter' && event.key !== ' ')) {
              return;
            }

            event.preventDefault();
            setFooterSectionOpen(heading, list, !heading.classList.contains('is-open'));
          });
        }
      });
    });
  }

  function init() {
    normalisePrimaryNavigation();
    normaliseFooterNavigation();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  if (media.addEventListener) {
    media.addEventListener('change', init);
  } else {
    media.addListener(init);
  }
})();
