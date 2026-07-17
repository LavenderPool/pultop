@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banks.css') }}">
    <link rel="stylesheet" href="{{ asset('css/deposits.css') }}">
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
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">Вклады</span>
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
                <div class="deposits-intro">
                    <p>
                        Представляем актуальные вклады (депозиты) в банках Узбекистана для физических лиц.
                        Сравнивайте вклады «До востребования», «Срочные» и «Сберегательные» по множеству параметров,
                        а также рассчитывайте их доходность. Для вашего удобства у нас есть ещё и отдельный
                        <a href="{{ route('calculators.deposit') }}">калькулятор вкладов</a>.
                    </p>
                    <p><strong>Читайте внимательно вкладку «Особые условия»!</strong></p>
                </div>

                <form id="deposits-filter" class="filters" data-deposits-filter action="{{ route('api.deposits') }}" method="get">
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
                                    placeholder="Сумма вклада"
                                    inputmode="numeric">
                            </div>
                        </div>
                    </div>
                    <div class="filters-bottom">
                        <div class="filters-wrapper bottom">
                            <div class="filters-content filters-flags">
                                <label class="filter-item">
                                    <input type="checkbox" name="is_online" value="1"
                                        @checked(!empty($filters['is_online']))>
                                    On-line
                                </label>
                            </div>
                        </div>
                    </div>
                </form>

                <section id="content-data" class="items-list deposits" items-type="deposit"
                    data-deposits-list
                    data-page="1"
                    data-has-more="{{ $hasMore ? '1' : '0' }}">
                    @include('public.deposits.partials.cards', [
                        'deposits' => $deposits,
                        'startIndex' => 0,
                    ])
                    @if ($deposits->isEmpty())
                        <p class="deposits-empty" data-deposits-empty>Вклады не найдены.</p>
                    @endif
                </section>

                <div class="deposits-load-more" data-deposits-load-more @if(! $hasMore) hidden @endif>
                    <button type="button" class="deposits-load-more-btn" data-deposits-load-more-btn>
                        Показать ещё
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/deposits.js') }}" defer></script>
@endpush
