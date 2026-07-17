@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banks.css') }}">
    <link rel="stylesheet" href="{{ asset('css/credits.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1 class="entry-title">{{ $title }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ route('credits.index') }}" title="Кредиты"><span itemprop="name">Кредиты</span></a>
                    <meta itemprop="position" content="2" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $type->name }}</span>
                    <meta itemprop="position" content="3" />
                </li>
            </ol>
        </div>
    </div>
</div>

<div id="main" class="sidebar-none sidebar-divider-vertical">
    <div class="main-gradient"></div>
    <div class="wf-wrap">
        <div class="wf-container-main">
            <div id="content" class="content" role="main">
                <p class="credits-disclaimer" style="margin-top: 0; margin-bottom: 1.25rem;">
                    Информация о ставках и условиях взята из открытых источников.
                    Пожалуйста, уточняйте условия продуктов в отделениях банков.
                </p>

                <form class="filters" method="get" action="{{ url()->current() }}">
                    <div class="filters-wrapper">
                        <div class="filters-content">
                            <div class="filter-item-block">
                                <label class="filter-label" for="filter-bank">Банк</label>
                                <select id="filter-bank" name="bank_id" class="filter-item">
                                    <option value="">Все банки</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}" @selected(($filters['bank_id'] ?? null) == $bank->id)>
                                            {{ $bank->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-item-block">
                                <label class="filter-label" for="filter-currency">Валюта</label>
                                <select id="filter-currency" name="currency" class="filter-item">
                                    <option value="">Все</option>
                                    @foreach ($currencies as $code)
                                        <option value="{{ $code }}" @selected(($filters['currency'] ?? null) === $code)>
                                            {{ $code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-item-block">
                                <label class="filter-label" for="filter-srok">Срок</label>
                                <select id="filter-srok" name="srok" class="filter-item">
                                    @foreach ($termOptions as $option)
                                        <option value="{{ $option['value'] }}" @selected(($filters['srok'] ?? 'all') === $option['value'])>
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-item-block">
                                <label class="filter-label" for="filter-summa">Сумма</label>
                                <input id="filter-summa" type="text" name="summa" class="filter-item"
                                    value="{{ $filters['summa'] ?? '' }}"
                                    placeholder="Сумма кредита"
                                    inputmode="numeric">
                            </div>
                            <div class="filter-item-block" style="align-self: flex-end;">
                                <button type="submit" class="link" style="padding: 0.5rem 1rem;">Показать</button>
                            </div>
                        </div>
                    </div>
                </form>

                <section id="content-data" class="items-list credits" items-type="credit">
                    @include('public.credits.partials.cards', [
                        'credits' => $credits,
                        'startIndex' => 0,
                    ])
                    @if ($credits->isEmpty())
                        <p>Кредиты по выбранным фильтрам не найдены.</p>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
