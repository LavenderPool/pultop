@props([
    'heading',
    'amountLabel',
    'credit' => '10000',
    'firstpay' => '1000',
    'term' => '12',
    'percent' => '10',
    'slider' => null,
    'buttonColor' => '#ffffff',
    'inputBorder' => '#1f365c',
    'showResetBg' => true,
])

<x-public.calculators.theme-styles
    :button-color="$buttonColor"
    :input-border="$inputBorder"
    :show-reset-bg="$showResetBg"
/>

<div
    class="wp-calc-finance"
    id="wp_calc_finance_0"
    data-calc-type="loan"
    @if ($slider)
        data-slider-min="{{ $slider['min'] }}"
        data-slider-max="{{ $slider['max'] }}"
        data-slider-value="{{ $slider['value'] }}"
        data-slider-target="credit"
    @endif
>
    <input type="hidden" class="export_pdf" value="1"/>
    <input type="hidden" class="export_graph" value="1"/>
    <input type="hidden" class="watermark" value="pultop.uz"/>
    <input type="hidden" class="textfirstpayment" value="Первый платеж"/>
    <input type="hidden" class="textmonthlypayment" value="Ежемесячный платеж"/>
    <input type="hidden" class="textPeriod" value="Период"/>
    <input type="hidden" class="textPayment" value="Платеж"/>
    <input type="hidden" class="textPrincipal" value="Погашение кредита"/>
    <input type="hidden" class="textInterest" value="Погашение процентов"/>
    <input type="hidden" class="textBalance" value="Остаток задолженности"/>
    <input type="hidden" class="textTotal" value="Итого"/>
    <input type="hidden" class="textmonths" value=" месяц"/>
    <input type="hidden" class="textyear" value=" год"/>

    <h3>{{ $heading }}</h3>

    <form id="mainform" data-calc-form>
        <div class="wp-calc-finance-contract">
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-4"><b>{{ $amountLabel }}</b></div>
                <div class="wp-calc-finance-col-4">
                    <input
                        class="credit"
                        value="{{ $credit }}"
                        size="4"
                        type="text"
                        data-format-money
                        data-default="{{ $credit }}"
                    /> сум
                    @if ($slider)
                        <div
                            class="wp-calc-finance-slider wp-calc-finance-slider-credit"
                            data-calc-slider
                            data-slider-for="credit"
                            data-min="{{ $slider['min'] }}"
                            data-max="{{ $slider['max'] }}"
                            data-value="{{ $slider['value'] }}"
                        ></div>
                    @else
                        <div class="wp-calc-finance-slider wp-calc-finance-slider-credit" style="display: none">
                            <div class="slider-btn ui-slider-handle"></div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-4"><b>Первоначальный взнос</b></div>
                <div class="wp-calc-finance-col-4">
                    <input
                        type="text"
                        class="firstpay"
                        value="{{ $firstpay }}"
                        size="4"
                        data-format-money
                        data-default="{{ $firstpay }}"
                    /> сум
                </div>
            </div>

            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-4"><b>Срок кредита</b></div>
                <div class="wp-calc-finance-col-4 wp-calc-finance-val">
                    <input class="term" value="{{ $term }}" size="4" type="text" data-default="{{ $term }}"/>
                </div>
                <div class="wp-calc-finance-col-4 unit">
                    <select class="vremya" data-default="1">
                        <option value="1" selected> месяцев </option>
                        <option value="2"> лет </option>
                    </select>
                </div>
            </div>

            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-4"><b>Процентная ставка</b></div>
                <div class="wp-calc-finance-col-4">
                    <input class="percent" size="4" value="{{ $percent }}" type="text" data-default="{{ $percent }}"/>
                </div>
                <div class="wp-calc-finance-col-4">
                    <select class="nachisl" data-default="1">
                        <option value="1">% в год</option>
                        <option value="2">% в месяц </option>
                    </select>
                </div>
            </div>

            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-4"><b>Схема погашения</b></div>
                <div class="wp-calc-finance-col-8">
                    <input type="hidden" class="Shema" value="annuitet" data-default="annuitet"/>
                    <ul class="btn-switcher" data-switcher data-switcher-target="Shema">
                        <li class="switcher__button switcher__button--active" data-switcher-value="annuitet">аннуитет</li>
                        <li class="switcher__button" data-switcher-value="classic">классический</li>
                    </ul>
                </div>
            </div>

            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-4">Единоразовая комиссия </div>
                <div class="wp-calc-finance-col-4"><input size="4" placeholder="%" class="pr1" value="" type="text" data-default=""/> %</div>
                <div class="wp-calc-finance-col-4"><input size="4" placeholder="" class="com1" value="" type="text" data-default=""/> сум</div>
            </div>

            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-4">Ежемесячная комиссия</div>
                <div class="wp-calc-finance-col-4"><input placeholder="%" class="pr2" value="" size="4" type="text" data-default=""/> %</div>
                <div class="wp-calc-finance-col-4"><input size="4" placeholder="" class="com2" value="" type="text" data-default=""/> сум</div>
            </div>

            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-4">Ежегодная комиссия</div>
                <div class="wp-calc-finance-col-4"><input size="4" placeholder="%" class="pr3" value="" type="text" data-default=""/> %</div>
                <div class="wp-calc-finance-col-4"><input size="4" placeholder="" class="com3" value="" type="text" data-default=""/> сум</div>
            </div>

            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-12 buttons">
                    <input class="calc_finance_button" data-calc-action="loan-result" value="Рассчитать" type="button"/>
                    <input type="button" value="Сброс" class="calc_finance_reset_button" data-calc-reset/>
                </div>
            </div>
        </div>

        <div class="wp-calc-finance-result">
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><b><span class="pay_header">Ежемесячный платеж</span> </b></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="monthPay resaltmain result" value="0.00" readonly="readonly"/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span>Ежемесячная комиссия</span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="monthlyFee resaltother result" value="0.00" readonly="readonly"/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span><b>Переплата в денежном выражении</b></span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="overpayment resaltmain result" value="0.00" readonly="readonly"/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-2"></div>
                <div class="wp-calc-finance-col-10"><i>в том числе</i></div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span>Проценты по кредиту</span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="interestOnLoan resaltother result" value="0.00" readonly="readonly"/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span>Ежемесячные выплаты по процентам</span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="monthlyPayment resaltother result" value="0.00" readonly="readonly"/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span>Единоразовая комиссия</span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="singleFee resaltother result" value="0.00" readonly="readonly"/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span>Ежемесячная комиссия</span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="summMonthlyFee resaltother result" value="0.00" readonly="readonly"/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span>Ежегодные платежи</span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="summEearFee resaltother result" value="0.00" readonly="readonly"/> сум</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span><b>Переплата в процентах</b></span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="overpaymentPercentage resaltmain result" value="0.00" readonly="readonly"/> %</div>
            </div>
            <div class="wp-calc-finance-col">
                <div class="wp-calc-finance-col-6"><span><b>Общая сумма к возврату</b></span></div>
                <div class="wp-calc-finance-col-6"><input type="text" class="allPay resaltmain result" value="0.00" readonly="readonly"/> сум</div>
            </div>
        </div>

        <div class="wp-calc-finance-col">
            <div class="wp-calc-finance-col-12">
                <center>
                    <input class="calc_finance_button" value="Показать таблицу" data-calc-action="loan-schedule" type="button"/>
                </center>
            </div>
        </div>

        <div class="table_data"></div>
    </form>

    <x-public.calculators.chart-modal />
</div>
