<x-public.calculators.theme-styles button-color="#ffffff" />

<div
    class="wp-calc-finance"
    id="wp_calc_finance_0"
    data-calc-type="deposit"
    data-currency="сум"
    data-slider-min="1000000"
    data-slider-max="50000000"
    data-slider-value="1000000"
    data-slider-target="summa"
>
    <input type="hidden" class="export_pdf" value="1"/>
    <input type="hidden" class="export_graph" value="1"/>
    <input type="hidden" class="watermark" value="pultop.uz"/>
    <input type="hidden" class="textTotal" value="Итого"/>
    <input type="hidden" class="textMonth" value=" Месяц"/>
    <input type="hidden" class="round_up_calc" value="0"/>

    <h3>Расчет выплат по вкладу</h3>

    <form name="deposit_0" data-calc-form>
        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-5">Cумма вклада</div>
            <div class="wp-calc-finance-col-6">
                <input type="text" class="summa" name="deposit_summa" value="1000000" data-format-money data-default="1000000"/> сум
                <div
                    class="wp-calc-finance-slider wp-calc-finance-slider-credit"
                    data-calc-slider
                    data-slider-for="summa"
                    data-min="1000000"
                    data-max="50000000"
                    data-value="1000000"
                ></div>
            </div>
        </div>

        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-5">Процентная ставка</div>
            <div class="wp-calc-finance-col-6">
                <input type="text" class="percent" name="deposit_percent" value="14" data-default="14"/> %
            </div>
        </div>

        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-5">Срок вклада</div>
            <div class="wp-calc-finance-col-6">
                <input type="text" class="srok" name="deposit_srok" value="12" data-default="12"/> месяц
            </div>
        </div>

        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-5">Ежемесячные проценты</div>
            <div class="wp-calc-finance-col-6">
                <input type="hidden" class="was" value="1" data-default="1"/>
                <ul class="btn-switcher" data-switcher data-switcher-target="was">
                    <li class="switcher__button switcher__button--active" data-switcher-value="1">реинвестируются</li>
                    <li class="switcher__button" data-switcher-value="2">снимаются</li>
                </ul>
            </div>
        </div>

        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-12">
                <center>
                    <input type="button" value="Рассчитать" data-calc-action="deposit" class="calc_finance_button"/>
                    <input type="button" value="Сброс" class="calc_finance_reset_button" data-calc-reset/>
                </center>
            </div>
        </div>

        <span class="result-summary">
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-5">Итого</div>
                <div class="wp-calc-finance-col-6"><input type="text" class="op result" readonly/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-5">Доход</div>
                <div class="wp-calc-finance-col-6"><input type="text" class="profit result" readonly/> сум</div>
            </div>
        </span>

        <span class="resmore"></span>
    </form>

    <x-public.calculators.chart-modal />
</div>
