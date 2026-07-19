@extends('layouts.public')

@section('title', $title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banks.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1 class="entry-title">{{ $h1 }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">Карты</span>
                    <meta itemprop="position" content="2" />
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
                <p style="margin-bottom: 1.25rem;">
                    Информация о банковских картах и условиях их оформления взята из открытых источников.
                    Пожалуйста, уточняйте условия продуктов в отделениях банков.
                </p>

                <section class="filters">
                    <form method="get" action="{{ route('cards.index') }}" class="filters-form">
                        <div class="filters-wrapper">
                            <div class="filters-content">
                                <div class="filter-item-block">
                                    <div>Банк</div>
                                    <select id="banks_list" name="bank_id" class="filter-item" aria-label="Список банков">
                                        <option value="">Все банки</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}" @selected((int) ($filters['bank_id'] ?? 0) === $bank->id)>
                                                {{ $bank->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-item-block">
                                    <div>Валюта</div>
                                    <select class="filter-item" id="currency" name="currency" aria-label="Валюта">
                                        <option value="">Любая</option>
                                        @foreach ($currencies as $code => $label)
                                            <option value="{{ $code }}" @selected(($filters['currency'] ?? null) === $code)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-item-block">
                                    <div>Платежная система</div>
                                    <select class="filter-item" id="payment_system" name="payment_system" aria-label="Платежная система">
                                        <option value="">Любая</option>
                                        @foreach ($paymentSystems as $system)
                                            <option value="{{ $system }}" @selected(($filters['payment_system'] ?? null) === $system)>
                                                {{ $system }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-item-block">
                                    <div>Тип карты</div>
                                    <select class="filter-item" id="type_card" name="card_type" aria-label="Тип карты">
                                        <option value="">Любая</option>
                                        @foreach ($cardTypes as $type)
                                            <option value="{{ $type->value }}" @selected(($filters['card_type'] ?? null) === $type->value)>
                                                {{ $type->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-item-block" style="align-self: flex-end;">
                                    <button type="submit" class="link" style="padding: 0.5rem 1rem;">Показать</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>

                <section id="content-data" class="items-list cards" items-type="card">
                    @forelse ($cards as $index => $card)
                        <div class="item-content credit v-anim-fadein" style="--delay: {{ $index * 50 }}ms">
                            @if ($card->imageUrl())
                                <div class="card-image" style="width: 300px;">
                                    <img src="{{ $card->imageUrl() }}" alt="{{ $card->title }}">
                                </div>
                            @endif

                            <div style="margin-left: 25px;">
                                <div class="bank-logo">
                                    @if ($card->bank?->logoUrl())
                                        <img class="bank-logo-img" src="{{ $card->bank->logoUrl() }}"
                                            alt="Logo {{ $card->bank->name }}"
                                            title="{{ $card->bank->name }}">
                                    @endif
                                    @if ($card->bank)
                                        <a class="item-name-bank" href="{{ route('banks.show', $card->bank) }}">
                                            {{ $card->bank->name }}
                                        </a>
                                    @endif
                                </div>

                                <div class="item-data">
                                    <a href="{{ route('cards.show', $card) }}" style="text-decoration: none;" class="item-name">
                                        <span>{{ $card->title }}</span>
                                    </a>
                                </div>
                            </div>

                            <div style="flex: 1;">
                                @if ($card->payment_system)
                                    <div><span style="color: #797979;">Платежная система:</span> {{ $card->payment_system }}</div>
                                @endif
                                @if ($card->card_type)
                                    <div><span style="color: #797979;">Тип карты:</span> {{ $card->card_type->label() }}</div>
                                @endif
                                @if ($card->category)
                                    <div><span style="color: #797979;">Категория:</span> {{ $card->category }}</div>
                                @endif
                                @if ($card->issue_cost_display)
                                    <div><span style="color: #797979;">Стоимость выпуска карты:</span> {{ $card->issue_cost_display }}</div>
                                @endif
                                @if ($card->validity_display)
                                    <div><span style="color: #797979;">Срок действия:</span> {{ $card->validity_display }}</div>
                                @endif
                            </div>

                            <div class="btn-more">
                                <a class="link" href="{{ route('cards.show', $card) }}">Подробнее</a>
                            </div>
                        </div>
                    @empty
                        <p>Карты по выбранным фильтрам не найдены.</p>
                    @endforelse
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
