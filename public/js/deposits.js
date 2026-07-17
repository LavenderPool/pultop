(function () {
  const form = document.querySelector('[data-deposits-filter]');
  const list = document.querySelector('[data-deposits-list]');
  const loadMoreWrap = document.querySelector('[data-deposits-load-more]');
  const loadMoreBtn = document.querySelector('[data-deposits-load-more-btn]');

  if (!form || !list) {
    return;
  }

  const endpoint = form.getAttribute('action') || '/api/deposits';
  let page = Number(list.getAttribute('data-page') || '1') || 1;
  let hasMore = list.getAttribute('data-has-more') === '1';
  let loading = false;
  let debounceTimer = null;

  function collectFilters() {
    const data = new FormData(form);
    const params = new URLSearchParams();

    for (const [key, value] of data.entries()) {
      if (value === '' || value == null) {
        continue;
      }
      params.set(key, String(value));
    }

    return params;
  }

  function setLoading(state) {
    loading = state;
    form.classList.toggle('is-loading', state);
    if (loadMoreBtn) {
      loadMoreBtn.disabled = state;
    }
  }

  function updateLoadMore(nextHasMore) {
    hasMore = Boolean(nextHasMore);
    list.setAttribute('data-has-more', hasMore ? '1' : '0');
    if (loadMoreWrap) {
      loadMoreWrap.hidden = !hasMore;
    }
  }

  async function fetchPage(targetPage, { append }) {
    if (loading) {
      return;
    }

    setLoading(true);

    const params = collectFilters();
    params.set('page', String(targetPage));

    try {
      const response = await fetch(`${endpoint}?${params.toString()}`, {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const payload = await response.json();
      const emptyNode = list.querySelector('[data-deposits-empty]');

      if (!append) {
        list.innerHTML = payload.html || '';
        if (!payload.html) {
          list.innerHTML =
            '<p class="deposits-empty" data-deposits-empty>Вклады не найдены.</p>';
        }
      } else if (payload.html) {
        if (emptyNode) {
          emptyNode.remove();
        }
        list.insertAdjacentHTML('beforeend', payload.html);
      }

      page = Number(payload.page || targetPage) || targetPage;
      list.setAttribute('data-page', String(page));
      updateLoadMore(payload.has_more);
    } catch (error) {
      console.error('deposits filter failed', error);
    } finally {
      setLoading(false);
    }
  }

  function reloadFromFilters() {
    page = 1;
    fetchPage(1, { append: false });
  }

  function scheduleReload() {
    window.clearTimeout(debounceTimer);
    debounceTimer = window.setTimeout(reloadFromFilters, 250);
  }

  form.addEventListener('change', scheduleReload);
  form.addEventListener('input', (event) => {
    const target = event.target;
    if (target && target.name === 'summa') {
      scheduleReload();
    }
  });
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    reloadFromFilters();
  });

  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
      if (!hasMore || loading) {
        return;
      }
      fetchPage(page + 1, { append: true });
    });
  }

  updateLoadMore(hasMore);
})();
