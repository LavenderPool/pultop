@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gold.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1>{{ $title }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $title }}</span>
                    <meta itemprop="position" content="2" />
                </li>
            </ol>
        </div>
    </div>
</div>

<div class="wf-wrap">
<div class="wf-container-main">
<div id="main" class="sidebar-none"
     data-gold-page
     data-api-url="{{ $chartApiUrl }}">
<div id="content" class="content" role="main">

<p>Сколько сегодня стоит золото в Узбекистане? Где купить золото в слитках и монетах? Посмотреть динамику изменения стоимости золота можно на этой странице. Мы ежедневно обновляем стоимость продажи золотых слитков, которая устанавливается Центральным банком Узбекистана исходя из конъюнктуры международного рынка драгоценных металлов.</p>

@if ($pricedOn)
    <p style="text-align: center; color: #999;"><span class="item-title">Дата обновления: {{ $pricedOn }}</span></p>
@endif

<section class="gold-price">
    <div class="gold-title">Номинал слитка</div>

    <div class="tabs">
        <div class="tab-header">
            @foreach ($prices as $price)
                <div @class(['active' => $loop->first]) tab-id="{{ $price['wp_index'] }}">
                    <span>
                        <img class="gold-img"
                             src="{{ asset('images/gold/'.$price['image']) }}"
                             alt="{{ $price['weight_label'] }}"
                             height="50%">
                    </span>
                    {{ $price['weight_label'] }}
                </div>
            @endforeach
        </div>
        <div class="tab-indicator"></div>
        <div class="tab-body"></div>
    </div>

    <div class="gold-title">Динамика изменения цены</div>
    <canvas id="goldChart"></canvas>

    <div class="gold-title">Изменение стоимости одного слитка золота</div>
    <div class="gold-table">
        <div class="btn-period">
            <button type="button" class="btn-period-gold active" data="7">За 7 дней</button>
            <button type="button" class="btn-period-gold" data="30">За 30 дней</button>
            <button type="button" class="btn-period-gold" data="90">За 90 дней</button>
        </div>
        <div style="background: #344a6d; color: white;">
            <div>Дата</div>
            <div>Цена, сум</div>
            <div>Темп, сум</div>
        </div>
    </div>
    <div id="goldTable" class="gold-table"></div>

    <div class="gold-place-panel">
        <h6>Места продаж золотых слитков</h6>
        @if ($regions !== [])
            <select id="region">
                @foreach ($regions as $region)
                    <option value="{{ $region }}" @selected($region === $defaultRegion)>{{ $region }}</option>
                @endforeach
            </select>
        @endif
    </div>

    <div id="goldSaleTable" class="gold-table"></div>

    <table class="gold-place-sale">
        <tbody>
            <tr class="header" style="background: #f9f4f4;">
                <th><b>Название банка</b></th>
                <th><b>Адрес</b></th>
                <th><b>Контактный номер</b></th>
            </tr>
            @forelse ($salePoints as $point)
                <tr region="{{ $point['region'] }}"
                    @if ($defaultRegion !== null && $point['region'] !== $defaultRegion) style="display:none" @endif>
                    <td>{{ $point['bank_name'] }}</td>
                    <td>{{ $point['address'] }}</td>
                    <td>{{ $point['phone'] ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="padding: 16px; color: #888;">Места продаж пока не загружены.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

</div>
</div>
</div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script src="{{ asset('js/gold.js') }}"></script>
@endpush
