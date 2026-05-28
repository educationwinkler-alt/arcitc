(() => {
  'use strict';

  const onReady = (callback) => {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback, { once: true });
      return;
    }

    callback();
  };

  const isActivateKey = (event) => event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar';

  const setFaqExpanded = (card, expanded) => {
    const panelId = card.getAttribute('aria-controls') || '';
    const panel = panelId ? document.getElementById(panelId) : card.querySelector('p');
    const icon = card.querySelector('.f-support-faq-card__icon');

    card.classList.toggle('is-open', expanded);
    card.setAttribute('aria-expanded', expanded ? 'true' : 'false');

    if (panel) {
      panel.hidden = !expanded;
    }

    if (icon) {
      icon.textContent = expanded ? '−' : '+';
    }
  };

  const initSupportFaq = (section) => {
    const cards = Array.from(section.querySelectorAll('[data-support-faq-card]'));
    const chips = Array.from(section.querySelectorAll('[data-support-filter]'));

    if (!cards.length) {
      return;
    }

    const applyFilter = (filterKey) => {
      const normalizedFilter = (filterKey || 'all').toLowerCase();
      const visibleCards = [];

      cards.forEach((card) => {
        const category = (card.getAttribute('data-support-category') || '').toLowerCase();
        const matches = normalizedFilter === 'all' || category === normalizedFilter;

        card.hidden = !matches;

        if (matches) {
          visibleCards.push(card);
        } else {
          setFaqExpanded(card, false);
        }
      });

      const openVisibleCard = visibleCards.find((card) => card.classList.contains('is-open'));

      if (!openVisibleCard && visibleCards.length) {
        setFaqExpanded(visibleCards[0], true);
      }
    };

    cards.forEach((card) => {
      const toggle = () => {
        const shouldOpen = !card.classList.contains('is-open');

        cards.forEach((otherCard) => {
          if (otherCard !== card) {
            setFaqExpanded(otherCard, false);
          }
        });

        setFaqExpanded(card, shouldOpen);
      };

      card.addEventListener('click', (event) => {
        if (event.target && event.target.closest('a, button, input, textarea, select')) {
          return;
        }

        toggle();
      });

      card.addEventListener('keydown', (event) => {
        if (!isActivateKey(event)) {
          return;
        }

        event.preventDefault();
        toggle();
      });
    });

    chips.forEach((chip) => {
      chip.addEventListener('click', () => {
        const filterKey = chip.getAttribute('data-support-filter') || 'all';

        chips.forEach((otherChip) => {
          const active = otherChip === chip;
          otherChip.classList.toggle('is-active', active);
          otherChip.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        applyFilter(filterKey);
      });
    });

    const initiallyActiveChip = chips.find((chip) => chip.classList.contains('is-active'));
    applyFilter(initiallyActiveChip ? initiallyActiveChip.getAttribute('data-support-filter') : 'all');
  };

  const setDownloadGroupExpanded = (group, expanded) => {
    const toggle = group.querySelector('[data-download-group-toggle]');
    const panelId = toggle ? toggle.getAttribute('aria-controls') : '';
    const panel = panelId ? document.getElementById(panelId) : group.querySelector('[data-download-group-panel]');
    const icon = group.querySelector('.f-download-group__icon');

    group.classList.toggle('is-open', expanded);
    if (toggle) {
      toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }

    if (panel) {
      panel.hidden = !expanded;
    }

    if (icon) {
      icon.textContent = expanded ? '−' : '+';
    }
  };

  const initDownloadGroups = (section) => {
    const groups = Array.from(section.querySelectorAll('[data-download-group]'));

    groups.forEach((group) => {
      const toggle = group.querySelector('[data-download-group-toggle]');
      const panel = group.querySelector('[data-download-group-panel]');

      if (!toggle || !panel) {
        return;
      }

      const initiallyExpanded = toggle.getAttribute('aria-expanded') === 'true' || group.classList.contains('is-open');
      setDownloadGroupExpanded(group, initiallyExpanded);

      const handleToggle = () => {
        const nextExpanded = !group.classList.contains('is-open');
        setDownloadGroupExpanded(group, nextExpanded);
      };

      toggle.addEventListener('click', handleToggle);
      toggle.addEventListener('keydown', (event) => {
        if (!isActivateKey(event)) {
          return;
        }

        event.preventDefault();
        handleToggle();
      });
    });
  };

  const initDownloadFilters = (section) => {
    const chips = Array.from(section.querySelectorAll('[data-download-filter]'));
    const groups = Array.from(section.querySelectorAll('[data-download-group]'));
    const cards = Array.from(section.querySelectorAll('[data-download-card]'));

    if (!chips.length || !cards.length) {
      return;
    }

    const applyFilter = (filterType) => {
      const normalizedFilter = (filterType || '').toLowerCase();

      cards.forEach((card) => {
        const cardType = (card.getAttribute('data-download-filter-type') || '').toLowerCase();
        card.hidden = normalizedFilter && cardType !== normalizedFilter;
      });

      groups.forEach((group) => {
        const panel = group.querySelector('[data-download-group-panel]');
        const hasVisibleCards = !!group.querySelector('[data-download-card]:not([hidden])');

        group.hidden = !hasVisibleCards;

        if (!panel || !hasVisibleCards) {
          return;
        }

        if (!group.classList.contains('is-open')) {
          panel.hidden = true;
        }
      });

      const firstVisibleGroup = groups.find((group) => !group.hidden);
      if (firstVisibleGroup && !firstVisibleGroup.classList.contains('is-open')) {
        setDownloadGroupExpanded(firstVisibleGroup, true);
      }
    };

    chips.forEach((chip) => {
      chip.addEventListener('click', () => {
        const wasActive = chip.classList.contains('is-active');
        const filterType = chip.getAttribute('data-download-filter') || '';

        chips.forEach((otherChip) => {
          otherChip.classList.remove('is-active');
          otherChip.setAttribute('aria-selected', 'false');
        });

        if (wasActive) {
          applyFilter('');
          return;
        }

        chip.classList.add('is-active');
        chip.setAttribute('aria-selected', 'true');
        applyFilter(filterType);
      });
    });

    const initiallyActiveChip = chips.find((chip) => chip.classList.contains('is-active'));
    applyFilter(initiallyActiveChip ? initiallyActiveChip.getAttribute('data-download-filter') : '');
  };

  const initContactHashStrategy = () => {
    if (!window.location.pathname.startsWith('/kontakt')) {
      return;
    }

    if (window.location.hash.toLowerCase() !== '#formular') {
      return;
    }

    const anchorTarget = document.getElementById('formular');
    if (anchorTarget) {
      anchorTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    const contactTrigger = document.querySelector('.js-off__trigger[data-off="contact"]');
    if (!contactTrigger) {
      return;
    }

    window.setTimeout(() => {
      contactTrigger.click();
    }, 160);
  };

  onReady(() => {
    document.querySelectorAll('.f-section--support-faq').forEach(initSupportFaq);
    document.querySelectorAll('.f-section--support-downloads').forEach((section) => {
      initDownloadGroups(section);
      initDownloadFilters(section);
    });

    initContactHashStrategy();
  });
})();
