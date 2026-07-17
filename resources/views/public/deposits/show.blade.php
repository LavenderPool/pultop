@extends('layouts.public')

@section('title', $title.' - '.config('app.name', 'Pultop'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banks.css') }}">
    <link rel="stylesheet" href="{{ asset('css/deposits.css') }}">
@endpush

@section('content')
<div class="page-title content-left solid-bg page-title-responsive-enabled">
    <div class="wf-wrap">
        <div class="page-title-head hgroup"><h1 class="entry-title">{{ $deposit->title }}</h1></div>
        <div class="page-title-breadcrumbs">
            <div class="assistive-text">You are here:</div>
            <ol class="breadcrumbs text-small" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ url('/') }}" title="Home"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ route('deposits.index') }}" title="Вклады"><span itemprop="name">Вклады</span></a>
                    <meta itemprop="position" content="2" />
                </li>
                <li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ \Illuminate\Support\Str::limit($deposit->title, 40) }}</span>
                    <meta itemprop="position" content="3" />
                </li>
            </ol>
        </div>
    </div>
</div>

<div id="main" class="sidebar-right sidebar-divider-vertical">
    <div class="main-gradient"></div>
    <div class="wf-wrap">
        <div class="wf-container-main">
            <div id="content" class="content" role="main">
                <div class="card-header">
                    <div>
                        @if ($deposit->bank)
                            <h2 style="margin-bottom: 0.25rem;">
                                <a href="{{ route('banks.show', $deposit->bank) }}">{{ $deposit->bank->name }}</a>
                            </h2>
                        @endif
                        <p>
                            вклад: <strong>{{ $deposit->title }}</strong>
                        </p>
                    </div>
                    @if ($deposit->bank?->logoUrl())
                        <div>
                            <img src="{{ $deposit->bank->logoUrl() }}" width="50" height="50"
                                alt="лого банка: {{ $deposit->bank->name }}">
                        </div>
                    @endif
                </div>

                <div class="info-top">
                    <div class="info-rate">
                        <h3 class="card-title">Таблица ставок</h3>
                        <div class="info-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Ставка</th>
                                        <th scope="col">Срок</th>
                                        <th scope="col">Примечание</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deposit->rates as $row)
                                        <tr class="rate-data">
                                            <th scope="row">{{ $row->rate }}</th>
                                            <td style="white-space: nowrap;">{{ $row->term }}</td>
                                            <td>{{ $row->note }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="info-conditions">
                    <div class="base">
                        <h2>Базовые условия</h2>
                        <div>
                            <ul class="item-detail" style="margin-left: 0;">
                                @foreach ($deposit->conditions as $condition)
                                    <li class="item-detail-item">
                                        <span class="item-detail-item-label">{{ $condition->label }}</span>
                                        <span class="item-detail-item-value">
                                            <span class="item-detail-item-text fw-bold">{{ $condition->value }}</span>
                                            @if ($condition->note)
                                                <div><small>{{ $condition->note }}</small></div>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @if ($deposit->special_conditions)
                        <div class="special" style="overflow: auto;">
                            <h2>Особые условия</h2>
                            <div>{!! $deposit->special_conditions !!}</div>
                        </div>
                    @endif
                </div>

                <div class="info-footer">
                    @if ($deposit->bank)
                        <div class="info-btn">
                            <a class="info-link-btn" href="{{ route('banks.show', $deposit->bank) }}">Карточка банка </a>
                        </div>
                    @endif
                    @if ($deposit->apply_url)
                        <div class="info-btn">
                            <a class="info-link-btn" href="{{ $deposit->apply_url }}" target="_blank" rel="noopener noreferrer">
                                Открыть вклад на сайте банка
                            </a>
                        </div>
                    @endif
                    <div class="info-btn">
                        <a class="info-link-btn" href="{{ route('calculators.deposit') }}">Калькулятор вкладов </a>
                    </div>
                </div>

                @if ($otherDeposits->isNotEmpty())
                    <div class="info-more">
                        <h3>Другие вклады банка</h3>
                        <table>
                            <tbody>
                                <tr>
                                    <th>Название</th>
                                    <th>Срок</th>
                                    <th>Ставка</th>
                                    <th>Сумма</th>
                                    <th></th>
                                </tr>
                                @foreach ($otherDeposits as $other)
                                    <tr>
                                        <td>
                                            <a href="{{ route('deposits.show', $other) }}">{{ $other->title }}</a>
                                        </td>
                                        <td>{{ $other->term_display }}</td>
                                        <td>{{ $other->rate_display }}</td>
                                        <td>{{ $other->amount_display }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <x-public.sidebar />
        </div>
    </div>
</div>
@endsection
