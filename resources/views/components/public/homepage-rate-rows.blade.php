@php
    use App\Support\Money;
@endphp

@forelse ($rates as $item)
    @php
        $code = strtolower($item['code']);
        $diff = $item['cbu_diff'] ?? null;
        $diffClass = $diff !== null && (float) $diff >= 0 ? 'shift-green' : 'shift-red';
        $buy = $item['best_buy'] ?? null;
        $sell = $item['best_sell'] ?? null;
    @endphp
    <div class="currency-row" data-currency-code="{{ $code }}">
        <div class="currency-col">
            <div class="currency-header">Валюта</div>
            <div class="img-block">
                <span class="img-currency" aria-hidden="true" style="font-size:22px;line-height:1;">{{ $item['flag'] }}</span>
                <div class="currency-name">{{ $item['name_ru'] }}</div>
            </div>
        </div>
        <div class="currency-col">
            <div class="currency-header">Официальный курс</div>
            <div class="official-row">
                <div>1 {{ $item['code'] }} = </div>
                <div>
                    <span class="currency-value of-curs-value">{{ Money::formatRate($item['cbu_rate'] ?? null) }}</span><span> сум</span>
                    @if ($diff !== null)
                        <span class="currency-shift {{ $diffClass }}">{{ Money::formatRate($diff) }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="value-row">
            <div class="currency-col-value left">
                <div class="currency-header">Лучшая покупка</div>
                <div class="currency-row-value">
                    <span id="buy_{{ $code }}" class="currency-value">{{ Money::formatRate($buy['rate'] ?? null, 0) }}</span><span>&nbsp;сум</span>
                </div>
                @if ($buy)
                    <a id="link_buy_{{ $code }}"
                        href="{{ route('exchange-rates.show', ['currency' => $code]) }}"
                        class="currency-bank-link">{{ $buy['bank_name'] }}</a>
                @else
                    <span class="currency-bank-link">—</span>
                @endif
            </div>
            <div class="currency-col-value left">
                <div class="currency-header">Лучшая продажа</div>
                <div class="currency-row-value">
                    <span id="sale_{{ $code }}" class="currency-value">{{ Money::formatRate($sell['rate'] ?? null, 0) }}</span><span>&nbsp;сум</span>
                </div>
                @if ($sell)
                    <a id="link_sale_{{ $code }}"
                        href="{{ route('exchange-rates.show', ['currency' => $code]) }}"
                        class="currency-bank-link">{{ $sell['bank_name'] }}</a>
                @else
                    <span class="currency-bank-link">—</span>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="currency-row">
        <div class="currency-col">
            <div class="currency-name">Курсы пока недоступны. Запустите парсер в админке.</div>
        </div>
    </div>
@endforelse
