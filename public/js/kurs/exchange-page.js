document.addEventListener('DOMContentLoaded', () => {
  if (typeof responsiveDetails === 'function') {
    responsiveDetails();
  }

  if (typeof CustomSelect === 'function') {
    const currencySelect = new CustomSelect('select-currency');
    currencySelect.container?.addEventListener('selectChange', (e) => {
      const value = e.detail?.value;
      if (!value) {
        return;
      }
      if (value.startsWith('http') || value.startsWith('/')) {
        location.href = value;
      } else {
        location.href = `/${value}`;
      }
    });

    // Prefer direct links in options
    document
      .querySelectorAll('#select-currency .select-option[href]')
      .forEach((link) => {
        link.addEventListener('click', (event) => {
          event.preventDefault();
          location.href = link.getAttribute('href');
        });
      });

    new CustomSelect('action');
    new CustomSelect('place');
  }

  if (window.currencyHistory && typeof createCurrencyChart === 'function') {
    createCurrencyChart(window.currencyHistory);
  }
});
