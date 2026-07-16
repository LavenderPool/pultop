<x-public.calculators.theme-styles button-color="#cecece" input-border="inherit" />

<div class="wp-calc-finance" id="wp_calc_finance_0" data-calc-type="vat">
    <input type="hidden" class="round_up_calc" value="0"/>

    <h3>Рассчитайте НДС</h3>

    <form name="vat_0" data-calc-form>
        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-5">НДС</div>
            <div class="wp-calc-finance-col-6">
                <input type="hidden" class="inVatAction" value="1" data-default="1"/>
                <ul class="btn-switcher" data-switcher data-switcher-target="inVatAction" data-switcher-recalc="vat">
                    <li class="switcher__button switcher__button--active" data-switcher-value="1">Выделить</li>
                    <li class="switcher__button" data-switcher-value="2">Начислить</li>
                </ul>
            </div>
        </div>

        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-5">Сумма</div>
            <div class="wp-calc-finance-col-6">
                <input type="text" class="inSumm" value="10000000" data-format-cn data-default="10000000"/> сум
            </div>
        </div>

        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-5">Ставка НДС</div>
            <div class="wp-calc-finance-col-6">
                <input type="text" class="inInterest" value="20" data-default="20"/> %
            </div>
        </div>

        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-12 buttons">
                <input type="button" value="Рассчитать" data-calc-action="vat" class="calc_finance_button"/>
                <input type="button" value="Сброс" class="calc_finance_reset_button" data-calc-reset/>
            </div>
        </div>

        <span class="result-summary">
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-5">Сумма с НДС</div>
                <div class="wp-calc-finance-col-6"><input type="text" class="sum result" readonly/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-5">НДС</div>
                <div class="wp-calc-finance-col-6"><input type="text" class="vat result" readonly/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-5">Сумма без НДС</div>
                <div class="wp-calc-finance-col-6"><input type="text" class="sumWithoutVat result" readonly/> сум</div>
            </div>
        </span>
    </form>
</div>
