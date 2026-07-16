@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kurs/currency.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kurs/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kurs/custom-input.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
			<div class="wf-wrap">

				<div class="page-title-head hgroup"><h1>{{ $title }}</h1></div><div class="page-title-breadcrumbs"><div class="assistive-text">You are here:</div><ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList"><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ url('/kurs-obmena-valyut') }}/" title="Курс валют в банках Узбекистана"><span itemprop="name">Курс валют в банках Узбекистана</span></a><meta itemprop="position" content="2" /></li><li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name">{{ $currency['name_ru'] }}</span><meta itemprop="position" content="3" /></li></ol></div>			</div>
		</div>
<div id="main" class="sidebar-right sidebar-divider-vertical"
     data-exchange-rates
     data-api-url="{{ $apiUrl }}"
     data-currency="{{ $currency['code'] }}"
     data-operation="{{ $operation }}"
     data-place="{{ $place }}">
<div class="main-gradient"></div>
<div class="wf-wrap">
<div class="wf-container-main">
<div id="content" class="content" role="main">
<h2>Курс {{ mb_strtolower($currency['name_ru']) }} к суму</h2>
<p>Актуальный курс обмена {{ $currency['name_ru'] }} ({{ $currency['code_upper'] }}) на узбекские сумы (UZS) в Узбекистане сегодня. Выберите самые выгодные условия покупки или продажи {{ mb_strtolower($currency['name_ru']) }} в банках Узбекистана.</p>
<div class="custom-select" id="select-currency" style="width: 100%; min-width: 100%; margin-top: 32px;">
    <div class="select-selected" style="border-radius: 0; padding: 16px 16px 16px 32px;">
    <div class="cbu-rate">
        <span class="selected-text"><span>{{ $currency['flag'] }}</span>&nbsp;{{ $currency['name_ru'] }}</span>
        @if ($currency['cbu_rate'])
            <span style="position: relative; font-size: 1.3em; font-weight: bold;">
                <span>$</span>1 = {{ \App\Support\Money::formatRate($currency['cbu_rate']) }}
                &nbsp;<span>UZS</span>
                @if ($currency['cbu_diff'] !== null)
                    <span style="font-size: 0.7em; position: absolute; top: -15px; color: {{ (float)$currency['cbu_diff'] >= 0 ? 'green' : 'red' }};">
                        {{ \App\Support\Money::formatRate($currency['cbu_diff']) }}
                    </span>
                @endif
            </span>
        @endif
    </div>
        <span class="select-arrow">▼</span>
        <input type="hidden" name="selected-value" value="" autocomplete="off">
    </div>
    <div class="select-items" style="border-radius: 0;">
        <div class="search-container" style="display: none;">
            <input type="text" class="select-search" placeholder="Поиск..." autocomplete="off">
        </div>
        <div class="select-options" style="max-height: 320px; overflow: auto;">
            @foreach ($currencies as $item)
                <a class="select-option {{ !empty($item['is_current']) ? 'selected' : '' }}" data-value="kurs-obmena-valyut/{{ $item['code'] }}" href="{{ $item['url'] }}">
                    {{ $item['flag'] }} {{ $item['name_ru'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>
<details class="exchange-rate-chart" data-source="1">
    <summary>
        <div class="summary-wrapper">
            <div class="summary-title">
                <h3>График изменения курса</h3>
            </div>
            <div class="summary-icon">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentcolor">
                        <path d="m18.7 9.7-6 6c-.2.2-.5.3-.7.3s-.5-.1-.7-.3l-6-6c-.4-.4-.4-1 0-1.4s1-.4 1.4 0l5.3 5.3 5.3-5.3c.4-.4 1-.4 1.4 0s.4 1 0 1.4z"></path>
                    </svg>
                </span>
            </div>
        </div>
    </summary>
    <div class="details-content">
        <section class="panel-chart">
            <div class="panel-chart-contnet">
                <div id="cbu-chart"></div>
            </div>
        </section>
    </div>
</details>
<details class="rate-block" data-source="1" open>
    <summary>
        <div class="summary-wrapper">
            <div class="summary-title"><h3>Лучший курс</h3></div>
            <div class="summary-icon"><span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentcolor"><path d="m18.7 9.7-6 6c-.2.2-.5.3-.7.3s-.5-.1-.7-.3l-6-6c-.4-.4-.4-1 0-1.4s1-.4 1.4 0l5.3 5.3 5.3-5.3c.4-.4 1-.4 1.4 0s.4 1 0 1.4z"></path></svg>
            </span></div>
        </div>
    </summary>
    <div class="rate-content" data-best-rates>
        @foreach ($places as $placeItem)
            @php $bestItem = $best[$placeItem['value']] ?? ['buy' => null, 'sell' => null]; @endphp
            <div class="rate-row" data-place="{{ $placeItem['value'] }}">
                <div class="rate-label">{{ $placeItem['label'] }}</div>
                <div class="rate-values">
                    <div class="rate-value">
                        <span class="rate-currency" data-best-buy>{{ \App\Support\Money::formatRate($bestItem['buy'] ?? null, 0) }}</span>
                        <span>UZS</span>
                        <span class="rate-brige-operation">покупка</span>
                    </div>
                    <div class="rate-value">
                        <span class="rate-currency" data-best-sell>{{ \App\Support\Money::formatRate($bestItem['sell'] ?? null, 0) }}</span>
                        <span>UZS</span>
                        <span class="rate-brige-operation">продажа</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</details>
<form class="action-details" id="filter" method="post" onsubmit="formSubmit(event)" onchange="formChange(event)">

    <input type="hidden" name="nonce" value="">
    <input type="hidden" name="currency" value="{{ $currency['code'] }}">

    <div class="custom-select" id="action">
        <div class="select-selected" style="border-radius: 0;">
            <span class="selected-text">{{ $operation === 'buy' ? 'Я покупаю' : 'Я продаю' }}</span>
            <span class="select-arrow">▼</span>
            <input type="hidden" name="selected-value" value="{{ $operation }}" autocomplete="off">
        </div>
        <div class="select-items" style="border-radius: 0;">
            <div class="search-container" style="display: none;">
                <input type="text" class="select-search" placeholder="Поиск..." autocomplete="off">
            </div>
            <div class="select-options">
                <div class="select-option {{ $operation === 'buy' ? 'selected' : '' }}" data-value="buy">Я покупаю</div>
                <div class="select-option {{ $operation === 'sell' ? 'selected' : '' }}" data-value="sell">Я продаю</div>
            </div>
        </div>
    </div>
    <div class="custom-select" id="place">
        <div class="select-selected" style="border-radius: 0;">
            <span class="selected-text">{{ $place === 'cash' ? 'В обменном пунктe' : ($place === 'atm' ? 'В банкомате' : 'В приложении банка') }}</span>
            <span class="select-arrow">▼</span>
            <input type="hidden" name="selected-value" value="{{ $place }}" autocomplete="off">
        </div>
        <div class="select-items" style="border-radius: 0;">
            <div class="search-container" style="display: none;">
                <input type="text" class="select-search" placeholder="Поиск..." autocomplete="off">
            </div>
            <div class="select-options">
                <div class="select-option {{ $place === 'cash' ? 'selected' : '' }}" data-value="cash">В обменном пунктe</div>
                <div class="select-option {{ $place === 'atm' ? 'selected' : '' }}" data-value="atm">В банкомате</div>
                <div class="select-option {{ $place === 'app' ? 'selected' : '' }}" data-value="app">В приложении банка</div>
            </div>
        </div>
    </div>
    <div class="custom-input" style="position: relative;">
        <input id="amount" type="text" name="amount" placeholder="Сумма" style="border-radius: 0; margin-bottom: 0;" autocomplete="off" value="" inputmode="numeric" enterkeyhint="done">
        <div style="position: absolute;top: 12px;right: 10px;color: #888; font-weight: 100; text-transform: uppercase;">{{ $currency['code'] }}</div>
    </div>
</form>
<section class="rates">
    <div class="rates-list" data-rates-list>
        @forelse ($rates as $rate)
            <div class="UniSearchList-Item">
                <div class="FinanceItem FinanceItem_view_horizontal-button UniSearchDepositsItem">
                    <div class="FinanceItem-Upper">
                        <div class="FinanceItem-Header">
                            <div class="FinanceItem-BankIcon">
                                <div class="FinanceItem-BankIconImage" @if(!empty($rate['logo_url'])) style="background-image:url({{ $rate['logo_url'] }})" @endif></div>
                            </div>
                            <div class="FinanceItem-HeaderTitleContainer">
                                <h3 class="FinanceItem-HeaderTitle">{{ $rate['bank_name'] }}</h3>
                                <div class="FinanceItem-HeaderSubtitleContainer">
                                    <div class="FinanceItem-HeaderSubtitle">
                                        @if ($rate['fetched_at']){{ $rate['fetched_at'] }} 🕒 @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="FinanceItem-Body">
                            <div class="FinanceItem-ProductDetails_view_horizontal FinanceItem-ProductDetails" style="display: flex;">
                                <div class="FinanceItem-ProductDetail">
                                    <div class="FinanceItem-ProductDetailLabel">{{ $operation === 'buy' ? 'Курс продажи' : 'Курс покупки' }}</div>
                                    <div class="FinanceItem-ProductDetailValue">{{ \App\Support\Money::formatRate($rate['rate'], 0) }}&nbsp;<span style="color: #888; font-weight: 100;">UZS</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p style="padding: 16px; margin-top: 32px; color: #888;">Курсы банков пока недоступны.</p>
        @endforelse
    </div>
</section>

</div>

<x-public.sidebar />

</div>
</div>
</div>
@endsection

@push('scripts')
    <script>
        window.hasScrolled = true;
        window.currency = @json($currency['code_upper']);
        window.currencyHistory = {
            currency: @json($currency['code_upper']),
            dates: @json(collect($history)->pluck('date')->values()),
            values: @json(collect($history)->pluck('rate')->values())
        };
        window.ratesApiUrl = @json($apiUrl);
    </script>
    <script src="{{ asset('js/kurs/apexcharts.js') }}"></script>
    <script src="{{ asset('js/kurs/custom-select.js') }}"></script>
    <script src="{{ asset('js/kurs/custom-input.js') }}"></script>
    <script src="{{ asset('js/kurs/currency.js') }}"></script>
    <script src="{{ asset('js/kurs/exchange-page.js') }}"></script>
@endpush
