(function () {
    'use strict';

    var root = document.querySelector('[data-gold-page]');
    if (!root) {
        return;
    }

    var apiUrl = root.getAttribute('data-api-url') || '/api/gold-chart';
    var regionSelect = document.querySelector('#region');
    var tabHeader = document.querySelector('.tab-header');
    var tabIndicator = document.querySelector('.tab-indicator');
    var periodWrap = document.querySelector('.btn-period');
    var chartCanvas = document.getElementById('goldChart');
    var tableEl = document.querySelector('#goldTable');

    if (!tabHeader || !periodWrap || !chartCanvas || typeof Chart === 'undefined') {
        return;
    }

    var numFormat = new Intl.NumberFormat('ru-RU', {
        maximumFractionDigits: 0,
    });

    function formatSum(value) {
        if (value === null || value === undefined || value === '') {
            return '—';
        }
        return numFormat.format(Number(value)) + ' сум';
    }

    if (regionSelect) {
        regionSelect.addEventListener('change', function () {
            var region = regionSelect.value;
            document.querySelectorAll('.gold-place-sale tr').forEach(function (row) {
                if (row.classList.contains('header')) {
                    return;
                }
                row.style.display =
                    row.getAttribute('region') === region ? 'table-row' : 'none';
            });
        });
    }

    tabHeader.querySelectorAll('[tab-id]').forEach(function (el) {
        el.addEventListener('click', function () {
            var active = tabHeader.querySelector('.active');
            if (active) {
                active.classList.remove('active');
            }
            el.classList.add('active');
            var tabId = el.getAttribute('tab-id');
            if (tabIndicator) {
                tabIndicator.style.left = 'calc(calc(100% / 5) * ' + tabId + ')';
            }
            var periodBtn = periodWrap.querySelector('button.active');
            var period = periodBtn ? periodBtn.getAttribute('data') : '7';
            getChart(tabId, period);
        });
    });

    periodWrap.querySelectorAll('button').forEach(function (el) {
        el.addEventListener('click', function () {
            var active = periodWrap.querySelector('.active');
            if (active) {
                active.classList.remove('active');
            }
            el.classList.add('active');
            var period = el.getAttribute('data');
            var tabActive = tabHeader.querySelector('.active');
            var tabId = tabActive ? tabActive.getAttribute('tab-id') : '0';
            getChart(tabId, period);
        });
    });

    var goldChart = new Chart(chartCanvas, { data: {} });

    function setTable(rows) {
        if (!tableEl) {
            return;
        }
        tableEl.innerHTML = '';
        (rows || []).forEach(function (item) {
            var row = document.createElement('div');
            row.innerHTML =
                '<div>' +
                item.date +
                '</div><div>' +
                formatSum(item.price) +
                '</div><div>' +
                formatSum(item.diff) +
                '</div>';
            tableEl.appendChild(row);
        });
    }

    function getChart(tabId, period) {
        var url =
            apiUrl +
            '?gold=' +
            encodeURIComponent(tabId) +
            '&period=' +
            encodeURIComponent(period);

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (payload) {
                goldChart.data.labels = payload.data.labels;
                goldChart.data.datasets = payload.data.datasets;
                goldChart.update();
                setTable(payload.rows);
            })
            .catch(function (error) {
                console.error('Gold chart fetch error:', error);
            });
    }

    getChart('0', '7');
})();
