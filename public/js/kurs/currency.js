function formChange(event) {
  event.preventDefault();
  event.stopPropagation();

  const target = event.target;

  if (target.type === 'text') {
    clearTimeout(window.currencyTimeout);
    window.currencyTimeout = setTimeout(() => {
      fetchCurrencyData(target);
    }, 500);
    return;
  }

  fetchCurrencyData(target);
}

function formSubmit(event) {
  event.preventDefault();
  event.stopPropagation();
  return false;
}

function formatRate(value, decimals = 0) {
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

function renderRatesList(data, operation) {
  const list = document.querySelector('.rates-list');
  if (!list) {
    return;
  }

  if (!Array.isArray(data) || data.length === 0) {
    list.innerHTML =
      '<p style="padding: 16px; margin-top: 32px; color: #888;">Курсы банков пока недоступны.</p>';
    return;
  }

  const label = operation === 'buy' ? 'Курс продажи' : 'Курс покупки';

  list.innerHTML = data
    .map((item) => {
      const logo = item.logo_url
        ? `style="background-image:url(${item.logo_url})"`
        : '';
      const time = item.fetched_at ? `${item.fetched_at} 🕒` : '';
      return `
        <div class="UniSearchList-Item">
          <div class="FinanceItem FinanceItem_view_horizontal-button UniSearchDepositsItem">
            <div class="FinanceItem-Upper">
              <div class="FinanceItem-Header">
                <div class="FinanceItem-BankIcon">
                  <div class="FinanceItem-BankIconImage" ${logo}></div>
                </div>
                <div class="FinanceItem-HeaderTitleContainer">
                  <h3 class="FinanceItem-HeaderTitle">${item.bank_name}</h3>
                  <div class="FinanceItem-HeaderSubtitleContainer">
                    <div class="FinanceItem-HeaderSubtitle">${time}</div>
                  </div>
                </div>
              </div>
              <div class="FinanceItem-Body">
                <div class="FinanceItem-ProductDetails_view_horizontal FinanceItem-ProductDetails" style="display: flex;">
                  <div class="FinanceItem-ProductDetail">
                    <div class="FinanceItem-ProductDetailLabel">${label}</div>
                    <div class="FinanceItem-ProductDetailValue">${formatRate(item.rate, 0)}&nbsp;<span style="color: #888; font-weight: 100;">UZS</span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>`;
    })
    .join('');
}

function updateBestRates(best) {
  if (!best) {
    return;
  }
  document.querySelectorAll('[data-best-rates] .rate-row').forEach((row) => {
    const place = row.dataset.place;
    const item = best[place];
    if (!item) {
      return;
    }
    const buy = row.querySelector('[data-best-buy]');
    const sell = row.querySelector('[data-best-sell]');
    if (buy) {
      buy.textContent = formatRate(item.buy, 0);
    }
    if (sell) {
      sell.textContent = formatRate(item.sell, 0);
    }
  });
}

function fetchCurrencyData(element) {
  if (typeof hasScrolled !== 'undefined' && !hasScrolled) {
    return;
  }

  const form = element.closest('form') || document.getElementById('filter');
  if (!form) {
    return;
  }

  const currency = form.querySelector('input[name="currency"]')?.value;
  const selects = form.querySelectorAll('input[name="selected-value"]');
  const operation = selects[0]?.value || 'buy';
  const place = selects[1]?.value || 'cash';
  const apiUrl = window.ratesApiUrl || '/api/rates';

  const url = new URL(apiUrl, window.location.origin);
  url.searchParams.set('currency', currency);
  url.searchParams.set('operation', operation);
  url.searchParams.set('place', place);

  const params = new URLSearchParams(window.location.search);
  params.set('operation', operation);
  params.set('place', place);
  window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);

  fetch(url.toString(), {
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
  })
    .then((response) => response.json())
    .then((payload) => {
      renderRatesList(payload.data || [], operation);
      updateBestRates(payload.best);
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}

function createCurrencyChart(currencyData) {
  if (!currencyData?.dates?.length || !window.ApexCharts) {
    return;
  }

  const chartData = currencyData.dates.map(function (date, index) {
    var timestamp = new Date(date).getTime();
    return [timestamp, parseFloat(currencyData.values[index])];
  });

  const options = {
    series: [
      {
        name: `Курс ${window.currency}`,
        data: chartData,
      },
    ],
    chart: {
      type: 'line',
      height: 400,
      zoom: { enabled: false },
      toolbar: { show: true },
    },
    xaxis: { type: 'datetime' },
    yaxis: {
      title: { text: `Курс ${window.currency} ЦБ РУз` },
    },
    tooltip: {
      x: { format: 'dd MMM yyyy' },
    },
  };

  const el = document.querySelector('#cbu-chart');
  if (!el) {
    return;
  }

  const chart = new ApexCharts(el, options);
  chart.render();
  window.currentChart = chart;
}

let hasScrolled = true;

window.addEventListener(
  'scroll',
  () => {
    if (!hasScrolled) {
      hasScrolled = true;
    }
  },
  { once: true },
);

function responsiveDetails() {
  const allDetails = document.querySelectorAll('details');
  const isMobile = window.innerWidth <= 768;
  allDetails.forEach((details) => {
    details.open = !isMobile;
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const amount = document.getElementById('amount');
  amount?.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      this.blur();
    }
  });
});
