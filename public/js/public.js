(() => {
  const body = document.body;
  const page = document.getElementById('page') || body;
  const masthead = document.querySelector('.masthead');
  const mobileHeader = document.querySelector('.dt-mobile-header');
  const openBtn = document.querySelector('.dt-mobile-menu-icon');
  const closeBtn = document.querySelector('.dt-close-mobile-menu-icon');
  const primaryMenu = document.getElementById('primary-menu');
  const mobileMenu = document.getElementById('mobile-menu');
  const footerMenu = document.getElementById('bottom-menu');
  const desktopMq = window.matchMedia('(min-width: 993px)');

  function openMobileMenu(event) {
    event?.preventDefault();
    page.classList.add('show-mobile-header');
    body.classList.add('show-mobile-header');
    mobileHeader?.setAttribute('aria-hidden', 'false');
  }

  function closeMobileMenu(event) {
    event?.preventDefault();
    page.classList.remove('show-mobile-header');
    body.classList.remove('show-mobile-header');
    mobileHeader?.setAttribute('aria-hidden', 'true');
  }

  openBtn?.addEventListener('click', openMobileMenu);
  closeBtn?.addEventListener('click', closeMobileMenu);

  // Sticky / phantom header
  function stickyOffset() {
    return masthead?.offsetHeight || 60;
  }

  function updateSticky() {
    if (!masthead) {
      return;
    }
    if (window.scrollY >= stickyOffset()) {
      body.classList.add('sticky-on', 'phantom-on');
      masthead.classList.add('sticky-on', 'phantom-on');
    } else {
      body.classList.remove('sticky-on', 'phantom-on');
      masthead.classList.remove('sticky-on', 'phantom-on');
    }
  }

  window.addEventListener('scroll', updateSticky, { passive: true });
  updateSticky();

  // Scroll to top
  const scrollTopBtn = document.querySelector('.scroll-top');
  const scrollTopThreshold = 200;

  function updateScrollTop() {
    if (!scrollTopBtn) {
      return;
    }
    if (window.scrollY > scrollTopThreshold) {
      scrollTopBtn.classList.add('on');
      scrollTopBtn.classList.remove('off');
    } else {
      scrollTopBtn.classList.add('off');
      scrollTopBtn.classList.remove('on');
    }
  }

  scrollTopBtn?.addEventListener('click', (event) => {
    event.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  window.addEventListener('scroll', updateScrollTop, { passive: true });
  updateScrollTop();

  // WPBakery full-width rows (no VC runtime) — stretch to viewport
  function layoutVcFullWidth() {
    const pageEl = document.getElementById('page') || document.documentElement;
    const pageWidth = pageEl.getBoundingClientRect().width || window.innerWidth;
    const offset = Math.max(0, Math.round((pageWidth - (document.querySelector('.wf-wrap')?.getBoundingClientRect().width || pageWidth)) / 2));

    document.querySelectorAll('[data-vc-full-width="true"]').forEach((row) => {
      const width = Math.round(pageWidth);
      const left = -offset;
      row.style.position = 'relative';
      row.style.boxSizing = 'border-box';
      row.style.width = `${width}px`;
      row.style.left = `${left}px`;
      row.style.paddingLeft = `${offset}px`;
      row.style.paddingRight = `${offset}px`;
      row.setAttribute('data-vc-full-width-init', 'true');

      row.querySelectorAll('.upb_row_bg[data-bg-override="ex-full"]').forEach((bg) => {
        bg.style.minWidth = `${width}px`;
        bg.style.width = `${width}px`;
        bg.style.left = `${left}px`;
      });
    });

    document.querySelectorAll('.upb_row_bg[data-bg-override="ex-full"]').forEach((bg) => {
      if (bg.closest('[data-vc-full-width="true"]')) {
        return;
      }
      const width = Math.round(pageWidth);
      bg.style.minWidth = `${width}px`;
      bg.style.width = `${width}px`;
      bg.style.left = `${-offset}px`;
    });
  }

  layoutVcFullWidth();
  window.addEventListener('resize', layoutVcFullWidth, { passive: true });

  function normalizePath(path) {
    if (!path) {
      return '/';
    }
    try {
      const url = new URL(path, window.location.origin);
      path = url.pathname;
    } catch {
      // keep as-is for relative paths
    }
    if (path.length > 1 && path.endsWith('/')) {
      return path.slice(0, -1);
    }
    return path || '/';
  }

  function clearMenuActiveState(menu) {
    if (!menu) {
      return;
    }
    menu.querySelectorAll('.act, .current-menu-item, .current-menu-ancestor, .current-menu-parent, .current_page_item').forEach((el) => {
      el.classList.remove(
        'act',
        'current-menu-item',
        'current-menu-ancestor',
        'current-menu-parent',
        'current_page_item',
      );
    });
  }

  function markActiveByUrl(...menus) {
    const current = normalizePath(window.location.pathname);
    if (current === '/') {
      menus.forEach(clearMenuActiveState);
      return;
    }

    menus.forEach((menu) => {
      if (!menu) {
        return;
      }
      clearMenuActiveState(menu);

      const links = menu.querySelectorAll('a[href]');
      let match = null;

      links.forEach((link) => {
        const href = link.getAttribute('href');
        if (!href || href === '#' || href.startsWith('#')) {
          return;
        }
        if (normalizePath(href) === current) {
          match = link;
        }
      });

      if (!match) {
        return;
      }

      const item = match.closest('li');
      if (!item) {
        return;
      }

      item.classList.add('act', 'current-menu-item');

      let parent = item.parentElement?.closest('li.has-children, li.menu-item-has-children');
      while (parent && menu.contains(parent)) {
        parent.classList.add('act', 'current-menu-ancestor');
        parent = parent.parentElement?.closest('li.has-children, li.menu-item-has-children');
      }
    });
  }

  markActiveByUrl(primaryMenu, mobileMenu, footerMenu);

  const desktopMenus = [primaryMenu, footerMenu].filter(Boolean);

  function closeAllDesktopDropdowns() {
    desktopMenus.forEach((menu) => {
      menu.querySelectorAll('li.dt-hovered').forEach((item) => {
        item.classList.remove('dt-hovered');
      });
    });
  }

  function initDesktopDropdowns(menu) {
    if (!menu) {
      return;
    }

    const items = menu.querySelectorAll('li.has-children, li.menu-item-has-children');

    items.forEach((item) => {
      const link = item.querySelector(':scope > a');
      const sub = item.querySelector(':scope > .sub-nav, :scope > .footer-sub-nav');
      if (!link || !sub) {
        return;
      }

      item.addEventListener('mouseenter', () => {
        if (!desktopMq.matches) {
          return;
        }
        item.classList.add('dt-hovered');
      });

      item.addEventListener('mouseleave', () => {
        if (!desktopMq.matches) {
          return;
        }
        item.classList.remove('dt-hovered');
      });

      link.addEventListener('click', (event) => {
        const href = link.getAttribute('href') || '';
        const isHash = !href || href === '#' || href.startsWith('#');

        if (!desktopMq.matches) {
          return;
        }

        // Hash parents (Калькулятор, Ещё): toggle only
        if (isHash) {
          event.preventDefault();
          const wasOpen = item.classList.contains('dt-hovered');
          // Close siblings at the same level
          item.parentElement?.querySelectorAll(':scope > li.dt-hovered').forEach((sibling) => {
            if (sibling !== item) {
              sibling.classList.remove('dt-hovered');
            }
          });
          item.classList.toggle('dt-hovered', !wasOpen);
          return;
        }

        // Touch / coarse pointer: first tap opens, second navigates
        if (window.matchMedia('(hover: none)').matches) {
          if (!item.classList.contains('dt-hovered')) {
            event.preventDefault();
            item.parentElement?.querySelectorAll(':scope > li.dt-hovered').forEach((sibling) => {
              if (sibling !== item) {
                sibling.classList.remove('dt-hovered');
              }
            });
            item.classList.add('dt-hovered');
          }
        }
      });
    });
  }

  desktopMenus.forEach(initDesktopDropdowns);

  document.addEventListener('click', (event) => {
    if (!desktopMq.matches) {
      return;
    }
    const insideMenu = desktopMenus.some((menu) => menu.contains(event.target));
    if (!insideMenu) {
      closeAllDesktopDropdowns();
    }
  });

  document.querySelectorAll('#bottom-bar .menu-select select').forEach((select) => {
    select.addEventListener('change', () => {
      const value = (select.value || '').trim();
      if (!value || value === '#' || value.startsWith('#')) {
        select.selectedIndex = 0;
        return;
      }
      window.location.href = value;
    });
  });

  // Touch / click accordion for nested mobile menu items
  mobileHeader?.querySelectorAll('.menu-item-has-children > a, .has-children > a').forEach((link) => {
    link.addEventListener('click', (event) => {
      const item = link.parentElement;
      if (!item || desktopMq.matches) {
        return;
      }
      const sub = item.querySelector(':scope > .sub-nav');
      if (!sub) {
        return;
      }
      event.preventDefault();
      item.classList.toggle('act');
      item.classList.toggle('open-sub');
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeMobileMenu();
      closeAllDesktopDropdowns();
    }
  });

  function formatHomepageRate(value, decimals = 0) {
    if (value === null || value === undefined || value === '') {
      return '—';
    }
    const num = Number(value);
    if (Number.isNaN(num)) {
      return '—';
    }
    return num.toLocaleString('ru-RU', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    });
  }

  function renderHomepageRates(root, rates) {
    const list = root.querySelector('[data-homepage-rates-list]');
    if (!list || !Array.isArray(rates)) {
      return;
    }

    list.innerHTML = rates
      .map((item) => {
        const code = String(item.code || '').toLowerCase();
        const diff = item.cbu_diff;
        const diffClass =
          diff !== null && diff !== undefined && Number(diff) >= 0
            ? 'shift-green'
            : 'shift-red';
        const buy = item.best_buy;
        const sell = item.best_sell;
        const buyLink = buy
          ? `<a id="link_buy_${code}" href="/kurs-obmena-valyut/${code}" class="currency-bank-link">${buy.bank_name}</a>`
          : '<span class="currency-bank-link">—</span>';
        const sellLink = sell
          ? `<a id="link_sale_${code}" href="/kurs-obmena-valyut/${code}" class="currency-bank-link">${sell.bank_name}</a>`
          : '<span class="currency-bank-link">—</span>';
        const diffHtml =
          diff !== null && diff !== undefined
            ? `<span class="currency-shift ${diffClass}">${formatHomepageRate(diff, 2)}</span>`
            : '';

        return `
          <div class="currency-row" data-currency-code="${code}">
            <div class="currency-col">
              <div class="currency-header">Валюта</div>
              <div class="img-block">
                <span class="img-currency" aria-hidden="true" style="font-size:22px;line-height:1;">${item.flag || ''}</span>
                <div class="currency-name">${item.name_ru || ''}</div>
              </div>
            </div>
            <div class="currency-col">
              <div class="currency-header">Официальный курс</div>
              <div class="official-row">
                <div>1 ${item.code || ''} = </div>
                <div>
                  <span class="currency-value of-curs-value">${formatHomepageRate(item.cbu_rate, 2)}</span><span> сум</span>
                  ${diffHtml}
                </div>
              </div>
            </div>
            <div class="value-row">
              <div class="currency-col-value left">
                <div class="currency-header">Лучшая покупка</div>
                <div class="currency-row-value">
                  <span id="buy_${code}" class="currency-value">${formatHomepageRate(buy?.rate, 0)}</span><span>&nbsp;сум</span>
                </div>
                ${buyLink}
              </div>
              <div class="currency-col-value left">
                <div class="currency-header">Лучшая продажа</div>
                <div class="currency-row-value">
                  <span id="sale_${code}" class="currency-value">${formatHomepageRate(sell?.rate, 0)}</span><span>&nbsp;сум</span>
                </div>
                ${sellLink}
              </div>
            </div>
          </div>
        `;
      })
      .join('');
  }

  function initHomepageRates() {
    const root = document.querySelector('[data-homepage-rates]');
    if (!root) {
      return;
    }

    let byPlace = {};
    try {
      byPlace = JSON.parse(root.dataset.ratesByPlace || '{}');
    } catch {
      byPlace = {};
    }

    root.querySelectorAll('.place-btn[data-place]').forEach((btn) => {
      btn.addEventListener('click', () => {
        root.querySelectorAll('.place-btn').forEach((el) => el.classList.remove('active'));
        btn.classList.add('active');
        const place = btn.dataset.place;
        renderHomepageRates(root, byPlace[place] || []);
      });
    });
  }

  function initPostsCarousel() {
    const root = document.querySelector('[data-posts-carousel]');
    if (!root) {
      return;
    }

    const tabs = root.querySelectorAll('[data-posts-tab]');
    const panels = root.querySelectorAll('[data-posts-panel]');

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const key = tab.getAttribute('data-posts-tab');
        tabs.forEach((el) => el.classList.remove('active'));
        tab.classList.add('active');
        panels.forEach((panel) => {
          panel.classList.toggle(
            'is-active',
            panel.getAttribute('data-posts-panel') === key,
          );
        });
      });
    });
  }

  initHomepageRates();
  initPostsCarousel();
})();
