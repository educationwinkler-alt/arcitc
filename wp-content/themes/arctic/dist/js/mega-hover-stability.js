(() => {
  const DESKTOP_MEDIA_QUERY = '(min-width: 1400px)';
  const CLOSE_DELAY_MS = 220;

  const STATE_CLASSES = {
    active: 'is-mega-active',
    hotTubs: 'is-mega-hot-tubs',
    swimspa: 'is-mega-swimspa',
  };

  const clearState = (header, body) => {
    header.classList.remove(STATE_CLASSES.active, STATE_CLASSES.hotTubs, STATE_CLASSES.swimspa);
    if (body) {
      body.classList.remove(STATE_CLASSES.active);
    }
  };

  const stateClassForKey = (key) => (
    key === 'hot-tubs' ? STATE_CLASSES.hotTubs : STATE_CLASSES.swimspa
  );

  const initMegaHoverStability = () => {
    const header = document.querySelector('.f-header');
    const body = document.body;
    if (!header || header.dataset.megaHoverStability === 'ready') {
      return;
    }

    const hotTrigger = header.querySelector('.f-navigation__list > .arctic-menu-products:nth-child(1)');
    const swimspaTrigger = header.querySelector('.f-navigation__list > .arctic-menu-products:nth-child(2)');
    const hotMenu = header.querySelector('.f-mega-menu--hot-tubs');
    const swimspaMenu = header.querySelector('.f-mega-menu--swimspa');

    if (!hotTrigger || !swimspaTrigger || !hotMenu || !swimspaMenu) {
      return;
    }

    header.dataset.megaHoverStability = 'ready';

    const media = window.matchMedia(DESKTOP_MEDIA_QUERY);
    let closeTimer = null;

    const cancelClose = () => {
      if (closeTimer !== null) {
        window.clearTimeout(closeTimer);
        closeTimer = null;
      }
    };

    const activate = (key) => {
      if (!media.matches) {
        return;
      }

      cancelClose();
      clearState(header, body);
      header.classList.add(STATE_CLASSES.active, stateClassForKey(key));
      body.classList.add(STATE_CLASSES.active);
    };

    const hasActiveFocus = () => {
      const active = document.activeElement;
      return !!(active && header.contains(active));
    };

    const hasActiveHover = () => (
      hotTrigger.matches(':hover')
      || swimspaTrigger.matches(':hover')
      || hotMenu.matches(':hover')
      || swimspaMenu.matches(':hover')
    );

    const closeIfInactive = () => {
      if (!media.matches) {
        clearState(header, body);
        return;
      }

      if (hasActiveHover() || hasActiveFocus()) {
        return;
      }

      clearState(header, body);
    };

    const scheduleClose = () => {
      cancelClose();
      closeTimer = window.setTimeout(closeIfInactive, CLOSE_DELAY_MS);
    };

    const registerPair = (trigger, panel, key) => {
      trigger.addEventListener('mouseenter', () => activate(key));
      panel.addEventListener('mouseenter', () => activate(key));
      trigger.addEventListener('mouseleave', scheduleClose);
      panel.addEventListener('mouseleave', scheduleClose);
      trigger.addEventListener('focusin', () => activate(key));
      panel.addEventListener('focusin', () => activate(key));
    };

    registerPair(hotTrigger, hotMenu, 'hot-tubs');
    registerPair(swimspaTrigger, swimspaMenu, 'swimspa');

    header.addEventListener('focusout', scheduleClose);
    header.addEventListener('mouseleave', scheduleClose);
    header.addEventListener('mouseenter', cancelClose);

    const handleModeChange = () => {
      cancelClose();
      if (!media.matches) {
        clearState(header, body);
      }
    };

    if (typeof media.addEventListener === 'function') {
      media.addEventListener('change', handleModeChange);
    } else if (typeof media.addListener === 'function') {
      media.addListener(handleModeChange);
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMegaHoverStability, { once: true });
    return;
  }

  initMegaHoverStability();
})();
