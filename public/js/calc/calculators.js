(() => {
  'use strict';

  function qs(root, selector) {
    return root.querySelector(selector);
  }

  function val(root, selector) {
    const el = qs(root, selector);
    return el ? el.value : '';
  }

  function setVal(root, selector, value) {
    const el = qs(root, selector);
    if (el) {
      el.value = value;
    }
  }

  function setText(root, selector, value) {
    const el = qs(root, selector);
    if (el) {
      el.textContent = value;
    }
  }

  function formatFloat(value) {
    return String(value ?? '')
      .replace(/,/g, '.')
      .replace(/[^0-9.]/g, '');
  }

  function parseMoney(value) {
    return Number(String(value ?? '').replace(/\s/g, '').replace(',', '.')) || 0;
  }

  function round(value, digits) {
    const n = Math.round(Number(value) * Math.pow(10, digits));
    let t = n < 0 ? '' : String(n);
    if (digits > 0) {
      while (t.length <= digits) {
        t = `0${t}`;
      }
      t = `${t.substring(0, t.length - digits)}.${t.substring(t.length - digits)}`;
    }
    if (t.charAt(0) === '.') {
      t = `0${t}`;
    }
    if (t.charAt(t.length - 1) === '.') {
      t += '0';
    }
    return t;
  }

  function disNum(amount, digits, decimalSep, thousandSep) {
    let a = Math.round(Number(amount) * Math.pow(10, digits)) / Math.pow(10, digits);
    let e = `${a}`;
    const f = e.split('.');
    if (!f[0]) {
      f[0] = '0';
    }
    if (!f[1]) {
      f[1] = '';
    }
    if (f[1].length < digits) {
      let g = f[1];
      for (let i = f[1].length + 1; i <= digits; i += 1) {
        g += '0';
      }
      f[1] = g;
    }
    if (thousandSep !== '' && f[0].length > 3) {
      const h = f[0];
      f[0] = '';
      let j = 3;
      for (; j < h.length; j += 3) {
        const i = h.slice(h.length - j, h.length - j + 3);
        f[0] = thousandSep + i + f[0];
      }
      j = h.substr(0, h.length % 3 === 0 ? 3 : h.length % 3);
      f[0] = j + f[0];
    }
    const r = digits <= 0 ? '' : decimalSep;
    return f[0] + r + f[1];
  }

  function formatMoneyInput(input) {
    const raw = input.value.replace(/\s*/g, '');
    input.value = raw.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
  }

  function formatCn(input) {
    let a = input.value;
    if (Number.isNaN(Number(a)) && a !== '' && a !== '.') {
      a = a.substring(0, a.length - 1);
      input.value = a;
    }
  }

  function switchBtn(button, value, targetClass, root) {
    const hidden = qs(root, `.${targetClass}`);
    if (hidden) {
      hidden.value = value;
    }
    const list = button.parentElement;
    if (!list) {
      return;
    }
    list.querySelectorAll('.switcher__button').forEach((el) => {
      el.classList.remove('switcher__button--active');
    });
    button.classList.add('switcher__button--active');
  }

  function downloadCsv(filename, rows) {
    const csv = rows
      .map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(';'))
      .join('\n');
    const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
  }

  function openChart(root, labels, values, seriesName) {
    const overlay = qs(root, '[data-calc-chart-overlay]');
    const chartEl = qs(root, '#wp_calc_finance_0_chart');
    if (!overlay || !chartEl || typeof c3 === 'undefined') {
      return;
    }

    if (root._c3Chart) {
      root._c3Chart.destroy();
      root._c3Chart = null;
    }

    chartEl.innerHTML = '';
    const xCol = ['x'].concat(labels);
    const yCol = [seriesName || 'y'].concat(values);

    root._c3Chart = c3.generate({
      bindto: chartEl,
      data: {
        x: 'x',
        columns: [xCol, yCol],
      },
      axis: {
        y: {
          min: 1,
          padding: {
            top: 0,
            bottom: 0,
          },
        },
      },
      color: {
        pattern: ['#3cc88c'],
      },
      tooltip: {
        format: {
          title(d) {
            return `Month: ${d}`;
          },
        },
      },
    });
    root._c3Chart.resize({ height: 400, width: 750 });
    root._c3Chart.flush();

    overlay.hidden = false;
    overlay.classList.add('is-open');
  }

  function closeChart(root) {
    const overlay = qs(root, '[data-calc-chart-overlay]');
    if (!overlay) {
      return;
    }
    overlay.classList.remove('is-open');
    overlay.hidden = true;
  }

  function loanConsiderResult(root) {
    const textfirstpayment = val(root, '.textfirstpayment');
    const textmonthlypayment = val(root, '.textmonthlypayment');
    const proc = val(root, '.nachisl');
    let p;
    if (proc === '2') {
      p = 12 * Number(val(root, '.percent').replace(',', '.'));
    } else {
      p = Number(val(root, '.percent').replace(',', '.'));
    }
    const srok = val(root, '.vremya');
    let t;
    if (srok === '2') {
      t = 12 * Number(val(root, '.term').replace(',', '.'));
    } else {
      t = Number(val(root, '.term').replace(',', '.'));
    }

    let credit = parseMoney(val(root, '.credit'));
    const firstpay = parseMoney(val(root, '.firstpay'));
    credit -= firstpay;
    let a = credit;
    const schema = val(root, '.Shema');

    if (schema === 'classic') {
      setText(root, '.pay_header', textfirstpayment);
    } else if (schema === 'annuitet') {
      setText(root, '.pay_header', textmonthlypayment);
    } else {
      setText(root, '.pay_header', '');
    }

    let o = 0;
    if (schema === 'classic') {
      o = a / t + (a * p) / 1200;
    } else if (schema === 'annuitet') {
      o = ((a * p) / 1200) / (1 - Math.pow(1 + p / 1200, -t));
    }

    let u = 0;
    let s = 0;
    let yearly = 0;
    for (let i = 1; i <= 3; i += 1) {
      let f = (Number(val(root, `.pr${i}`).replace(',', '.')) * a) / 100;
      const r = Number(val(root, `.com${i}`).replace(',', '.'));
      if (r && r > f) {
        f = r;
      }
      if (i === 1) {
        u += f || 0;
      }
      if (i === 2) {
        s += f || 0;
      }
      if (i === 3) {
        yearly += f || 0;
      }
    }

    let c = Number(round(a / t, 2));
    let z = Number(round(((a * p) / 1200) / (1 - Math.pow(1 + p / 1200, -t)), 2));
    let v = 0;
    let y = 0;
    let m = 0;
    let balance = a;

    for (let i = 1; i < t; i += 1) {
      let l;
      if (schema === 'classic') {
        l = Number(round((balance * p) / 1200, 2));
        z = Number(round(c + l, 2));
        balance -= c;
      } else if (schema === 'annuitet') {
        l = Number(round((balance * p) / 1200, 2));
        c = Number(round(z - l, 2));
        balance -= c;
      } else {
        l = 0;
      }
      v += l;
      y += c;
      m += z;
    }

    c = balance;
    const lLast = Number(round((c * p) / 1200, 2));
    z = Number(round(c + lLast, 2));
    v += lLast;
    y += c;
    m += z;

    const n = Math.floor(t / 12) === t / 12 ? t / 12 : Math.floor(t / 12);
    const overpayment = m - credit + u + s * t + yearly * n;
    const summAll = m + u + s * t + yearly * n;
    const summMonthlyFee = s * t;
    const summEearFee = yearly * n;
    const overpaymentPercentage = credit !== 0 ? (overpayment / credit) * 100 : 0;

    setVal(root, '.monthPay', disNum(o, 2, '.', ' '));
    setVal(root, '.monthlyFee', disNum(s, 2, '.', ' '));
    setVal(root, '.allPay', disNum(summAll, 2, '.', ' '));
    setVal(root, '.overpayment', disNum(overpayment, 2, '.', ' '));
    setVal(root, '.interestOnLoan', disNum(v, 2, '.', ' '));
    setVal(root, '.monthlyPayment', disNum(Math.round((v / t) * 100) / 100, 2, '.', ' '));
    setVal(root, '.singleFee', disNum(u, 2, '.', ' '));
    setVal(root, '.summMonthlyFee', disNum(summMonthlyFee, 2, '.', ' '));
    setVal(root, '.summEearFee', disNum(summEearFee, 2, '.', ' '));
    setVal(root, '.overpaymentPercentage', disNum(overpaymentPercentage, 1, '.', ' '));
  }

  function loanSchedule(root) {
    let a = parseMoney(val(root, '.credit'));
    const firstpay = parseMoney(val(root, '.firstpay'));
    a -= firstpay;

    const proc = val(root, '.nachisl');
    let percent;
    if (proc === '2') {
      percent = 12 * Number(val(root, '.percent').replace(',', '.'));
    } else {
      percent = Number(val(root, '.percent').replace(',', '.'));
    }

    const srok = val(root, '.vremya');
    let term;
    if (srok === '2') {
      term = 12 * Number(val(root, '.term').replace(',', '.'));
    } else {
      term = Number(val(root, '.term').replace(',', '.'));
    }

    const textPeriod = val(root, '.textPeriod');
    const textPayment = val(root, '.textPayment');
    const textPrincipal = val(root, '.textPrincipal');
    const textInterest = val(root, '.textInterest');
    const textBalance = val(root, '.textBalance');
    const textTotal = val(root, '.textTotal');
    const textmonths = val(root, '.textmonths');
    const textyear = val(root, '.textyear');
    const schema = val(root, '.Shema');

    let html = `<table class='table-schedule'><thead><tr><th>${textPeriod}</th><th>${textPayment}</th><th>${textPrincipal}</th><th>${textInterest}</th><th>${textBalance}</th></tr></thead><tbody>`;
    let r = Number(round(a / term, 2));
    let c = Number(round(((a * percent) / 1200) / (1 - Math.pow(1 + percent / 1200, -term)), 2));
    let yearNo = 0;
    const pdfRows = [[textPeriod, textPayment, textPrincipal, textInterest, textBalance]];
    const chartX = [];
    const chartY = [];
    let sumInterest = 0;
    let sumPrincipal = 0;
    let sumPayment = 0;
    let balance = a;

    for (let i = 1; i <= term; i += 1) {
      let n;
      if (schema === 'classic') {
        n = Number(round((balance * percent) / 1200, 2));
        c = Number(round(r + n, 2));
        balance -= r;
      } else if (schema === 'annuitet') {
        n = Number(round((balance * percent) / 1200, 2));
        r = Number(round(c - n, 2));
        balance -= r;
      } else {
        n = 0;
      }

      if ((i - 1) % 12 === 0) {
        yearNo += 1;
        html += `<tr><td colspan=5><b>${yearNo}${textyear}</b></td></tr>`;
        pdfRows.push([`${yearNo}${textyear}`, '', '', '', '']);
      }

      html += `<tr><td><nobr>${i}${textmonths}</nobr></td><td><b>${round(c, 2)}</b></td><td>${round(r, 2)}</td><td>${round(n, 2)}</td><td>${round(balance, 2)}</td></tr>`;
      pdfRows.push([`${i}${textmonths}`, round(c, 2), round(r, 2), round(n, 2), round(balance, 2)]);
      chartX.push(i);
      chartY.push(Number(round(balance, 2)));
      sumInterest += n;
      sumPrincipal += r;
      sumPayment += c;
    }

    html += `<tfoot><tr><td><b>${textTotal}</b></td><td><b>${round(sumPayment, 2)}</b></td><td><b>${round(sumPrincipal, 2)}</b></td><td><b>${round(sumInterest, 2)}</b></td><td></td></tr></tfoot></tbody></table>`;

    const exportPdf = val(root, '.export_pdf') === '1';
    const exportGraph = val(root, '.export_graph') === '1';
    const tableData = qs(root, '.table_data');
    if (!tableData) {
      return;
    }

    let links = '';
    if (exportPdf) {
      links += '<a class="pdf" style="word-wrap: initial; cursor:pointer;"><span class="pdf_icon" title="PDF"></span></a>';
    }
    if (exportGraph) {
      links += '<a class="graph" style="word-wrap: initial; cursor:pointer;"><span class="graph_icon" title="Graph"></span></a>';
    }
    tableData.innerHTML = `${links}<br/>&nbsp;<br/>${html}`;

    const graphLink = tableData.querySelector('a.graph');
    if (graphLink) {
      graphLink.addEventListener('click', (event) => {
        event.preventDefault();
        openChart(root, chartX, chartY, textBalance);
      });
    }

    const pdfLink = tableData.querySelector('a.pdf');
    if (pdfLink) {
      pdfLink.addEventListener('click', (event) => {
        event.preventDefault();
        downloadCsv('payments.csv', pdfRows);
      });
    }

    root._lastSchedule = { chartX, chartY, pdfRows };
  }

  function depositCalc(root) {
    const currency = root.dataset.currency || 'сум';
    const roundUp = val(root, '.round_up_calc') === '1';
    const digits = roundUp ? 0 : 2;
    const c = formatFloat(val(root, '.summa'));
    const l = formatFloat(val(root, '.percent'));
    const o = formatFloat(val(root, '.srok'));
    const u = parseInt(val(root, '.was'), 10);
    const s = parseInt(o, 10);
    const monthlyRate = parseFloat(l) / 12;
    let profit = 0;

    if (Number.isNaN(parseFloat(c))) {
      qs(root, '.summa')?.focus();
      return;
    }
    if (Number.isNaN(s)) {
      qs(root, '.srok')?.focus();
      return;
    }

    const textTotal = val(root, '.textTotal');
    const textMonth = val(root, '.textMonth');
    let html = `<table class="table-schedule"><thead><tr><th>${textMonth}</th><th>${textTotal}</th></tr></thead><tbody>`;
    const pdfRows = [[textMonth, textTotal]];
    const chartX = [];
    const chartY = [];
    const principal = parseFloat(c);

    for (let i = 1; i <= s; i += 1) {
      let sum = u === 1
        ? (monthlyRate / 100) * (principal + profit)
        : (monthlyRate / 100) * principal;
      let f = Math.round(parseFloat(sum) * 1e4) / 1e4;
      profit += f;
      f = round(Number(f).toFixed(2), digits);
      html += `<tr><td>${i}</td><td> ${f} ${currency}</td></tr>`;
      pdfRows.push([i, f]);
      chartX.push(i);
      chartY.push(Number(f));
    }
    html += '</table>';

    profit = Math.round(parseFloat(profit) * 1e4) / 1e4;
    const total = Math.round((profit + principal) * 1e4) / 1e4;
    setVal(root, '.result-summary .op', round(total.toFixed(2), digits));
    setVal(root, '.result-summary .profit', round(profit.toFixed(2), digits));

    const exportPdf = val(root, '.export_pdf') === '1';
    const exportGraph = val(root, '.export_graph') === '1';
    const resmore = qs(root, '.resmore');
    if (!resmore) {
      return;
    }

    let links = '';
    if (exportPdf) {
      links += '<a class="pdf" style="word-wrap: initial; cursor:pointer;"><span class="pdf_icon" title="PDF"></span></a>';
    }
    if (exportGraph) {
      links += '<a class="graph" style="word-wrap: initial; cursor:pointer;"><span class="graph_icon" title="Graph"></span></a>';
    }
    resmore.innerHTML = `${links}&nbsp;${html}`;
    resmore.style.display = 'block';

    resmore.querySelector('a.graph')?.addEventListener('click', (event) => {
      event.preventDefault();
      openChart(root, chartX, chartY, textTotal);
    });
    resmore.querySelector('a.pdf')?.addEventListener('click', (event) => {
      event.preventDefault();
      downloadCsv('payments.csv', pdfRows);
    });
  }

  function vatCalc(root) {
    const roundUp = val(root, '.round_up_calc') === '1';
    const digits = roundUp ? 0 : 2;
    const action = val(root, '.inVatAction');
    const amount = Number(formatFloat(val(root, '.inSumm')));
    const rate = Number(formatFloat(val(root, '.inInterest')));

    if (action === '1' || action === 1) {
      const without = Number((amount / (rate / 100 + 1)).toFixed(2));
      const vat = Number((amount - without).toFixed(2));
      setVal(root, 'input.sum', round(amount, digits));
      setVal(root, 'input.vat', round(vat, digits));
      setVal(root, 'input.sumWithoutVat', round(without, digits));
    }
    if (action === '2' || action === 2) {
      const vat = Number(((amount * rate) / 100).toFixed(2));
      const withVat = Number((amount + vat).toFixed(2));
      setVal(root, 'input.sum', round(withVat, digits));
      setVal(root, 'input.vat', round(vat, digits));
      setVal(root, 'input.sumWithoutVat', round(amount, digits));
    }
  }

  function monthlyPayment(root) {
    const roundUp = val(root, '.round_up_calc') === '1';
    const digits = roundUp ? 0 : 2;
    const principal = Number(formatFloat(val(root, '.l')));
    const rate = Number(formatFloat(val(root, '.i')));
    const years = Number(formatFloat(val(root, '.n')));
    const comp = val(root, '.comp');

    let periodRate;
    let periods;
    if (comp === '0') {
      periodRate = Number(parseFloat(rate / 1200).toFixed(6));
      periods = 12 * years;
    } else if (comp === '1') {
      periodRate = Number(parseFloat(rate / 400).toFixed(6));
      periods = 4 * years;
    } else if (comp === '2') {
      periodRate = Number(parseFloat(rate / 200).toFixed(6));
      periods = 2 * years;
    } else if (comp === '3') {
      periodRate = Number(parseFloat(rate / 100).toFixed(6));
      periods = years;
    }

    const u = 1 + periodRate;
    const s = Number(parseFloat(principal * periodRate).toFixed(5));
    const denom = Number(parseFloat(1 - Math.pow(u, -periods)).toFixed(5));
    const payment = Number(parseFloat(s / denom).toFixed(2));
    setVal(root, '.op', round(payment, digits));
  }

  function defaultActionFor(root) {
    switch (root.dataset.calcType) {
      case 'loan':
        return 'loan-result';
      case 'deposit':
        return 'deposit';
      case 'vat':
        return 'vat';
      case 'monthly':
        return 'monthly';
      default:
        return null;
    }
  }

  function runAction(root, action) {
    switch (action) {
      case 'loan-result':
        loanConsiderResult(root);
        break;
      case 'loan-schedule':
        loanSchedule(root);
        break;
      case 'deposit':
        depositCalc(root);
        break;
      case 'vat':
        vatCalc(root);
        break;
      case 'monthly':
        monthlyPayment(root);
        break;
      default:
        break;
    }
  }

  function autoCalc(root) {
    const action = defaultActionFor(root);
    if (action) {
      runAction(root, action);
    }
  }

  function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
  }

  function initUiSlider(root, track) {
    const min = Number(track.dataset.min);
    const max = Number(track.dataset.max);
    const fieldClass = track.dataset.sliderFor;
    const input = qs(root, `.${fieldClass}`);
    let value = Number(track.dataset.value);
    if (input) {
      const fromInput = parseMoney(input.value);
      if (!Number.isNaN(fromInput) && fromInput > 0) {
        value = fromInput;
      }
    }
    value = clamp(value, min, max);

    track.classList.add(
      'ui-slider',
      'ui-corner-all',
      'ui-slider-horizontal',
      'ui-widget',
      'ui-widget-content',
    );
    track.innerHTML = '';

    const range = document.createElement('div');
    range.className = 'ui-slider-range ui-corner-all ui-widget-header ui-slider-range-min';

    const handle = document.createElement('span');
    handle.className = 'ui-slider-handle ui-corner-all ui-state-default slider-btn';
    handle.tabIndex = 0;
    handle.setAttribute('role', 'slider');
    handle.setAttribute('aria-valuemin', String(min));
    handle.setAttribute('aria-valuemax', String(max));

    track.appendChild(range);
    track.appendChild(handle);
    track.style.display = '';

    const api = {
      min,
      max,
      value,
      setValue(next, syncInput = true) {
        api.value = clamp(Number(next), min, max);
        const pct = ((api.value - min) / (max - min)) * 100;
        handle.style.left = `${pct}%`;
        range.style.width = `${pct}%`;
        handle.setAttribute('aria-valuenow', String(api.value));
        if (syncInput && input) {
          input.value = String(Math.round(api.value));
          if (input.hasAttribute('data-format-money')) {
            formatMoneyInput(input);
          }
        }
      },
    };

    api.setValue(value, false);
    if (input && input.hasAttribute('data-format-money')) {
      formatMoneyInput(input);
    }

    function valueFromPointer(clientX) {
      const rect = track.getBoundingClientRect();
      const ratio = rect.width ? (clientX - rect.left) / rect.width : 0;
      return clamp(min + ratio * (max - min), min, max);
    }

    let dragging = false;

    handle.addEventListener('mousedown', (event) => {
      event.preventDefault();
      dragging = true;
      handle.classList.add('ui-state-active');
    });

    track.addEventListener('mousedown', (event) => {
      if (event.target === handle) {
        return;
      }
      event.preventDefault();
      dragging = true;
      handle.classList.add('ui-state-active');
      api.setValue(valueFromPointer(event.clientX));
    });

    document.addEventListener('mousemove', (event) => {
      if (!dragging) {
        return;
      }
      api.setValue(valueFromPointer(event.clientX));
    });

    document.addEventListener('mouseup', () => {
      if (!dragging) {
        return;
      }
      dragging = false;
      handle.classList.remove('ui-state-active');
    });

    handle.addEventListener('keydown', (event) => {
      const step = Math.max(1, Math.round((max - min) / 100));
      if (event.key === 'ArrowLeft' || event.key === 'ArrowDown') {
        event.preventDefault();
        api.setValue(api.value - step);
      } else if (event.key === 'ArrowRight' || event.key === 'ArrowUp') {
        event.preventDefault();
        api.setValue(api.value + step);
      } else if (event.key === 'Home') {
        event.preventDefault();
        api.setValue(min);
      } else if (event.key === 'End') {
        event.preventDefault();
        api.setValue(max);
      }
    });

    if (input) {
      input.addEventListener('change', () => {
        api.setValue(parseMoney(input.value), false);
        if (input.hasAttribute('data-format-money')) {
          formatMoneyInput(input);
        }
      });
    }

    track._sliderApi = api;
    return api;
  }

  function syncSliderFromInput(root, fieldClass) {
    const track = qs(root, `[data-calc-slider][data-slider-for="${fieldClass}"]`);
    if (!track || !track._sliderApi) {
      return;
    }
    track._sliderApi.setValue(parseMoney(qs(root, `.${fieldClass}`)?.value), false);
  }

  function resetForm(root) {
    root.querySelectorAll('[data-default]').forEach((el) => {
      const def = el.getAttribute('data-default');
      if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        el.value = def ?? '';
      }
    });

    root.querySelectorAll('[data-switcher]').forEach((list) => {
      const target = list.dataset.switcherTarget;
      const hidden = qs(root, `.${target}`);
      const def = hidden?.getAttribute('data-default') ?? hidden?.value;
      list.querySelectorAll('.switcher__button').forEach((btn) => {
        btn.classList.toggle('switcher__button--active', String(btn.dataset.switcherValue) === String(def));
      });
      if (hidden && def != null) {
        hidden.value = def;
      }
    });

    root.querySelectorAll('[data-format-money]').forEach(formatMoneyInput);

    root.querySelectorAll('[data-calc-slider]').forEach((track) => {
      const field = track.dataset.sliderFor;
      const input = qs(root, `.${field}`);
      const fallback = Number(track.dataset.value);
      const num = input ? parseMoney(input.value) : fallback;
      if (track._sliderApi) {
        track._sliderApi.setValue(num, false);
      }
    });

    root.querySelectorAll('.result').forEach((el) => {
      if (el.classList.contains('monthPay') || el.classList.contains('monthlyFee')
        || el.classList.contains('overpayment') || el.classList.contains('interestOnLoan')
        || el.classList.contains('monthlyPayment') || el.classList.contains('singleFee')
        || el.classList.contains('summMonthlyFee') || el.classList.contains('summEearFee')
        || el.classList.contains('overpaymentPercentage') || el.classList.contains('allPay')) {
        el.value = '0.00';
      } else {
        el.value = '';
      }
    });

    const tableData = qs(root, '.table_data');
    if (tableData) {
      tableData.innerHTML = '';
    }
    const resmore = qs(root, '.resmore');
    if (resmore) {
      resmore.innerHTML = '';
    }
    setText(root, '.pay_header', 'Ежемесячный платеж');
    autoCalc(root);
  }

  function initRoot(root) {
    root.querySelectorAll('[data-format-money]').forEach((input) => {
      formatMoneyInput(input);
      input.addEventListener('keyup', () => formatMoneyInput(input));
      input.addEventListener('change', () => {
        formatMoneyInput(input);
        syncSliderFromInput(root, input.classList[0]);
      });
    });

    root.querySelectorAll('[data-format-cn]').forEach((input) => {
      input.addEventListener('keyup', () => formatCn(input));
    });

    root.querySelectorAll('[data-calc-slider]').forEach((track) => {
      initUiSlider(root, track);
    });

    root.querySelectorAll('[data-switcher] .switcher__button').forEach((button) => {
      button.addEventListener('click', () => {
        const list = button.closest('[data-switcher]');
        const target = list?.dataset.switcherTarget;
        const value = button.dataset.switcherValue;
        if (!list || !target) {
          return;
        }
        switchBtn(button, value, target, root);
        if (list.dataset.switcherRecalc) {
          runAction(root, list.dataset.switcherRecalc);
        }
      });
    });

    root.querySelectorAll('[data-calc-action]').forEach((button) => {
      button.addEventListener('click', (event) => {
        event.preventDefault();
        runAction(root, button.dataset.calcAction);
      });
    });

    root.querySelectorAll('[data-calc-change]').forEach((el) => {
      el.addEventListener('change', () => runAction(root, el.dataset.calcChange));
    });

    root.querySelectorAll('[data-calc-reset]').forEach((button) => {
      button.addEventListener('click', (event) => {
        event.preventDefault();
        resetForm(root);
      });
    });

    root.querySelectorAll('[data-calc-chart-close]').forEach((button) => {
      button.addEventListener('click', () => closeChart(root));
    });

    qs(root, '[data-calc-chart-overlay]')?.addEventListener('click', (event) => {
      if (event.target === event.currentTarget) {
        closeChart(root);
      }
    });

    autoCalc(root);
  }

  function init() {
    document.querySelectorAll('.wp-calc-finance[data-calc-type]').forEach(initRoot);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
