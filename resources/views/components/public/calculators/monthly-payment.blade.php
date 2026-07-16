<x-public.calculators.theme-styles />

<div
    class="wp-calc-finance"
    id="wp_calc_finance_0"
    data-calc-type="monthly"
    data-slider-min="1000000"
    data-slider-max="100000000"
    data-slider-value="10000000"
    data-slider-target="l"
>
    <input type="hidden" class="round_up_calc" value="0"/>

    <h3>Сколько платить по кредиту в месяц?</h3>

    <form name="month_payment_0" data-calc-form>
        <table cellspacing="0" cellpadding="3" class="wp-calc-finance-layout">
            <tr>
                <td>Сумма кредита</td>
                <td>
                    <input name="l" class="l" type="text" value="10000000" data-format-money data-default="10000000"/> сум
                    <div
                        class="wp-calc-finance-slider wp-calc-finance-slider-credit"
                        data-calc-slider
                        data-slider-for="l"
                        data-min="1000000"
                        data-max="100000000"
                        data-value="10000000"
                    ></div>
                </td>
            </tr>
            <tr>
                <td>Процентная ставка</td>
                <td><input name="i" class="i" type="text" value="20" data-default="20"/> %</td>
            </tr>
            <tr>
                <td>Срок кредитования</td>
                <td><input name="n" class="n" type="text" value="1" data-default="1"/> лет</td>
            </tr>
            <tr>
                <td>Начисление процентов</td>
                <td>
                    <select name="month_payment_comp" class="comp" data-default="0" data-calc-change="monthly">
                        <option value="0">Ежемесячно</option>
                        <option value="1">Поквартально</option>
                        <option value="2">Раз в полгода</option>
                        <option value="3">Ежегодно</option>
                    </select>
                </td>
            </tr>
        </table>

        <p align="center">
            <input type="button" name="calc" value="Рассчитать" data-calc-action="monthly" class="calc_finance_button"/>
            <input type="button" value="Сброс" class="calc_finance_reset_button" data-calc-reset/>
        </p>

        <table cellspacing="0" cellpadding="3" class="wp-calc-finance-layout">
            <tr>
                <td>Сумма платежа</td>
                <td><input type="text" name="op" class="op result" readonly/> сум</td>
            </tr>
        </table>
    </form>
</div>
