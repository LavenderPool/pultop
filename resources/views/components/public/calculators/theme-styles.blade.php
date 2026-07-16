@props([
    'buttonColor' => '#cecece',
    'inputBorder' => '#1f365c',
    'showResetBg' => true,
])

<style>
#wp_calc_finance_0 {
    border-color: #1f365c;
}
#wp_calc_finance_0 .wp-calc-finance-slider .slider-btn,
#wp_calc_finance_0 .wp-calc-finance-slider .ui-widget-header {
    background: #1f365c !important;
}
#wp_calc_finance_0 .wp-calc-finance-slider .slider-btn:before {
    border-bottom-color: #1f365c;
}
#wp_calc_finance_0 .btn-switcher .switcher__button--active,
#wp_calc_finance_0 .btn-switcher .switcher__button--active:hover {
    background: #1f365c;
}
#wp_calc_finance_0 .calc_finance_button {
    background-image: none;
    background-color: #1f365c !important;
    color: {{ $buttonColor }} !important;
}
#wp_calc_finance_0 .calc_finance_button:hover {
    color: #dd9933 !important;
}
#wp_calc_finance_0 .calc_finance_reset_button {
@if ($showResetBg)
    background-image: none;
    background-color: #dd9933 !important;
@endif
    display: initial !important;
}
@if ($inputBorder !== 'inherit')
#wp_calc_finance_0 input[type="text"],
#wp_calc_finance_0 input[type="tel"],
#wp_calc_finance_0 input[type="number"],
#wp_calc_finance_0 input[type="date"],
#wp_calc_finance_0 select,
#wp_calc_finance_0 textarea {
    border-color: {{ $inputBorder }} !important;
}
@endif
#wp_calc_finance_0 input[type="text"].result {
    background-color: inherit !important;
}
.wp-calc-finance-chart-overlay {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: none;
    background: rgba(0, 0, 0, 0.35);
}
.wp-calc-finance-chart-overlay.is-open {
    display: block;
}
.wp-calc-finance-chart-overlay .ui-dialog.wp-calc-finance-chart {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 800px;
    max-width: calc(100vw - 32px);
    max-height: 90vh;
    overflow: auto;
    padding: 0;
    box-sizing: border-box;
    background: #fff !important;
    background-image: none !important;
    border: 1px solid #aaa !important;
    border-radius: 4px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.25);
    color: #333;
}
.wp-calc-finance-chart-overlay .ui-dialog.wp-calc-finance-chart .ui-dialog-titlebar,
.wp-calc-finance-chart-overlay .ui-dialog.wp-calc-finance-chart .ui-widget-header {
    background: #ffb536 !important;
    background-image: none !important;
    border: 0;
    border-bottom: 1px solid #e0a030 !important;
    border-radius: 4px 4px 0 0;
    color: #333;
    font-weight: 600;
}
.wp-calc-finance-chart-overlay .ui-dialog.wp-calc-finance-chart .ui-dialog-content,
.wp-calc-finance-chart-overlay .ui-dialog.wp-calc-finance-chart.ui-widget-content {
    background: #fff !important;
    background-image: none !important;
    border: 0 !important;
}
.wp-calc-finance-chart-overlay .ui-dialog-content {
    padding: 12px 16px 16px;
    overflow: visible;
}
.wp-calc-finance-chart-overlay #wp_calc_finance_0_chart {
    min-height: 400px;
    background: #fff;
}
</style>
